<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function index()
    {
        $shift = Shift::currentFor(Auth::id());

        if (!$shift) {
            return redirect()->route('shifts.index')
                ->with('error', 'Silakan buka shift terlebih dahulu sebelum menggunakan kasir.');
        }

        $products = Product::with('category')
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        $taxRate = (float) Setting::get('tax_rate', 0);
        $storeName = Setting::get('store_name', 'KasirApp');

        return view('pos.index', compact('products', 'taxRate', 'storeName', 'shift'));
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $products = Product::with('category')
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('code', 'like', "%{$q}%");
            })
            ->limit(20)
            ->get();

        return response()->json($products);
    }

    public function checkout(Request $request)
    {
        $shift = Shift::currentFor(Auth::id());
        if (!$shift) {
            return response()->json(['success' => false, 'message' => 'Shift belum dibuka.'], 422);
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:100',
            'discount' => 'nullable|numeric|min:0',
            'paid' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer,qris,other',
            'notes' => 'nullable|string',
        ]);

        $taxRate = (float) Setting::get('tax_rate', 0);

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi. Sisa: {$product->stock}");
                }

                $itemSubtotal = $product->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $itemsData[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                ];
            }

            $discount = $validated['discount'] ?? 0;
            $afterDiscount = max(0, $subtotal - $discount);
            $tax = round($afterDiscount * ($taxRate / 100), 2);
            $total = $afterDiscount + $tax;
            $paid = $validated['paid'];
            $change = max(0, $paid - $total);

            if ($paid < $total) {
                throw new \Exception('Pembayaran kurang dari total belanja (Rp ' . number_format($total, 0, ',', '.') . ').');
            }

            $customerName = $validated['customer_name'] ?? null;
            if (!empty($validated['customer_id'])) {
                $cust = Customer::find($validated['customer_id']);
                $customerName = $cust?->name ?? $customerName;
            }

            $transaction = Transaction::create([
                'invoice_number' => Transaction::generateInvoiceNumber(),
                'user_id' => Auth::id(),
                'customer_id' => $validated['customer_id'] ?? null,
                'shift_id' => $shift->id,
                'customer_name' => $customerName,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax_rate' => $taxRate,
                'tax' => $tax,
                'total' => $total,
                'paid' => $paid,
                'change' => $change,
                'payment_method' => $validated['payment_method'],
                'status' => 'completed',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($itemsData as $data) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $data['product']->id,
                    'product_name' => $data['product']->name,
                    'price' => $data['product']->price,
                    'quantity' => $data['quantity'],
                    'subtotal' => $data['subtotal'],
                ]);

                // Kurangi stok + catat movement
                $data['product']->adjustStock(
                    -$data['quantity'],
                    'sale',
                    Auth::id(),
                    $transaction->invoice_number
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'transaction_id' => $transaction->id,
                'invoice' => $transaction->invoice_number,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
