@extends('layouts.app')

@section('title', 'Detail Produk - Admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
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
            </div>
        </div>

        <div class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kategori</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->category->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Satuan</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->unit }}</p>
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
                            @if($product->current_stock == 0)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Stok Habis
                                </span>
                            @elseif($product->current_stock <= $product->minimum_stock)
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
                                <p class="mt-1 text-sm text-gray-900">{{ $product->expired_date->format('d/m/Y') }}</p>
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
</div>
@endsection
