@extends('layouts.app')

@section('title', 'Detail Customer - ' . $customer->name)

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <div class="flex items-center mb-2">
                <a href="{{ route('reports.customer') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Detail Customer</h1>
            </div>
            <p class="text-gray-600">History transaksi dan statistik untuk {{ $customer->name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('reports.export.customer.detail', $customer->id) }}" 
               class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-file-excel mr-2"></i>
                Export Excel
            </a>
        </div>
    </div>
</div>

<!-- Customer Info Card -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Customer</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-500">Nama Customer</label>
                    <p class="text-gray-900">{{ $customer->name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Kontak</label>
                    <div class="space-y-1">
                        @if($customer->contact_person)
                            <p class="text-gray-900">{{ $customer->contact_person }}</p>
                        @endif
                        @if($customer->contact_person_2)
                            <p class="text-gray-600">{{ $customer->contact_person_2 }}</p>
                        @endif
                        @if($customer->contact_person_3)
                            <p class="text-gray-600">{{ $customer->contact_person_3 }}</p>
                        @endif
                        @if(!$customer->contact_person && !$customer->contact_person_2 && !$customer->contact_person_3)
                            <p class="text-gray-400">-</p>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Telepon</label>
                    <div class="space-y-1">
                        @if($customer->phone)
                            <p class="text-gray-900">{{ $customer->phone }}</p>
                        @endif
                        @if($customer->phone_2)
                            <p class="text-gray-600">{{ $customer->phone_2 }}</p>
                        @endif
                        @if($customer->phone_3)
                            <p class="text-gray-600">{{ $customer->phone_3 }}</p>
                        @endif
                        @if(!$customer->phone && !$customer->phone_2 && !$customer->phone_3)
                            <p class="text-gray-400">-</p>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Email</label>
                    <p class="text-gray-900">{{ $customer->email ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Alamat</label>
                    <p class="text-gray-900">{{ $customer->address ?? '-' }}</p>
                </div>
            </div>
        </div>
        
        <!-- Statistics -->
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik Transaksi</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-full">
                            <i class="fas fa-shopping-cart text-blue-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-600">Total Transaksi</p>
                            <p class="text-xl font-semibold text-blue-900">{{ $totalTransactions }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-full">
                            <i class="fas fa-boxes text-green-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-600">Total Quantity</p>
                            <p class="text-xl font-semibold text-green-900">{{ number_format($totalQuantity) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 p-4 rounded-lg col-span-2">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-full">
                            <i class="fas fa-calculator text-purple-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-purple-600">Total Nilai Penjualan</p>
                            <p class="text-2xl font-semibold text-purple-900">Rp {{ number_format($totalValue, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Purchase Chart -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Grafik Pembelian Produk per Bulan</h3>
            <p class="text-sm text-gray-600">Jumlah produk yang dibeli setiap bulan dalam tahun {{ $selectedYear }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <label for="yearSelect" class="text-sm font-medium text-gray-700">Tahun:</label>
            <select id="yearSelect" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @foreach($availableYears as $year)
                    <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div class="relative" style="height: 400px;">
        <canvas id="customerProductChart"></canvas>
    </div>
</div>

<!-- Sales Trend Chart -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Trend Penjualan Bulanan</h3>
            <p class="text-sm text-gray-600">Nilai penjualan dalam rupiah setiap bulan dalam tahun {{ $selectedYear }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <label for="salesYearSelect" class="text-sm font-medium text-gray-700">Tahun:</label>
            <select id="salesYearSelect" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @foreach($availableYears as $year)
                    <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div class="relative" style="height: 400px;">
        <canvas id="salesTrendChart"></canvas>
    </div>
</div>

<!-- Transaction History -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">History Transaksi</h3>
            <button id="toggleFilters" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-filter mr-2"></i>
                <span id="filterToggleText">Tampilkan Filter</span>
                <i class="fas fa-chevron-down ml-2" id="filterChevron"></i>
            </button>
        </div>
        
        <!-- Filter Section -->
        <div id="filterSection" class="hidden bg-gray-50 -mx-6 px-6 py-4 border-t border-gray-200">
            <form method="GET" action="{{ route('reports.customer.detail', $customer->id) }}" class="space-y-4">
                <!-- Preserve year parameter -->
                <input type="hidden" name="year" value="{{ $selectedYear }}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Date From -->
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dari</label>
                        <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <!-- Date To -->
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Sampai</label>
                        <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <!-- Product Filter -->
                    <div>
                        <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Produk</label>
                        <select id="product_id" name="product_id" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Produk</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Per Page -->
                    <div>
                        <label for="per_page" class="block text-sm font-medium text-gray-700 mb-1">Tampilkan</label>
                        <select id="per_page" name="per_page" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 per halaman</option>
                            <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20 per halaman</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 per halaman</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 per halaman</option>
                        </select>
                    </div>
                </div>
                
                
                <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                    <div class="flex space-x-3">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-search mr-2"></i>
                            Terapkan Filter
                        </button>
                        <a href="{{ route('reports.customer.detail', $customer->id) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-times mr-2"></i>
                            Reset Filter
                        </a>
                    </div>
                    
                    @if($dateFrom || $dateTo || $productId)
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">Hasil Filter:</span> 
                            {{ number_format($filteredTransactions) }} transaksi, 
                            {{ number_format($filteredQuantity) }} qty, 
                            Rp {{ number_format($filteredValue, 0, ',', '.') }}
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'transaction_date', 'sort_order' => $sortBy == 'transaction_date' && $sortOrder == 'asc' ? 'desc' : 'asc']) }}" 
                           class="group inline-flex items-center hover:text-gray-900">
                            Tanggal
                            @if($sortBy == 'transaction_date')
                                <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }} ml-1 text-gray-400 group-hover:text-gray-600"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-300 group-hover:text-gray-400"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'reference_number', 'sort_order' => $sortBy == 'reference_number' && $sortOrder == 'asc' ? 'desc' : 'asc']) }}" 
                           class="group inline-flex items-center hover:text-gray-900">
                            No. Referensi
                            @if($sortBy == 'reference_number')
                                <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }} ml-1 text-gray-400 group-hover:text-gray-600"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-300 group-hover:text-gray-400"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'order_number', 'sort_order' => $sortBy == 'order_number' && $sortOrder == 'asc' ? 'desc' : 'asc']) }}" 
                           class="group inline-flex items-center hover:text-gray-900">
                            No. Pemesanan
                            @if($sortBy == 'order_number')
                                <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }} ml-1 text-gray-400 group-hover:text-gray-600"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-300 group-hover:text-gray-400"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'invoice_number', 'sort_order' => $sortBy == 'invoice_number' && $sortOrder == 'asc' ? 'desc' : 'asc']) }}" 
                           class="group inline-flex items-center hover:text-gray-900">
                            No. Invoice
                            @if($sortBy == 'invoice_number')
                                <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }} ml-1 text-gray-400 group-hover:text-gray-600"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-300 group-hover:text-gray-400"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'product_name', 'sort_order' => $sortBy == 'product_name' && $sortOrder == 'asc' ? 'desc' : 'asc']) }}" 
                           class="group inline-flex items-center hover:text-gray-900">
                            Produk
                            @if($sortBy == 'product_name')
                                <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }} ml-1 text-gray-400 group-hover:text-gray-600"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-300 group-hover:text-gray-400"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'quantity', 'sort_order' => $sortBy == 'quantity' && $sortOrder == 'asc' ? 'desc' : 'asc']) }}" 
                           class="group inline-flex items-center hover:text-gray-900">
                            Qty
                            @if($sortBy == 'quantity')
                                <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }} ml-1 text-gray-400 group-hover:text-gray-600"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-300 group-hover:text-gray-400"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'unit_price', 'sort_order' => $sortBy == 'unit_price' && $sortOrder == 'asc' ? 'desc' : 'asc']) }}" 
                           class="group inline-flex items-center hover:text-gray-900">
                            Harga Satuan
                            @if($sortBy == 'unit_price')
                                <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }} ml-1 text-gray-400 group-hover:text-gray-600"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-300 group-hover:text-gray-400"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'total_value', 'sort_order' => $sortBy == 'total_value' && $sortOrder == 'asc' ? 'desc' : 'asc']) }}" 
                           class="group inline-flex items-center hover:text-gray-900">
                            Total Nilai
                            @if($sortBy == 'total_value')
                                <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }} ml-1 text-gray-400 group-hover:text-gray-600"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-300 group-hover:text-gray-400"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($stockMovements as $movement)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $movement->transaction_date->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $movement->reference_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $movement->order_number ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $movement->invoice_number ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $movement->product->name ?? 'Produk Dihapus' }}</div>
                            <div class="text-sm text-gray-500">{{ $movement->product->code ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($movement->quantity) }} {{ $movement->product->unit ?? 'pcs' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($movement->unit_price ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($movement->quantity * ($movement->unit_price ?? 0), 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ Str::limit($movement->notes ?? '-', 30) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada history transaksi
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($stockMovements->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $stockMovements->links() }}
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Product Chart configuration
    const ctx = document.getElementById('customerProductChart').getContext('2d');
    const chartData = @json($chartData);
    
    const chart = new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Pembelian Produk per Bulan - {{ $customer->name }} ({{ $selectedYear }})',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Produk'
                    },
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Bulan'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            elements: {
                point: {
                    radius: 4,
                    hoverRadius: 6
                }
            }
        }
    });

    // Sales Trend Chart configuration
    const salesCtx = document.getElementById('salesTrendChart').getContext('2d');
    const salesTrendData = @json($salesTrendData);
    
    const salesChart = new Chart(salesCtx, {
        type: 'line',
        data: salesTrendData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Trend Penjualan Bulanan - {{ $customer->name }} ({{ $selectedYear }})',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Nilai Penjualan: Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Nilai Penjualan (Rp)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                notation: 'compact',
                                compactDisplay: 'short'
                            }).format(value);
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Bulan'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Year selection change handler for product chart
    document.getElementById('yearSelect').addEventListener('change', function() {
        const selectedYear = this.value;
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('year', selectedYear);
        window.location.href = currentUrl.toString();
    });

    // Year selection change handler for sales trend chart
    document.getElementById('salesYearSelect').addEventListener('change', function() {
        const selectedYear = this.value;
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('year', selectedYear);
        window.location.href = currentUrl.toString();
    });

    // Filter toggle functionality
    const toggleFiltersBtn = document.getElementById('toggleFilters');
    const filterSection = document.getElementById('filterSection');
    const filterToggleText = document.getElementById('filterToggleText');
    const filterChevron = document.getElementById('filterChevron');
    
    // Check if filters are active and show them by default
    const hasActiveFilters = {{ ($dateFrom || $dateTo || $productId || $perPage != 20) ? 'true' : 'false' }};
    
    if (hasActiveFilters) {
        filterSection.classList.remove('hidden');
        filterToggleText.textContent = 'Sembunyikan Filter';
        filterChevron.classList.remove('fa-chevron-down');
        filterChevron.classList.add('fa-chevron-up');
    }
    
    toggleFiltersBtn.addEventListener('click', function() {
        const isHidden = filterSection.classList.contains('hidden');
        
        if (isHidden) {
            filterSection.classList.remove('hidden');
            filterToggleText.textContent = 'Sembunyikan Filter';
            filterChevron.classList.remove('fa-chevron-down');
            filterChevron.classList.add('fa-chevron-up');
        } else {
            filterSection.classList.add('hidden');
            filterToggleText.textContent = 'Tampilkan Filter';
            filterChevron.classList.remove('fa-chevron-up');
            filterChevron.classList.add('fa-chevron-down');
        }
    });
    
    // Auto-submit form when per_page changes
    document.getElementById('per_page').addEventListener('change', function() {
        this.form.submit();
    });
});
</script>
@endsection
