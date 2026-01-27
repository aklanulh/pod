@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Stok</h1>
            <p class="text-gray-600">Daftar stok produk saat ini</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('reports.stock', ['is_active' => 1]) }}" 
               class="inline-flex items-center px-3 py-1 text-sm rounded-lg @if($isActive == 1) bg-green-600 text-white @else bg-gray-200 text-gray-700 hover:bg-gray-300 @endif">
                <i class="fas fa-check-circle mr-1"></i> Aktif
            </a>
            <a href="{{ route('reports.stock', ['is_active' => 0]) }}" 
               class="inline-flex items-center px-3 py-1 text-sm rounded-lg @if($isActive == 0) bg-red-600 text-white @else bg-gray-200 text-gray-700 hover:bg-gray-300 @endif">
                <i class="fas fa-times-circle mr-1"></i> Tidak Aktif
            </a>
            <a href="{{ route('reports.stock') }}" 
               class="inline-flex items-center px-3 py-1 text-sm rounded-lg @if($isActive === null) bg-blue-600 text-white @else bg-gray-200 text-gray-700 hover:bg-gray-300 @endif">
                <i class="fas fa-boxes mr-1"></i> Semua
            </a>
            <a href="{{ route('reports.export.stock', request()->query()) }}" 
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
        Menampilkan {{ $products->count() }} dari {{ $totalProducts }} produk total
        @if($isActive == 1)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <i class="fas fa-check-circle mr-1"></i> Aktif ({{ $activeProducts }})
            </span>
        @elseif($isActive == 0)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                <i class="fas fa-times-circle mr-1"></i> Tidak Aktif ({{ $inactiveProducts }})
            </span>
        @else
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                <i class="fas fa-boxes mr-1"></i> Semua ({{ $totalProducts }})
            </span>
        @endif
        @if($request->category_id)
            untuk kategori "{{ \App\Models\ProductCategory::find($request->category_id)->name }}"
        @endif
        @if($request->low_stock)
            dengan filter stok menipis
        @endif
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form method="GET" action="{{ route('reports.stock') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
            <select name="category_id" class="w-full border border-gray-300 rounded-md px-3 py-2">
                <option value="">Semua Kategori</option>
                @foreach(\App\Models\ProductCategory::all() as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Filter Stok</label>
            <select name="low_stock" class="w-full border border-gray-300 rounded-md px-3 py-2">
                <option value="">Semua Produk</option>
                <option value="1" {{ request('low_stock') == '1' ? 'selected' : '' }}>Stok Menipis</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
        </div>
    </form>
</div>

<!-- Stock Report Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Data Stok Produk</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Saat Ini</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Minimum Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-100 cursor-pointer transition-colors" 
                        onclick="window.location.href='{{ route('reports.stock.detail', $product->id) }}'">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                            <div class="text-sm text-gray-500">{{ $product->code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $product->category->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($product->current_stock) }} {{ $product->unit }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($product->minimum_stock) }} {{ $product->unit }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->current_stock <= $product->minimum_stock)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Stok Menipis
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Stok Aman
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data produk
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
        <!-- Active Products Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                    <i class="fas fa-boxes text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Produk Aktif</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $activeProducts }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Stok Aman</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $products->filter(function($product) { return $product->current_stock > $product->minimum_stock; })->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Stok Menipis</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $products->filter(function($product) { return $product->current_stock <= $product->minimum_stock; })->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-500">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Stok Habis</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $products->where('current_stock', 0)->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-500">
                    <i class="fas fa-calculator text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Nilai Stok Aktif</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        Rp {{ number_format($products->sum(function($product) { 
                            return $product->current_stock * $product->price; 
                        }), 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    @elseif($isActive == 0)
        <!-- Inactive Products Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gray-100 text-gray-500">
                    <i class="fas fa-boxes text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Produk Tidak Aktif</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $inactiveProducts }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                    <i class="fas fa-info-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Status Stok (Tidak Aktif)</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        @php
                            $safeStock = $products->filter(function($product) { return $product->current_stock > 0; })->count();
                            $lowStock = $products->filter(function($product) { return $product->current_stock <= $product->minimum_stock; })->count();
                            $outOfStock = $products->filter(function($product) { return $product->current_stock == 0; })->count();
                        @endphp
                        {{ $safeStock }} Aman, {{ $lowStock }} Menipis, {{ $outOfStock }} Habis
                    @endphp
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                    <i class="fas fa-calculator text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Nilai Stok (Tidak Aktif)</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        Rp {{ number_format($products->sum(function($product) { 
                            return $product->current_stock * $product->price; 
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
                    <p class="text-sm font-medium text-gray-600">Rata-rata Nilai/Produk</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        Rp {{ $products->count() > 0 ? number_format($products->sum(function($product) { 
                            return $product->current_stock * $product->price; 
                        }) / $products->count(), 0, ',', '.') : 0 }}
                    </p>
                </div>
            </div>
        </div>
    @else
        <!-- All Products Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                    <i class="fas fa-boxes text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Produk</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalProducts }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ round(($activeProducts / $totalProducts) * 100, 1) }}% aktif, {{ round(($inactiveProducts / $totalProducts) * 100, 1) }}% tidak aktif</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Produk Aktif</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $activeProducts }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ round(($activeProducts / $totalProducts) * 100, 1) }}% dari total</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-500">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Produk Tidak Aktif</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $inactiveProducts }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ round(($inactiveProducts / $totalProducts) * 100, 1) }}% dari total</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Stok Menipis</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $products->filter(function($product) { return $product->current_stock <= $product->minimum_stock; })->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-500">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Stok Habis</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $products->where('current_stock', 0)->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-500">
                    <i class="fas fa-calculator text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Nilai Stok</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        Rp {{ number_format($products->sum(function($product) { 
                            return $product->current_stock * $product->price; 
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
                    <p class="text-sm font-medium text-gray-600">Rata-rata Nilai/Produk</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        Rp {{ $products->count() > 0 ? number_format($products->sum(function($product) { 
                            return $product->current_stock * $product->price; 
                        }) / $products->count(), 0, ',', '.') : 0 }}
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
