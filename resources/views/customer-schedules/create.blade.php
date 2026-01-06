@extends('layouts.app')

@section('title', 'Tambah Jadwal Customer - PT. Mitrajaya Selaras Abadi')

@section('content')
<div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-lg p-6 mb-8 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold mb-2">Tambah Jadwal Customer</h2>
            <p class="text-blue-100">Buat jadwal pembelian dan reminder untuk customer</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('customer-schedules.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-2 rounded-lg transition-all duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-lg p-8" x-data="scheduleForm()">
    <form action="{{ route('customer-schedules.store') }}" method="POST">
        @csrf
        
        <!-- Step 1: Customer Selection -->
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">1</span>
                Pilih Customer
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                    <select name="customer_id" id="customer_id" x-model="selectedCustomer" @change="loadCustomerPurchases()" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Pilih Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Step 2: Product Selection (Based on Last Purchases) -->
        <div class="mb-8" x-show="lastPurchases.length > 0">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">2</span>
                Pilih Produk (Berdasarkan Pembelian Terakhir)
            </h3>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <p class="text-sm text-gray-600 mb-3">Produk yang pernah dibeli customer:</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <template x-for="purchase in lastPurchases" :key="purchase.product_id">
                        <div class="bg-white rounded-lg p-3 border border-gray-200 cursor-pointer hover:border-blue-500 transition-colors"
                             :class="selectedProduct && selectedProduct.id == purchase.product_id ? 'border-blue-500 bg-blue-50' : ''"
                             @click="selectProduct(purchase)">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="font-semibold text-sm text-gray-900" x-text="purchase.product.name"></p>
                                    <p class="text-xs text-gray-500" x-text="purchase.product.code"></p>
                                    <p class="text-xs text-blue-600" x-text="purchase.product.category ? purchase.product.category.name : 'Tanpa Kategori'"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">Pembelian Terakhir:</p>
                                    <p class="text-xs font-medium text-gray-700" x-text="formatDate(purchase.last_purchase_date)"></p>
                                    <p class="text-xs font-bold text-purple-600" x-text="formatNumber(purchase.total_quantity) + ' ' + (purchase.product.unit || 'pcs')"></p>
                                    <p class="text-xs text-green-600" x-text="'Rp ' + formatNumber(purchase.avg_price)"></p>
                                </div>
                            </div>
                        </div>
                    </template>
            </div>

            <input type="hidden" name="product_id" x-model="selectedProduct ? selectedProduct.id : ''">
            @error('product_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Step 3: Schedule Details -->
        <div class="mb-8" :class="selectedProduct ? '' : 'hidden'">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">3</span>
                Detail Jadwal
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Jadwal</label>
                    <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date') }}" min="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    @error('scheduled_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>



                <div class="flex items-center">
                    <input type="checkbox" name="is_recurring" id="is_recurring" value="1" {{ old('is_recurring') ? 'checked' : '' }} class="rounded border-gray-300 mr-2" x-model="isRecurring">
                    <label for="is_recurring" class="text-sm font-medium text-gray-700">Jadwal Berulang</label>
                </div>
            </div>

            <div class="mt-4" x-show="isRecurring">
                <label for="recurring_days" class="block text-sm font-medium text-gray-700 mb-2">Interval Hari</label>
                <input type="number" name="recurring_days" id="recurring_days" value="{{ old('recurring_days') }}" min="1" class="w-full md:w-1/3 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Berapa hari sekali">
                @error('recurring_days')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>


            <div class="mt-4">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea name="notes" id="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Catatan internal">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('customer-schedules.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors" :class="selectedProduct ? '' : 'hidden'">
                <i class="fas fa-save mr-2"></i>Simpan Jadwal
            </button>
        </div>
    </form>
</div>

<script>
function scheduleForm() {
    return {
        selectedCustomer: '',
        lastPurchases: [],
        selectedProduct: null,
        isRecurring: false,
        
        async loadCustomerPurchases() {
            if (!this.selectedCustomer) {
                this.lastPurchases = [];
                this.selectedProduct = null;
                return;
            }
            
            try {
                const response = await fetch(`/customers/${this.selectedCustomer}/last-purchases`);
                const data = await response.json();
                this.lastPurchases = data;
                this.selectedProduct = null;
            } catch (error) {
                console.error('Error loading customer purchases:', error);
                this.lastPurchases = [];
            }
        },
        
        selectProduct(purchase) {
            this.selectedProduct = purchase.product;
            
            // Product selected from purchase history
        },
        
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID');
        },
        
        formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
    }
}
</script>
@endsection
