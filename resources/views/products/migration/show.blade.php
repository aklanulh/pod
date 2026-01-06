@extends('layouts.app')

@section('title', 'Product Migration Detail')

@section('content')
<div class="container-fluid p-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Detail Migration Produk</h1>
            <p class="text-gray-600">History migrasi dan stock movement produk</p>
        </div>
        <div>
            <a href="{{ route('products.migration.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Product Info -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Main Product Info -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Informasi Produk</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kode Produk</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->code }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <p class="mt-1">
                            @if($product->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Migrated
                                </span>
                            @endif
                        </p>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Nama Produk</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->name }}</p>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Kategori</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->category->name ?? '-' }}</p>
                    </div>
                    @if($product->migration_notes)
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Catatan Migrasi</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->migration_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Migration Info -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Informasi Migrasi</h3>
            </div>
            <div class="p-6">
                @if($product->migratedToProduct)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Dimigrasikan Ke</label>
                        <div class="mt-1 p-3 bg-green-50 rounded-md">
                            <div class="font-medium text-green-800">{{ $product->migratedToProduct->name }}</div>
                            <div class="text-sm text-green-600">{{ $product->migratedToProduct->code }}</div>
                        </div>
                    </div>
                @endif
                
                @if($product->migratedFromProducts->count() > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Produk Yang Dimigrasikan Ke Sini</label>
                        <div class="space-y-2">
                            @foreach($product->migratedFromProducts as $migratedProduct)
                                <div class="p-3 bg-yellow-50 rounded-md">
                                    <div class="font-medium text-yellow-800">{{ $migratedProduct->name }}</div>
                                    <div class="text-sm text-yellow-600">{{ $migratedProduct->code }}</div>
                                    @if($migratedProduct->migration_notes)
                                        <div class="text-xs text-gray-500 mt-1">{{ Str::limit($migratedProduct->migration_notes, 100) }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">Tidak ada produk yang dimigrasikan ke produk ini.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Stock Movements -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">History Stock Movement (10 Terakhir)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier/Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ref</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($product->stockMovements as $movement)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $movement->transaction_date->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($movement->type === 'in')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        IN
                                    </span>
                                @elseif($movement->type === 'out')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        OUT
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        OPNAME
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $movement->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($movement->unit_price)
                                    Rp. {{ number_format($movement->unit_price, 2, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($movement->type === 'in' && $movement->supplier)
                                    {{ $movement->supplier->name }}
                                @elseif($movement->type === 'out' && $movement->customer)
                                    {{ $movement->customer->name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $movement->reference_number }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada history stock movement
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
