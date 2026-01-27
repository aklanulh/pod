@extends('layouts.app')

@section('title', 'Laporan Customer')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Customer</h1>
            <p class="text-gray-600">Data customer dan statistik transaksi</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('reports.customer', ['is_active' => 1]) }}" 
               class="inline-flex items-center px-3 py-1 text-sm rounded-lg @if($isActive == 1) bg-green-600 text-white @else bg-gray-200 text-gray-700 hover:bg-gray-300 @endif">
                <i class="fas fa-user-check mr-1"></i> Aktif
            </a>
            <a href="{{ route('reports.customer', ['is_active' => 0]) }}" 
               class="inline-flex items-center px-3 py-1 text-sm rounded-lg @if($isActive == 0) bg-red-600 text-white @else bg-gray-200 text-gray-700 hover:bg-gray-300 @endif">
                <i class="fas fa-user-times mr-1"></i> Tidak Aktif
            </a>
            <a href="{{ route('reports.customer') }}" 
               class="inline-flex items-center px-3 py-1 text-sm rounded-lg @if($isActive === null) bg-blue-600 text-white @else bg-gray-200 text-gray-700 hover:bg-gray-300 @endif">
                <i class="fas fa-users mr-1"></i> Semua
            </a>
            <a href="{{ route('reports.export.customer') }}" 
               class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-3 py-1 text-sm rounded-lg transition-colors">
                <i class="fas fa-file-excel mr-1"></i>
                Export Excel
            </a>
            <a href="{{ route('reports.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 text-sm rounded-lg">
                <i class="fas fa-arrow-left mr-1"></i>Kembali
            </a>
        </div>
    </div>
    <div class="mt-2 text-sm text-gray-600">
        Menampilkan {{ $customers->count() }} dari {{ $totalCustomers }} customer total
        @if($isActive == 1)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <i class="fas fa-user-check mr-1"></i> Aktif ({{ $activeCustomers }})
            </span>
        @elseif($isActive == 0)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                <i class="fas fa-user-times mr-1"></i> Tidak Aktif ({{ $inactiveCustomers }})
            </span>
        @else
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                <i class="fas fa-users mr-1"></i> Semua ({{ $totalCustomers }})
            </span>
        @endif
    </div>
</div>

<!-- Customer Report Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Data Customer</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Transaksi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Nilai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($customers as $customer)
                    @php
                        $totalQuantity = $customer->stockMovements->where('type', 'out')->sum('quantity');
                        $totalValue = $customer->stockMovements->where('type', 'out')->sum(function($movement) {
                            return $movement->quantity * ($movement->unit_price ?? 0);
                        });
                    @endphp
                    @if($customer->stock_movements_count > 0)
                        <tr class="hover:bg-gray-100 cursor-pointer transition-colors" 
                            onclick="window.location.href='{{ route('reports.customer.detail', $customer->id) }}'">
                    @else
                        <tr class="opacity-75">
                    @endif
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                            <div class="text-sm text-gray-500">{{ $customer->address ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $customer->phone ?? '-' }}</div>
                            <div class="text-sm text-gray-500">{{ $customer->email ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $customer->stock_movements_count }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($totalQuantity) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($totalValue, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($customer->stock_movements_count > 0)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Belum Transaksi
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data customer
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
    @if($isActive == 1)
        <!-- Active Customer Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Customer Aktif</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $activeCustomers }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500">
                    <i class="fas fa-exchange-alt text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Transaksi Aktif</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $customers->sum('stock_movements_count') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                    <i class="fas fa-calculator text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Penjualan Aktif</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        Rp {{ number_format($customers->sum(function($customer) {
                            return $customer->stockMovements->where('type', 'out')->sum(function($movement) {
                                return $movement->quantity * ($movement->unit_price ?? 0);
                            });
                        }), 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Rata-rata Transaksi/Customer</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $activeCustomers > 0 ? round($customers->sum('stock_movements_count') / $activeCustomers, 1) : 0 }}
                    </p>
                </div>
            </div>
        </div>
    @elseif($isActive == 0)
        <!-- Inactive Customer Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-500">
                    <i class="fas fa-user-times text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Customer Tidak Aktif</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $inactiveCustomers }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gray-100 text-gray-500">
                    <i class="fas fa-history text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Transaksi (Historis)</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $customers->sum('stock_movements_count') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-500">
                    <i class="fas fa-calculator text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Penjualan (Historis)</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        Rp {{ number_format($customers->sum(function($customer) {
                            return $customer->stockMovements->where('type', 'out')->sum(function($movement) {
                                return $movement->quantity * ($movement->unit_price ?? 0);
                            });
                        }), 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Rata-rata Transaksi/Customer</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $customers->sum('stock_movements_count') > 0 ? round($customers->sum('stock_movements_count') / $customers->count(), 1) : 0 }}
                    </p>
                </div>
            </div>
        </div>
    @else
        <!-- All Customers Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Customer</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalCustomers }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Customer Aktif</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $activeCustomers }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ round(($activeCustomers / $totalCustomers) * 100, 1) }}% dari total</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-500">
                    <i class="fas fa-user-times text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Customer Tidak Aktif</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $inactiveCustomers }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ round(($inactiveCustomers / $totalCustomers) * 100, 1) }}% dari total</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                    <i class="fas fa-exchange-alt text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $customers->sum('stock_movements_count') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-500">
                    <i class="fas fa-calculator text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Penjualan</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        Rp {{ number_format($customers->sum(function($customer) {
                            return $customer->stockMovements->where('type', 'out')->sum(function($movement) {
                                return $movement->quantity * ($movement->unit_price ?? 0);
                            });
                        }), 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
</div>
@endsection
