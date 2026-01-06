@extends('layouts.app')

@section('title', 'Buat Stok Opname')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('stock.opname.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Buat Stok Opname Baru</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6" x-data="opnameForm()">
        <form action="{{ route('stock.opname.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="opname_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Opname</label>
                    <input type="date" name="opname_date" id="opname_date" 
                           value="{{ old('opname_date', now()->format('Y-m-d')) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('opname_date') border-red-500 @enderror">
                    @error('opname_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <input type="text" name="notes" id="notes" value="{{ old('notes') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pilih Produk untuk Opname</h3>
                
                <div class="mb-4">
                    <button type="button" @click="addAllProducts()" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm">
                        Tambah Semua Produk
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(product, index) in selectedProducts" :key="index">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-center">
                                <div>
                                    <select :name="`products[${index}][product_id]`" 
                                            x-model="product.product_id"
                                            @change="updateSystemStock(index)"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Pilih Produk</option>
                                        @foreach($products as $prod)
                                            <option value="{{ $prod->id }}" data-stock="{{ $prod->current_stock }}">
                                                {{ $prod->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Stok System</label>
                                    <input type="text" x-model="product.system_stock" readonly 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Stok Fisik</label>
                                    <input type="number" :name="`products[${index}][physical_stock]`" 
                                           x-model="product.physical_stock" min="0"
                                           @input="calculateDifference(index)"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Selisih</label>
                                    <input type="number" :name="`products[${index}][difference]`" 
                                           x-model="product.difference"
                                           @input="updatePhysicalFromDifference(index)"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           :class="product.difference > 0 ? 'text-green-600' : product.difference < 0 ? 'text-red-600' : 'text-gray-900'">
                                </div>

                                <div class="flex items-end">
                                    <button type="button" @click="removeProduct(index)" 
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-3">
                                <input type="text" :name="`products[${index}][notes]`" 
                                       x-model="product.notes" placeholder="Catatan untuk produk ini..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </template>

                    <div x-show="selectedProducts.length === 0" class="text-center py-8 text-gray-500">
                        Belum ada produk yang dipilih untuk opname
                    </div>
                </div>

                <div class="mt-4">
                    <button type="button" @click="addProduct()" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Produk
                    </button>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('stock.opname.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                        :disabled="selectedProducts.length === 0">
                    Simpan Opname
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function opnameForm() {
    return {
        selectedProducts: [],
        
        addProduct() {
            this.selectedProducts.push({
                product_id: '',
                system_stock: 0,
                physical_stock: 0,
                difference: 0,
                notes: ''
            });
        },
        
        removeProduct(index) {
            this.selectedProducts.splice(index, 1);
        },
        
        addAllProducts() {
            const products = @json($products);
            this.selectedProducts = [];
            
            products.forEach(product => {
                this.selectedProducts.push({
                    product_id: product.id.toString(),
                    system_stock: product.current_stock,
                    physical_stock: product.current_stock,
                    difference: 0,
                    notes: ''
                });
            });
        },
        
        updateSystemStock(index) {
            const productId = this.selectedProducts[index].product_id;
            const selectElement = document.querySelector(`select[name="products[${index}][product_id]"]`);
            const selectedOption = selectElement.querySelector(`option[value="${productId}"]`);
            
            if (selectedOption) {
                const stock = parseInt(selectedOption.getAttribute('data-stock')) || 0;
                this.selectedProducts[index].system_stock = stock;
                this.selectedProducts[index].physical_stock = stock;
                this.calculateDifference(index);
            }
        },
        
        calculateDifference(index) {
            const product = this.selectedProducts[index];
            product.difference = (product.physical_stock || 0) - (product.system_stock || 0);
        },
        
        updatePhysicalFromDifference(index) {
            const product = this.selectedProducts[index];
            product.physical_stock = (product.system_stock || 0) + (product.difference || 0);
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection
