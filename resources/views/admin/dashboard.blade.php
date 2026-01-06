@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<!-- Quick Actions Bar -->
<div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-lg p-6 mb-8 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold mb-2">Dashboard Admin - PT. Mitrajaya Selaras Abadi</h2>
            <p class="text-blue-100">Kelola stok dan transaksi dengan mudah dan efisien</p>
        </div>
        <div class="flex space-x-3">
            <span class="bg-white bg-opacity-20 px-4 py-2 rounded-lg">{{ auth()->user()->name }}</span>
        </div>
    </div>
</div>

<!-- Main KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <!-- Total Products -->
    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Total Produk</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($totalProducts) }}</p>
                <p class="text-xs text-blue-600 mt-1">
                    <i class="fas fa-boxes mr-1"></i>Item Tersedia
                </p>
            </div>
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-boxes text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Critical Stock Alert -->
    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Stok Kritis</p>
                <p class="text-3xl font-bold text-red-600">{{ number_format($lowStockProducts) }}</p>
                <p class="text-xs text-red-600 mt-1">
                    <i class="fas fa-exclamation-triangle mr-1"></i>Perlu Restock
                </p>
            </div>
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Out of Stock -->
    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Stok Habis</p>
                <p class="text-3xl font-bold text-orange-600">{{ number_format($outOfStockProducts) }}</p>
                <p class="text-xs text-orange-600 mt-1">
                    <i class="fas fa-times-circle mr-1"></i>Produk
                </p>
            </div>
            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                <i class="fas fa-times-circle text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Suppliers -->
    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Total Distributor</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($totalSuppliers) }}</p>
                <p class="text-xs text-purple-600 mt-1">
                    <i class="fas fa-truck mr-1"></i>Partner
                </p>
            </div>
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-truck text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Customers -->
    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-indigo-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Total Customer</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($totalCustomers) }}</p>
                <p class="text-xs text-indigo-600 mt-1">
                    <i class="fas fa-users mr-1"></i>Klien
                </p>
            </div>
            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                <i class="fas fa-users text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Activity (No Financial Data) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium">Stok Masuk (1 Bulan)</p>
                <p class="text-3xl font-bold mt-2">{{ number_format($monthlyStockInSelected) }}</p>
                <p class="text-blue-100 text-sm mt-1">Unit Masuk</p>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-full">
                <i class="fas fa-arrow-down text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-100 text-sm font-medium">Stok Keluar (1 Bulan)</p>
                <p class="text-3xl font-bold mt-2">{{ number_format($monthlyStockOutSelected) }}</p>
                <p class="text-red-100 text-sm mt-1">Unit Keluar</p>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-full">
                <i class="fas fa-arrow-up text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium">Transaksi (1 Bulan)</p>
                <p class="text-3xl font-bold mt-2">{{ number_format($monthlyTransactionsSelected) }}</p>
                <p class="text-purple-100 text-sm mt-1">Total Aktivitas</p>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-full">
                <i class="fas fa-exchange-alt text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Stok Baru Masuk & Expiry Warnings -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Recent Stock In -->
    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-cyan-500">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-arrow-down text-cyan-600 mr-2"></i>
                    Stok Baru Masuk
                </h3>
                <p class="text-sm text-gray-600">30 hari terakhir</p>
            </div>
            <div class="p-3 rounded-full bg-cyan-100 text-cyan-600">
                <span class="text-2xl font-bold">{{ $recentStockIn->count() }}</span>
            </div>
        </div>
        <div class="max-h-48 overflow-y-auto">
            @forelse($recentStockIn as $stockIn)
                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">{{ $stockIn->product->name }}</p>
                        <p class="text-xs text-gray-500">{{ $stockIn->product->category->name ?? 'Tanpa Kategori' }}</p>
                        <p class="text-xs text-cyan-600">{{ $stockIn->transaction_date->diffForHumans() }}</p>
                        <p class="text-xs text-gray-500">Distributor: {{ $stockIn->supplier->name ?? 'N/A' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-cyan-600">+{{ number_format($stockIn->quantity) }}</p>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                            Masuk
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="fas fa-arrow-down text-gray-300 text-3xl mb-2"></i>
                    <p class="text-sm text-gray-500">Tidak ada stok masuk</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Products Expiring in 3 Months -->
    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                    Expired 3 Bulan
                </h3>
                <p class="text-sm text-gray-600">Peringatan dini</p>
            </div>
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <span class="text-2xl font-bold">{{ $expiring3Months->count() }}</span>
            </div>
        </div>
        <div class="max-h-48 overflow-y-auto">
            @forelse($expiring3Months as $product)
                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">{{ $product->name }}</p>
                        <p class="text-xs text-gray-500">{{ $product->category->name ?? 'Tanpa Kategori' }}</p>
                        <p class="text-xs text-yellow-600">{{ \Carbon\Carbon::parse($product->expired_date)->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ \Carbon\Carbon::parse($product->expired_date)->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="fas fa-check-circle text-gray-300 text-3xl mb-2"></i>
                    <p class="text-sm text-gray-500">Tidak ada produk mendekati expired</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Products Expiring in 2 Months -->
    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-red-500">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
                    Expired 2 Bulan
                </h3>
                <p class="text-sm text-gray-600">Peringatan kritis</p>
            </div>
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <span class="text-2xl font-bold">{{ $expiring2Months->count() }}</span>
            </div>
        </div>
        <div class="max-h-48 overflow-y-auto">
            @forelse($expiring2Months as $product)
                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">{{ $product->name }}</p>
                        <p class="text-xs text-gray-500">{{ $product->category->name ?? 'Tanpa Kategori' }}</p>
                        <p class="text-xs text-red-600">{{ \Carbon\Carbon::parse($product->expired_date)->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ \Carbon\Carbon::parse($product->expired_date)->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="fas fa-check-circle text-gray-300 text-3xl mb-2"></i>
                    <p class="text-sm text-gray-500">Tidak ada produk mendekati expired</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Stock Aging Analysis -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Fast Moving Products -->
    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-green-500">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-rocket text-green-600 mr-2"></i>
                    Fast Moving
                </h3>
                <p class="text-sm text-gray-600">Bergerak dalam 30 hari</p>
            </div>
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <span class="text-2xl font-bold">{{ $stockAging['fast_moving'] }}</span>
            </div>
        </div>
        <div class="max-h-48 overflow-y-auto">
            @forelse($stockAging['fast_moving_products'] as $product)
                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">{{ $product->name }}</p>
                        <p class="text-xs text-gray-500">{{ $product->category->name ?? 'Tanpa Kategori' }}</p>
                        <p class="text-xs text-green-600">Stok: {{ number_format($product->current_stock) }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Aktif
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="fas fa-chart-line text-gray-300 text-3xl mb-2"></i>
                    <p class="text-sm text-gray-500">Tidak ada produk fast moving</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Slow Moving Products -->
    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-clock text-yellow-600 mr-2"></i>
                    Slow Moving
                </h3>
                <p class="text-sm text-gray-600">Bergerak 30-90 hari</p>
            </div>
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <span class="text-2xl font-bold">{{ $stockAging['slow_moving'] }}</span>
            </div>
        </div>
        <div class="max-h-48 overflow-y-auto">
            @forelse($stockAging['slow_moving_products'] as $product)
                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">{{ $product->name }}</p>
                        <p class="text-xs text-gray-500">{{ $product->category->name ?? 'Tanpa Kategori' }}</p>
                        <p class="text-xs text-yellow-600">Stok: {{ number_format($product->current_stock) }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Lambat
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="fas fa-hourglass-half text-gray-300 text-3xl mb-2"></i>
                    <p class="text-sm text-gray-500">Tidak ada produk slow moving</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Dead Stock Products -->
    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-red-500">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-stop-circle text-red-600 mr-2"></i>
                    Dead Stock
                </h3>
                <p class="text-sm text-gray-600">Tidak bergerak >90 hari</p>
            </div>
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <span class="text-2xl font-bold">{{ $stockAging['dead_stock'] }}</span>
            </div>
        </div>
        <div class="max-h-48 overflow-y-auto">
            @forelse($stockAging['dead_stock_products'] as $product)
                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">{{ $product->name }}</p>
                        <p class="text-xs text-gray-500">{{ $product->category->name ?? 'Tanpa Kategori' }}</p>
                        <p class="text-xs text-red-600">Stok: {{ number_format($product->current_stock) }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Mati
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="fas fa-ban text-gray-300 text-3xl mb-2"></i>
                    <p class="text-sm text-gray-500">Tidak ada dead stock</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Monthly Summary & Business Metrics -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Monthly Summary -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-calendar-alt text-blue-600 mr-3"></i>
            Ringkasan Bulan {{ now()->format('F Y') }}
        </h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-500 rounded-lg text-white mr-4">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-blue-700">{{ number_format($monthlyStockIn) }}</div>
                        <div class="text-sm text-blue-600 font-medium">Total Stok Masuk</div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-red-50 to-red-100 rounded-lg border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="p-2 bg-red-500 rounded-lg text-white mr-4">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-red-700">{{ number_format($monthlyStockOut) }}</div>
                        <div class="text-sm text-red-600 font-medium">Total Stok Keluar</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Metrics -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-chart-bar text-purple-600 mr-3"></i>
            Metrik Bisnis
        </h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-truck text-purple-600 mr-3 text-xl"></i>
                    <span class="text-gray-700 font-medium">Distributor Aktif (30 hari)</span>
                </div>
                <span class="text-2xl font-bold text-purple-600">{{ $activeSuppliers }}</span>
            </div>
            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-indigo-50 to-indigo-100 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-users text-indigo-600 mr-3 text-xl"></i>
                    <span class="text-gray-700 font-medium">Customer Aktif (30 hari)</span>
                </div>
                <span class="text-2xl font-bold text-indigo-600">{{ $activeCustomers }}</span>
            </div>
            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-percentage text-yellow-600 mr-3 text-xl"></i>
                    <span class="text-gray-700 font-medium">Tingkat Stok Kritis</span>
                </div>
                <span class="text-2xl font-bold text-yellow-600">{{ $totalProducts > 0 ? number_format(($lowStockProducts / $totalProducts) * 100, 1) : 0 }}%</span>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Chart and Critical Stock Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Enhanced Stock Movement Chart -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-chart-area text-blue-600 mr-3"></i>
                Tren Pergerakan Stok (1 Bulan)
            </h3>
            <div class="flex items-center space-x-3">
                <!-- Month Selection Dropdown -->
                <div class="relative">
                    <select id="monthSelector" class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach($monthOptions as $option)
                            <option value="{{ $option['value'] }}" data-year="{{ $option['year'] }}" {{ $option['selected'] ? 'selected' : '' }}>
                                {{ $option['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Legend -->
                <div class="flex space-x-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div>
                        Masuk
                    </span>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <div class="w-2 h-2 bg-red-500 rounded-full mr-1"></div>
                        Keluar
                    </span>
                </div>
            </div>
        </div>
        <div style="height: 300px;">
            <canvas id="stockChart"></canvas>
        </div>
    </div>

    <!-- Enhanced Critical Stock Products -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-exclamation-triangle text-red-600 mr-3"></i>
                Alert Stok Kritis
            </h3>
            <a href="{{ route('reports.stock') }}?low_stock=1" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Semua</a>
        </div>
        <div class="space-y-3 max-h-80 overflow-y-auto">
            @forelse($criticalStockList as $product)
                <div class="flex items-center justify-between p-4 {{ $product->current_stock == 0 ? 'bg-red-50 border-l-4 border-red-500' : 'bg-orange-50 border-l-4 border-orange-400' }} rounded-lg shadow-sm">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900 text-sm">{{ $product->name }}</p>
                        <p class="text-xs text-gray-600 mt-1">
                            <i class="fas fa-tag mr-1"></i>{{ $product->category->name ?? 'Tanpa Kategori' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-barcode mr-1"></i>{{ $product->code }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold {{ $product->current_stock == 0 ? 'text-red-600' : 'text-orange-600' }}">
                            {{ $product->current_stock }}
                        </p>
                        <p class="text-xs text-gray-500">Min: {{ $product->minimum_stock }}</p>
                        @if($product->current_stock == 0)
                            <span class="inline-block px-2 py-1 text-xs bg-red-500 text-white rounded-full mt-1 font-medium">
                                <i class="fas fa-times mr-1"></i>HABIS
                            </span>
                        @else
                            <span class="inline-block px-2 py-1 text-xs bg-orange-500 text-white rounded-full mt-1 font-medium">
                                <i class="fas fa-exclamation mr-1"></i>KRITIS
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-3xl text-green-500"></i>
                    </div>
                    <p class="text-gray-600 font-medium">Semua produk memiliki stok yang aman</p>
                    <p class="text-gray-500 text-sm mt-1">Tidak ada produk dengan stok kritis</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Recent Stock Movements -->
<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900">Aktivitas Stok Terbaru</h3>
        <a href="{{ route('admin.stock.movements') }}" 
           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200">
            <i class="fas fa-list mr-1"></i>
            Lihat Semua
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Partner</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recentMovements as $movement)
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $movement->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $movement->product->name }}</div>
                        <div class="text-sm text-gray-500">{{ $movement->product->code }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($movement->type === 'in')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-arrow-down mr-1"></i>
                                Masuk
                            </span>
                        @elseif($movement->type === 'out')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-arrow-up mr-1"></i>
                                Keluar
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-clipboard-check mr-1"></i>
                                Opname
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ number_format($movement->quantity) }} unit
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($movement->supplier)
                            {{ $movement->supplier->name }}
                        @elseif($movement->customer)
                            {{ $movement->customer->name }}
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $movement->notes ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-4"></i>
                            <p class="text-sm">Belum ada aktivitas stok terbaru</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced Stock Movement Chart
    const ctx = document.getElementById('stockChart').getContext('2d');
    const chartData = @json($chartData ?? []);
    
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(item => item.period),
            datasets: [{
                label: 'Stok Masuk',
                data: chartData.map(item => item.stock_in || 0),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }, {
                label: 'Stok Keluar',
                data: chartData.map(item => item.stock_out || 0),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#ef4444',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxTicksLimit: 15,
                        color: '#6b7280'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        color: '#6b7280',
                        callback: function(value) {
                            return value + ' unit';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(0, 0, 0, 0.1)',
                    borderWidth: 1,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        title: function(context) {
                            const dataIndex = context[0].dataIndex;
                            return chartData[dataIndex].full_date;
                        },
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' unit';
                        }
                    }
                }
            }
        }
    });

    // Month selector functionality
    const monthSelector = document.getElementById('monthSelector');
    if (monthSelector) {
        monthSelector.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const month = selectedOption.value;
            const year = selectedOption.getAttribute('data-year');
            
            // Show loading state
            const chartContainer = document.querySelector('#stockChart').parentElement;
            chartContainer.style.opacity = '0.5';
            
            // Reload page with new parameters
            const url = new URL(window.location);
            url.searchParams.set('month', month);
            url.searchParams.set('year', year);
            window.location.href = url.toString();
        });
    }
});
</script>
@endsection
