<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $stats = [
            'today_sales' => Transaction::whereDate('created_at', $today)
                ->where('status', 'completed')
                ->sum('total'),
            'today_transactions' => Transaction::whereDate('created_at', $today)
                ->where('status', 'completed')
                ->count(),
            'total_products' => Product::where('is_active', true)->count(),
            'low_stock' => Product::where('is_active', true)
                ->whereColumn('stock', '<=', 'min_stock')
                ->count(),
            'total_categories' => Category::count(),
            'total_users' => User::count(),
        ];

        $recentTransactions = Transaction::with('user')
            ->where('status', 'completed')
            ->latest()
            ->take(8)
            ->get();

        $lowStockProducts = Product::with('category')
            ->where('is_active', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->take(5)
            ->get();

        // Chart data: 7 hari terakhir
        $salesChart = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total')
            )
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date');

        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('d M');
            $chartData[] = $salesChart[$date] ?? 0;
        }

        return view('dashboard.index', compact(
            'stats', 'recentTransactions', 'lowStockProducts',
            'chartLabels', 'chartData'
        ));
    }
}
