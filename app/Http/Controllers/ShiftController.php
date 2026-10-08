<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::with('user')->latest()->paginate(15);
        $current = Shift::currentFor(Auth::id());
        return view('shifts.index', compact('shifts', 'current'));
    }

    public function open(Request $request)
    {
        if (Shift::currentFor(Auth::id())) {
            return back()->with('error', 'Anda masih memiliki shift yang terbuka.');
        }

        $validated = $request->validate([
            'opening_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        Shift::create([
            'user_id' => Auth::id(),
            'opened_at' => now(),
            'opening_cash' => $validated['opening_cash'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'open',
        ]);

        return redirect()->route('shifts.index')->with('success', 'Shift berhasil dibuka. Selamat bekerja!');
    }

    public function close(Request $request, Shift $shift)
    {
        if ($shift->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }
        if (!$shift->isOpen()) {
            return back()->with('error', 'Shift sudah ditutup.');
        }

        $validated = $request->validate([
            'closing_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $sales = Transaction::where('shift_id', $shift->id)
            ->where('status', 'completed')
            ->selectRaw('COUNT(*) as cnt, COALESCE(SUM(total),0) as total, COALESCE(SUM(CASE WHEN payment_method = "cash" THEN paid - `change` ELSE 0 END),0) as cash_sales')
            ->first();

        // Expected cash = opening + cash payments net
        $cashSales = Transaction::where('shift_id', $shift->id)
            ->where('status', 'completed')
            ->where('payment_method', 'cash')
            ->sum(DB::raw('paid - `change`'));

        $expected = $shift->opening_cash + $cashSales;
        $difference = $validated['closing_cash'] - $expected;

        $shift->update([
            'closed_at' => now(),
            'closing_cash' => $validated['closing_cash'],
            'expected_cash' => $expected,
            'difference' => $difference,
            'total_transactions' => $sales->cnt ?? 0,
            'total_sales' => $sales->total ?? 0,
            'notes' => trim(($shift->notes ? $shift->notes . "\n" : '') . ($validated['notes'] ?? '')),
            'status' => 'closed',
        ]);

        return redirect()->route('shifts.index')->with('success', 'Shift berhasil ditutup.');
    }

    public function show(Shift $shift)
    {
        $shift->load(['user', 'transactions.items']);
        return view('shifts.show', compact('shift'));
    }
}
