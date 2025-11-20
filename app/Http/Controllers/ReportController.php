<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function stockReport(Request $request)
    {
        $products = Product::with('category')
            ->when($request->category_id, function ($query) use ($request) {
                return $query->where('category_id', $request->category_id);
            })
            ->when($request->low_stock, function ($query) {
                return $query->lowStock();
            })
            ->orderBy('name')
            ->get();

        return view('reports.stock', compact('products'));
    }

    public function movementReport(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        $movements = StockMovement::with(['product', 'supplier', 'customer'])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->when($request->type, function ($query) use ($request) {
                return $query->where('type', $request->type);
            })
            ->orderBy('transaction_date', 'desc')
            ->get();

        return view('reports.movement', compact('movements', 'startDate', 'endDate'));
    }

    public function supplierReport()
    {
        $suppliers = Supplier::withCount('stockMovements')
            ->with(['stockMovements' => function ($query) {
                $query->where('type', 'in');
            }])
            ->orderBy('name')
            ->get();

        return view('reports.supplier', compact('suppliers'));
    }

    public function customerReport()
    {
        $customers = Customer::withCount('stockMovements')
            ->with(['stockMovements' => function ($query) {
                $query->where('type', 'out');
            }])
            ->orderBy('name')
            ->get();

        return view('reports.customer', compact('customers'));
    }

    // Excel Export Methods
    public function exportStockReport(Request $request)
    {
        $products = Product::with('category')
            ->when($request->category_id, function ($query) use ($request) {
                return $query->where('category_id', $request->category_id);
            })
            ->when($request->low_stock, function ($query) {
                return $query->lowStock();
            })
            ->orderBy('name')
            ->get();

        $filename = 'laporan_stok_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($products, $request) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 to ensure proper encoding in Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Set delimiter and enclosure for better Excel compatibility
            $delimiter = ';';
            $enclosure = '"';

            // Report Header Information
            fputcsv($file, ['LAPORAN STOK PRODUK'], $delimiter, $enclosure);
            fputcsv($file, ['PT. Mitrajaya Selaras Abadi'], $delimiter, $enclosure);
            fputcsv($file, ['Tanggal Export: ' . now()->format('d/m/Y H:i:s')], $delimiter, $enclosure);
            fputcsv($file, ['Filter: ' . ($request->low_stock ? 'Stok Menipis' : 'Semua Produk')], $delimiter, $enclosure);
            fputcsv($file, [''], $delimiter, $enclosure); // Empty row

            // Summary Information
            $totalProducts = $products->count();
            $totalStockValue = $products->sum(function ($product) {
                return $product->current_stock * $product->price;
            });
            $lowStockCount = $products->filter(function ($product) {
                return $product->isLowStock();
            })->count();
            $outOfStockCount = $products->where('current_stock', 0)->count();
            $expiredCount = $products->filter(function ($product) {
                return $product->isExpired();
            })->count();
            $expiringSoonCount = $products->filter(function ($product) {
                return $product->isExpiringSoon();
            })->count();

            fputcsv($file, ['RINGKASAN LAPORAN'], $delimiter, $enclosure);
            fputcsv($file, ['Total Produk', $totalProducts], $delimiter, $enclosure);
            fputcsv($file, ['Total Nilai Stok', 'Rp ' . number_format($totalStockValue, 0, ',', '.')], $delimiter, $enclosure);
            fputcsv($file, ['Produk Stok Menipis', $lowStockCount], $delimiter, $enclosure);
            fputcsv($file, ['Produk Stok Habis', $outOfStockCount], $delimiter, $enclosure);
            fputcsv($file, ['Produk Kedaluwarsa', $expiredCount], $delimiter, $enclosure);
            fputcsv($file, ['Produk Akan Kedaluwarsa (30 hari)', $expiringSoonCount], $delimiter, $enclosure);
            fputcsv($file, [''], $delimiter, $enclosure); // Empty row

            // Column Headers
            fputcsv($file, [
                'No',
                'Kode Produk',
                'Nama Produk',
                'Kategori',
                'Stok Saat Ini',
                'Stok Minimum',
                'Satuan',
                'Harga Satuan',
                'Total Nilai Stok',
                'Status Stok',
                'Status Kedaluwarsa',
                'Nomor Lot',
                'Tanggal Kedaluwarsa',
                'Nomor Izin Edar',
                'Deskripsi',
                'Tanggal Dibuat',
                'Terakhir Diupdate'
            ], $delimiter, $enclosure);

            $no = 1;
            foreach ($products as $product) {
                // Stock Status
                if ($product->current_stock == 0) {
                    $stockStatus = 'Stok Habis';
                } elseif ($product->isLowStock()) {
                    $stockStatus = 'Stok Menipis';
                } else {
                    $stockStatus = 'Stok Aman';
                }

                // Expiry Status
                if ($product->isExpired()) {
                    $expiryStatus = 'Kedaluwarsa';
                } elseif ($product->isExpiringSoon()) {
                    $expiryStatus = 'Akan Kedaluwarsa';
                } elseif ($product->expired_date) {
                    $expiryStatus = 'Masih Valid';
                } else {
                    $expiryStatus = 'Tidak Ada Tanggal';
                }

                $expiredDate = $product->expired_date ? $product->expired_date->format('d/m/Y') : '-';
                $totalStockValue = $product->current_stock * $product->price;

                fputcsv($file, [
                    $no++,
                    $product->code,
                    $product->name,
                    $product->category->name ?? '-',
                    $product->current_stock,
                    $product->minimum_stock,
                    $product->unit,
                    number_format($product->price, 0, ',', '.'),
                    number_format($totalStockValue, 0, ',', '.'),
                    $stockStatus,
                    $expiryStatus,
                    $product->lot_number ?? '-',
                    $expiredDate,
                    $product->distribution_permit ?? '-',
                    $product->description ?? '-',
                    $product->created_at->format('d/m/Y H:i'),
                    $product->updated_at->format('d/m/Y H:i')
                ], $delimiter, $enclosure);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportMovementReport(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        $movements = StockMovement::with(['product.category', 'supplier', 'customer'])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->when($request->type, function ($query) use ($request) {
                return $query->where('type', $request->type);
            })
            ->orderBy('transaction_date', 'desc')
            ->get();

        $filename = 'laporan_pergerakan_stok_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($movements, $startDate, $endDate, $request) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 to ensure proper encoding in Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Set delimiter and enclosure for better Excel compatibility
            $delimiter = ';';
            $enclosure = '"';

            // Report Header Information
            fputcsv($file, ['LAPORAN PERGERAKAN STOK'], $delimiter, $enclosure);
            fputcsv($file, ['PT. Mitrajaya Selaras Abadi'], $delimiter, $enclosure);
            fputcsv($file, ['Tanggal Export: ' . now()->format('d/m/Y H:i:s')], $delimiter, $enclosure);
            fputcsv($file, ['Periode: ' . $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y')], $delimiter, $enclosure);
            fputcsv($file, ['Filter Jenis: ' . ($request->type ? ucfirst($request->type) : 'Semua Transaksi')], $delimiter, $enclosure);
            fputcsv($file, [''], $delimiter, $enclosure); // Empty row

            // Summary Information
            $totalMovements = $movements->count();
            $stockInCount = $movements->where('type', 'in')->count();
            $stockOutCount = $movements->where('type', 'out')->count();
            $opnameCount = $movements->where('type', 'opname')->count();

            $totalStockIn = $movements->where('type', 'in')->sum('quantity');
            $totalStockOut = $movements->where('type', 'out')->sum('quantity');

            $totalValueIn = $movements->where('type', 'in')->sum(function ($movement) {
                return $movement->final_amount ?? ($movement->quantity * ($movement->unit_price ?? $movement->product->price));
            });
            $totalValueOut = $movements->where('type', 'out')->sum(function ($movement) {
                return $movement->final_amount ?? ($movement->quantity * ($movement->unit_price ?? $movement->product->price));
            });

            // Calculate total tax
            $totalTaxIn = $movements->where('type', 'in')->where('include_tax', true)->sum('tax_amount');
            $totalTaxOut = $movements->where('type', 'out')->where('include_tax', true)->sum('tax_amount');

            fputcsv($file, ['RINGKASAN PERGERAKAN STOK'], $delimiter, $enclosure);
            fputcsv($file, ['Total Transaksi', $totalMovements], $delimiter, $enclosure);
            fputcsv($file, ['Transaksi Masuk', $stockInCount . ' transaksi'], $delimiter, $enclosure);
            fputcsv($file, ['Transaksi Keluar', $stockOutCount . ' transaksi'], $delimiter, $enclosure);
            fputcsv($file, ['Stock Opname', $opnameCount . ' transaksi'], $delimiter, $enclosure);
            fputcsv($file, ['Total Unit Masuk', number_format($totalStockIn, 0, ',', '.')], $delimiter, $enclosure);
            fputcsv($file, ['Total Unit Keluar', number_format($totalStockOut, 0, ',', '.')], $delimiter, $enclosure);
            fputcsv($file, ['Total Nilai Masuk', 'Rp ' . number_format($totalValueIn, 0, ',', '.')], $delimiter, $enclosure);
            fputcsv($file, ['Total Nilai Keluar', 'Rp ' . number_format($totalValueOut, 0, ',', '.')], $delimiter, $enclosure);
            fputcsv($file, ['Total PPN Masuk', 'Rp ' . number_format($totalTaxIn, 0, ',', '.')], $delimiter, $enclosure);
            fputcsv($file, ['Total PPN Keluar', 'Rp ' . number_format($totalTaxOut, 0, ',', '.')], $delimiter, $enclosure);
            fputcsv($file, [''], $delimiter, $enclosure); // Empty row

            // Column Headers
            fputcsv($file, [
                'No',
                'Tanggal & Waktu',
                'No. Pemesanan',
                'No. Invoice',
                'Kode Produk',
                'Nama Produk',
                'Kategori',
                'Jenis Transaksi',
                'Jumlah',
                'Satuan',
                'Harga Satuan',
                'Subtotal',
                'PPN 11%',
                'Total Akhir',
                'Distributor/Customer',
                'Kontak Partner',
                'Stok Sebelum',
                'Stok Sesudah',
                'Selisih Stok',
                'Catatan',
                'User Input',
                'Waktu Input'
            ], $delimiter, $enclosure);

            $no = 1;
            foreach ($movements as $movement) {
                $type = $movement->type == 'in' ? 'Stok Masuk' : ($movement->type == 'out' ? 'Stok Keluar' : 'Stock Opname');

                $partner = '';
                $partnerContact = '';
                if ($movement->supplier) {
                    $partner = $movement->supplier->name;
                    $partnerContact = $movement->supplier->phone ?? $movement->supplier->email ?? '-';
                } elseif ($movement->customer) {
                    $partner = $movement->customer->name;
                    $partnerContact = $movement->customer->phone ?? $movement->customer->email ?? '-';
                } else {
                    $partner = '-';
                    $partnerContact = '-';
                }

                $unitPrice = $movement->unit_price ?? $movement->product->price ?? 0;
                $subtotal = $movement->quantity * $unitPrice;
                $taxAmount = $movement->tax_amount ?? 0;
                $finalAmount = $movement->final_amount ?? $subtotal;
                $stockDifference = $movement->stock_after - $movement->stock_before;

                // Format PPN display
                $ppnDisplay = $movement->include_tax ? 'Rp ' . number_format($taxAmount, 0, ',', '.') : '-';

                fputcsv($file, [
                    $no++,
                    $movement->transaction_date->format('d/m/Y H:i:s'),
                    $movement->order_number ?? '-',
                    $movement->invoice_number ?? '-',
                    $movement->product->code ?? '-',
                    $movement->product->name ?? 'Produk Dihapus',
                    $movement->product->category->name ?? '-',
                    $type,
                    number_format($movement->quantity, 0, ',', '.'),
                    $movement->product->unit ?? 'pcs',
                    'Rp ' . number_format($unitPrice, 0, ',', '.'),
                    'Rp ' . number_format($subtotal, 0, ',', '.'),
                    $ppnDisplay,
                    'Rp ' . number_format($finalAmount, 0, ',', '.'),
                    $partner,
                    $partnerContact,
                    number_format($movement->stock_before, 0, ',', '.'),
                    number_format($movement->stock_after, 0, ',', '.'),
                    ($stockDifference >= 0 ? '+' : '') . number_format($stockDifference, 0, ',', '.'),
                    $movement->notes ?? '-',
                    $movement->user->name ?? 'System',
                    $movement->created_at->format('d/m/Y H:i:s')
                ], $delimiter, $enclosure);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportSupplierReport()
    {
        $suppliers = Supplier::withCount(['stockMovements' => function ($query) {
            $query->where('type', 'in');
        }])
            ->with(['stockMovements' => function ($query) {
                $query->where('type', 'in')->with('product');
            }])
            ->orderBy('name')
            ->get();

        $filename = 'laporan_distributor_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($suppliers) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 to ensure proper encoding in Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Set delimiter and enclosure for better Excel compatibility
            $delimiter = ';';
            $enclosure = '"';

            // Report Header Information
            fputcsv($file, ['LAPORAN DISTRIBUTOR'], $delimiter, $enclosure);
            fputcsv($file, ['PT. Mitrajaya Selaras Abadi'], $delimiter, $enclosure);
            fputcsv($file, ['Tanggal Export: ' . now()->format('d/m/Y H:i:s')], $delimiter, $enclosure);
            fputcsv($file, [''], $delimiter, $enclosure); // Empty row

            // Summary Information
            $totalSuppliers = $suppliers->count();
            $activeSuppliers = $suppliers->filter(function ($supplier) {
                return $supplier->stock_movements_count > 0;
            })->count();
            $totalTransactions = $suppliers->sum('stock_movements_count');
            $totalValue = $suppliers->sum(function ($supplier) {
                return $supplier->stockMovements->sum(function ($movement) {
                    return $movement->quantity * ($movement->unit_price ?? $movement->product->price ?? 0);
                });
            });

            fputcsv($file, ['RINGKASAN DISTRIBUTOR'], $delimiter, $enclosure);
            fputcsv($file, ['Total Distributor', $totalSuppliers], $delimiter, $enclosure);
            fputcsv($file, ['Distributor Aktif', $activeSuppliers], $delimiter, $enclosure);
            fputcsv($file, ['Total Transaksi Masuk', $totalTransactions], $delimiter, $enclosure);
            fputcsv($file, ['Total Nilai Pembelian', 'Rp ' . number_format($totalValue, 0, ',', '.')], $delimiter, $enclosure);
            fputcsv($file, [''], $delimiter, $enclosure); // Empty row

            // Column Headers
            fputcsv($file, [
                'No',
                'Nama Distributor',
                'Email',
                'Telepon',
                'Alamat',
                'Total Transaksi Masuk',
                'Total Unit Masuk',
                'Total Nilai Transaksi',
                'Rata-rata Nilai per Transaksi',
                'Transaksi Terakhir',
                'Status',
                'Tanggal Terdaftar'
            ], $delimiter, $enclosure);

            $no = 1;
            foreach ($suppliers as $supplier) {
                $totalUnits = $supplier->stockMovements->sum('quantity');
                $totalValue = $supplier->stockMovements->sum(function ($movement) {
                    return $movement->quantity * ($movement->unit_price ?? $movement->product->price ?? 0);
                });

                $avgValuePerTransaction = $supplier->stock_movements_count > 0 ?
                    $totalValue / $supplier->stock_movements_count : 0;

                $lastTransaction = $supplier->stockMovements->sortByDesc('transaction_date')->first();
                $lastTransactionDate = $lastTransaction ?
                    $lastTransaction->transaction_date->format('d/m/Y') : '-';

                // Determine distributor status
                $status = 'Tidak Aktif';
                if ($supplier->stock_movements_count > 0) {
                    $daysSinceLastTransaction = $lastTransaction ?
                        $lastTransaction->transaction_date->diffInDays(now()) : 999;

                    if ($daysSinceLastTransaction <= 30) {
                        $status = 'Sangat Aktif';
                    } elseif ($daysSinceLastTransaction <= 90) {
                        $status = 'Aktif';
                    } else {
                        $status = 'Kurang Aktif';
                    }
                }

                fputcsv($file, [
                    $no++,
                    $supplier->name,
                    $supplier->email ?? '-',
                    $supplier->phone ?? '-',
                    $supplier->address ?? '-',
                    $supplier->stock_movements_count,
                    number_format($totalUnits, 0, ',', '.'),
                    number_format($totalValue, 0, ',', '.'),
                    number_format($avgValuePerTransaction, 0, ',', '.'),
                    $lastTransactionDate,
                    $status,
                    $supplier->created_at->format('d/m/Y')
                ], $delimiter, $enclosure);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function supplierDetail($id)
    {
        $supplier = Supplier::findOrFail($id);

        $stockMovements = StockMovement::with(['product.category'])
            ->where('supplier_id', $id)
            ->where('type', 'in')
            ->orderBy('transaction_date', 'desc')
            ->paginate(20);

        // Calculate statistics
        // OPTIMIZED: Using helper method from StockMovement model
        $totalStats = StockMovement::getSupplierStats($id);

        $totalTransactions = $totalStats->total_transactions ?? 0;
        $totalQuantity = $totalStats->total_quantity ?? 0;
        $totalValue = $totalStats->total_value ?? 0;

        return view('reports.supplier-detail', compact(
            'supplier',
            'stockMovements',
            'totalTransactions',
            'totalQuantity',
            'totalValue'
        ));
    }

    public function exportSupplierDetail($id)
    {
        $supplier = Supplier::findOrFail($id);

        $stockMovements = StockMovement::with(['product.category'])
            ->where('supplier_id', $id)
            ->where('type', 'in')
            ->orderBy('transaction_date', 'desc')
            ->get();

        $filename = 'detail_distributor_' . str_replace(' ', '_', $supplier->name) . '_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($supplier, $stockMovements) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 to ensure proper encoding in Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $delimiter = ';';
            $enclosure = '"';

            // Report Header
            fputcsv($file, ['DETAIL TRANSAKSI DISTRIBUTOR'], $delimiter, $enclosure);
            fputcsv($file, ['PT. Mitrajaya Selaras Abadi'], $delimiter, $enclosure);
            fputcsv($file, ['Tanggal Export: ' . now()->format('d/m/Y H:i:s')], $delimiter, $enclosure);
            fputcsv($file, ['Distributor: ' . $supplier->name], $delimiter, $enclosure);
            fputcsv($file, [''], $delimiter, $enclosure);

            // Summary
            $totalTransactions = $stockMovements->count();
            $totalQuantity = $stockMovements->sum('quantity');
            $totalValue = $stockMovements->sum(function ($movement) {
                return $movement->final_amount ?? ($movement->quantity * ($movement->unit_price ?? 0));
            });
            $totalTax = $stockMovements->where('include_tax', true)->sum('tax_amount');

            fputcsv($file, ['RINGKASAN'], $delimiter, $enclosure);
            fputcsv($file, ['Total Transaksi', $totalTransactions], $delimiter, $enclosure);
            fputcsv($file, ['Total Quantity', number_format($totalQuantity)], $delimiter, $enclosure);
            fputcsv($file, ['Total Nilai', 'Rp ' . number_format($totalValue, 0, ',', '.')], $delimiter, $enclosure);
            fputcsv($file, ['Total PPN', 'Rp ' . number_format($totalTax, 0, ',', '.')], $delimiter, $enclosure);
            fputcsv($file, [''], $delimiter, $enclosure);

            // Column Headers
            fputcsv($file, [
                'No',
                'Tanggal',
                'No. Referensi',
                'No. Pemesanan',
                'No. Invoice',
                'Kode Produk',
                'Nama Produk',
                'Kategori',
                'Quantity',
                'Satuan',
                'Harga Satuan',
                'Total Nilai',
                'Catatan'
            ], $delimiter, $enclosure);

            $no = 1;
            foreach ($stockMovements as $movement) {
                $totalItemValue = $movement->quantity * ($movement->unit_price ?? 0);

                fputcsv($file, [
                    $no++,
                    $movement->transaction_date->format('d/m/Y H:i'),
                    $movement->reference_number,
                    $movement->order_number ?? '-',
                    $movement->invoice_number ?? '-',
                    $movement->product->code ?? '-',
                    $movement->product->name ?? 'Produk Dihapus',
                    $movement->product->category->name ?? '-',
                    number_format($movement->quantity),
                    $movement->product->unit ?? 'pcs',
                    number_format($movement->unit_price ?? 0, 0, ',', '.'),
                    number_format($totalItemValue, 0, ',', '.'),
                    $movement->notes ?? '-'
                ], $delimiter, $enclosure);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function customerDetail(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        // Get selected year from request, default to current year
        $selectedYear = $request->get('year', date('Y'));

        // Get filter parameters
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $productId = $request->get('product_id');
        $sortBy = $request->get('sort_by', 'transaction_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 20);

        // Build query with filters
        $query = StockMovement::with(['product.category'])
            ->where('customer_id', $id)
            ->where('type', 'out');

        // Apply date range filter
        if ($dateFrom) {
            $query->whereDate('transaction_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('transaction_date', '<=', $dateTo);
        }

        // Apply product filter
        if ($productId) {
            $query->where('product_id', $productId);
        }

        // Apply sorting
        $validSortColumns = [
            'transaction_date',
            'reference_number',
            'order_number',
            'invoice_number',
            'quantity',
            'unit_price',
            'total_value',
            'product_name'
        ];

        if (in_array($sortBy, $validSortColumns)) {
            if ($sortBy === 'total_value') {
                // For total value, we need to calculate it
                $query->selectRaw('stock_movements.*, (quantity * unit_price) as total_value')
                    ->orderBy('total_value', $sortOrder);
            } elseif ($sortBy === 'product_name') {
                // For product name, we need to join with products table
                $query->join('products', 'stock_movements.product_id', '=', 'products.id')
                    ->select('stock_movements.*')
                    ->orderBy('products.name', $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        } else {
            $query->orderBy('transaction_date', 'desc');
        }

        // Get paginated results
        $stockMovements = $query->paginate($perPage)->appends($request->query());

        // Calculate statistics (for all transactions, not filtered)
        // OPTIMIZED: Using helper method from StockMovement model
        $totalStats = StockMovement::getCustomerStats($id);

        $totalTransactions = $totalStats->total_transactions ?? 0;
        $totalQuantity = $totalStats->total_quantity ?? 0;
        $totalValue = $totalStats->total_value ?? 0;

        // Calculate filtered statistics
        // OPTIMIZED: Single query with database calculation
        $filteredQuery = StockMovement::where('customer_id', $id)->where('type', 'out');
        if ($dateFrom) $filteredQuery->whereDate('transaction_date', '>=', $dateFrom);
        if ($dateTo) $filteredQuery->whereDate('transaction_date', '<=', $dateTo);
        if ($productId) $filteredQuery->where('product_id', $productId);

        $filteredStats = $filteredQuery->selectRaw('
                COUNT(*) as filtered_transactions,
                SUM(quantity) as filtered_quantity,
                SUM(quantity * COALESCE(unit_price, 0)) as filtered_value
            ')
            ->first();

        $filteredTransactions = $filteredStats->filtered_transactions ?? 0;
        $filteredQuantity = $filteredStats->filtered_quantity ?? 0;
        $filteredValue = $filteredStats->filtered_value ?? 0;

        // Generate chart data for selected year
        // OPTIMIZED: Using helper method from StockMovement model
        $chartData = $this->processChartData(StockMovement::getCustomerChartData($id, $selectedYear));

        // Generate sales trend data (nilai penjualan bulanan)
        $salesTrendData = $this->generateSalesTrendData($id, $selectedYear);

        // Get available years for dropdown
        $yearExpression = config('database.default') === 'sqlite'
            ? "strftime('%Y', transaction_date) as year"
            : 'YEAR(transaction_date) as year';

        $availableYears = StockMovement::where('customer_id', $id)
            ->where('type', 'out')
            ->selectRaw("strftime('%Y', transaction_date) as year")
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [date('Y')];
        }

        // Get products for filter dropdown
        // OPTIMIZED: Direct join without loading unnecessary data
        $products = Product::whereIn('id', function ($query) use ($id) {
            $query->select('product_id')
                ->from('stock_movements')
                ->where('customer_id', $id)
                ->where('type', 'out')
                ->distinct();
        })
            ->orderBy('name')
            ->get();

        return view('reports.customer-detail', compact(
            'customer',
            'stockMovements',
            'totalTransactions',
            'totalQuantity',
            'totalValue',
            'filteredTransactions',
            'filteredQuantity',
            'filteredValue',
            'chartData',
            'salesTrendData',
            'selectedYear',
            'availableYears',
            'products',
            'dateFrom',
            'dateTo',
            'productId',
            'sortBy',
            'sortOrder',
            'perPage'
        ));
    }

    /**
     * Process chart data from StockMovement helper methods
     * 
     * @param \Illuminate\Support\Collection $chartData
     * @return array
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

    /**
     * Generate sales trend data for monthly sales value chart
     * 
     * @param int $customerId
     * @param int $year
     * @return array
     */
    private function generateSalesTrendData($customerId, $year)
    {
        // Get monthly sales data for the customer
        $yearExpr = config('database.default') === 'sqlite' ? "strftime('%Y', transaction_date)" : "YEAR(transaction_date)";
        $monthExpr = config('database.default') === 'sqlite' ? "CAST(strftime('%m', transaction_date) AS INTEGER)" : "MONTH(transaction_date)";

        $monthlySales = StockMovement::where('customer_id', $customerId)
            ->where('type', 'out')
            ->whereRaw("$yearExpr = ?", [$year])
            ->selectRaw("$monthExpr as month, SUM(quantity * COALESCE(unit_price, 0)) as total_value")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Generate months array
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('M', mktime(0, 0, 0, $i, 1));
        }

        // Initialize monthly data with 0
        $monthlyData = array_fill(0, 12, 0);

        // Fill actual data
        foreach ($monthlySales as $data) {
            $monthlyData[$data->month - 1] = (float) $data->total_value;
        }

        return [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Nilai Penjualan (Rp)',
                    'data' => $monthlyData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => 'rgb(34, 197, 94)',
                    'pointBorderColor' => 'rgb(255, 255, 255)',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 5,
                    'pointHoverRadius' => 7
                ]
            ]
        ];
    }

    private function generateProductChartData($productId, $year)
    {
        // OPTIMIZED: Using helper method from StockMovement model
        return $this->processChartData(StockMovement::getProductChartData($productId, $year));
    }

    public function exportCustomerDetail($id)
    {
        $customer = Customer::findOrFail($id);

        $stockMovements = StockMovement::with(['product.category'])
            ->where('customer_id', $id)
            ->where('type', 'out')
            ->orderBy('transaction_date', 'desc')
            ->get();

        $filename = 'detail_customer_' . str_replace(' ', '_', $customer->name) . '_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($customer, $stockMovements) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 to ensure proper encoding in Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $delimiter = ';';
            $enclosure = '"';

            // Report Header
            fputcsv($file, ['DETAIL TRANSAKSI CUSTOMER'], $delimiter, $enclosure);
            fputcsv($file, ['PT. Mitrajaya Selaras Abadi'], $delimiter, $enclosure);
            fputcsv($file, ['Tanggal Export: ' . now()->format('d/m/Y H:i:s')], $delimiter, $enclosure);
            fputcsv($file, ['Customer: ' . $customer->name], $delimiter, $enclosure);
            fputcsv($file, [''], $delimiter, $enclosure);

            // Summary
            $totalTransactions = $stockMovements->count();
            $totalQuantity = $stockMovements->sum('quantity');
            $totalValue = $stockMovements->sum(function ($movement) {
                return $movement->quantity * ($movement->unit_price ?? 0);
            });

            fputcsv($file, ['RINGKASAN'], $delimiter, $enclosure);
            fputcsv($file, ['Total Transaksi', $totalTransactions], $delimiter, $enclosure);
            fputcsv($file, ['Total Quantity', number_format($totalQuantity)], $delimiter, $enclosure);
            fputcsv($file, ['Total Nilai', 'Rp ' . number_format($totalValue, 0, ',', '.')], $delimiter, $enclosure);
            fputcsv($file, [''], $delimiter, $enclosure);

            // Column Headers
            fputcsv($file, [
                'No',
                'Tanggal',
                'No. Referensi',
                'No. Pemesanan',
                'No. Invoice',
                'Kode Produk',
                'Nama Produk',
                'Kategori',
                'Quantity',
                'Satuan',
                'Harga Satuan',
                'Total Nilai',
                'Catatan'
            ], $delimiter, $enclosure);

            $no = 1;
            foreach ($stockMovements as $movement) {
                $totalItemValue = $movement->quantity * ($movement->unit_price ?? 0);

                fputcsv($file, [
                    $no++,
                    $movement->transaction_date->format('d/m/Y H:i'),
                    $movement->reference_number,
                    $movement->order_number ?? '-',
                    $movement->invoice_number ?? '-',
                    $movement->product->code ?? '-',
                    $movement->product->name ?? 'Produk Dihapus',
                    $movement->product->category->name ?? '-',
                    number_format($movement->quantity),
                    $movement->product->unit ?? 'pcs',
                    number_format($movement->unit_price ?? 0, 0, ',', '.'),
                    number_format($totalItemValue, 0, ',', '.'),
                    $movement->notes ?? '-'
                ], $delimiter, $enclosure);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function stockDetail(Request $request, $id)
    {
        $product = Product::with('category')->findOrFail($id);

        // Get selected year from request, default to current year
        $selectedYear = $request->get('year', date('Y'));

        $stockMovements = StockMovement::with(['supplier', 'customer'])
            ->where('product_id', $id)
            ->orderBy('transaction_date', 'desc')
            ->paginate(20);

        // Calculate statistics
        $totalStockIn = StockMovement::where('product_id', $id)
            ->where('type', 'in')
            ->sum('quantity');

        $totalStockOut = StockMovement::where('product_id', $id)
            ->where('type', 'out')
            ->sum('quantity');

        // Generate chart data for selected year
        $chartData = $this->generateProductChartData($id, $selectedYear);

        // Get available years for dropdown
        $yearExpression = config('database.default') === 'sqlite'
            ? "strftime('%Y', transaction_date) as year"
            : 'YEAR(transaction_date) as year';

        $availableYears = StockMovement::where('product_id', $id)
            ->where('type', 'out')
            ->selectRaw($yearExpression)
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [date('Y')];
        }

        return view('reports.stock-detail', compact(
            'product',
            'stockMovements',
            'totalStockIn',
            'totalStockOut',
            'chartData',
            'selectedYear',
            'availableYears'
        ));
    }

    public function exportStockDetail($id)
    {
        $product = Product::with('category')->findOrFail($id);

        $stockMovements = StockMovement::with(['supplier', 'customer'])
            ->where('product_id', $id)
            ->orderBy('transaction_date', 'desc')
            ->get();

        $filename = 'detail_stok_' . str_replace(' ', '_', $product->name) . '_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($product, $stockMovements) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 to ensure proper encoding in Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $delimiter = ';';
            $enclosure = '"';

            // Report Header
            fputcsv($file, ['DETAIL PERGERAKAN STOK PRODUK'], $delimiter, $enclosure);
            fputcsv($file, ['PT. Mitrajaya Selaras Abadi'], $delimiter, $enclosure);
            fputcsv($file, ['Tanggal Export: ' . now()->format('d/m/Y H:i:s')], $delimiter, $enclosure);
            fputcsv($file, ['Produk: ' . $product->name . ' (' . $product->code . ')'], $delimiter, $enclosure);
            fputcsv($file, [''], $delimiter, $enclosure);

            // Product Info
            fputcsv($file, ['INFORMASI PRODUK'], $delimiter, $enclosure);
            fputcsv($file, ['Nama Produk', $product->name], $delimiter, $enclosure);
            fputcsv($file, ['Kode Produk', $product->code], $delimiter, $enclosure);
            fputcsv($file, ['Kategori', $product->category->name ?? '-'], $delimiter, $enclosure);
            fputcsv($file, ['Satuan', $product->unit], $delimiter, $enclosure);
            fputcsv($file, ['Harga Satuan', 'Rp ' . number_format($product->price, 0, ',', '.')], $delimiter, $enclosure);
            fputcsv($file, ['Stok Saat Ini', number_format($product->current_stock)], $delimiter, $enclosure);
            fputcsv($file, ['Minimum Stok', number_format($product->minimum_stock)], $delimiter, $enclosure);
            fputcsv($file, ['Status Stok', $product->current_stock <= $product->minimum_stock ? 'Stok Menipis' : 'Stok Aman'], $delimiter, $enclosure);
            fputcsv($file, [''], $delimiter, $enclosure);

            // Summary
            $totalStockIn = $stockMovements->where('type', 'in')->sum('quantity');
            $totalStockOut = $stockMovements->where('type', 'out')->sum('quantity');
            $totalMovements = $stockMovements->count();

            fputcsv($file, ['RINGKASAN PERGERAKAN'], $delimiter, $enclosure);
            fputcsv($file, ['Total Pergerakan', $totalMovements], $delimiter, $enclosure);
            fputcsv($file, ['Total Stok Masuk', number_format($totalStockIn)], $delimiter, $enclosure);
            fputcsv($file, ['Total Stok Keluar', number_format($totalStockOut)], $delimiter, $enclosure);
            fputcsv($file, ['Nilai Stok Saat Ini', 'Rp ' . number_format($product->current_stock * $product->price, 0, ',', '.')], $delimiter, $enclosure);
            fputcsv($file, [''], $delimiter, $enclosure);

            // Column Headers
            fputcsv($file, [
                'No',
                'Tanggal',
                'Jenis Transaksi',
                'No. Referensi',
                'No. Pemesanan',
                'No. Invoice',
                'Partner',
                'Quantity',
                'Satuan',
                'Stok Sebelum',
                'Stok Sesudah',
                'Selisih',
                'Catatan'
            ], $delimiter, $enclosure);

            $no = 1;
            foreach ($stockMovements as $movement) {
                $type = $movement->type == 'in' ? 'Stok Masuk' : ($movement->type == 'out' ? 'Stok Keluar' : 'Stock Opname');

                $partner = '';
                if ($movement->supplier) {
                    $partner = $movement->supplier->name;
                } elseif ($movement->customer) {
                    $partner = $movement->customer->name;
                } else {
                    $partner = '-';
                }

                $stockDifference = $movement->stock_after - $movement->stock_before;

                fputcsv($file, [
                    $no++,
                    $movement->transaction_date->format('d/m/Y H:i'),
                    $type,
                    $movement->reference_number,
                    $movement->order_number ?? '-',
                    $movement->invoice_number ?? '-',
                    $partner,
                    number_format($movement->quantity),
                    $product->unit,
                    number_format($movement->stock_before),
                    number_format($movement->stock_after),
                    ($stockDifference >= 0 ? '+' : '') . number_format($stockDifference),
                    $movement->notes ?? '-'
                ], $delimiter, $enclosure);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCustomerReport()
    {
        $customers = Customer::withCount(['stockMovements' => function ($query) {
            $query->where('type', 'out');
        }])
            ->with(['stockMovements' => function ($query) {
                $query->where('type', 'out')->with('product');
            }])
            ->orderBy('name')
            ->get();

        $filename = 'laporan_customer_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($customers) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 to ensure proper encoding in Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Set delimiter and enclosure for better Excel compatibility
            $delimiter = ';';
            $enclosure = '"';

            // Report Header Information
            fputcsv($file, ['LAPORAN CUSTOMER'], $delimiter, $enclosure);
            fputcsv($file, ['PT. Mitrajaya Selaras Abadi'], $delimiter, $enclosure);
            fputcsv($file, ['Tanggal Export: ' . now()->format('d/m/Y H:i:s')], $delimiter, $enclosure);
            fputcsv($file, [''], $delimiter, $enclosure); // Empty row

            // Summary Information
            $totalCustomers = $customers->count();
            $activeCustomers = $customers->filter(function ($customer) {
                return $customer->stock_movements_count > 0;
            })->count();
            $totalTransactions = $customers->sum('stock_movements_count');
            $totalValue = $customers->sum(function ($customer) {
                return $customer->stockMovements->sum(function ($movement) {
                    return $movement->quantity * ($movement->unit_price ?? $movement->product->price ?? 0);
                });
            });

            fputcsv($file, ['RINGKASAN CUSTOMER'], $delimiter, $enclosure);
            fputcsv($file, ['Total Customer', $totalCustomers], $delimiter, $enclosure);
            fputcsv($file, ['Customer Aktif', $activeCustomers], $delimiter, $enclosure);
            fputcsv($file, ['Total Transaksi Keluar', $totalTransactions], $delimiter, $enclosure);
            fputcsv($file, ['Total Nilai Penjualan', 'Rp ' . number_format($totalValue, 0, ',', '.')], $delimiter, $enclosure);
            fputcsv($file, [''], $delimiter, $enclosure); // Empty row

            // Column Headers
            fputcsv($file, [
                'No',
                'Nama Customer',
                'Email',
                'Telepon',
                'Alamat',
                'Total Transaksi Keluar',
                'Total Unit Keluar',
                'Total Nilai Transaksi',
                'Rata-rata Nilai per Transaksi',
                'Transaksi Terakhir',
                'Status',
                'Tanggal Terdaftar'
            ], $delimiter, $enclosure);

            $no = 1;
            foreach ($customers as $customer) {
                $totalUnits = $customer->stockMovements->sum('quantity');
                $totalValue = $customer->stockMovements->sum(function ($movement) {
                    return $movement->quantity * ($movement->unit_price ?? $movement->product->price ?? 0);
                });

                $avgValuePerTransaction = $customer->stock_movements_count > 0 ?
                    $totalValue / $customer->stock_movements_count : 0;

                $lastTransaction = $customer->stockMovements->sortByDesc('transaction_date')->first();
                $lastTransactionDate = $lastTransaction ?
                    $lastTransaction->transaction_date->format('d/m/Y') : '-';

                // Determine customer status
                $status = 'Tidak Aktif';
                if ($customer->stock_movements_count > 0) {
                    $daysSinceLastTransaction = $lastTransaction ?
                        $lastTransaction->transaction_date->diffInDays(now()) : 999;

                    if ($daysSinceLastTransaction <= 30) {
                        $status = 'Sangat Aktif';
                    } elseif ($daysSinceLastTransaction <= 90) {
                        $status = 'Aktif';
                    } else {
                        $status = 'Kurang Aktif';
                    }
                }

                fputcsv($file, [
                    $no++,
                    $customer->name,
                    $customer->email ?? '-',
                    $customer->phone ?? '-',
                    $customer->address ?? '-',
                    $customer->stock_movements_count,
                    number_format($totalUnits, 0, ',', '.'),
                    number_format($totalValue, 0, ',', '.'),
                    number_format($avgValuePerTransaction, 0, ',', '.'),
                    $lastTransactionDate,
                    $status,
                    $customer->created_at->format('d/m/Y')
                ], $delimiter, $enclosure);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
