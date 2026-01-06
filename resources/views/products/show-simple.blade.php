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

    <!-- Error Notice -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">
                    Mode Sederhana
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Chart data tidak dapat dimuat. Menampilkan informasi produk dasar.</p>
                </div>
            </div>
        </div>
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

        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                            <dd class="text-sm text-gray-900">{{ $product->category->name ?? 'Tidak ada kategori' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Satuan</dt>
                            <dd class="text-sm text-gray-900">{{ $product->unit }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Harga</dt>
                            <dd class="text-sm text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</dd>
                        </div>
                        @if($product->lot_number)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Lot Number</dt>
                            <dd class="text-sm text-gray-900">{{ $product->lot_number }}</dd>
                        </div>
                        @endif
                        @if($product->expired_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Expired</dt>
                            <dd class="text-sm text-gray-900">{{ $product->expired_date->format('d/m/Y') }}</dd>
                        </div>
                        @endif
                        @if($product->distribution_permit)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">NIE</dt>
                            <dd class="text-sm text-gray-900">{{ $product->distribution_permit }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <!-- Stock Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Stok</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Stok Saat Ini</dt>
                            <dd class="text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $product->current_stock <= $product->minimum_stock ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $product->current_stock }} {{ $product->unit }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Stok Minimum</dt>
                            <dd class="text-sm text-gray-900">{{ $product->minimum_stock }} {{ $product->unit }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status Stok</dt>
                            <dd class="text-sm">
                                @if($product->current_stock <= 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Habis
                                    </span>
                                @elseif($product->current_stock <= $product->minimum_stock)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Stok Rendah
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Stok Aman
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if($product->description)
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Deskripsi</h3>
                <p class="text-sm text-gray-700">{{ $product->description }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex justify-end space-x-3">
        <a href="{{ route('products.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
            Kembali ke Daftar Produk
        </a>
        <button onclick="window.location.reload()" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-refresh mr-2"></i>
            Coba Lagi
        </button>
    </div>
</div>
@endsection
