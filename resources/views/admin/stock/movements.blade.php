@extends('layouts.app')

@section('title', 'Pergerakan Stok - Admin')

@section('content')
<!-- Page Header -->
<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Pergerakan Stok</h1>
            <p class="text-gray-600">Riwayat lengkap pergerakan stok produk</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.stock.in.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors duration-200">
                <i class="fas fa-arrow-down mr-2"></i>
                Stok Masuk
            </a>
            <a href="{{ route('admin.stock.out.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition-colors duration-200">
                <i class="fas fa-arrow-up mr-2"></i>
                Stok Keluar
            </a>
        </div>
    </div>
</div>

<!-- Movements Table -->
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Riwayat Pergerakan Stok</h2>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referensi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Partner</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($movements as $movement)
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $movement->transaction_date->format('d/m/Y') }}
                        <div class="text-xs text-gray-500">{{ $movement->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $movement->reference_number }}</div>
                        @if($movement->order_number)
                            <div class="text-xs text-gray-500">Order: {{ $movement->order_number }}</div>
                        @endif
                        @if($movement->invoice_number)
                            <div class="text-xs text-gray-500">Invoice: {{ $movement->invoice_number }}</div>
                        @endif
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
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ number_format($movement->quantity) }} unit</div>
                        <div class="text-xs text-gray-500">
                            {{ number_format($movement->stock_before) }} → {{ number_format($movement->stock_after) }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($movement->supplier)
                            <div class="flex items-center">
                                <i class="fas fa-truck text-green-500 mr-2"></i>
                                {{ $movement->supplier->name }}
                            </div>
                        @elseif($movement->customer)
                            <div class="flex items-center">
                                <i class="fas fa-user text-blue-500 mr-2"></i>
                                {{ $movement->customer->name }}
                            </div>
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
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-4"></i>
                            <p class="text-sm">Belum ada pergerakan stok</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($movements->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $movements->links() }}
        </div>
    @endif
</div>
@endsection
