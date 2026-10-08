<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $summary = Transaction::where('status', 'completed')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->selectRaw('
                COUNT(*) as total_transactions,
                SUM(subtotal) as total_subtotal,
                SUM(discount) as total_discount,
                SUM(tax) as total_tax,
                SUM(total) as total_sales,
                SUM(paid) as total_paid
            ')
            ->first();

        $dailySales = Transaction::where('status', 'completed')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topProducts = TransactionItem::join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.status', 'completed')
            ->whereDate('transactions.created_at', '>=', $dateFrom)
            ->whereDate('transactions.created_at', '<=', $dateTo)
            ->select(
                'transaction_items.product_name',
                DB::raw('SUM(transaction_items.quantity) as total_qty'),
                DB::raw('SUM(transaction_items.subtotal) as total_sales')
            )
            ->groupBy('transaction_items.product_name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        $paymentMethods = Transaction::where('status', 'completed')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get();

        return view('reports.index', compact(
            'summary', 'dailySales', 'topProducts', 'paymentMethods',
            'dateFrom', 'dateTo'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $filename = "laporan-penjualan-{$dateFrom}-{$dateTo}.csv";

        return response()->streamDownload(function () use ($dateFrom, $dateTo) {
            $out = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Invoice', 'Tanggal', 'Kasir', 'Pelanggan', 'Subtotal', 'Diskon', 'Pajak', 'Total', 'Bayar', 'Metode', 'Status']);

            Transaction::with('user')
                ->whereIn('status', ['completed', 'cancelled'])
                ->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo)
                ->orderBy('created_at')
                ->chunk(200, function ($rows) use ($out) {
                    foreach ($rows as $t) {
                        fputcsv($out, [
                            $t->invoice_number,
                            $t->created_at->format('Y-m-d H:i'),
                            $t->user->name ?? '',
                            $t->customer_name ?? '',
                            $t->subtotal,
                            $t->discount,
                            $t->tax,
                            $t->total,
                            $t->paid,
                            $t->payment_method,
                            $t->status,
                        ]);
                    }
                });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $summary = Transaction::where('status', 'completed')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->selectRaw('COUNT(*) as total_transactions, SUM(total) as total_sales, SUM(discount) as total_discount, SUM(tax) as total_tax')
            ->first();

        $transactions = Transaction::with('user')
            ->where('status', 'completed')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->orderBy('created_at')
            ->get();

        $storeName = Setting::get('store_name', 'KasirApp');

        return view('reports.pdf', compact('summary', 'transactions', 'dateFrom', 'dateTo', 'storeName'));
    }
}
