<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public static function middleware(): array
    {
        return [
            'auth',
            'super_admin'
        ];
    }
    public function index(Request $request)
    {
        $isActive = $request->get('is_active', 1);

        $query = Product::with('category');

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        // Handle search
        $search = $request->get('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', '%' . $search . '%')
                    ->orWhere('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%')
                    ->orWhere('lot_number', 'LIKE', '%' . $search . '%')
                    ->orWhere('distribution_permit', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'LIKE', '%' . $search . '%');
                    });
            });
        }

        // Handle sorting
        $sortField = $request->get('sort', 'stock_priority');
        $direction = $request->get('direction', 'desc');

        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['code', 'name', 'category', 'current_stock', 'price', 'expired_date', 'stock_priority'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'stock_priority';
        }

        // Apply sorting
        switch ($sortField) {
            case 'stock_priority':
                $query->orderByRaw("CASE WHEN current_stock > 0 THEN 1 ELSE 0 END DESC, name ASC");
                break;
            case 'category':
                $query->join('product_categories', 'products.category_id', '=', 'product_categories.id')
                    ->orderBy('product_categories.name', $direction)
                    ->select('products.*', 'product_categories.name as category_name');
                break;
            case 'expired_date':
                $query->orderByRaw("CASE WHEN expired_date IS NULL THEN 1 ELSE 0 END, expired_date {$direction}");
                break;
            default:
                $query->orderBy($sortField, $direction);
                break;
        }

        $products = $query->get();

        // Get counts for different statuses
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $inactiveProducts = Product::where('is_active', false)->count();

        $categories = ProductCategory::withCount('products')
            ->orderBy('name')
            ->get();

        return view('products.index', compact('products', 'categories', 'isActive', 'search', 'totalProducts', 'activeProducts', 'inactiveProducts'));
    }

    public function create()
    {
        $categories = ProductCategory::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:products',
            'name' => 'required',
            'category_id' => 'required|exists:product_categories,id',
            'unit' => 'required',
            'price' => 'required|numeric|min:0',
            'minimum_stock' => 'integer|min:0',
            'lot' => 'nullable|string|max:255',
            'exp' => 'nullable|date|after:today',
            'distribution_permit' => 'nullable|string|max:255'
        ]);

        $data = $request->all();

        // Map form field names to database column names
        if (isset($data['lot'])) {
            $data['lot_number'] = $data['lot'];
            unset($data['lot']);
        }
        if (isset($data['exp'])) {
            $data['expired_date'] = $data['exp'];
            unset($data['exp']);
        }

        $product = Product::create($data);

        // Handle AJAX request
        if ($request->expectsJson()) {
            return response()->json([
                'id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'message' => 'Produk berhasil ditambahkan'
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    public function show(Request $request, Product $product)
    {
        try {
            $product->load('category', 'stockMovements.supplier', 'stockMovements.customer');

            // Get selected year from request, default to current year
            $selectedYear = $request->get('year', date('Y'));

            // Generate chart data for selected year
            $chartData = $this->generateProductChartData($product->id, $selectedYear);

            // Get available years for dropdown
            $yearExpression = config('database.default') === 'sqlite'
                ? "strftime('%Y', transaction_date) as year"
                : 'YEAR(transaction_date) as year';

            $availableYears = StockMovement::where('product_id', $product->id)
                ->where('type', 'out')
                ->selectRaw("strftime('%Y', transaction_date) as year")
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->toArray();

            if (empty($availableYears)) {
                $availableYears = [date('Y')];
            }

            return view('products.show', compact('product', 'chartData', 'selectedYear', 'availableYears'));
        } catch (\Exception $e) {
            Log::error('Product show error: ' . $e->getMessage() . ' for product ID: ' . $product->id);

            // Return simplified view without chart data
            return view('products.show-simple', compact('product'));
        }
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'code' => 'required|unique:products,code,' . $product->id,
            'name' => 'required',
            'category_id' => 'required|exists:product_categories,id',
            'unit' => 'required',
            'price' => 'required|numeric|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'lot_number' => 'nullable|string|max:255',
            'expired_date' => 'nullable|date',
            'distribution_permit' => 'nullable|string|max:255'
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diupdate');
    }

    public function destroy(Product $product)
    {
        if ($product->stockMovements()->count() > 0) {
            return redirect()->route('products.index')
                ->with('error', 'Produk tidak dapat dihapus karena memiliki riwayat transaksi');
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus');
    }

    private function generateProductChartData($productId, $year)
    {
        // Get all customers who purchased this product in the selected year
        $customers = StockMovement::with('customer')
            ->where('product_id', $productId)
            ->where('type', 'out')
            ->whereYear('transaction_date', $year)
            ->select('customer_id')
            ->distinct()
            ->get()
            ->pluck('customer')
            ->filter()
            ->unique('id');

        // Generate months array
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('M', mktime(0, 0, 0, $i, 1));
        }

        // Generate colors for customers
        $colors = [
            '#3B82F6',
            '#EF4444',
            '#10B981',
            '#F59E0B',
            '#8B5CF6',
            '#06B6D4',
            '#84CC16',
            '#F97316',
            '#EC4899',
            '#6366F1',
            '#14B8A6',
            '#F472B6',
            '#A855F7',
            '#22D3EE',
            '#FDE047'
        ];

        $datasets = [];
        $colorIndex = 0;

        foreach ($customers as $customer) {
            $monthlyData = [];

            for ($month = 1; $month <= 12; $month++) {
                $quantity = StockMovement::where('product_id', $productId)
                    ->where('customer_id', $customer->id)
                    ->where('type', 'out')
                    ->whereYear('transaction_date', $year)
                    ->whereMonth('transaction_date', $month)
                    ->sum('quantity');

                $monthlyData[] = $quantity;
            }

            $datasets[] = [
                'label' => $customer->name,
                'data' => $monthlyData,
                'backgroundColor' => $colors[$colorIndex % count($colors)],
                'borderColor' => $colors[$colorIndex % count($colors)],
                'borderWidth' => 2,
                'fill' => false,
                'tension' => 0.1
            ];

            $colorIndex++;
        }

        return [
            'labels' => $months,
            'datasets' => $datasets
        ];
    }
}
