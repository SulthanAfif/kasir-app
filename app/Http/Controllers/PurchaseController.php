<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'user'])->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%');
        }
        $purchases = $query->paginate(15)->withQueryString();
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'paid' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.cost' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'receive_now' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $itemsData = [];
            foreach ($validated['items'] as $row) {
                $product = Product::findOrFail($row['product_id']);
                $line = $row['cost'] * $row['quantity'];
                $subtotal += $line;
                $itemsData[] = [
                    'product' => $product,
                    'cost' => $row['cost'],
                    'quantity' => $row['quantity'],
                    'subtotal' => $line,
                ];
            }

            $discount = $validated['discount'] ?? 0;
            $tax = $validated['tax'] ?? 0;
            $total = max(0, $subtotal - $discount + $tax);
            $paid = $validated['paid'] ?? 0;
            $paymentStatus = $paid <= 0 ? 'unpaid' : ($paid >= $total ? 'paid' : 'partial');
            $receiveNow = $request->boolean('receive_now');

            $purchase = Purchase::create([
                'invoice_number' => Purchase::generateInvoiceNumber(),
                'supplier_id' => $validated['supplier_id'],
                'user_id' => Auth::id(),
                'branch_id' => Auth::user()->branch_id,
                'purchase_date' => $validated['purchase_date'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'paid' => $paid,
                'payment_status' => $paymentStatus,
                'status' => $receiveNow ? 'received' : 'draft',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($itemsData as $data) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $data['product']->id,
                    'product_name' => $data['product']->name,
                    'cost' => $data['cost'],
                    'quantity' => $data['quantity'],
                    'subtotal' => $data['subtotal'],
                ]);

                if ($receiveNow) {
                    $data['product']->adjustStock(
                        $data['quantity'],
                        'in',
                        Auth::id(),
                        $purchase->invoice_number,
                        'Pembelian dari supplier'
                    );
                    // Update cost if needed
                    $data['product']->update(['cost' => $data['cost']]);
                }
            }

            DB::commit();
            return redirect()->route('purchases.show', $purchase)
                ->with('success', $receiveNow ? 'Pembelian diterima & stok diperbarui.' : 'Draft pembelian disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'items.product', 'branch']);
        return view('purchases.show', compact('purchase'));
    }

    public function receive(Purchase $purchase)
    {
        if ($purchase->status !== 'draft') {
            return back()->with('error', 'Hanya draft yang bisa diterima.');
        }

        try {
            DB::beginTransaction();
            $purchase->load('items.product');
            foreach ($purchase->items as $item) {
                if ($item->product) {
                    $item->product->adjustStock(
                        $item->quantity,
                        'in',
                        Auth::id(),
                        $purchase->invoice_number,
                        'Penerimaan PO'
                    );
                    $item->product->update(['cost' => $item->cost]);
                }
            }
            $purchase->update(['status' => 'received']);
            DB::commit();
            return back()->with('success', 'Barang diterima, stok diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Purchase $purchase)
    {
        if ($purchase->status === 'cancelled') {
            return back()->with('error', 'Sudah dibatalkan.');
        }
        if ($purchase->status === 'received') {
            // Kembalikan stok
            try {
                DB::beginTransaction();
                $purchase->load('items.product');
                foreach ($purchase->items as $item) {
                    if ($item->product) {
                        $item->product->adjustStock(
                            -$item->quantity,
                            'out',
                            Auth::id(),
                            $purchase->invoice_number,
                            'Batal PO'
                        );
                    }
                }
                $purchase->update(['status' => 'cancelled']);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', $e->getMessage());
            }
        } else {
            $purchase->update(['status' => 'cancelled']);
        }
        return back()->with('success', 'Pembelian dibatalkan.');
    }
}
