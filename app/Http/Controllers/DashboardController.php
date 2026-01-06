<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\ProductCategory;
use App\Models\CustomerSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get selected month and year from request, default to current month
        $selectedMonth = $request->get('month', now()->month);
        $selectedYear = $request->get('year', now()->year);

        // Main KPI Data
        $totalProducts = Product::count();
        $lowStockProducts = Product::lowStock()->count();
        $totalInventoryValue = Product::sum(DB::raw('current_stock * price'));
        $outOfStockProducts = Product::where('current_stock', 0)->count();
        $totalSuppliers = Supplier::count();
        $totalCustomers = Customer::count();

        // Selected month activities
        $monthlyStockInSelected = StockMovement::stockIn()
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->sum('quantity');
        $monthlyStockOutSelected = StockMovement::stockOut()
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->sum('quantity');
        $monthlyRevenueSelected = StockMovement::stockOut()
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->sum(DB::raw('quantity * COALESCE(unit_price, 0)'));
        $monthlyTransactionsSelected = StockMovement::whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->count();

        // Additional transaction counts for selected month
        $monthlyStockInTransactionsSelected = StockMovement::stockIn()
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->count();
        $monthlyStockOutTransactionsSelected = StockMovement::stockOut()
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->count();
        $monthlyPurchaseSelected = StockMovement::stockIn()
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->sum(DB::raw('quantity * COALESCE(unit_price, 0)'));

        // This month's summary
        $monthlyStockIn = StockMovement::stockIn()
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('quantity');
        $monthlyStockOut = StockMovement::stockOut()
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('quantity');
        $monthlyRevenue = StockMovement::stockOut()
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum(DB::raw('quantity * COALESCE(unit_price, 0)'));

        // Stock aging analysis with product details (with error handling)
        $fastMovingProducts = collect([]);
        $slowMovingProducts = collect([]);
        $deadStockProducts = collect([]);

        try {
            $fastMovingProducts = Product::with('category')
                ->where('current_stock', '>', 0)
                ->whereHas('stockMovements', function ($q) {
                    $q->where('transaction_date', '>=', Carbon::now()->subDays(30));
                })->get();

            $slowMovingProducts = Product::with('category')
                ->where('current_stock', '>', 0)
                ->whereHas('stockMovements', function ($q) {
                    $q->where('transaction_date', '<', Carbon::now()->subDays(30))
                        ->where('transaction_date', '>=', Carbon::now()->subDays(90));
                })->get();

            $deadStockProducts = Product::with('category')
                ->where('current_stock', '>', 0)
                ->whereDoesntHave('stockMovements', function ($q) {
                    $q->where('transaction_date', '>=', Carbon::now()->subDays(90));
                })->get();
        } catch (\Exception $e) {
            Log::warning('Error loading stock aging data: ' . $e->getMessage());
        }

        $stockAging = [
            'fast_moving' => $fastMovingProducts->count(),
            'slow_moving' => $slowMovingProducts->count(),
            'dead_stock' => $deadStockProducts->count(),
            'fast_moving_products' => $fastMovingProducts,
            'slow_moving_products' => $slowMovingProducts,
            'dead_stock_products' => $deadStockProducts
        ];

        // Top performing products (with error handling)
        $topProducts = collect([]);
        try {
            $topProducts = Product::select('products.*', DB::raw('SUM(stock_movements.quantity) as total_sold'))
                ->join('stock_movements', 'products.id', '=', 'stock_movements.product_id')
                ->where('stock_movements.type', 'out')
                ->where('stock_movements.transaction_date', '>=', Carbon::now()->subMonth())
                ->groupBy('products.id', 'products.code', 'products.name', 'products.description', 'products.category_id', 'products.unit', 'products.lot_number', 'products.expired_date', 'products.distribution_permit', 'products.price', 'products.current_stock', 'products.minimum_stock', 'products.created_at', 'products.updated_at')
                ->orderBy('total_sold', 'desc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            Log::warning('Error loading top products: ' . $e->getMessage());
            $topProducts = collect([]);
        }

        // Recent stock movements (last 15)
        $recentMovements = StockMovement::with(['product', 'supplier', 'customer'])
            ->orderBy('transaction_date', 'desc')
            ->take(15)
            ->get();

        // Enhanced chart data for selected month (daily)
        $chartData = [];
        $startOfMonth = Carbon::create($selectedYear, $selectedMonth, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $daysInMonth = $endOfMonth->day;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($selectedYear, $selectedMonth, $day);

            $stockIn = StockMovement::stockIn()
                ->whereDate('transaction_date', $date)
                ->sum('quantity');
            $stockOut = StockMovement::stockOut()
                ->whereDate('transaction_date', $date)
                ->sum('quantity');
            $revenue = StockMovement::stockOut()
                ->whereDate('transaction_date', $date)
                ->sum(DB::raw('quantity * COALESCE(unit_price, 0)'));

            $chartData[] = [
                'period' => $date->format('d'),
                'full_date' => $date->format('d/m/Y'),
                'stock_in' => $stockIn,
                'stock_out' => $stockOut,
                'revenue' => $revenue
            ];
        }

        // Critical stock alerts (products with stock <= minimum)
        $criticalStockList = Product::with('category')
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->orderBy('current_stock', 'asc')
            ->take(10)
            ->get();

        // Top categories by stock value
        $topCategories = ProductCategory::with('products')
            ->get()
            ->map(function ($category) {
                $totalValue = $category->products->sum(function ($product) {
                    return $product->current_stock * $product->price;
                });
                $totalStock = $category->products->sum('current_stock');
                return [
                    'name' => $category->name,
                    'total_value' => $totalValue,
                    'total_stock' => $totalStock,
                    'product_count' => $category->products->count()
                ];
            })
            ->sortByDesc('total_value')
            ->take(5);

        // Active distributors and customers
        $activeSuppliers = Supplier::whereHas('stockMovements', function ($query) {
            $query->where('transaction_date', '>=', Carbon::now()->subMonth());
        })->count();

        $activeCustomers = Customer::whereHas('stockMovements', function ($query) {
            $query->where('transaction_date', '>=', Carbon::now()->subMonth());
        })->count();

        // Stock turnover rate
        $avgInventoryValue = $totalInventoryValue;
        $monthlyCoGS = StockMovement::stockOut()
            ->whereMonth('transaction_date', now()->month)
            ->sum(DB::raw('quantity * COALESCE(unit_price, 0)'));
        $stockTurnover = $avgInventoryValue > 0 ? ($monthlyCoGS * 12) / $avgInventoryValue : 0;

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

        // Customer Schedules for alerts (with error handling)
        $customerSchedules = [
            'overdue' => collect([]),
            'due_today' => collect([]),
            'due_this_week' => collect([]),
            'pending' => collect([])
        ];

        $scheduleStats = [
            'total_pending' => 0,
            'overdue_count' => 0,
            'due_today_count' => 0,
            'due_this_week_count' => 0
        ];

        // Try to load customer schedules if table exists
        try {
            if (DB::getSchemaBuilder()->hasTable('customer_schedules')) {
                $customerSchedules = [
                    'overdue' => CustomerSchedule::with(['customer', 'product'])
                        ->overdue()
                        ->orderBy('scheduled_date', 'asc')
                        ->take(5)
                        ->get(),
                    'due_today' => CustomerSchedule::with(['customer', 'product'])
                        ->dueToday()
                        ->orderBy('scheduled_date', 'asc')
                        ->take(5)
                        ->get(),
                    'due_this_week' => CustomerSchedule::with(['customer', 'product'])
                        ->dueThisWeek()
                        ->orderBy('scheduled_date', 'asc')
                        ->take(10)
                        ->get(),
                    'pending' => CustomerSchedule::with(['customer', 'product'])
                        ->pending()
                        ->orderBy('scheduled_date', 'asc')
                        ->take(10)
                        ->get()
                ];

                $scheduleStats = [
                    'total_pending' => CustomerSchedule::pending()->count(),
                    'overdue_count' => CustomerSchedule::overdue()->count(),
                    'due_today_count' => CustomerSchedule::dueToday()->count(),
                    'due_this_week_count' => CustomerSchedule::dueThisWeek()->count()
                ];
            }
        } catch (\Exception $e) {
            // If customer_schedules table doesn't exist, use empty collections
            Log::warning('CustomerSchedule table not found or error: ' . $e->getMessage());
        }

        return view('dashboard', compact(
            'totalProducts',
            'lowStockProducts',
            'totalInventoryValue',
            'outOfStockProducts',
            'totalSuppliers',
            'totalCustomers',
            'monthlyStockInSelected',
            'monthlyStockOutSelected',
            'monthlyRevenueSelected',
            'monthlyTransactionsSelected',
            'monthlyStockInTransactionsSelected',
            'monthlyStockOutTransactionsSelected',
            'monthlyPurchaseSelected',
            'monthlyStockIn',
            'monthlyStockOut',
            'monthlyRevenue',
            'stockAging',
            'topProducts',
            'recentMovements',
            'chartData',
            'criticalStockList',
            'topCategories',
            'activeSuppliers',
            'activeCustomers',
            'stockTurnover',
            'selectedMonth',
            'selectedYear',
            'monthOptions',
            'recentStockIn',
            'expiring3Months',
            'expiring2Months',
            'customerSchedules',
            'scheduleStats'
        ));
    }
}
