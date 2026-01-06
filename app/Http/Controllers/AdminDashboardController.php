<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\ProductCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get selected month and year from request, default to current month
        $selectedMonth = $request->get('month', now()->month);
        $selectedYear = $request->get('year', now()->year);
        
        // Basic stock statistics (no financial data)
        $totalProducts = Product::count();
        $lowStockProducts = Product::whereColumn('current_stock', '<=', 'minimum_stock')->count();
        $outOfStockProducts = Product::where('current_stock', 0)->count();
        $totalSuppliers = Supplier::count();
        $totalCustomers = Customer::count();

        // Selected month activities (quantities only, no financial data)
        $monthlyStockInSelected = StockMovement::where('type', 'in')
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->sum('quantity');
        $monthlyStockOutSelected = StockMovement::where('type', 'out')
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->sum('quantity');
        $monthlyTransactionsSelected = StockMovement::whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->count();

        // This month's summary (quantities only)
        $monthlyStockIn = StockMovement::where('type', 'in')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('quantity');
        $monthlyStockOut = StockMovement::where('type', 'out')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('quantity');

        // Stock aging analysis with product details
        $fastMovingProducts = Product::with('category')
            ->where('current_stock', '>', 0)
            ->whereHas('stockMovements', function($q) {
                $q->where('transaction_date', '>=', Carbon::now()->subDays(30));
            })->take(10)->get();
            
        $slowMovingProducts = Product::with('category')
            ->where('current_stock', '>', 0)
            ->whereHas('stockMovements', function($q) {
                $q->where('transaction_date', '<', Carbon::now()->subDays(30))
                  ->where('transaction_date', '>=', Carbon::now()->subDays(90));
            })->take(10)->get();
            
        $deadStockProducts = Product::with('category')
            ->where('current_stock', '>', 0)
            ->whereDoesntHave('stockMovements', function($q) {
                $q->where('transaction_date', '>=', Carbon::now()->subDays(90));
            })->take(10)->get();

        $stockAging = [
            'fast_moving' => $fastMovingProducts->count(),
            'slow_moving' => $slowMovingProducts->count(),
            'dead_stock' => $deadStockProducts->count(),
            'fast_moving_products' => $fastMovingProducts,
            'slow_moving_products' => $slowMovingProducts,
            'dead_stock_products' => $deadStockProducts
        ];

        // Recent stock movements (last 15)
        $recentMovements = StockMovement::with(['product', 'supplier', 'customer'])
            ->orderBy('transaction_date', 'desc')
            ->take(15)
            ->get();

        // Enhanced chart data for selected month (daily quantities only)
        $chartData = [];
        $startOfMonth = Carbon::create($selectedYear, $selectedMonth, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $daysInMonth = $endOfMonth->day;
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($selectedYear, $selectedMonth, $day);
            
            $stockIn = StockMovement::where('type', 'in')
                ->whereDate('transaction_date', $date)
                ->sum('quantity');
            $stockOut = StockMovement::where('type', 'out')
                ->whereDate('transaction_date', $date)
                ->sum('quantity');
            
            $chartData[] = [
                'period' => $date->format('d'),
                'full_date' => $date->format('d/m/Y'),
                'stock_in' => $stockIn,
                'stock_out' => $stockOut
            ];
        }

        // Critical stock alerts (products with stock <= minimum)
        $criticalStockList = Product::with('category')
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->orderBy('current_stock', 'asc')
            ->take(10)
            ->get();

        // Recent stock in transactions (last 30 days)
        $recentStockIn = StockMovement::with(['product.category', 'supplier'])
            ->where('type', 'in')
            ->where('transaction_date', '>=', Carbon::now()->subDays(30))
            ->orderBy('transaction_date', 'desc')
            ->take(10)
            ->get();

        // Products expiring in 3 months
        $expiring3Months = Product::with('category')
            ->whereNotNull('expired_date')
            ->whereBetween('expired_date', [
                Carbon::now(),
                Carbon::now()->addMonths(3)
            ])
            ->orderBy('expired_date', 'asc')
            ->take(10)
            ->get();

        // Products expiring in 2 months
        $expiring2Months = Product::with('category')
            ->whereNotNull('expired_date')
            ->whereBetween('expired_date', [
                Carbon::now(),
                Carbon::now()->addMonths(2)
            ])
            ->orderBy('expired_date', 'asc')
            ->take(10)
            ->get();

        // Active distributors and customers
        $activeSuppliers = Supplier::whereHas('stockMovements', function ($query) {
            $query->where('transaction_date', '>=', Carbon::now()->subMonth());
        })->count();

        $activeCustomers = Customer::whereHas('stockMovements', function ($query) {
            $query->where('transaction_date', '>=', Carbon::now()->subMonth());
        })->count();

        // Generate month options for dropdown
        $monthOptions = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthOptions[] = [
                'value' => $date->month,
                'year' => $date->year,
                'label' => $date->format('F Y'),
                'selected' => $date->month == $selectedMonth && $date->year == $selectedYear
            ];
        }

        return view('admin.dashboard', compact(
            'totalProducts',
            'lowStockProducts',
            'outOfStockProducts',
            'totalSuppliers',
            'totalCustomers',
            'monthlyStockInSelected',
            'monthlyStockOutSelected', 
            'monthlyTransactionsSelected',
            'monthlyStockIn',
            'monthlyStockOut',
            'stockAging',
            'recentMovements',
            'chartData',
            'criticalStockList',
            'activeSuppliers',
            'activeCustomers',
            'selectedMonth',
            'selectedYear',
            'monthOptions',
            'recentStockIn',
            'expiring3Months',
            'expiring2Months'
        ));
    }
}
