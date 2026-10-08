<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['product', 'user'])->latest();

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $movements = $query->paginate(20)->withQueryString();
        $products = Product::orderBy('name')->get(['id', 'name', 'code']);

        return view('stock.index', compact('movements', 'products'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('stock.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $qty = (int) $validated['quantity'];

        // For out & adjustment downward we need enough stock conceptually
        if ($validated['type'] === 'out') {
            if ($product->stock < $qty) {
                return back()->with('error', "Stok tidak cukup. Sisa: {$product->stock}")->withInput();
            }
            $qty = -$qty; // negative for out
        } elseif ($validated['type'] === 'adjustment') {
            // adjustment: quantity is the NEW absolute stock
            $newStock = $qty;
            $qty = $newStock - $product->stock; // delta
        }
        // type 'in' stays positive

        try {
            DB::beginTransaction();
            $product->adjustStock(
                $qty,
                $validated['type'],
                Auth::id(),
                null,
                $validated['notes'] ?? null
            );
            DB::commit();
            return redirect()->route('stock.index')->with('success', 'Stok berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
