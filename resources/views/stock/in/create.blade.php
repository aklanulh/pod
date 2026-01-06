@extends('layouts.app')

@section('title', isset($draft) ? 'Edit Draft Stok Masuk' : 'Tambah Stok Masuk')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('stock.in.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">@isset($draft) Edit Draft Stok Masuk @else Tambah Stok Masuk @endisset</h1>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <strong>Error:</strong>
            <ul class="mt-1 ml-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div x-data="stockInForm()" class="space-y-6">
        

        <!-- Form Informasi Distributor -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-truck mr-2 text-green-500"></i>
                    Form Informasi Distributor (existing/tambah baru)
                </h3>
                <span x-show="selectedSupplier" class="text-green-600">
                    <i class="fas fa-check-circle"></i> Selesai
                </span>
            </div>

            <div class="mb-4">
                <div class="flex space-x-4 mb-4">
                    <label class="flex items-center">
                        <input type="radio" x-model="supplierType" value="existing" class="mr-2">
                        Pilih Distributor Existing
                    </label>
                    <label class="flex items-center">
                        <input type="radio" x-model="supplierType" value="new" class="mr-2">
                        Tambah Distributor Baru
                    </label>
                </div>

                <!-- Existing Supplier -->
                <div x-show="supplierType === 'existing'">
                    <select x-model="selectedSupplierId" @change="selectExistingSupplier()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Distributor</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" data-name="{{ $supplier->name }}">
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- New Distributor Form -->
                <div x-show="supplierType === 'new'" class="mt-6">
                    <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-truck text-green-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-lg font-semibold text-gray-900">Tambah Distributor Baru</h4>
                                <p class="text-sm text-gray-500">Lengkapi informasi distributor yang akan ditambahkan</p>
                            </div>
                        </div>
                        
                        <div class="space-y-6">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">
                                    Nama Distributor <span class="text-red-500">*</span>
                                </label>
                                <input type="text" x-model="newSupplier.name" required
                                       placeholder="Contoh: PT. Distributor Medis Indonesia"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Kontak Person
                                    </label>
                                    <input type="text" x-model="newSupplier.contact_person"
                                           placeholder="Contoh: Budi Santoso"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Telepon
                                    </label>
                                    <input type="text" x-model="newSupplier.phone"
                                           placeholder="Contoh: 021-12345678"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                            </div>
                            
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">
                                    Email
                                </label>
                                <input type="email" x-model="newSupplier.email"
                                       placeholder="Contoh: info@supplier.com"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>
                            
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">
                                    Alamat
                                </label>
                                <textarea x-model="newSupplier.address" rows="3"
                                          placeholder="Contoh: Jl. Sudirman No. 123, Jakarta Pusat"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="button" @click="saveNewSupplier()" :disabled="!canSaveSupplier() || savingSupplier"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        <span x-show="!savingSupplier">Simpan Supplier</span>
                        <span x-show="savingSupplier">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Menyimpan...
                        </span>
                    </button>
                </div>

                <!-- Supplier Success Message -->
                <div x-show="supplierSaved" x-transition class="mt-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-md">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <span x-text="supplierMessage" class="font-medium"></span>
                    </div>
                </div>
            </div>

            <!-- Selected Supplier Display -->
            <div x-show="selectedSupplier" class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
                <h4 class="font-medium text-green-800">Supplier Terpilih:</h4>
                <p class="text-green-700" x-text="selectedSupplier"></p>
            </div>
        </div>

        <!-- Form Pilih Produk untuk Stok Masuk -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-shopping-cart mr-2 text-purple-500"></i>
                    Pilih Produk untuk Stok Masuk
                </h3>
            </div>

            <div class="mb-6">
                <!-- Info message jika supplier belum dipilih -->
                <div x-show="!selectedSupplier && !supplierSaved" class="mb-4 p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-md">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-yellow-500 mr-3"></i>
                        <span class="font-medium">Silakan pilih atau tambahkan distributor terlebih dahulu di bagian atas</span>
                    </div>
                </div>

                <!-- Radio buttons for product selection type -->
                <div x-show="selectedSupplier || supplierSaved" class="flex space-x-4 mb-4">
                    <label class="flex items-center">
                        <input type="radio" x-model="productSelectionType" value="existing" class="mr-2">
                        Pilih Produk Existing
                    </label>
                    <label class="flex items-center">
                        <input type="radio" x-model="productSelectionType" value="new" class="mr-2">
                        Tambah Produk Baru
                    </label>
                </div>

                <!-- Existing Product Selection -->
                <div x-show="productSelectionType === 'existing'" class="space-y-4">
                    <div x-show="selectedSupplier || supplierSaved">
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Produk</label>
                            <select x-model="selectedProductForCart" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Produk --</option>
                                <template x-for="product in availableProducts" :key="product.id">
                                    <option :value="product.id" x-text="product.code + ' - ' + product.name + ' (Stok: ' + (product.current_stock || 0) + ' ' + product.unit + ')'"></option>
                                </template>
                            </select>
                        </div>
                        <div class="w-32">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Qty</label>
                            <input type="number" x-model="tempQuantity" min="1" value="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="w-40">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Harga Satuan</label>
                            <input type="number" x-model="tempUnitPrice" step="0.01" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex items-end">
                            <button type="button" @click="addExistingProductToCart()" 
                                    :disabled="!selectedProductForCart || !tempQuantity || !tempUnitPrice"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-plus mr-2"></i>
                                Tambah
                            </button>
                        </div>
                    </div>
                </div>

                <!-- New Product Form -->
                <div x-show="productSelectionType === 'new'" class="space-y-4">
                    <div x-show="selectedSupplier || supplierSaved">
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <h4 class="text-md font-semibold text-blue-900 mb-3">
                            <i class="fas fa-box mr-2"></i>
                            Tambah Produk Baru
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Kode Produk <span class="text-red-500">*</span>
                                </label>
                                <input type="text" x-model="newProductForCart.code" required
                                       placeholder="Contoh: PRD001"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Produk <span class="text-red-500">*</span>
                                </label>
                                <input type="text" x-model="newProductForCart.name" required
                                       placeholder="Contoh: Reagent DS Diluent"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Kategori <span class="text-red-500">*</span>
                                </label>
                                <select x-model="newProductForCart.category_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Satuan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" x-model="newProductForCart.unit" required
                                       placeholder="pcs, box, ml, kg, dll"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Harga Satuan <span class="text-red-500">*</span>
                                </label>
                                <input type="number" x-model="newProductForCart.price" step="0.01" min="0" required
                                       placeholder="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Minimal Stok
                                </label>
                                <input type="number" x-model="newProductForCart.minimum_stock" min="0" value="0"
                                       placeholder="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tanggal Expired
                                </label>
                                <input type="date" x-model="newProductForCart.exp"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Lot Number
                                </label>
                                <input type="text" x-model="newProductForCart.lot"
                                       placeholder="Contoh: LOT001"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    NIE (Nomor Izin Edar)
                                </label>
                                <input type="text" x-model="newProductForCart.distribution_permit"
                                       placeholder="Contoh: DKL1234567890123"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div class="flex space-x-4 mt-4">
                            <div class="w-32">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Qty</label>
                                <input type="number" x-model="tempQuantity" min="1" value="1"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="flex items-end">
                                <button type="button" @click="addNewProductToCart()" 
                                        :disabled="!canAddNewProduct() || savingNewProduct"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span x-show="!savingNewProduct">
                                        <i class="fas fa-plus mr-2"></i>
                                        Simpan & Tambah
                                    </span>
                                    <span x-show="savingNewProduct">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>
                                        Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Produk yang Dipilih -->
            <div x-show="productCart.length > 0" class="mb-6">
                <h4 class="text-md font-semibold text-gray-900 mb-3">Produk yang Dipilih:</h4>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="space-y-3">
                        <template x-for="(item, index) in productCart" :key="index">
                            <div class="flex items-center justify-between bg-white p-3 rounded border">
                                <div class="flex-1">
                                    <div class="font-medium" x-text="item.code + ' - ' + item.name"></div>
                                    <div class="text-sm text-gray-500" x-text="'Satuan: ' + item.unit"></div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="text-center">
                                        <div class="text-sm text-gray-500">Qty</div>
                                        <div class="font-medium" x-text="item.quantity"></div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm text-gray-500">Harga Satuan</div>
                                        <div class="font-medium" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.unit_price)"></div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm text-gray-500">Total</div>
                                        <div class="font-medium text-green-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.quantity * item.unit_price)"></div>
                                    </div>
                                    <button type="button" @click="removeFromCart(index)"
                                            class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-900">Total Keseluruhan:</span>
                            <span class="text-lg font-bold text-green-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(getTotalAmount())"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Detail Stok Masuk -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-clipboard-list mr-2 text-purple-500"></i>
                    Detail Transaksi Stok Masuk
                </h3>
            </div>

            <!-- Info message jika belum ada produk -->
            <div x-show="productCart.length === 0" class="p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-md">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-yellow-500 mr-3"></i>
                    <span class="font-medium">Silakan tambahkan produk terlebih dahulu di bagian "Pilih Produk untuk Stok Masuk"</span>
                </div>
            </div>

            <form action="@isset($draft) {{ route('stock.in.draft.update', $draft->id) }} @else {{ route('stock.in.store') }} @endisset" method="POST" @submit="console.log('Form submitted with data:', {supplier_id: finalSupplierId, products: productCart, include_tax: includeTax, tax_amount: getTaxAmount(), final_amount: getFinalAmount()}); if (!finalSupplierId || productCart.length === 0) { alert('Data tidak lengkap!'); $event.preventDefault(); return false; }">
                @csrf
                @isset($draft)
                    @method('PUT')
                @endisset
                
                <!-- Hidden field untuk supplier -->
                <input type="hidden" name="supplier_id" x-model="finalSupplierId">
                
                <!-- Hidden field untuk PPN -->
                <input type="hidden" name="include_tax" :value="includeTax ? '1' : '0'">
                <input type="hidden" name="tax_amount" :value="getTaxAmount()">
                <input type="hidden" name="final_amount" :value="getFinalAmount()">
                
                <!-- Hidden fields untuk multiple products -->
                <template x-for="(item, index) in productCart" :key="index">
                    <div>
                        <input type="hidden" :name="'products[' + index + '][product_id]'" :value="item.product_id">
                        <input type="hidden" :name="'products[' + index + '][quantity]'" :value="item.quantity">
                        <input type="hidden" :name="'products[' + index + '][unit_price]'" :value="item.unit_price">
                    </div>
                </template>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="order_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor Pemesanan</label>
                        <input type="text" name="order_number" id="order_number" 
                               value="{{ old('order_number') }}" 
                               placeholder="Masukkan nomor pemesanan"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="invoice_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor Invoice</label>
                        <input type="text" name="invoice_number" id="invoice_number" 
                               value="{{ old('invoice_number') }}" 
                               placeholder="Masukkan nomor invoice"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Transaksi</label>
                        <input type="datetime-local" name="transaction_date" id="transaction_date" 
                               value="{{ old('transaction_date', now()->format('Y-m-d\TH:i')) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <input type="checkbox" x-model="includeTax" class="mr-2">
                            Termasuk PPN 11%
                        </label>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span>Subtotal:</span>
                                <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(getTotalAmount())"></span>
                            </div>
                            <div class="flex justify-between text-sm" x-show="includeTax">
                                <span>PPN 11%:</span>
                                <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(getTaxAmount())"></span>
                            </div>
                            <div class="flex justify-between text-lg font-semibold text-green-600 border-t pt-2">
                                <span>Total:</span>
                                <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(getFinalAmount())"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea name="notes" id="notes" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>

                <div class="flex justify-end space-x-4 mt-8">
                    <a href="{{ route('stock.in.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="button" @click="printPurchaseOrder()"
                            :disabled="!finalSupplierId || productCart.length === 0"
                            x-bind:class="(!finalSupplierId || productCart.length === 0) ? 'px-4 py-2 bg-gray-400 text-white rounded-md cursor-not-allowed' : 'px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700'"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <i class="fas fa-print mr-2"></i>
                        Cetak PO
                    </button>
                    <button type="button" @click="saveDraft()"
                            class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Draft
                    </button>
                    <button type="submit" 
                            x-bind:disabled="!finalSupplierId || productCart.length === 0"
                            x-bind:class="(!finalSupplierId || productCart.length === 0) ? 'px-4 py-2 bg-gray-400 text-white rounded-md cursor-not-allowed' : 'px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700'"
                            @click="if (!finalSupplierId) { alert('Pilih distributor terlebih dahulu!'); return false; } if (productCart.length === 0) { alert('Tambahkan produk terlebih dahulu!'); return false; }">
                        <i class="fas fa-check mr-2"></i>
                        @isset($draft) Update Draft @else Simpan Stok Masuk @endisset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function stockInForm() {
    return {
        // Product
        productType: 'existing',
        selectedProductId: '',
        selectedProduct: '',
        finalProductId: '',
        newProduct: {
            code: '',
            name: '',
            category_id: '',
            unit: '',
            price: '',
            minimum_stock: 0
        },
        productSaved: false,
        productMessage: '',
        saving: false,
        
        // Multi-product cart
        availableProducts: @json($products),
        selectedProductForCart: '',
        tempQuantity: 1,
        tempUnitPrice: '',
        productCart: [],
        
        // PPN
        includeTax: false,
        productSelectionType: 'existing',
        newProductForCart: {
            code: '',
            name: '',
            category_id: '',
            unit: '',
            price: '',
            minimum_stock: 0,
            exp: '',
            lot: '',
            distribution_permit: ''
        },
        savingNewProduct: false,
        
        // Supplier
        supplierType: 'existing',
        selectedSupplierId: '',
        selectedSupplier: '',
        finalSupplierId: '',
        newSupplier: {
            name: '',
            contact_person: '',
            phone: '',
            email: '',
            address: ''
        },
        supplierSaved: false,
        supplierMessage: '',
        savingSupplier: false,

        // Initialize draft data if editing
        init() {
            @if(isset($draft))
                // Load draft data
                this.productCart = @json($draft->cart_data ?? []);
                this.selectedSupplierId = '{{ $draft->supplier_id ?? '' }}';
                this.selectedSupplier = '{{ $draft->supplier_name ?? '' }}';
                this.finalSupplierId = '{{ $draft->supplier_id ?? '' }}';
                this.includeTax = {{ $draft->include_tax ? 'true' : 'false' }};
                
                // Set form values
                document.querySelector('input[name="order_number"]').value = '{{ $draft->order_number ?? '' }}';
                document.querySelector('input[name="invoice_number"]').value = '{{ $draft->invoice_number ?? '' }}';
                document.querySelector('input[name="transaction_date"]').value = '{{ $draft->transaction_date ?? now()->format('Y-m-d\TH:i') }}';
                document.querySelector('textarea[name="notes"]').value = '{{ $draft->notes ?? '' }}';
            @endif
        },

        selectExistingProduct() {
            if (this.selectedProductId) {
                const select = document.querySelector('select[x-model="selectedProductId"]');
                const option = select.querySelector(`option[value="${this.selectedProductId}"]`);
                if (option) {
                    this.selectedProduct = `${option.dataset.code} - ${option.dataset.name}`;
                    this.finalProductId = this.selectedProductId;
                }
            }
        },

        selectExistingSupplier() {
            if (this.selectedSupplierId) {
                const select = document.querySelector('select[x-model="selectedSupplierId"]');
                const option = select.querySelector(`option[value="${this.selectedSupplierId}"]`);
                if (option) {
                    this.selectedSupplier = option.dataset.name;
                    this.finalSupplierId = this.selectedSupplierId;
                }
            }
        },

        canSaveProduct() {
            if (this.productType === 'existing') {
                return this.selectedProductId;
            } else {
                return this.newProduct.code && this.newProduct.name && 
                       this.newProduct.category_id && this.newProduct.unit && 
                       this.newProduct.price;
            }
        },

        async saveNewProduct() {
            if (!this.canSaveProduct()) {
                return;
            }

            this.saving = true;
            try {
                const response = await fetch('{{ route("products.ajax.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.newProduct)
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.newProduct.id = data.id;
                    this.productSaved = true;
                    this.productMessage = data.message;
                    this.selectedProduct = `${data.code} - ${data.name}`;
                    this.finalProductId = data.id;
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            } finally {
                this.saving = false;
            }
        },

        canSaveSupplier() {
            if (this.supplierType === 'existing') {
                return this.selectedSupplierId;
            } else {
                return this.newSupplier.name;
            }
        },

        async saveNewSupplier() {
            if (!this.canSaveSupplier()) {
                return;
            }

            this.savingSupplier = true;
            try {
                const response = await fetch('{{ route("suppliers.ajax.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.newSupplier)
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.newSupplier.id = data.id;
                    this.supplierSaved = true;
                    this.supplierMessage = data.message;
                    this.selectedSupplier = data.name;
                    this.finalSupplierId = data.id;
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            } finally {
                this.savingSupplier = false;
            }
        },

        addExistingProductToCart() {
            if (!this.selectedProductForCart || !this.tempQuantity || !this.tempUnitPrice) {
                return;
            }

            const product = this.availableProducts.find(p => p.id == this.selectedProductForCart);
            if (!product) return;

            // Check if product already in cart
            const existingIndex = this.productCart.findIndex(item => item.product_id == this.selectedProductForCart);
            
            if (existingIndex >= 0) {
                // Update existing item
                this.productCart[existingIndex].quantity = parseInt(this.productCart[existingIndex].quantity) + parseInt(this.tempQuantity);
                this.productCart[existingIndex].unit_price = parseFloat(this.tempUnitPrice);
            } else {
                // Add new item
                this.productCart.push({
                    product_id: product.id,
                    code: product.code,
                    name: product.name,
                    unit: product.unit,
                    quantity: parseInt(this.tempQuantity),
                    unit_price: parseFloat(this.tempUnitPrice)
                });
            }

            // Reset form
            this.selectedProductForCart = '';
            this.tempQuantity = 1;
            this.tempUnitPrice = '';
        },

        canAddNewProduct() {
            return this.newProductForCart.code && 
                   this.newProductForCart.name && 
                   this.newProductForCart.category_id && 
                   this.newProductForCart.unit && 
                   this.newProductForCart.price && 
                   this.tempQuantity;
        },

        async addNewProductToCart() {
            if (!this.canAddNewProduct()) {
                return;
            }

            this.savingNewProduct = true;
            try {
                const response = await fetch('{{ route("products.ajax.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.newProductForCart)
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Add the new product to cart
                    this.productCart.push({
                        product_id: data.id,
                        code: data.code,
                        name: data.name,
                        unit: data.unit,
                        quantity: parseInt(this.tempQuantity),
                        unit_price: parseFloat(this.newProductForCart.price)
                    });

                    // Update available products list
                    this.availableProducts.push({
                        id: data.id,
                        code: data.code,
                        name: data.name,
                        unit: data.unit
                    });

                    // Reset form
                    this.newProductForCart = {
                        code: '',
                        name: '',
                        category_id: '',
                        unit: '',
                        price: '',
                        minimum_stock: 0,
                        exp: '',
                        lot: '',
                        distribution_permit: ''
                    };
                    this.tempQuantity = 1;
                    
                    // Switch back to existing product selection
                    this.productSelectionType = 'existing';
                    
                    alert('Produk berhasil ditambahkan ke keranjang!');
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            } finally {
                this.savingNewProduct = false;
            }
        },

        removeFromCart(index) {
            this.productCart.splice(index, 1);
        },

        getTotalAmount() {
            return this.productCart.reduce((total, item) => {
                return total + (item.quantity * item.unit_price);
            }, 0);
        },

        getTaxAmount() {
            if (!this.includeTax) return 0;
            return this.getTotalAmount() * 0.11;
        },

        getFinalAmount() {
            return this.getTotalAmount() + this.getTaxAmount();
        },

        async saveDraft() {
            if (this.productCart.length === 0) {
                alert('Tambahkan produk terlebih dahulu sebelum menyimpan draft!');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('cart_data', JSON.stringify(this.productCart));
                formData.append('supplier_id', this.finalSupplierId || '');
                formData.append('supplier_name', this.selectedSupplier || 'Supplier');
                formData.append('order_number', document.querySelector('input[name="order_number"]').value || '');
                formData.append('invoice_number', document.querySelector('input[name="invoice_number"]').value || '');
                formData.append('transaction_date', document.querySelector('input[name="transaction_date"]').value || new Date().toISOString().split('T')[0]);
                formData.append('notes', document.querySelector('textarea[name="notes"]').value || '');
                formData.append('include_tax', this.includeTax ? '1' : '0');

                const response = await fetch('{{ route("stock.in.draft.save") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    alert('Draft stok masuk berhasil disimpan!');
                    window.location.href = '{{ route("stock.in.index") }}';
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        },

        printPurchaseOrder() {
            if (!this.finalSupplierId || this.productCart.length === 0) {
                alert('Pilih supplier dan tambahkan produk terlebih dahulu!');
                return;
            }

            // Generate temporary data for PO
            const orderNumber = document.querySelector('input[name="order_number"]').value || 'PO-' + new Date().toISOString().slice(0,10).replace(/-/g,'');
            const invoiceNumber = document.querySelector('input[name="invoice_number"]').value || 'INV-' + new Date().toISOString().slice(0,10).replace(/-/g,'');
            const transactionDate = document.querySelector('input[name="transaction_date"]').value || new Date().toISOString().slice(0,10);
            
            // Create form data
            const formData = new FormData();
            formData.append('order_number', orderNumber);
            formData.append('invoice_number', invoiceNumber);
            formData.append('supplier_id', this.finalSupplierId);
            formData.append('transaction_date', transactionDate);
            
            // Add product data temporarily for PO generation
            this.productCart.forEach((item, index) => {
                formData.append(`products[${index}][product_id]`, item.product_id);
                formData.append(`products[${index}][quantity]`, item.quantity);
                formData.append(`products[${index}][unit_price]`, item.unit_price);
            });
            
            // Open in new window
            const newWindow = window.open('', '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
            
            // Fetch the purchase order
            fetch('/stock/in/export-purchase-order', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.text())
            .then(html => {
                newWindow.document.write(html);
                newWindow.document.close();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mencetak Purchase Order');
                newWindow.close();
            });
        }
    }
}
</script>
@endsection
