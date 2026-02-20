<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockMovement;
use App\Models\AdminActivityLog;
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

        // Log activity
        AdminActivityLog::logActivity(
            'create',
            'product',
            "Menambah produk '{$product->name}' (Kode: {$product->code})",
            [
                'product_id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'price' => $product->price
            ]
        );

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
            Log::info('Loading product detail', ['product_id' => $product->id, 'product_code' => $product->code]);

            $product->load('category', 'stockMovements.supplier', 'stockMovements.customer');

            // Get selected year from request
            $selectedYear = $request->get('year');

            // If no year selected, use first available year
            if (!$selectedYear) {
                $selectedYear = date('Y');
            }

            Log::info('Getting available years for product chart', ['product_id' => $product->id]);

            // Get available years for dropdown
            try {
                $availableYears = StockMovement::where('product_id', $product->id)
                    ->where('type', 'out')
                    ->selectRaw('DISTINCT strftime("%Y", transaction_date) as year')
                    ->orderBy('year', 'desc')
                    ->pluck('year')
                    ->toArray();

                if (empty($availableYears)) {
                    $availableYears = [date('Y')];
                }

                // Always include 2025 in available years
                if (!in_array(2025, $availableYears)) {
                    $availableYears[] = 2025;
                    sort($availableYears);
                    $availableYears = array_reverse($availableYears);
                }

                // Ensure selected year is valid, fallback to first available year
                if (!in_array($selectedYear, $availableYears)) {
                    $selectedYear = $availableYears[0];
                }

                Log::info('Available years retrieved', ['available_years' => $availableYears, 'selected_year' => $selectedYear]);

                // Generate chart data for selected year (AFTER year is finalized)
                $chartData = $this->generateProductChartData($product->id, $selectedYear);
                Log::info('Chart data generated successfully', ['product_id' => $product->id, 'year' => $selectedYear]);
            } catch (\Exception $chartException) {
                Log::error('Error generating chart data', [
                    'product_id' => $product->id,
                    'error' => $chartException->getMessage(),
                    'trace' => $chartException->getTraceAsString()
                ]);
                $chartData = null;
                $availableYears = [date('Y')];
            }

            return view('products.show', compact('product', 'chartData', 'selectedYear', 'availableYears'));
        } catch (\Exception $e) {
            Log::error('Product show error: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'product_code' => $product->code,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            // Return simplified view without chart data
            try {
                return view('products.show-simple', compact('product'));
            } catch (\Exception $viewException) {
                Log::error('Error loading fallback view', [
                    'error' => $viewException->getMessage(),
                    'product_id' => $product->id
                ]);

                // Last resort - return basic error page
                return response()->view('errors.500', [
                    'error' => 'Terjadi kesalahan saat memuat detail produk. Silakan coba lagi nanti.',
                    'product_id' => $product->id
                ], 500);
            }
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
        // Using the same efficient method as ReportController
        return $this->processChartData(StockMovement::getProductChartData($productId, $year));
    }

    /**
     * Process chart data from StockMovement helper methods
     */
    private function processChartData($chartData)
    {
        // Generate months array
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('M', mktime(0, 0, 0, $i, 1));
        }

        // Generate colors
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

        // Process grouped data
        foreach ($chartData as $entityId => $entityData) {
            $monthlyData = array_fill(0, 12, 0); // Initialize 12 months with 0
            $entityName = '';

            foreach ($entityData as $data) {
                // Handle customer_name for product chart
                $entityName = $data->customer_name ?? 'Unknown';
                $monthlyData[$data->month - 1] = (int) $data->total_quantity;
            }

            $datasets[] = [
                'label' => $entityName,
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
