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
            <a href="{{ route('reports.export.movement', request()->query()) }}" 
               class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-file-excel mr-2"></i>
                Export Excel
            </a>
            <a href="{{ route('reports.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form method="GET" action="{{ route('reports.movement') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
            <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" 
                   class="w-full border border-gray-300 rounded-md px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
            <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" 
                   class="w-full border border-gray-300 rounded-md px-3 py-2">
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
        <p class="text-sm text-gray-600">Periode: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
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
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mt-6">
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
            <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                <i class="fas fa-exchange-alt text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $movements->count() }}</p>
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
</div>
@endsection
