@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Detail Produk</h1>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $product->name }}</h2>
                    <p class="text-sm text-gray-600">Kode: {{ $product->code }}</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('products.edit', $product) }}" 
                       class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
                    </a>
                </div>
            </div>
        </div>

        <div class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kategori</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->category->name ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Satuan</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->unit }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Harga</label>
                        <p class="mt-1 text-sm text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Stok Saat Ini</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->current_stock }} {{ $product->unit }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Stok Minimum</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->minimum_stock }} {{ $product->unit }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status Stok</label>
                        <div class="mt-1">
                            @if($product->isLowStock())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Stok Menipis
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Normal
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($product->description)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $product->description }}</p>
                </div>
            @endif

            <!-- Regulatory Information -->
            @if($product->lot_number || $product->expired_date || $product->distribution_permit)
                <div class="mt-8 border-t pt-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Informasi Regulasi & Kedaluwarsa</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Lot</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->lot_number ?? '-' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Kedaluwarsa</label>
                            @if($product->expired_date)
                                <div class="mt-1">
                                    <p class="text-sm {{ $product->isExpired() ? 'text-red-600 font-semibold' : ($product->isExpiringSoon() ? 'text-yellow-600 font-medium' : 'text-gray-900') }}">
                                        {{ $product->expired_date->format('d/m/Y') }}
                                        @if($product->isExpired())
                                            <i class="fas fa-exclamation-triangle ml-1 text-red-600"></i>
                                        @elseif($product->isExpiringSoon())
                                            <i class="fas fa-clock ml-1 text-yellow-600"></i>
                                        @endif
                                    </p>
                                    @if($product->isExpired())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                                            Kedaluwarsa
                                        </span>
                                    @elseif($product->isExpiringSoon())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                            Akan Kedaluwarsa
                                        </span>
                                    @endif
                                </div>
                            @else
                                <p class="mt-1 text-sm text-gray-900">-</p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Izin Edar</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->distribution_permit ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Simple Stock Movements -->
    @if($product->stockMovements && $product->stockMovements->count() > 0)
        <div class="mt-8 bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Pergerakan Stok</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Partner</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($product->stockMovements->sortByDesc('transaction_date')->take(10) as $movement)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $movement->transaction_date->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($movement->type === 'in')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Masuk
                                        </span>
                                    @elseif($movement->type === 'out')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Keluar
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Opname
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $movement->quantity }} {{ $product->unit }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($movement->supplier)
                                        {{ $movement->supplier->name }}
                                    @elseif($movement->customer)
                                        {{ $movement->customer->name }}
                                    @else
                                        {{ $movement->supplier_name ?? $movement->customer_name ?? '-' }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Notice about simplified view -->
    <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Tampilan Sederhana</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Grafik pembelian customer tidak tersedia karena terjadi kesalahan saat memuat data. Silakan hubungi administrator untuk memeriksa error logs.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
