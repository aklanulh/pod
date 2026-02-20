<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\KsoItem;
use App\Models\KsoSupportItem;
use App\Models\MaintenanceSchedule;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KsoRoiController extends Controller
{
    public static function middleware(): array
    {
        return [
            'auth',
            'super_admin'
        ];
    }
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
        $customers = Customer::active()->orderBy('name')->get();
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
            'nilai_alat_utama' => 'nullable|numeric|min:0',
            'tanggal_investasi' => 'required|date|date_format:Y-m-d',
            'tanggal_deployment' => 'required|date|date_format:Y-m-d',
            'garansi_berakhir' => 'nullable|date',
            'durasi_kso_bulan' => 'nullable|integer|min:1',
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
            'support_items.*.nilai_item' => 'nullable|numeric|min:0',
            'support_items.*.kondisi' => 'nullable|string|max:255',
            'support_items.*.garansi_berakhir' => 'nullable|date',
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
                            'kondisi' => $supportItem['kondisi'] ?? 'baik',
                            'nilai_item' => $supportItem['nilai_item'] ?? 0,
                            'garansi_berakhir' => $supportItem['garansi_berakhir'] ?? null,
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
            'nilai_alat_utama' => 'nullable|numeric|min:0',
            'tanggal_investasi' => 'required|date|date_format:Y-m-d',
            'tanggal_deployment' => 'required|date|date_format:Y-m-d',
            'garansi_berakhir' => 'nullable|date',
            'durasi_kso_bulan' => 'nullable|integer|min:1',
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
            'support_items.*.nilai_item' => 'nullable|numeric|min:0',
            'support_items.*.kondisi' => 'nullable|string|max:255',
            'support_items.*.garansi_berakhir' => 'nullable|date',
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
                            'kondisi' => $supportItem['kondisi'] ?? 'baik',
                            'nilai_item' => $supportItem['nilai_item'] ?? 0,
                            'garansi_berakhir' => $supportItem['garansi_berakhir'] ?? null,
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
     * Update customer information
     */
    public function updateCustomer(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'type' => 'nullable|in:regular,premium,vip',
            'status' => 'required|in:active,inactive',
            'email' => 'nullable|email|max:255|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:255'
        ]);

        $customer->update($request->all());

        return redirect()->route('kso-roi.customer-detail', $customer->id)
            ->with('success', 'Data customer berhasil diperbarui');
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
     * Handle manual search by unique_id
     */
    public function qrSearch(Request $request, $uniqueId)
    {
        $request->validate([
            'search_id' => 'required|string|min:6|max:8'
        ]);

        $searchId = $request->search_id;

        // Cari KSO item berdasarkan unique_id
        $ksoItem = KsoItem::with(['customer', 'supportItems'])
            ->where('unique_id', $searchId)
            ->first();

        if (!$ksoItem) {
            return back()->with('error', 'KSO Item dengan ID tersebut tidak ditemukan');
        }

        // Redirect ke verification page dengan item yang ditemukan
        return redirect()->route('qr.verify', $ksoItem->unique_id)
            ->with('success', 'KSO Item ditemukan: ' . $ksoItem->nama_alat);
    }

    /**
     * Show QR verification page (tanpa login)
     * Simplified flow: /qr/kso/{uniqueId} langsung ke halaman verifikasi
     */
    public function qrVerify($uniqueId)
    {
        // Hanya cari berdasarkan unique_id, tidak boleh ID
        $ksoItem = KsoItem::with(['customer', 'supportItems'])
            ->where('unique_id', $uniqueId)
            ->first();

        if (!$ksoItem) {
            return redirect()->route('kso-roi.kso-items')->with('error', 'KSO Item tidak ditemukan');
        }

        return view('kso-roi.qr-verify', [
            'ksoItem' => $ksoItem,
            'uniqueId' => $ksoItem->unique_id
        ]);
    }

    /**
     * Verify password and show detail
     */
    public function verifyQrPassword(Request $request, $uniqueId)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        // Hanya cari berdasarkan unique_id, tidak boleh ID
        $ksoItem = KsoItem::with(['customer', 'supportItems'])
            ->where('unique_id', $uniqueId)
            ->first();

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
            'qr_token_' . $ksoItem->id => $token,
            'qr_token_expires_' . $ksoItem->id => now()->addMinutes(15)
        ]);

        return redirect()->route('qr.detail', ['uniqueId' => $ksoItem->unique_id, 'token' => $token]);
    }

    /**
     * Show QR detail (setelah password verified)
     */
    public function qrDetail($uniqueId)
    {
        // Ambil token dari URL
        $token = request('token');

        // Hanya cari berdasarkan unique_id, tidak boleh ID
        $ksoItem = KsoItem::with(['customer', 'supportItems'])
            ->where('unique_id', $uniqueId)
            ->first();

        if (!$ksoItem) {
            return view('kso-roi.qr-detail', [
                'error' => 'KSO Item tidak ditemukan'
            ]);
        }

        // Check if token ada dan valid
        $sessionToken = session('qr_token_' . $ksoItem->id);
        $tokenExpires = session('qr_token_expires_' . $ksoItem->id);

        // Validasi token
        if (!$token || !$sessionToken || $token !== $sessionToken) {
            return redirect()->route('qr.verify', $ksoItem->unique_id)
                ->with('error', 'Silakan masukkan password terlebih dahulu');
        }

        // Validasi expiry
        if ($tokenExpires && now() > $tokenExpires) {
            session()->forget(['qr_token_' . $ksoItem->id, 'qr_token_expires_' . $ksoItem->id]);
            return redirect()->route('qr.verify', $ksoItem->unique_id)
                ->with('error', 'Session sudah kadaluarsa, silakan masukkan password lagi');
        }

        return view('kso-roi.qr-detail', [
            'ksoItem' => $ksoItem
        ]);
    }

    /**
     * Display technician dashboard for equipment monitoring
     */
    public function technicianDashboard(Request $request)
    {
        // Get all KSO items with relationships
        $ksoItems = KsoItem::with(['customer', 'supportItems', 'qcRecords', 'maintenanceSchedules'])
            ->where('status', 'active')
            ->get();

        // Calculate statistics
        $stats = [
            'total_equipment' => $ksoItems->count(),
            'overdue_qc' => 0,
            'overdue_calibration' => 0,
            'qc_due_this_month' => 0,
            'calibration_due_this_month' => 0,
        ];

        $now = now();
        $thisMonth = $now->copy()->startOfMonth();
        $nextMonth = $now->copy()->addMonth()->startOfMonth();

        foreach ($ksoItems as $item) {
            // Check QC records
            $qcRecords = $item->qcRecords;
            if ($qcRecords->isEmpty()) {
                // No QC records, check if overdue (6 months from install)
                $installDate = Carbon::parse($item->tanggal_install);
                $nextQcDue = $installDate->copy()->addMonths(6);

                if ($nextQcDue->isPast()) {
                    $stats['overdue_qc']++;
                } elseif ($nextQcDue->between($thisMonth, $nextMonth)) {
                    $stats['qc_due_this_month']++;
                }
            } else {
                // Check latest QC record
                $latestQc = $qcRecords->sortByDesc('created_at')->first();
                $nextQcDue = Carbon::parse($latestQc->created_at)->addMonths(6);

                if ($nextQcDue->isPast()) {
                    $stats['overdue_qc']++;
                } elseif ($nextQcDue->between($thisMonth, $nextMonth)) {
                    $stats['qc_due_this_month']++;
                }
            }

            // Check calibration (assuming calibration is a type of QC)
            $calibrationRecords = $qcRecords->where('type', 'calibration');
            if ($calibrationRecords->isEmpty()) {
                // No calibration records, check if overdue (12 months from install)
                $installDate = Carbon::parse($item->tanggal_install);
                $nextCalibrationDue = $installDate->copy()->addMonths(12);

                if ($nextCalibrationDue->isPast()) {
                    $stats['overdue_calibration']++;
                } elseif ($nextCalibrationDue->between($thisMonth, $nextMonth)) {
                    $stats['calibration_due_this_month']++;
                }
            } else {
                // Check latest calibration record
                $latestCalibration = $calibrationRecords->sortByDesc('created_at')->first();
                $nextCalibrationDue = Carbon::parse($latestCalibration->created_at)->addMonths(12);

                if ($nextCalibrationDue->isPast()) {
                    $stats['overdue_calibration']++;
                } elseif ($nextCalibrationDue->between($thisMonth, $nextMonth)) {
                    $stats['calibration_due_this_month']++;
                }
            }
        }

        // Prepare data for table
        $tableData = [];
        foreach ($ksoItems as $item) {
            $maintenanceSchedules = $item->maintenanceSchedules;
            $lastMaintenance = $maintenanceSchedules->sortByDesc('created_at')->first();
            $nextMaintenance = $maintenanceSchedules->where('next_maintenance_date', '>=', now())
                ->sortBy('next_maintenance_date')
                ->first();

            $qcRecords = $item->qcRecords;
            $lastQc = $qcRecords->sortByDesc('created_at')->first();
            $lastCalibration = $qcRecords->where('type', 'calibration')->sortByDesc('created_at')->first();

            // Calculate QC status
            $qcStatus = 'ok';
            $qcStatusColor = 'green';
            $qcStatusText = 'OK';
            $qcDaysOverdue = 0;
            $nextQcDue = null;

            if ($lastQc) {
                $nextQcDue = Carbon::parse($lastQc->created_at)->addMonths(6);
                if ($nextQcDue->isPast()) {
                    $qcStatus = 'terlambat';
                    $qcStatusColor = 'red';
                    $qcStatusText = 'Terlambat';
                    $qcDaysOverdue = $nextQcDue->startOfDay()->diffInDays(now()->startOfDay());
                } elseif ($nextQcDue->startOfDay()->diffInDays(now()->startOfDay()) <= 30) {
                    $qcStatus = 'due';
                    $qcStatusColor = 'yellow';
                    $qcStatusText = 'Akan Jatuh Tempo';
                }
            } else {
                $installDate = Carbon::parse($item->tanggal_install);
                $nextQcDue = $installDate->copy()->addMonths(6);
                if ($nextQcDue->isPast()) {
                    $qcStatus = 'terlambat';
                    $qcStatusColor = 'red';
                    $qcStatusText = 'Belum QC - Terlambat';
                    $qcDaysOverdue = $nextQcDue->startOfDay()->diffInDays(now()->startOfDay());
                } elseif ($nextQcDue->startOfDay()->diffInDays(now()->startOfDay()) <= 30) {
                    $qcStatus = 'due';
                    $qcStatusColor = 'yellow';
                    $qcStatusText = 'Belum QC - Akan Jatuh Tempo';
                }
            }

            // Calculate Calibration status
            $calibrationStatus = 'ok';
            $calibrationStatusColor = 'green';
            $calibrationStatusText = 'Baik';
            $calibrationDaysOverdue = 0;
            $nextCalibrationDue = null;

            if ($lastCalibration) {
                $nextCalibrationDue = Carbon::parse($lastCalibration->created_at)->addMonths(12);
                if ($nextCalibrationDue->isPast()) {
                    $calibrationStatus = 'terlambat';
                    $calibrationStatusColor = 'red';
                    $calibrationStatusText = 'Terlambat';
                    $calibrationDaysOverdue = $nextCalibrationDue->startOfDay()->diffInDays(now()->startOfDay());
                } elseif ($nextCalibrationDue->startOfDay()->diffInDays(now()->startOfDay()) <= 30) {
                    $calibrationStatus = 'due';
                    $calibrationStatusColor = 'yellow';
                    $calibrationStatusText = 'Akan Jatuh Tempo';
                }
            } else {
                $installDate = Carbon::parse($item->tanggal_install);
                $nextCalibrationDue = $installDate->copy()->addMonths(12);
                if ($nextCalibrationDue->isPast()) {
                    $calibrationStatus = 'terlambat';
                    $calibrationStatusColor = 'red';
                    $calibrationStatusText = 'Belum Kalibrasi - Terlambat';
                    $calibrationDaysOverdue = $nextCalibrationDue->startOfDay()->diffInDays(now()->startOfDay());
                } elseif ($nextCalibrationDue->startOfDay()->diffInDays(now()->startOfDay()) <= 30) {
                    $calibrationStatus = 'due';
                    $calibrationStatusColor = 'yellow';
                    $calibrationStatusText = 'Belum Kalibrasi - Akan Jatuh Tempo';
                }
            }

            $tableData[] = [
                'id' => $item->id,
                'unique_id' => $item->unique_id,
                'nama_alat' => $item->nama_alat,
                'brand' => $item->brand,
                'model' => $item->model,
                'serial_number' => $item->serial_number,
                'customer' => $item->customer->name,
                'lokasi_penempatan' => $item->lokasi_penempatan,
                'last_maintenance' => $lastMaintenance ? $lastMaintenance->next_maintenance_date : null,
                'next_maintenance' => $nextMaintenance ? $nextMaintenance->next_maintenance_date : null,
                'maintenance' => $nextMaintenance,
                'last_qc' => $lastQc ? $lastQc->created_at : null,
                'last_qc_record' => $lastQc,
                'last_calibration' => $lastCalibration ? $lastCalibration->created_at : null,
                'qc_status' => $qcStatus,
                'qc_status_color' => $qcStatusColor,
                'qc_status_text' => $qcStatusText,
                'qc_days_overdue' => $qcDaysOverdue,
                'next_qc_due' => $nextQcDue,
                'calibration_status' => $calibrationStatus,
                'calibration_status_color' => $calibrationStatusColor,
                'calibration_status_text' => $calibrationStatusText,
                'calibration_days_overdue' => $calibrationDaysOverdue,
                'next_calibration_due' => $nextCalibrationDue,
                'status' => $item->status,
            ];
        }

        // Filter overdue items
        $overdueQc = collect($tableData)->filter(function ($item) {
            return $item['qc_status'] === 'terlambat';
        });

        $overdueCalibration = collect($tableData)->filter(function ($item) {
            return $item['calibration_status'] === 'terlambat';
        });

        return view('kso-roi.technician-dashboard', [
            'stats' => $stats,
            'ksoItems' => $tableData,
            'todaySchedule' => collect([]), // Empty collection for now
            'weeklySchedule' => collect([]), // Empty collection for now
            'recentQcRecords' => collect([]), // Empty collection for now
            'overdueQc' => $overdueQc,
            'overdueCalibration' => $overdueCalibration,
            'title' => 'Dashboard Teknisi - Monitoring QC & Kalibrasi Semua Alat'
        ]);
    }

    /**
     * Calculate next maintenance date
     */
    private function calculateNextMaintenance($lastMaintenanceDate)
    {
        if (!$lastMaintenanceDate) {
            return now()->addMonths(6); // Default 6 months from now
        }

        return Carbon::parse($lastMaintenanceDate)->addMonths(6);
    }

    /**
     * Get equipment status based on warranty
     */
    private function getEquipmentStatus($warrantyExpiry)
    {
        if (!$warrantyExpiry) {
            return 'active';
        }

        $warrantyDate = Carbon::parse($warrantyExpiry);
        if (now()->gt($warrantyDate)) {
            return 'warranty_expired';
        } elseif (now()->diffInDays($warrantyDate) <= 30) {
            return 'warranty_expiring';
        }

        return 'active';
    }

    /**
     * Get maintenance calendar events
     */
    private function getMaintenanceCalendarEvents($equipment, $currentDate = null)
    {
        $events = [];
        $currentDate = $currentDate ?: now();

        foreach ($equipment as $item) {
            if ($item['next_maintenance']) {
                // Only include events for the selected month
                if ($item['next_maintenance']->format('Y-m') === $currentDate->format('Y-m')) {
                    $events[] = [
                        'date' => $item['next_maintenance']->format('Y-m-d'),
                        'title' => 'Maintenance: ' . $item['name'],
                        'customer' => $item['customer'],
                        'type' => $item['type'],
                        'status' => $this->getMaintenanceStatus($item['next_maintenance'])
                    ];
                }
            }
        }

        return $events;
    }

    /**
     * Get maintenance status based on date
     */
    private function getMaintenanceStatus($maintenanceDate)
    {
        $daysUntil = now()->diffInDays($maintenanceDate, false);

        if ($daysUntil < 0) {
            return 'terlambat';
        } elseif ($daysUntil <= 7) {
            return 'penting';
        } elseif ($daysUntil <= 30) {
            return 'dijadwalkan';
        }

        return 'direncanakan';
    }

    /**
     * Show form to create new maintenance schedule
     */
    public function createMaintenanceSchedule()
    {
        $ksoItems = KsoItem::with(['customer', 'supportItems'])->get();

        return view('kso-roi.maintenance-schedule-create', compact('ksoItems'));
    }

    /**
     * Store new maintenance schedule
     */
    public function storeMaintenanceSchedule(Request $request)
    {
        $request->validate([
            'maintenance_category' => 'required|in:kso,personal',
            'kso_item_id' => 'required_if:maintenance_category,kso|exists:kso_items,id',
            'personal_item_name' => 'required_if:maintenance_category,personal|string|max:255',
            'next_maintenance_date' => 'required|date|after:today',
            'maintenance_type' => 'required|string|max:255',
            'technician' => 'nullable|string|max:255',
            'technician_notes' => 'nullable|string',
        ]);

        // Set default values
        $requestData = $request->all();
        $requestData['status'] = 'scheduled';
        $requestData['last_maintenance_date'] = null;
        $requestData['description'] = null;
        $requestData['notes'] = null;
        $requestData['cost'] = null;

        if ($request->maintenance_category === 'kso') {
            // KSO Item maintenance
            $requestData['equipment_type'] = 'main';
            $requestData['equipment_name'] = '';

            // Get equipment name from KSO item
            $ksoItem = KsoItem::find($request->kso_item_id);
            if ($ksoItem) {
                $requestData['equipment_name'] = $ksoItem->nama_alat;
            }
        } else {
            // Personal item maintenance
            $requestData['equipment_type'] = 'personal';
            $requestData['equipment_name'] = $request->personal_item_name;
            $requestData['kso_item_id'] = null;
        }

        MaintenanceSchedule::create($requestData);

        return redirect()->route('kso-roi.technician-dashboard')
            ->with('success', 'Jadwal maintenance berhasil ditambahkan!');
    }

// ... (rest of the code remains the same)
    /**
     * Show form to edit maintenance schedule
     */
    public function editMaintenanceSchedule(MaintenanceSchedule $maintenanceSchedule)
    {
        $ksoItems = KsoItem::with(['customer', 'supportItems'])->get();

        return view('kso-roi.maintenance-schedule-edit', compact('maintenanceSchedule', 'ksoItems'));
    }

    /**
     * Update maintenance schedule
     */
    public function updateMaintenanceSchedule(Request $request, MaintenanceSchedule $maintenanceSchedule)
    {
        $request->validate([
            'maintenance_category' => 'required|in:kso,personal',
            'kso_item_id' => 'required_if:maintenance_category,kso|exists:kso_items,id',
            'personal_item_name' => 'required_if:maintenance_category,personal|string|max:255',
            'next_maintenance_date' => 'required|date',
            'maintenance_type' => 'required|string|max:255',
            'technician' => 'nullable|string|max:255',
            'technician_notes' => 'nullable|string',
        ]);

        // Keep original values for hidden fields
        $requestData = $request->all();
        $requestData['status'] = $maintenanceSchedule->status;
        $requestData['last_maintenance_date'] = $maintenanceSchedule->last_maintenance_date;
        $requestData['description'] = $maintenanceSchedule->description;
        $requestData['notes'] = $maintenanceSchedule->notes;
        $requestData['cost'] = $maintenanceSchedule->cost;

        if ($request->maintenance_category === 'kso') {
            // KSO Item maintenance
            $requestData['equipment_type'] = 'main';
            $requestData['equipment_name'] = '';

            // Get equipment name from KSO item
            $ksoItem = KsoItem::find($request->kso_item_id);
            if ($ksoItem) {
                $requestData['equipment_name'] = $ksoItem->nama_alat;
            }
        } else {
            // Personal item maintenance
            $requestData['equipment_type'] = 'personal';
            $requestData['equipment_name'] = $request->personal_item_name;
            $requestData['kso_item_id'] = null;
        }

        $maintenanceSchedule->update($requestData);

        return redirect()->route('kso-roi.technician-dashboard')
            ->with('success', 'Jadwal maintenance berhasil diperbarui!');
    }

    /**
     * Delete maintenance schedule
     */
    public function destroyMaintenanceSchedule(MaintenanceSchedule $maintenanceSchedule)
    {
        $maintenanceSchedule->delete();

        return redirect()->route('kso-roi.technician-dashboard')
            ->with('success', 'Jadwal maintenance berhasil dihapus!');
    }
}
