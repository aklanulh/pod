<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\KsoItem;
use App\Models\KsoSupportItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KsoRoiController extends Controller
{
    /**
     * Display KSO ROI dashboard
     */
    public function index()
    {
        // Get overall statistics
        $totalInvestment = KsoItem::where('status', 'active')->sum('total_investasi');

        // Calculate total sales using same formula as customer report
        $salesStats = DB::table('stock_movements')
            ->where('type', 'out')
            ->selectRaw('SUM(quantity * COALESCE(unit_price, 0)) as total_value')
            ->first();
        $totalSales = $salesStats->total_value ?? 0;

        $overallROI = $totalInvestment > 0 ? ($totalSales / $totalInvestment) * 100 : 0;
        $roiStatus = $overallROI >= 100 ? 'ROI' : 'Belum ROI';

        // Calculate overall difference
        $overallDifference = $totalSales - $totalInvestment;
        $overallDifferenceData = [
            'amount' => abs($overallDifference),
            'type' => $overallDifference >= 0 ? 'profit' : 'kurang',
            'percentage_diff' => $overallROI - 100
        ];

        // Get customers with KSO items
        $customers = Customer::with(['ksoItems' => function ($query) {
            $query->where('status', 'active')->with('supportItems');
        }])
            ->whereHas('ksoItems', function ($query) {
                $query->where('status', 'active');
            })
            ->get()
            ->map(function ($customer) {
                $customer->total_investment = $customer->getTotalKsoInvestment();
                $customer->total_sales = $customer->getTotalSalesValue();
                $customer->roi_percentage = $customer->calculateOverallROI();
                $customer->roi_status = $customer->hasAchievedOverallROI() ? 'ROI' : 'Belum ROI';
                $customer->roi_difference = $customer->calculateROIDifference();

                // Count medical equipment vs computer/support equipment
                $customer->medical_equipment_count = $customer->ksoItems->count();
                $customer->computer_equipment_count = $customer->ksoItems->sum(function ($ksoItem) {
                    return $ksoItem->supportItems->count();
                });

                return $customer;
            });

        // Get top performing customers
        $topCustomers = $customers->sortByDesc('roi_percentage')->take(5);

        // Get recent KSO items
        $recentKsoItems = KsoItem::with(['customer', 'supportItems'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('kso-roi.index', compact(
            'totalInvestment',
            'totalSales',
            'overallROI',
            'roiStatus',
            'overallDifferenceData',
            'customers',
            'topCustomers',
            'recentKsoItems'
        ));
    }

    /**
     * Show KSO items management
     */
    public function ksoItems()
    {
        $ksoItems = KsoItem::with(['customer', 'supportItems'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $customers = Customer::orderBy('name')->get();

        return view('kso-roi.kso-items', compact('ksoItems', 'customers'));
    }

    /**
     * Show create KSO item form
     */
    public function createKsoItem()
    {
        $customers = Customer::orderBy('name')->get();
        return view('kso-roi.create-kso-item', compact('customers'));
    }

    /**
     * Store new KSO item
     */
    public function storeKsoItem(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'nama_alat' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'no_registrasi' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'kondisi' => 'nullable|string|max:255',
            'nilai_alat_utama' => 'required|numeric|min:0',
            'tanggal_investasi' => 'required|date|date_format:Y-m-d',
            'tanggal_deployment' => 'required|date|date_format:Y-m-d',
            'garansi_berakhir' => 'nullable|date',
            'durasi_kso_bulan' => 'required|integer|min:1',
            'lokasi_penempatan' => 'nullable|string|max:255',
            'pic_customer' => 'nullable|string|max:255',
            'pic_msa' => 'nullable|string|max:255',
            'butuh_komputer' => 'boolean',
            'keterangan' => 'nullable|string',
            'spesifikasi_teknis' => 'nullable|string',
            'support_items' => 'array',
            'support_items.*.nama_item' => 'required_with:support_items|string|max:255',
            'support_items.*.brand' => 'nullable|string|max:255',
            'support_items.*.model' => 'nullable|string|max:255',
            'support_items.*.serial_number' => 'nullable|string|max:255',
            'support_items.*.no_registrasi' => 'nullable|string|max:255',
            'support_items.*.kategori' => 'nullable|string|max:255',
            'support_items.*.jumlah' => 'required_with:support_items|integer|min:1',
            'support_items.*.kondisi' => 'nullable|string|max:255',
            'support_items.*.tanggal_install' => 'nullable|date',
            'support_items.*.lokasi_penempatan' => 'nullable|string|max:255',
            'support_items.*.garansi_berakhir' => 'nullable|date',
            'support_items.*.periode_kso_mulai' => 'nullable|date',
            'support_items.*.periode_kso_berakhir' => 'nullable|date',
            'support_items.*.status' => 'nullable|string|max:255',
            'support_items.*.spesifikasi' => 'nullable|string'
        ]);

        // Debug: Log support items data
        Log::info('Support items received in storeKsoItem:', [
            'has_support_items' => $request->has('support_items'),
            'support_items_data' => $request->support_items ?? 'null',
            'all_request_data' => $request->all()
        ]);

        DB::transaction(function () use ($request) {
            // Clean and validate date input
            $cleanDeploymentDate = $this->validateAndCleanDate($request->tanggal_deployment);
            $cleanInvestmentDate = $this->validateAndCleanDate($request->tanggal_investasi);

            // Calculate contract end date
            $deploymentDate = Carbon::createFromFormat('Y-m-d', $cleanDeploymentDate);
            $contractEndDate = $deploymentDate->copy()->addMonths((int) $request->durasi_kso_bulan);

            // Create KSO item
            $ksoItem = KsoItem::create([
                'customer_id' => $request->customer_id,
                'nama_alat' => $request->nama_alat,
                'brand' => $request->brand,
                'model' => $request->model,
                'serial_number' => $request->serial_number,
                'no_registrasi' => $request->no_registrasi,
                'kategori' => $request->kategori,
                'kondisi' => $request->kondisi,
                'nilai_alat_utama' => $request->nilai_alat_utama,
                'tanggal_investasi' => $request->tanggal_investasi,
                'tanggal_install' => $request->tanggal_deployment, // Use deployment date as install date
                'garansi_berakhir' => $request->garansi_berakhir,
                'periode_kso_mulai' => $request->tanggal_deployment, // Use deployment date as KSO start
                'periode_kso_berakhir' => $contractEndDate->toDateString(), // Auto-calculated
                'durasi_kso_bulan' => $request->durasi_kso_bulan,
                'lokasi_penempatan' => $request->lokasi_penempatan,
                'pic_customer' => $request->pic_customer,
                'pic_msa' => $request->pic_msa,
                'butuh_komputer' => $request->boolean('butuh_komputer'),
                'keterangan' => $request->keterangan,
                'spesifikasi_teknis' => $request->spesifikasi_teknis,
                'total_pendukung' => 0,
                'total_investasi' => $request->nilai_alat_utama
            ]);

            // Create support items if provided
            if ($request->has('support_items')) {
                foreach ($request->support_items as $supportItem) {
                    if (!empty($supportItem['nama_item'])) {
                        KsoSupportItem::create([
                            'kso_item_id' => $ksoItem->id,
                            'nama_item' => $supportItem['nama_item'],
                            'brand' => $supportItem['brand'] ?? null,
                            'model' => $supportItem['model'] ?? null,
                            'serial_number' => $supportItem['serial_number'] ?? null,
                            'no_registrasi' => $supportItem['no_registrasi'] ?? null,
                            'kategori' => $supportItem['kategori'] ?? null,
                            'jumlah' => $supportItem['jumlah'] ?? 1,
                            'kondisi' => $supportItem['kondisi'] ?? 'baik',
                            'nilai_item' => $supportItem['nilai_item'] ?? 0,
                            'tanggal_install' => $supportItem['tanggal_install'] ?? null,
                            'lokasi_penempatan' => $supportItem['lokasi_penempatan'] ?? null,
                            'garansi_berakhir' => $supportItem['garansi_berakhir'] ?? null,
                            'periode_kso_mulai' => $supportItem['periode_kso_mulai'] ?? null,
                            'periode_kso_berakhir' => $supportItem['periode_kso_berakhir'] ?? null,
                            'status' => $supportItem['status'] ?? 'active',
                            'spesifikasi' => $supportItem['spesifikasi'] ?? null
                        ]);
                    }
                }
            }

            // Update calculated totals
            $ksoItem->updateCalculatedTotals();
        });

        return redirect()->route('kso-roi.kso-items')
            ->with('success', 'KSO Item berhasil ditambahkan');
    }

    /**
     * Show edit KSO item form
     */
    public function editKsoItem(KsoItem $ksoItem)
    {
        $ksoItem->load('supportItems');
        $customers = Customer::orderBy('name')->get();
        return view('kso-roi.edit-kso-item', compact('ksoItem', 'customers'));
    }

    /**
     * Update KSO item
     */
    public function updateKsoItem(Request $request, KsoItem $ksoItem)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'nama_alat' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'no_registrasi' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'kondisi' => 'nullable|string|max:255',
            'nilai_alat_utama' => 'required|numeric|min:0',
            'tanggal_investasi' => 'required|date|date_format:Y-m-d',
            'tanggal_deployment' => 'required|date|date_format:Y-m-d',
            'garansi_berakhir' => 'nullable|date',
            'durasi_kso_bulan' => 'required|integer|min:1',
            'lokasi_penempatan' => 'nullable|string|max:255',
            'pic_customer' => 'nullable|string|max:255',
            'pic_msa' => 'nullable|string|max:255',
            'butuh_komputer' => 'boolean',
            'keterangan' => 'nullable|string',
            'spesifikasi_teknis' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'support_items' => 'array',
            'support_items.*.nama_item' => 'required_with:support_items|string|max:255',
            'support_items.*.brand' => 'nullable|string|max:255',
            'support_items.*.model' => 'nullable|string|max:255',
            'support_items.*.serial_number' => 'nullable|string|max:255',
            'support_items.*.no_registrasi' => 'nullable|string|max:255',
            'support_items.*.kategori' => 'nullable|string|max:255',
            'support_items.*.jumlah' => 'required_with:support_items|integer|min:1',
            'support_items.*.kondisi' => 'nullable|string|max:255',
            'support_items.*.tanggal_install' => 'nullable|date',
            'support_items.*.lokasi_penempatan' => 'nullable|string|max:255',
            'support_items.*.garansi_berakhir' => 'nullable|date',
            'support_items.*.periode_kso_mulai' => 'nullable|date',
            'support_items.*.periode_kso_berakhir' => 'nullable|date',
            'support_items.*.status' => 'nullable|string|max:255',
            'support_items.*.spesifikasi' => 'nullable|string'
        ]);

        // Debug: Log support items data for update
        Log::info('Support items received in updateKsoItem:', [
            'kso_item_id' => $ksoItem->id,
            'has_support_items' => $request->has('support_items'),
            'support_items_data' => $request->support_items ?? 'null',
            'existing_support_items_count' => $ksoItem->supportItems->count()
        ]);

        DB::transaction(function () use ($request, $ksoItem) {
            // Calculate contract end date with validation
            try {
                $deploymentDate = Carbon::createFromFormat('Y-m-d', $request->tanggal_deployment);
                if (!$deploymentDate) {
                    throw new \InvalidArgumentException('Invalid deployment date format');
                }
                $contractEndDate = $deploymentDate->copy()->addMonths((int) $request->durasi_kso_bulan);
            } catch (\Exception $e) {
                Log::error('Date parsing error: ' . $e->getMessage() . ' for date: ' . $request->tanggal_deployment);
                // Fallback: try to parse with Carbon's default parser
                $deploymentDate = Carbon::parse($request->tanggal_deployment);
                $contractEndDate = $deploymentDate->copy()->addMonths((int) $request->durasi_kso_bulan);
            }

            // Update KSO item
            $ksoItem->update([
                'customer_id' => $request->customer_id,
                'nama_alat' => $request->nama_alat,
                'brand' => $request->brand,
                'model' => $request->model,
                'serial_number' => $request->serial_number,
                'no_registrasi' => $request->no_registrasi,
                'kategori' => $request->kategori,
                'kondisi' => $request->kondisi,
                'nilai_alat_utama' => $request->nilai_alat_utama,
                'tanggal_investasi' => $request->tanggal_investasi,
                'tanggal_install' => $request->tanggal_deployment, // Use deployment date as install date
                'garansi_berakhir' => $request->garansi_berakhir,
                'periode_kso_mulai' => $request->tanggal_deployment, // Use deployment date as KSO start
                'periode_kso_berakhir' => $contractEndDate->toDateString(), // Auto-calculated
                'durasi_kso_bulan' => $request->durasi_kso_bulan,
                'lokasi_penempatan' => $request->lokasi_penempatan,
                'pic_customer' => $request->pic_customer,
                'pic_msa' => $request->pic_msa,
                'butuh_komputer' => $request->boolean('butuh_komputer'),
                'keterangan' => $request->keterangan,
                'spesifikasi_teknis' => $request->spesifikasi_teknis,
                'status' => $request->status
            ]);

            // Delete existing support items
            $ksoItem->supportItems()->delete();

            // Create new support items if provided
            if ($request->has('support_items')) {
                foreach ($request->support_items as $supportItem) {
                    if (!empty($supportItem['nama_item'])) {
                        KsoSupportItem::create([
                            'kso_item_id' => $ksoItem->id,
                            'nama_item' => $supportItem['nama_item'],
                            'brand' => $supportItem['brand'] ?? null,
                            'model' => $supportItem['model'] ?? null,
                            'serial_number' => $supportItem['serial_number'] ?? null,
                            'no_registrasi' => $supportItem['no_registrasi'] ?? null,
                            'kategori' => $supportItem['kategori'] ?? null,
                            'jumlah' => $supportItem['jumlah'] ?? 1,
                            'kondisi' => $supportItem['kondisi'] ?? 'baik',
                            'nilai_item' => $supportItem['nilai_item'] ?? 0,
                            'tanggal_install' => $supportItem['tanggal_install'] ?? null,
                            'lokasi_penempatan' => $supportItem['lokasi_penempatan'] ?? null,
                            'garansi_berakhir' => $supportItem['garansi_berakhir'] ?? null,
                            'periode_kso_mulai' => $supportItem['periode_kso_mulai'] ?? null,
                            'periode_kso_berakhir' => $supportItem['periode_kso_berakhir'] ?? null,
                            'status' => $supportItem['status'] ?? 'active',
                            'spesifikasi' => $supportItem['spesifikasi'] ?? null
                        ]);
                    }
                }
            }

            // Update calculated totals
            $ksoItem->updateCalculatedTotals();
        });

        return redirect()->route('kso-roi.kso-items')
            ->with('success', 'KSO Item berhasil diperbarui');
    }

    /**
     * Delete KSO item
     */
    public function destroyKsoItem(KsoItem $ksoItem)
    {
        $ksoItem->delete();

        return redirect()->route('kso-roi.kso-items')
            ->with('success', 'KSO Item berhasil dihapus');
    }

    /**
     * Show customer detail with ROI analysis
     */
    public function customerDetail(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        // Get selected year for chart
        $selectedYear = $request->get('year', date('Y'));

        // Load relationships
        $customer->load(['ksoItems.supportItems', 'stockMovements' => function ($query) {
            $query->where('type', 'out')->orderBy('transaction_date', 'desc');
        }]);

        $totalInvestment = $customer->getTotalKsoInvestment();
        $totalSales = $customer->getTotalSalesValue();
        $roiPercentage = $customer->calculateOverallROI();
        $roiStatus = $customer->hasAchievedOverallROI() ? 'ROI' : 'Belum ROI';

        // Monthly sales data for trend chart (using same calculation as customer report)
        $yearExpr = config('database.default') === 'sqlite' ? "strftime('%Y', transaction_date)" : "YEAR(transaction_date)";
        $monthExpr = config('database.default') === 'sqlite' ? "CAST(strftime('%m', transaction_date) AS INTEGER)" : "MONTH(transaction_date)";

        $monthlySales = $customer->stockMovements()
            ->where('type', 'out')
            ->selectRaw("$yearExpr as year, $monthExpr as month, SUM(quantity * COALESCE(unit_price, 0)) as total")
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        // Generate chart data for product purchases per month (similar to customer report)
        $chartData = $this->processChartData(StockMovement::getCustomerChartData($id, $selectedYear));

        // Get available years for dropdown
        $yearExpr = config('database.default') === 'sqlite' ? "strftime('%Y', transaction_date)" : "YEAR(transaction_date)";
        $availableYears = StockMovement::where('customer_id', $id)
            ->where('type', 'out')
            ->selectRaw("$yearExpr as year")
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [date('Y')];
        }

        return view('kso-roi.customer-detail', compact(
            'customer',
            'totalInvestment',
            'totalSales',
            'roiPercentage',
            'roiStatus',
            'monthlySales',
            'chartData',
            'selectedYear',
            'availableYears'
        ));
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
                // Handle both product_name and customer_name
                $entityName = $data->product_name ?? $data->customer_name ?? 'Unknown';
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

    public function analytics()
    {
        // Monthly ROI trend (using same calculation as customer report)
        $yearExpr = config('database.default') === 'sqlite' ? "strftime('%Y', transaction_date)" : "YEAR(transaction_date)";
        $monthExpr = config('database.default') === 'sqlite' ? "CAST(strftime('%m', transaction_date) AS INTEGER)" : "MONTH(transaction_date)";

        $monthlyData = DB::table('stock_movements')
            ->where('type', 'out')
            ->selectRaw("$yearExpr as year, $monthExpr as month, SUM(quantity * COALESCE(unit_price, 0)) as sales")
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        $totalInvestment = KsoItem::where('status', 'active')->sum('total_investasi');

        $chartData = $monthlyData->map(function ($item) use ($totalInvestment) {
            return [
                'period' => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT),
                'sales' => $item->sales,
                'roi' => $totalInvestment > 0 ? ($item->sales / $totalInvestment) * 100 : 0
            ];
        });

        return response()->json($chartData);
    }

    /**
     * Validate and clean date input to prevent parsing errors
     */
    private function validateAndCleanDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        // Remove any extra characters and normalize
        $cleaned = trim($dateString);

        // Log the original input for debugging
        Log::info('Processing date input: ' . $dateString);

        // Check if date matches Y-m-d format exactly
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $cleaned)) {
            return $cleaned;
        }

        // Handle corrupted date like '10262286-03-12'
        // Extract the last valid date pattern
        if (preg_match('/(\d{4})-(\d{1,2})-(\d{1,2})$/', $cleaned, $matches)) {
            $year = $matches[1];
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $day = str_pad($matches[3], 2, '0', STR_PAD_LEFT);

            // Validate the extracted date
            if (checkdate($month, $day, $year)) {
                $validDate = $year . '-' . $month . '-' . $day;
                Log::warning('Cleaned corrupted date from "' . $dateString . '" to "' . $validDate . '"');
                return $validDate;
            }
        }

        // Try to find any 4-digit year followed by month and day
        if (preg_match('/.*?(\d{4})-(\d{1,2})-(\d{1,2})/', $cleaned, $matches)) {
            $year = $matches[1];
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $day = str_pad($matches[3], 2, '0', STR_PAD_LEFT);

            if (checkdate($month, $day, $year)) {
                $validDate = $year . '-' . $month . '-' . $day;
                Log::warning('Extracted date from corrupted input "' . $dateString . '" to "' . $validDate . '"');
                return $validDate;
            }
        }

        // Log error and throw exception
        Log::error('Could not parse date: ' . $dateString);
        throw new \InvalidArgumentException('Invalid date format: ' . $dateString);
    }

    /**
     * Show QR verification page (tanpa login)
     * Simplified flow: /qr/kso/{uniqueId} langsung ke halaman verifikasi
     */
    public function qrVerify($uniqueId)
    {
        $ksoItem = KsoItem::with(['customer', 'supportItems'])->byUniqueId($uniqueId)->first();

        return view('kso-roi.qr-verify', [
            'uniqueId' => $uniqueId,
            'ksoItem' => $ksoItem
        ]);
    }

    /**
     * Search KSO item by unique_id (manual search untuk barcode rusak)
     */
    public function qrSearch(Request $request, $uniqueId)
    {
        $request->validate([
            'search_id' => 'required|string|min:6|max:8'
        ]);

        $searchId = trim($request->search_id);

        // Cari KSO item berdasarkan unique_id
        $ksoItem = KsoItem::with(['customer', 'supportItems'])->byUniqueId($searchId)->first();

        if (!$ksoItem) {
            return back()->with('error', 'KSO Item dengan ID ' . $searchId . ' tidak ditemukan. Silakan periksa kembali ID yang Anda masukkan.');
        }

        // Redirect ke halaman verifikasi dengan unique_id yang ditemukan
        return redirect()->route('qr.verify', $ksoItem->unique_id);
    }

    /**
     * Verify password and show detail
     */
    public function verifyQrPassword(Request $request, $uniqueId)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        $ksoItem = KsoItem::with(['customer', 'supportItems'])->byUniqueId($uniqueId)->first();

        if (!$ksoItem) {
            return back()->with('error', 'KSO Item tidak ditemukan');
        }

        // Verify password - menggunakan password default dari env atau config
        $qrPassword = config('app.qr_password', 'MSA2024');

        if ($request->password !== $qrPassword) {
            return back()->with('error', 'Password salah');
        }

        // Generate token pendek untuk verifikasi (12 karakter alphanumeric)
        $token = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 12));

        // Store token di session dengan expiry time (15 menit)
        session([
            'qr_token_' . $uniqueId => $token,
            'qr_token_expires_' . $uniqueId => now()->addMinutes(15)
        ]);

        return redirect()->route('qr.detail', ['uniqueId' => $uniqueId, 'token' => $token]);
    }

    /**
     * Show QR detail (setelah password verified)
     */
    public function qrDetail($uniqueId)
    {
        // Ambil token dari URL
        $token = request('token');

        // Check if token ada dan valid
        $sessionToken = session('qr_token_' . $uniqueId);
        $tokenExpires = session('qr_token_expires_' . $uniqueId);

        // Validasi token
        if (!$token || !$sessionToken || $token !== $sessionToken) {
            return redirect()->route('qr.verify', $uniqueId)
                ->with('error', 'Silakan masukkan password terlebih dahulu');
        }

        // Validasi expiry
        if ($tokenExpires && now() > $tokenExpires) {
            session()->forget(['qr_token_' . $uniqueId, 'qr_token_expires_' . $uniqueId]);
            return redirect()->route('qr.verify', $uniqueId)
                ->with('error', 'Session sudah kadaluarsa, silakan masukkan password lagi');
        }

        $ksoItem = KsoItem::with(['customer', 'supportItems'])->byUniqueId($uniqueId)->first();

        if (!$ksoItem) {
            return view('kso-roi.qr-detail', [
                'error' => 'KSO Item tidak ditemukan'
            ]);
        }

        return view('kso-roi.qr-detail', [
            'ksoItem' => $ksoItem
        ]);
    }
}
