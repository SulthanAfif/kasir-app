<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Setting;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosApiController extends Controller
{
    public function products(Request $request)
    {
        $q = $request->get('q');
        $query = Product::where('is_active', true)->where('stock', '>', 0);

        if ($q) {
            $query->where(function ($qb) use ($q) {
                $qb->where('name', 'like', "%{$q}%")->orWhere('code', 'like', "%{$q}%");
            });
        }

        return response()->json($query->orderBy('name')->limit(50)->get([
            'id', 'code', 'name', 'price', 'stock', 'unit', 'category_id'
        ]));
    }

    public function checkout(Request $request)
    {
        $user = $request->attributes->get('api_user');

        $shift = Shift::currentFor($user->id);
        if (!$shift) {
            return response()->json(['message' => 'Buka shift terlebih dahulu.'], 422);
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_name' => 'nullable|string|max:100',
            'discount' => 'nullable|numeric|min:0',
            'paid' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer,qris,other',
        ]);

        $taxRate = (float) Setting::get('tax_rate', 0);

        try {
            DB::beginTransaction();
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak cukup.");
                }
                $line = $product->price * $item['quantity'];
                $subtotal += $line;
                $itemsData[] = compact('product') + ['quantity' => $item['quantity'], 'subtotal' => $line];
            }

            $discount = $validated['discount'] ?? 0;
            $after = max(0, $subtotal - $discount);
            $tax = round($after * ($taxRate / 100), 2);
            $total = $after + $tax;
            $paid = $validated['paid'];

            if ($paid < $total) {
                throw new \Exception('Pembayaran kurang.');
            }

            $trx = Transaction::create([
                'invoice_number' => Transaction::generateInvoiceNumber(),
                'user_id' => $user->id,
                'branch_id' => $user->branch_id,
                'shift_id' => $shift->id,
                'customer_name' => $validated['customer_name'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax_rate' => $taxRate,
                'tax' => $tax,
                'total' => $total,
                'paid' => $paid,
                'change' => max(0, $paid - $total),
                'payment_method' => $validated['payment_method'],
                'status' => 'completed',
            ]);

            foreach ($itemsData as $d) {
                TransactionItem::create([
                    'transaction_id' => $trx->id,
                    'product_id' => $d['product']->id,
                    'product_name' => $d['product']->name,
                    'price' => $d['product']->price,
                    'quantity' => $d['quantity'],
                    'subtotal' => $d['subtotal'],
                ]);
                $d['product']->adjustStock(-$d['quantity'], 'sale', $user->id, $trx->invoice_number);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'invoice' => $trx->invoice_number,
                'transaction_id' => $trx->id,
                'total' => $trx->total,
                'change' => $trx->change,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function shifts(Request $request)
    {
        $user = $request->attributes->get('api_user');
        $current = Shift::currentFor($user->id);
        return response()->json(['current' => $current]);
    }
}
