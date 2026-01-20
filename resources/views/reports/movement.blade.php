@extends('layouts.app')

@section('title', 'Laporan Pergerakan Stok')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Pergerakan Stok</h1>
            <p class="text-gray-600">Riwayat transaksi masuk dan keluar</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('reports.movement', ['is_active' => 1]) }}" 
               class="inline-flex items-center px-3 py-1 text-sm rounded-lg @if($isActive == 1) bg-green-600 text-white @else bg-gray-200 text-gray-700 hover:bg-gray-300 @endif">
                <i class="fas fa-check-circle mr-1"></i> Aktif
            </a>
            <a href="{{ route('reports.movement', ['is_active' => 0]) }}" 
               class="inline-flex items-center px-3 py-1 text-sm rounded-lg @if($isActive == 0) bg-red-600 text-white @else bg-gray-200 text-gray-700 hover:bg-gray-300 @endif">
                <i class="fas fa-times-circle mr-1"></i> Tidak Aktif
            </a>
            <a href="{{ route('reports.movement') }}" 
               class="inline-flex items-center px-3 py-1 text-sm rounded-lg @if($isActive === null) bg-blue-600 text-white @else bg-gray-200 text-gray-700 hover:bg-gray-300 @endif">
                <i class="fas fa-exchange-alt mr-1"></i> Semua
            </a>
            <a href="{{ route('reports.export.movement', request()->query()) }}" 
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
        Menampilkan {{ $movements->count() }} dari {{ $totalMovements }} transaksi total
        @if($isActive == 1)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <i class="fas fa-check-circle mr-1"></i> Aktif ({{ $activeMovements }})
            </span>
        @elseif($isActive == 0)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                <i class="fas fa-times-circle mr-1"></i> Tidak Aktif ({{ $inactiveMovements }})
            </span>
        @else
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                <i class="fas fa-exchange-alt mr-1"></i> Semua ({{ $totalMovements }})
            </span>
        @endif
        @if($startDate && $endDate)
            untuk periode {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
        @endif
        @if($request->type)
            dengan filter jenis "{{ $request->type == 'in' ? 'Stok Masuk' : ($request->type == 'out' ? 'Stok Keluar' : 'Semua Transaksi') }}"
        @endif
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form method="GET" action="{{ route('reports.movement') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
            <input type="date" name="start_date" value="{{ request('start_date', $startDate ? $startDate->format('Y-m-d') : '') }}" 
                   class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="Semua periode">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
            <input type="date" name="end_date" value="{{ request('end_date', $endDate ? $endDate->format('Y-m-d') : '') }}" 
                   class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="Semua periode">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Transaksi</label>
            <select name="type" class="w-full border border-gray-300 rounded-md px-3 py-2">
                <option value="">Semua Transaksi</option>
                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stok Masuk</option>
                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stok Keluar</option>
                <option value="opname" {{ request('type') == 'opname' ? 'selected' : '' }}>Stock Opname</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tampilkan</label>
            <select name="limit" class="w-full border border-gray-300 rounded-md px-3 py-2">
                <option value="100" {{ request('limit', '100') == '100' ? 'selected' : '' }}>100 data</option>
                <option value="200" {{ request('limit') == '200' ? 'selected' : '' }}>200 data</option>
                <option value="500" {{ request('limit') == '500' ? 'selected' : '' }}>500 data</option>
                <option value="all" {{ request('limit') == 'all' ? 'selected' : '' }}>Semua data</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
        </div>
    </form>
</div>

<!-- Movement Report Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Data Pergerakan Stok</h3>
        <p class="text-sm text-gray-600">
    Periode: 
    @if($startDate && $endDate)
        {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
    @else
        Semua periode
    @endif
</p>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pemesanan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Invoice</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier/Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($movements as $movement)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $movement->transaction_date->format('d/m/Y') }}
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
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($movement->type == 'in')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-arrow-down mr-1"></i>Masuk
                                </span>
                            @elseif($movement->type == 'out')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-arrow-up mr-1"></i>Keluar
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <i class="fas fa-clipboard-check mr-1"></i>Opname
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($movement->quantity) }} {{ $movement->product->unit ?? 'pcs' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($movement->type == 'in' && $movement->supplier)
                                {{ $movement->supplier->name }}
                            @elseif($movement->type == 'out' && $movement->customer)
                                {{ $movement->customer->name }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($movement->unit_price)
                                Rp {{ number_format($movement->unit_price, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($movement->unit_price)
                                Rp {{ number_format($movement->quantity * $movement->unit_price, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data pergerakan stok pada periode ini
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination and Data Info -->
    <div class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
        <div class="text-sm text-gray-700">
            @if($limit === 'all')
                Menampilkan <span class="font-medium">{{ $movements->count() }}</span> dari <span class="font-medium">{{ $movements->count() }}</span> data (Semua data)
            @else
                Menampilkan <span class="font-medium">{{ $movements->firstItem() }}</span> - <span class="font-medium">{{ $movements->lastItem() }}</span> dari <span class="font-medium">{{ $movements->total() }}</span> data
            @endif
        </div>
        @if($limit !== 'all' && $movements->hasPages())
            <div class="flex items-center space-x-2">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mt-6">
    @if($isActive == 1)
        <!-- Active Movements Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                    <i class="fas fa-exchange-alt text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Transaksi Aktif</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $activeMovements }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500">
                    <i class="fas fa-arrow-down text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Stok Masuk (Aktif)</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($movements->where('type', 'in')->sum('quantity')) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-500">
                    <i class="fas fa-arrow-up text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Stok Keluar (Aktif)</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($movements->where('type', 'out')->sum('quantity')) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-emerald-100 text-emerald-600">
                    <i class="fas fa-plus-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Nilai Masuk (Aktif)</p>
                    <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($movements->where('type', 'in')->sum(function($movement) { return $movement->quantity * ($movement->unit_price ?? 0); }), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <i class="fas fa-minus-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Nilai Keluar (Aktif)</p>
                    <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($movements->where('type', 'out')->sum(function($movement) { return $movement->quantity * ($movement->unit_price ?? 0); }), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Rata-rata Transaksi/Hari</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        @php
                            $daysDiff = $startDate && $endDate ? $startDate->diffInDays($endDate) : 30;
                            $avgPerDay = $daysDiff > 0 ? $activeMovements / $daysDiff : 0;
                        @endphp
                        {{ number_format($avgPerDay, 1) }}
                    </p>
                </div>
            </div>
        </div>
    @elseif($isActive == 0)
        <!-- Inactive Movements Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gray-100 text-gray-500">
                    <i class="fas fa-exchange-alt text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Transaksi (Tidak Aktif)</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $inactiveMovements }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                    <i class="fas fa-arrow-down text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Stok Masuk (Historis)</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($movements->where('type', 'in')->sum('quantity')) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-500">
                    <i class="fas fa-arrow-up text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Stok Keluar (Historis)</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($movements->where('type', 'out')->sum('quantity')) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-emerald-100 text-emerald-600">
                    <i class="fas fa-plus-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Nilai Masuk (Historis)</p>
                    <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($movements->where('type', 'in')->sum(function($movement) { return $movement->quantity * ($movement->unit_price ?? 0); }), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <i class="fas fa-minus-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Nilai Keluar (Historis)</p>
                    <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($movements->where('type', 'out')->sum(function($movement) { return $movement->quantity * ($movement->unit_price ?? 0); }), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Rata-rata Transaksi/Hari</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        @php
                            $daysDiff = $startDate && $endDate ? $startDate->diffInDays($endDate) : 30;
                            $avgPerDay = $daysDiff > 0 ? $inactiveMovements / $daysDiff : 0;
                        @endphp
                        {{ number_format($avgPerDay, 1) }}
                    </p>
                </div>
            </div>
        </div>
    @else
        <!-- All Movements Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                    <i class="fas fa-exchange-alt text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalMovements }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ round(($activeMovements / $totalMovements) * 100, 1) }}% aktif, {{ round(($inactiveMovements / $totalMovements) * 100, 1) }}% tidak aktif</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500">
                    <i class="fas fa-arrow-down text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Stok Masuk</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($movements->where('type', 'in')->sum('quantity')) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-500">
                    <i class="fas fa-arrow-up text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Stok Keluar</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($movements->where('type', 'out')->sum('quantity')) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-emerald-100 text-emerald-600">
                    <i class="fas fa-plus-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Nilai Masuk</p>
                    <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($movements->where('type', 'in')->sum(function($movement) { return $movement->quantity * ($movement->unit_price ?? 0); }), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <i class="fas fa-minus-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Nilai Keluar</p>
                    <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($movements->where('type', 'out')->sum(function($movement) { return $movement->quantity * ($movement->unit_price ?? 0); }), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Rata-rata Transaksi/Hari</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        @php
                            $daysDiff = $startDate && $endDate ? $startDate->diffInDays($endDate) : 30;
                            $avgPerDay = $daysDiff > 0 ? $totalMovements / $daysDiff : 0;
                        @endphp
                        {{ number_format($avgPerDay, 1) }}
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
