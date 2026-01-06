@extends('layouts.app')

@section('title', 'Dashboard - PT. Mitrajaya Selaras Abadi')

@section('content')
<!-- Quick Actions Bar -->
<div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-lg p-6 mb-8 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold mb-2">Selamat Datang di Sistem Inventory MSA</h2>
            <p class="text-blue-100">Kelola stok dan transaksi dengan mudah dan efisien</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('stock.in.create') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-2 rounded-lg transition-all duration-200 flex items-center">
                <i class="fas fa-plus mr-2"></i> Stok Masuk
            </a>
            <a href="{{ route('stock.out.create') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-2 rounded-lg transition-all duration-200 flex items-center">
                <i class="fas fa-minus mr-2"></i> Stok Keluar
            </a>
            <a href="{{ route('products.create') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-2 rounded-lg transition-all duration-200 flex items-center">
                <i class="fas fa-box mr-2"></i> Produk Baru
            </a>
        </div>
    </div>
</div>

<!-- Main KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6 mb-8">
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

    <!-- Inventory Value -->
    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Nilai Inventori</p>
                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalInventoryValue, 0, ',', '.') }}</p>
                <p class="text-xs text-green-600 mt-1">
                    <i class="fas fa-chart-line mr-1"></i>Total Aset
                </p>
            </div>
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-money-bill-wave text-2xl"></i>
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

<!-- Monthly Activity -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-chart-line text-blue-600 mr-3"></i>
            Aktivitas Bulanan
        </h3>
        <div class="flex items-center space-x-3">
            <!-- Month Selection Dropdown for Activity Cards -->
            <div class="relative">
                <select id="activityMonthSelector" class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach($monthOptions as $option)
                        <option value="{{ $option['value'] }}" data-year="{{ $option['year'] }}" {{ $option['selected'] ? 'selected' : '' }}>
                            {{ $option['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Stok Masuk</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($monthlyStockInSelected) }}</p>
                    <p class="text-blue-100 text-sm mt-1">Unit Masuk</p>
                    <p class="text-blue-200 text-xs mt-2 flex items-center">
                        <i class="fas fa-list-alt mr-1"></i>
                        {{ number_format($monthlyStockInTransactionsSelected) }} Transaksi
                    </p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <i class="fas fa-arrow-down text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Stok Keluar</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($monthlyStockOutSelected) }}</p>
                    <p class="text-red-100 text-sm mt-1">Unit Keluar</p>
                    <p class="text-red-200 text-xs mt-2 flex items-center">
                        <i class="fas fa-list-alt mr-1"></i>
                        {{ number_format($monthlyStockOutTransactionsSelected) }} Transaksi
                    </p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <i class="fas fa-arrow-up text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Penjualan</p>
                    <p class="text-2xl font-bold mt-2">Rp {{ number_format($monthlyRevenueSelected, 0, ',', '.') }}</p>
                    <p class="text-green-100 text-sm mt-1">Penjualan</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Belanja</p>
                    <p class="text-2xl font-bold mt-2">Rp {{ number_format($monthlyPurchaseSelected, 0, ',', '.') }}</p>
                    <p class="text-purple-100 text-sm mt-1">Pembelian</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <i class="fas fa-shopping-cart text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customer Schedule Alerts -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-calendar-check text-purple-600 mr-3"></i>
            Jadwal & Alert Customer
        </h3>
        <div class="flex space-x-2">
            <a href="{{ route('customer-schedules.create') }}" class="text-sm bg-purple-600 text-white px-3 py-2 rounded-lg hover:bg-purple-700 font-medium">
                <i class="fas fa-plus mr-1"></i>Tambah Jadwal
            </a>
            <a href="{{ route('customer-schedules.index') }}" class="text-sm text-purple-600 hover:text-purple-800 font-medium">
                Lihat Semua
            </a>
        </div>
    </div>

    <!-- Schedule Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-lg p-4 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-red-700">Terlambat</p>
                    <p class="text-2xl font-bold text-red-600">{{ $scheduleStats['overdue_count'] }}</p>
                </div>
                <div class="p-2 bg-red-200 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg p-4 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-yellow-700">Hari Ini</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $scheduleStats['due_today_count'] }}</p>
                </div>
                <div class="p-2 bg-yellow-200 rounded-full">
                    <i class="fas fa-bell text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-700">Minggu Ini</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $scheduleStats['due_this_week_count'] }}</p>
                </div>
                <div class="p-2 bg-blue-200 rounded-full">
                    <i class="fas fa-calendar-week text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg p-4 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-700">Total Pending</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $scheduleStats['total_pending'] }}</p>
                </div>
                <div class="p-2 bg-purple-200 rounded-full">
                    <i class="fas fa-clock text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Alerts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Overdue Schedules -->
        <div class="bg-red-50 rounded-lg p-4 border border-red-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-red-800 flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                    Terlambat
                </h4>
                <span class="text-xs bg-red-200 text-red-800 px-2 py-1 rounded-full">{{ $customerSchedules['overdue']->count() }}</span>
            </div>
            <div class="space-y-2 max-h-48 overflow-y-auto">
                @forelse($customerSchedules['overdue'] as $schedule)
                    <div class="bg-white rounded-lg p-3 border border-red-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">{{ $schedule->customer->name }}</p>
                                <p class="text-xs text-gray-600">{{ $schedule->product->name }}</p>
                                <p class="text-xs text-red-600 mt-1">
                                    <i class="fas fa-calendar mr-1"></i>{{ $schedule->scheduled_date->format('d/m/Y') }}
                                </p>
                            </div>
                            <div class="flex space-x-1">
                                <a href="{{ route('reports.customer.detail', $schedule->customer_id) }}" class="text-xs text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('customer-schedules.notify', $schedule) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-green-600 hover:text-green-800" title="Notify">
                                        <i class="fas fa-bell"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                        <p class="text-xs text-gray-500">Tidak ada jadwal terlambat</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Due Today -->
        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-yellow-800 flex items-center">
                    <i class="fas fa-bell text-yellow-600 mr-2"></i>
                    Hari Ini
                </h4>
                <span class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">{{ $customerSchedules['due_today']->count() }}</span>
            </div>
            <div class="space-y-2 max-h-48 overflow-y-auto">
                @forelse($customerSchedules['due_today'] as $schedule)
                    <div class="bg-white rounded-lg p-3 border border-yellow-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">{{ $schedule->customer->name }}</p>
                                <p class="text-xs text-gray-600">{{ $schedule->product->name }}</p>
                                <p class="text-xs text-yellow-600 mt-1">
                                    <i class="fas fa-calendar mr-1"></i>{{ $schedule->scheduled_date->format('d/m/Y') }}
                                </p>
                            </div>
                            <div class="flex space-x-1">
                                <a href="{{ route('reports.customer.detail', $schedule->customer_id) }}" class="text-xs text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('customer-schedules.notify', $schedule) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-green-600 hover:text-green-800" title="Notify">
                                        <i class="fas fa-bell"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-check text-green-500 text-2xl mb-2"></i>
                        <p class="text-xs text-gray-500">Tidak ada jadwal hari ini</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Due This Week -->
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-blue-800 flex items-center">
                    <i class="fas fa-calendar-week text-blue-600 mr-2"></i>
                    Minggu Ini
                </h4>
                <span class="text-xs bg-blue-200 text-blue-800 px-2 py-1 rounded-full">{{ $customerSchedules['due_this_week']->count() }}</span>
            </div>
            <div class="space-y-2 max-h-48 overflow-y-auto">
                @forelse($customerSchedules['due_this_week'] as $schedule)
                    <div class="bg-white rounded-lg p-3 border border-blue-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">{{ $schedule->customer->name }}</p>
                                <p class="text-xs text-gray-600">{{ $schedule->product->name }}</p>
                                <p class="text-xs text-blue-600 mt-1">
                                    <i class="fas fa-calendar mr-1"></i>{{ $schedule->scheduled_date->format('d/m/Y') }}
                                </p>
                            </div>
                            <div class="flex space-x-1">
                                <a href="{{ route('reports.customer.detail', $schedule->customer_id) }}" class="text-xs text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('customer-schedules.notify', $schedule) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-green-600 hover:text-green-800" title="Notify">
                                        <i class="fas fa-bell"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-check text-green-500 text-2xl mb-2"></i>
                        <p class="text-xs text-gray-500">Tidak ada jadwal minggu ini</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pending Schedules -->
        <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-purple-800 flex items-center">
                    <i class="fas fa-clock text-purple-600 mr-2"></i>
                    Pending
                </h4>
                <span class="text-xs bg-purple-200 text-purple-800 px-2 py-1 rounded-full">{{ $customerSchedules['pending']->count() }}</span>
            </div>
            <div class="space-y-2 max-h-48 overflow-y-auto">
                @forelse($customerSchedules['pending'] as $schedule)
                    <div class="bg-white rounded-lg p-3 border border-purple-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">{{ $schedule->customer->name }}</p>
                                <p class="text-xs text-gray-600">{{ $schedule->product->name }}</p>
                                <p class="text-xs text-purple-600 mt-1">
                                    <i class="fas fa-calendar mr-1"></i>{{ $schedule->scheduled_date->format('d/m/Y') }}
                                </p>
                            </div>
                            <div class="flex space-x-1">
                                <a href="{{ route('reports.customer.detail', $schedule->customer_id) }}" class="text-xs text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('customer-schedules.notify', $schedule) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-green-600 hover:text-green-800" title="Notify">
                                        <i class="fas fa-bell"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                        <p class="text-xs text-gray-500">Tidak ada jadwal pending</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

<!-- New Product Alerts & Expiry Warnings -->
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
            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-lg border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-2 bg-green-500 rounded-lg text-white mr-4">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-green-700">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</div>
                        <div class="text-sm text-green-600 font-medium">Total Revenue</div>
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
            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-teal-50 to-teal-100 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-sync-alt text-teal-600 mr-3 text-xl"></i>
                    <span class="text-gray-700 font-medium">Stock Turnover Rate</span>
                </div>
                <span class="text-2xl font-bold text-teal-600">{{ number_format($stockTurnover, 1) }}x</span>
            </div>
        </div>
    </div>
</div>


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

<!-- Top Categories -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-layer-group text-purple-600 mr-3"></i>
            Kategori Teratas (Nilai Stok)
        </h3>
        <a href="{{ route('products.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Produk</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($topCategories as $index => $category)
            <div class="p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg border border-gray-200 hover:shadow-md transition-all duration-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">
                            {{ $index + 1 }}
                        </div>
                        <h4 class="font-semibold text-gray-900 text-sm">{{ $category['name'] }}</h4>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-600">Nilai Total:</span>
                        <span class="font-bold text-green-600 text-sm">Rp {{ number_format($category['total_value'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-600">Produk:</span>
                        <span class="font-medium text-gray-700 text-sm">{{ $category['product_count'] }} item</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-600">Total Stok:</span>
                        <span class="font-medium text-blue-600 text-sm">{{ number_format($category['total_stock']) }} unit</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-8">
                <i class="fas fa-layer-group text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">Belum ada data kategori</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Recent Stock Movements -->
<div class="bg-white rounded-xl shadow-lg">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-history text-blue-600 mr-3"></i>
            Aktivitas Terbaru
        </h3>
        <a href="{{ route('reports.movement') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Waktu</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Aktivitas</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Quantity</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Partner</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nilai</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recentMovements as $movement)
                    <tr class="hover:bg-blue-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="font-medium">{{ $movement->transaction_date->format('d/m H:i') }}</div>
                            <div class="text-xs text-blue-600">{{ $movement->transaction_date->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">{{ $movement->product->name ?? 'Produk Dihapus' }}</div>
                            <div class="text-xs text-gray-500 flex items-center">
                                <i class="fas fa-barcode mr-1"></i>{{ $movement->product->code ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($movement->type === 'in')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                    <i class="fas fa-arrow-down mr-1"></i> Stok Masuk
                                </span>
                            @elseif($movement->type === 'out')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                    <i class="fas fa-arrow-up mr-1"></i> Stok Keluar
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                    <i class="fas fa-clipboard-check mr-1"></i> Stock Opname
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                            <div class="flex items-center">
                                <span class="text-lg">{{ number_format($movement->quantity) }}</span>
                                <span class="text-xs text-gray-500 ml-1">{{ $movement->product->unit ?? 'pcs' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($movement->type === 'in' && $movement->supplier)
                                <div class="flex items-center">
                                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-2">
                                        <i class="fas fa-truck text-green-600 text-xs"></i>
                                    </div>
                                    <span class="font-medium">{{ Str::limit($movement->supplier->name, 15) }}</span>
                                </div>
                            @elseif($movement->type === 'out' && $movement->customer)
                                <div class="flex items-center">
                                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                        <i class="fas fa-user text-blue-600 text-xs"></i>
                                    </div>
                                    <span class="font-medium">{{ Str::limit($movement->customer->name, 15) }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 italic">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($movement->unit_price)
                                <div class="font-bold text-green-600">Rp {{ number_format($movement->quantity * $movement->unit_price, 0, ',', '.') }}</div>
                                <div class="text-xs text-gray-500">@ Rp {{ number_format($movement->unit_price, 0, ',', '.') }}</div>
                            @else
                                <span class="text-gray-400 italic">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-inbox text-3xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 font-medium">Belum ada aktivitas stok</p>
                            <p class="text-gray-400 text-sm mt-1">Transaksi akan muncul di sini</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('stockChart').getContext('2d');
    const chartData = @json($chartData);
    
    // Create gradient backgrounds
    const gradient1 = ctx.createLinearGradient(0, 0, 0, 300);
    gradient1.addColorStop(0, 'rgba(34, 197, 94, 0.8)');
    gradient1.addColorStop(1, 'rgba(34, 197, 94, 0.2)');
    
    const gradient2 = ctx.createLinearGradient(0, 0, 0, 300);
    gradient2.addColorStop(0, 'rgba(239, 68, 68, 0.8)');
    gradient2.addColorStop(1, 'rgba(239, 68, 68, 0.2)');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(item => item.period),
            datasets: [{
                label: 'Stok Masuk',
                data: chartData.map(item => item.stock_in),
                backgroundColor: gradient1,
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(34, 197, 94)',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }, {
                label: 'Stok Keluar',
                data: chartData.map(item => item.stock_out),
                backgroundColor: gradient2,
                borderColor: 'rgb(239, 68, 68)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(239, 68, 68)',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: false
                },
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: 'rgba(255, 255, 255, 0.2)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        title: function(context) {
                            return 'Tanggal: ' + context[0].label;
                        },
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString('id-ID') + ' unit';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#6B7280',
                        font: {
                            size: 11
                        },
                        maxTicksLimit: 15
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#6B7280',
                        font: {
                            size: 12
                        },
                        callback: function(value) {
                            return value.toLocaleString('id-ID');
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        color: '#6B7280',
                        font: {
                            size: 12,
                            weight: 'bold'
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            elements: {
                point: {
                    hoverBorderWidth: 3
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart'
            }
        }
    });

    // Month selector change handler for chart
    document.getElementById('monthSelector').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const month = selectedOption.value;
        const year = selectedOption.dataset.year;
        
        // Show loading state
        const canvas = document.getElementById('stockChart');
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.font = '16px Arial';
        ctx.fillStyle = '#6B7280';
        ctx.textAlign = 'center';
        ctx.fillText('Memuat data...', canvas.width / 2, canvas.height / 2);
        
        // Reload page with selected month and year
        const url = new URL(window.location);
        url.searchParams.set('month', month);
        url.searchParams.set('year', year);
        window.location.href = url.toString();
    });

    // Activity month selector change handler
    document.getElementById('activityMonthSelector').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const month = selectedOption.value;
        const year = selectedOption.dataset.year;
        
        // Show loading state on activity cards
        const activityCards = document.querySelectorAll('.bg-gradient-to-br');
        activityCards.forEach(card => {
            const valueElement = card.querySelector('.text-3xl, .text-2xl');
            if (valueElement) {
                valueElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }
        });
        
        // Reload page with selected month and year
        const url = new URL(window.location);
        url.searchParams.set('month', month);
        url.searchParams.set('year', year);
        window.location.href = url.toString();
    });

    // Toggle product list function for stock aging analysis
    window.toggleProductList = function(category) {
        const list = document.getElementById(category + '-list');
        const icon = document.getElementById(category + '-icon');
        const btnText = document.getElementById(category + '-btn-text');
        
        if (list.classList.contains('hidden')) {
            list.classList.remove('hidden');
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
            btnText.textContent = 'Sembunyikan';
        } else {
            list.classList.add('hidden');
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
            btnText.textContent = 'Lihat Produk';
        }
    };
});
</script>
@endsection
