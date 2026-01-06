@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Tambah Produk Baru</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center mb-6">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-box text-blue-600 text-sm"></i>
                </div>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-semibold text-gray-900">Tambah Produk Baru</h3>
                <p class="text-sm text-gray-500">Lengkapi informasi produk yang akan ditambahkan</p>
            </div>
        </div>

        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label for="code" class="block text-sm font-medium text-gray-700">
                        Kode Produk <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required
                           placeholder="Contoh: PRD001"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('code') border-red-500 @enderror">
                    @error('code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label for="category_id" class="block text-sm font-medium text-gray-700">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" id="category_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('category_id') border-red-500 @enderror">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 space-y-1">
                <label for="name" class="block text-sm font-medium text-gray-700">
                    Nama Produk <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       placeholder="Contoh: Reagent DS Diluent"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 space-y-1">
                <label for="description" class="block text-sm font-medium text-gray-700">
                    Deskripsi
                </label>
                <textarea name="description" id="description" rows="3" 
                          placeholder="Deskripsi produk (opsional)"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('description') }}</textarea>
            </div>

            <!-- Regulatory Information -->
            <div class="mt-8 border-t pt-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Informasi Regulasi & Kedaluwarsa</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <label for="lot_number" class="block text-sm font-medium text-gray-700">
                            Nomor Lot
                        </label>
                        <input type="text" name="lot_number" id="lot_number" value="{{ old('lot_number') }}"
                               placeholder="Contoh: LOT2024001"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('lot_number') border-red-500 @enderror">
                        @error('lot_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="expired_date" class="block text-sm font-medium text-gray-700">
                            Tanggal Kedaluwarsa
                        </label>
                        <input type="date" name="expired_date" id="expired_date" value="{{ old('expired_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('expired_date') border-red-500 @enderror">
                        @error('expired_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="distribution_permit" class="block text-sm font-medium text-gray-700">
                            Nomor Izin Edar
                        </label>
                        <input type="text" name="distribution_permit" id="distribution_permit" value="{{ old('distribution_permit') }}"
                               placeholder="Contoh: AKL20123456789"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('distribution_permit') border-red-500 @enderror">
                        @error('distribution_permit')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div class="space-y-1">
                    <label for="unit" class="block text-sm font-medium text-gray-700">
                        Satuan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="unit" id="unit" value="{{ old('unit') }}" required
                           placeholder="pcs, box, ml, kg, dll"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('unit') border-red-500 @enderror">
                    @error('unit')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label for="price" class="block text-sm font-medium text-gray-700">
                        Harga Satuan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="price" id="price" value="{{ old('price') }}" min="0" step="0.01" required
                               placeholder="0"
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('price') border-red-500 @enderror">
                    </div>
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label for="minimum_stock" class="block text-sm font-medium text-gray-700">
                        Minimal Stok
                    </label>
                    <input type="number" name="minimum_stock" id="minimum_stock" value="{{ old('minimum_stock', 0) }}" min="0"
                           placeholder="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('minimum_stock') border-red-500 @enderror">
                    <p class="text-xs text-gray-500">Stok minimum untuk peringatan stok habis</p>
                    @error('minimum_stock')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('products.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
