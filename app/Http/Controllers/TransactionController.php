<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'customer'])->whereIn('status', ['completed', 'cancelled']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();

        return view('transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['items.product', 'user', 'customer', 'shift']);
        return view('transactions.show', compact('transaction'));
    }

    public function receipt(Transaction $transaction)
    {
        $transaction->load(['items', 'user', 'customer']);
        $store = [
            'name' => Setting::get('store_name', 'KasirApp'),
            'address' => Setting::get('store_address', ''),
            'phone' => Setting::get('store_phone', ''),
            'footer' => Setting::get('receipt_footer', 'Terima kasih!'),
        ];
        return view('transactions.receipt', compact('transaction', 'store'));
    }

    /**
     * Refund / batalkan transaksi — kembalikan stok.
     */
    public function refund(Request $request, Transaction $transaction)
    {
        if (!$transaction->isRefundable()) {
            return back()->with('error', 'Transaksi ini tidak dapat di-refund.');
        }

        // Hanya admin atau kasir yang sama
        if (!Auth::user()->isAdmin() && $transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $transaction->load('items.product');

            foreach ($transaction->items as $item) {
                if ($item->product) {
                    $item->product->adjustStock(
                        $item->quantity, // kembalikan stok
                        'refund',
                        Auth::id(),
                        $transaction->invoice_number,
                        $request->reason
                    );
                }
            }

            $transaction->update([
                'status' => 'cancelled',
                'notes' => trim(($transaction->notes ? $transaction->notes . "\n" : '') . 'REFUND: ' . ($request->reason ?? '-')),
            ]);

            DB::commit();
            return redirect()->route('transactions.show', $transaction)
                ->with('success', 'Transaksi berhasil di-refund. Stok telah dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal refund: ' . $e->getMessage());
        }
    }
}
