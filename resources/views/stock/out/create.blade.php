@extends('layouts.app')

@section('title', 'Tambah Stok Keluar')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ isset($draft) ? route('stock.out.draft.index') : route('stock.out.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">
            @if(isset($draft))
                Edit Draft Stok Keluar - {{ $draft->draft_number }}
            @else
                Tambah Stok Keluar
            @endif
        </h1>
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

    <div class="space-y-6" x-data="stockOutForm()">

        <!-- Form Informasi Customer -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-users mr-2 text-purple-500"></i>
                    Form Informasi Customer (existing/tambah baru)
                </h3>
                <span x-show="selectedCustomer" class="text-green-600">
                    <i class="fas fa-check-circle"></i> Selesai
                </span>
            </div>

            <div class="mb-4">
                <div class="flex space-x-4 mb-4">
                    <label class="flex items-center">
                        <input type="radio" x-model="customerType" value="existing" class="mr-2">
                        Pilih Customer Existing
                    </label>
                    <label class="flex items-center">
                        <input type="radio" x-model="customerType" value="new" class="mr-2">
                        Tambah Customer Baru
                    </label>
                </div>

                <!-- Existing Customer -->
                <div x-show="customerType === 'existing'">
                    <select x-model="selectedCustomerId" @change="selectExistingCustomer()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" data-name="{{ $customer->name }}">
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- New Customer Form -->
                <div x-show="customerType === 'new'" class="mt-6">
                    <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-users text-purple-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-lg font-semibold text-gray-900">Tambah Customer Baru</h4>
                                <p class="text-sm text-gray-500">Lengkapi informasi customer yang akan ditambahkan</p>
                            </div>
                        </div>
                        
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Nama Customer <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" x-model="newCustomer.name" required
                                           placeholder="Contoh: PT. Customer Medis Indonesia"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Nama Kontak <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" x-model="newCustomer.contact_person" required
                                           placeholder="Contoh: Budi Santoso"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Telepon <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" x-model="newCustomer.phone" required
                                           placeholder="Contoh: 021-12345678"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" x-model="newCustomer.email" required
                                           placeholder="Contoh: info@customer.com"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                            </div>
                            
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">
                                    Alamat <span class="text-red-500">*</span>
                                </label>
                                <textarea x-model="newCustomer.address" rows="3" required
                                          placeholder="Contoh: Jl. Sudirman No. 123, Jakarta Pusat"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="button" @click="saveNewCustomer()" :disabled="!canSaveCustomer() || savingCustomer"
                            class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        <span x-show="!savingCustomer">Simpan Customer</span>
                        <span x-show="savingCustomer">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Menyimpan...
                        </span>
                    </button>
                </div>

                <!-- Customer Success Message -->
                <div x-show="customerSaved" x-transition class="mt-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-md">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <span x-text="customerMessage" class="font-medium"></span>
                    </div>
                </div>
            </div>

            <!-- Selected Customer Display -->
            <div x-show="selectedCustomer" class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
                <h4 class="font-medium text-green-800">Customer Terpilih:</h4>
                <p class="text-green-700" x-text="selectedCustomer"></p>
            </div>
        </div>

        <!-- Form Pilih Produk untuk Stok Keluar -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-shopping-cart mr-2 text-purple-500"></i>
                    Pilih Produk untuk Stok Keluar
                </h3>
            </div>

            <div class="mb-6">
                <!-- Info message jika customer belum dipilih -->
                <div x-show="!selectedCustomer && !customerSaved" class="mb-4 p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-md">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-yellow-500 mr-3"></i>
                        <span class="font-medium">Silakan pilih atau tambahkan customer terlebih dahulu di bagian atas</span>
                    </div>
                </div>

                <!-- Only existing product selection -->
                <div x-show="selectedCustomer || customerSaved" class="mb-4">
                    <p class="text-sm text-gray-600 mb-4">Pilih produk yang sudah tersedia untuk stok keluar</p>
                </div>

                <!-- Existing Product Selection -->
                <div x-show="selectedCustomer || customerSaved" class="space-y-4">
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Produk</label>
                            <select x-model="selectedProductForCart" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Produk --</option>
                                <template x-for="product in availableProducts" :key="product.id">
                                    <option :value="product.id" x-text="product.code + ' - ' + product.name + ' (Stok: ' + product.current_stock + ')'" :data-stock="product.current_stock"></option>
                                </template>
                            </select>
                        </div>
                        <div class="w-32">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Qty</label>
                            <input type="number" x-model="tempQuantity" min="1" value="1" @input="checkTempStock()"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   :class="tempStockWarning ? 'border-red-500 bg-red-50' : 'border-gray-300'">
                            <div x-show="tempStockWarning" class="mt-1 text-xs text-red-600" x-text="tempStockMessage"></div>
                        </div>
                        <div class="w-40">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Harga Satuan</label>
                            <input type="number" x-model="tempUnitPrice" step="0.01" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="w-32">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Diskon (%)</label>
                            <input type="number" x-model="tempDiscount" step="0.01" min="0" max="100" value="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex items-end">
                            <button type="button" @click="addExistingProductToCart()" 
                                    :disabled="!selectedProductForCart || !tempQuantity || !tempUnitPrice || tempStockWarning"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-plus mr-2"></i>
                                Tambah
                            </button>
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
                                    <div class="text-sm text-gray-500" x-text="'Satuan: ' + item.unit + ' | Stok Tersedia: ' + item.available_stock"></div>
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
                                        <div class="text-sm text-gray-500">Diskon</div>
                                        <div class="font-medium text-orange-600" x-text="item.discount + '%'"></div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm text-gray-500">Harga Netto</div>
                                        <div class="font-medium text-blue-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(getNettoUnitPrice(item))"></div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm text-gray-500">Total</div>
                                        <div class="font-medium text-green-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(getNettoAmount(item))"></div>
                                    </div>
                                    <button type="button" @click="editProductInCart(index)"
                                            class="text-blue-600 hover:text-blue-800 mr-2">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" @click="removeFromCart(index)"
                                            class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Total Sebelum Diskon:</span>
                                <span class="text-sm font-medium" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(getTotalAmount())"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Subtotal (Setelah Diskon):</span>
                                <span class="text-sm font-medium text-blue-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(getNettoTotalAmount())"></span>
                            </div>
                            <div class="flex justify-between items-center border-t pt-2">
                                <span class="text-lg font-semibold text-gray-900">Total Keseluruhan:</span>
                                <span class="text-lg font-bold text-green-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(getFinalAmount())"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Detail Stok Keluar -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-clipboard-list mr-2 text-red-500"></i>
                    Detail Transaksi Stok Keluar
                </h3>
            </div>

            <!-- Info message jika belum ada produk -->
            <div x-show="productCart.length === 0" class="p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-md">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-yellow-500 mr-3"></i>
                    <span class="font-medium">Silakan tambahkan produk terlebih dahulu di bagian "Pilih Produk untuk Stok Keluar"</span>
                </div>
            </div>

            <form action="{{ route('stock.out.store') }}" method="POST">
                @csrf
                
                <!-- Hidden field untuk customer -->
                <input type="hidden" name="customer_id" x-model="finalCustomerId">
                
                <!-- Hidden field untuk draft_id jika sedang edit draft -->
                <input type="hidden" name="draft_id" x-model="isEditingDraft && draftData ? draftData.id : ''">
                
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
                        <input type="hidden" :name="'products[' + index + '][discount]'" :value="item.discount || 0">
                    </div>
                </template>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="order_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor Pemesanan</label>
                        <input type="text" name="order_number" id="order_number" 
                               value="{{ old('order_number', isset($draft) ? $draft->order_number : '') }}" 
                               placeholder="Masukkan nomor pemesanan"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="invoice_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor Invoice</label>
                        <input type="text" name="invoice_number" id="invoice_number" 
                               value="{{ old('invoice_number', isset($draft) ? $draft->invoice_number : '') }}" 
                               placeholder="Masukkan nomor invoice"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Transaksi</label>
                        <input type="datetime-local" name="transaction_date" id="transaction_date" 
                               value="{{ old('transaction_date', isset($draft) ? $draft->transaction_date->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <input type="checkbox" x-model="includeTax" class="mr-2">
                            Termasuk PPN 11%
                        </label>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span>Subtotal (Setelah Diskon):</span>
                                <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(getNettoTotalAmount())"></span>
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                        <textarea name="notes" id="notes" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', isset($draft) ? $draft->notes : '') }}</textarea>
                    </div>
                    
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">Tempo Pembayaran</label>
                        <div class="flex items-center space-x-2">
                            <input type="number" name="payment_terms" id="payment_terms" 
                                   value="{{ old('payment_terms', isset($draft) ? $draft->payment_terms : '30') }}" 
                                   min="1" max="365"
                                   placeholder="30"
                                   class="w-20 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <span class="text-sm text-gray-600">hari</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Akan muncul sebagai "Tempo X hari" pada faktur</p>
                    </div>
                    
                    <div>
                        <label for="bank_option" class="block text-sm font-medium text-gray-700 mb-2">Bank untuk Payment</label>
                        <select name="bank_option" id="bank_option" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="mandiri_cibubur" {{ old('bank_option', isset($draft) ? $draft->bank_option : 'mandiri_cibubur') == 'mandiri_cibubur' ? 'selected' : '' }}>
                                Mandiri KCP Cibubur Kota Wisata
                            </option>
                            <option value="mandiri_bogor" {{ old('bank_option', isset($draft) ? $draft->bank_option : '') == 'mandiri_bogor' ? 'selected' : '' }}>
                                Mandiri KCP Pasar Anyar Bogor
                            </option>
                            <option value="dki" {{ old('bank_option', isset($draft) ? $draft->bank_option : '') == 'dki' ? 'selected' : '' }}>
                                Bank DKI KCP DKI PBS
                            </option>
                            <option value="bca" {{ old('bank_option', isset($draft) ? $draft->bank_option : '') == 'bca' ? 'selected' : '' }}>
                                BCA
                            </option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Pilih bank yang akan ditampilkan pada faktur</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="delivery_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor Surat Jalan</label>
                        <input type="text" name="delivery_number" id="delivery_number" 
                               value="{{ old('delivery_number', isset($draft) ? $draft->delivery_number : '') }}" 
                               placeholder="SJ/1036/IX/MSA/25"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex justify-end space-x-4 mt-8">
                    <a href="{{ route('stock.out.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="button" @click="exportToExcel()" 
                            :disabled="productCart.length === 0"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <i class="fas fa-print mr-2"></i>
                        Cetak Faktur
                    </button>
                    <button type="button" @click="exportDeliveryNote()" 
                            :disabled="productCart.length === 0"
                            class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <i class="fas fa-truck mr-2"></i>
                        Cetak Surat Jalan
                    </button>
                    <button type="button" @click="exportToExcelFile()" 
                            :disabled="productCart.length === 0"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export Excel
                    </button>
                    <button type="button" @click="saveDraft()" 
                            :disabled="productCart.length === 0"
                            class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        <span x-text="isEditingDraft ? 'Update Draft' : 'Simpan Draft'"></span>
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        <i class="fas fa-check mr-2"></i>
                        Selesaikan Stok Keluar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
function stockOutForm() {
    return {
        // Multi-product cart
        availableProducts: @json($products),
        selectedProductForCart: '',
        tempQuantity: 1,
        tempUnitPrice: '',
        tempDiscount: 0,
        productCart: @json(isset($draft) && $draft->cart_data ? $draft->cart_data : []),
        tempStockWarning: false,
        tempStockMessage: '',
        editingIndex: -1,
        
        // Draft data
        draftData: @json($draft ?? null),
        isEditingDraft: {{ isset($draft) ? 'true' : 'false' }},
        
        // PPN
        includeTax: {{ isset($draft) ? ($draft->include_tax ? 'true' : 'false') : 'false' }},
        
        // Customer
        customerType: 'existing',
        selectedCustomerId: '{{ isset($draft) && $draft->customer_id ? $draft->customer_id : "" }}',
        selectedCustomer: '{{ isset($draft) ? $draft->customer_name : "" }}',
        finalCustomerId: '{{ isset($draft) && $draft->customer_id ? $draft->customer_id : "" }}',
        newCustomer: {
            name: '',
            contact_person: '',
            phone: '',
            email: '',
            address: ''
        },
        customerSaved: false,
        customerMessage: '',
        savingCustomer: false,


        selectExistingCustomer() {
            if (this.selectedCustomerId) {
                const select = document.querySelector('select[x-model="selectedCustomerId"]');
                const option = select.querySelector(`option[value="${this.selectedCustomerId}"]`);
                if (option) {
                    this.selectedCustomer = option.dataset.name;
                    this.finalCustomerId = this.selectedCustomerId;
                }
            }
        },

        canSaveCustomer() {
            if (this.customerType === 'existing') {
                return this.selectedCustomerId;
            } else {
                return this.newCustomer.name && this.newCustomer.contact_person && 
                       this.newCustomer.phone && this.newCustomer.email && this.newCustomer.address;
            }
        },

        async saveNewCustomer() {
            if (!this.canSaveCustomer()) {
                return;
            }

            this.savingCustomer = true;
            try {
                const response = await fetch('{{ route("customers.ajax.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.newCustomer)
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.newCustomer.id = data.id;
                    this.customerSaved = true;
                    this.customerMessage = data.message;
                    this.selectedCustomer = data.name;
                    this.finalCustomerId = data.id;
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            } finally {
                this.savingCustomer = false;
            }
        },

        checkTempStock() {
            if (this.selectedProductForCart && this.tempQuantity > 0) {
                const product = this.availableProducts.find(p => p.id == this.selectedProductForCart);
                if (product) {
                    const currentStock = product.current_stock;
                    
                    if (this.tempQuantity > currentStock) {
                        this.tempStockWarning = true;
                        this.tempStockMessage = `Qty melebihi stok tersedia (${currentStock})`;
                    } else {
                        this.tempStockWarning = false;
                        this.tempStockMessage = '';
                    }
                }
            } else {
                this.tempStockWarning = false;
                this.tempStockMessage = '';
            }
        },

        addExistingProductToCart() {
            if (!this.selectedProductForCart || !this.tempQuantity || !this.tempUnitPrice || this.tempStockWarning) {
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
                this.productCart[existingIndex].discount = parseFloat(this.tempDiscount) || 0;
            } else {
                // Add new item
                this.productCart.push({
                    product_id: product.id,
                    code: product.code,
                    name: product.name,
                    unit: product.unit,
                    available_stock: product.current_stock,
                    quantity: parseInt(this.tempQuantity),
                    unit_price: parseFloat(this.tempUnitPrice),
                    discount: parseFloat(this.tempDiscount) || 0
                });
            }

            // Reset form
            this.selectedProductForCart = '';
            this.tempQuantity = 1;
            this.tempUnitPrice = '';
            this.tempDiscount = 0;
            this.tempStockWarning = false;
            this.tempStockMessage = '';
        },

        removeFromCart(index) {
            this.productCart.splice(index, 1);
        },

        editProductInCart(index) {
            const item = this.productCart[index];
            this.selectedProductForCart = item.product_id;
            this.tempQuantity = item.quantity;
            this.tempUnitPrice = item.unit_price;
            this.tempDiscount = item.discount || 0;
            this.editingIndex = index;
            
            // Remove the item temporarily
            this.productCart.splice(index, 1);
        },

        getNettoAmount(item) {
            const total = item.quantity * item.unit_price;
            const discountAmount = total * ((item.discount || 0) / 100);
            return total - discountAmount;
        },

        getNettoUnitPrice(item) {
            const discountPercent = item.discount || 0;
            return item.unit_price * (1 - discountPercent/100);
        },

        getTotalAmount() {
            return this.productCart.reduce((total, item) => {
                return total + (item.quantity * item.unit_price);
            }, 0);
        },

        getNettoTotalAmount() {
            return this.productCart.reduce((total, item) => {
                return total + this.getNettoAmount(item);
            }, 0);
        },

        getTaxAmount() {
            if (!this.includeTax) return 0;
            return this.getNettoTotalAmount() * 0.11;
        },

        getFinalAmount() {
            return this.getNettoTotalAmount() + this.getTaxAmount();
        },

        async exportToExcel() {
            if (this.productCart.length === 0) {
                return;
            }

            try {
                // Get form data
                const orderNumber = document.getElementById('order_number')?.value || '';
                const invoiceNumber = document.getElementById('invoice_number')?.value || this.generateInvoiceNumber();
                const paymentTerms = document.getElementById('payment_terms')?.value || '30';
                const bankOption = document.getElementById('bank_option')?.value || 'mandiri_cibubur';
                
                // Create form data
                const formData = new FormData();
                formData.append('cart_data', JSON.stringify(this.productCart));
                formData.append('customer_name', this.selectedCustomer || 'Customer');
                formData.append('customer_id', this.finalCustomerId || this.selectedCustomerId || '');
                formData.append('order_number', orderNumber);
                formData.append('invoice_number', invoiceNumber);
                formData.append('include_tax', this.includeTax ? '1' : '0');
                formData.append('payment_terms', paymentTerms);
                formData.append('bank_option', bankOption);
                
                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    return;
                }
                formData.append('_token', csrfToken);

                // Submit to export endpoint and open in new window
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("stock.out.export.invoice") }}';
                form.target = '_blank';
                form.style.display = 'none';

                // Add all form data as hidden inputs
                for (let [key, value] of formData.entries()) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    form.appendChild(input);
                }

                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);

            } catch (error) {
                console.error('Export error:', error);
            }
        },

        async exportToExcelFile() {
            if (this.productCart.length === 0) {
                return;
            }

            try {
                // Get form data
                const orderNumber = document.getElementById('order_number')?.value || '';
                const invoiceNumber = document.getElementById('invoice_number')?.value || '';
                const paymentTerms = document.getElementById('payment_terms')?.value || '30';
                const bankOption = document.getElementById('bank_option')?.value || 'mandiri_cibubur';
                
                // Create form data
                const formData = new FormData();
                formData.append('cart_data', JSON.stringify(this.productCart));
                formData.append('customer_name', this.selectedCustomer || 'Customer');
                formData.append('customer_id', this.finalCustomerId || this.selectedCustomerId || '');
                formData.append('order_number', orderNumber);
                formData.append('invoice_number', invoiceNumber);
                formData.append('include_tax', this.includeTax ? '1' : '0');
                formData.append('payment_terms', paymentTerms);
                formData.append('bank_option', bankOption);
                
                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    return;
                }
                formData.append('_token', csrfToken);

                // Submit to Excel export endpoint
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("stock.out.export.xlsx") }}';
                form.style.display = 'none';

                // Add all form data as hidden inputs
                for (let [key, value] of formData.entries()) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    form.appendChild(input);
                }

                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);

            } catch (error) {
                console.error('Export error:', error);
            }
        },

        async exportDeliveryNote() {
            if (this.productCart.length === 0) {
                return;
            }

            try {
                // Get form data
                const deliveryNumber = document.getElementById('delivery_number')?.value || '';
                
                // Create form data
                const formData = new FormData();
                formData.append('cart_data', JSON.stringify(this.productCart));
                formData.append('customer_name', this.selectedCustomer || 'Customer');
                formData.append('customer_id', this.finalCustomerId || this.selectedCustomerId || '');
                formData.append('delivery_number', deliveryNumber);
                
                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    return;
                }
                formData.append('_token', csrfToken);

                // Submit to delivery note export endpoint and open in new window
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("stock.out.export.delivery") }}';
                form.target = '_blank';
                form.style.display = 'none';

                // Add all form data as hidden inputs
                for (let [key, value] of formData.entries()) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    form.appendChild(input);
                }

                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);

            } catch (error) {
                console.error('Delivery note export error:', error);
            }
        },

        async saveDraft() {
            if (this.productCart.length === 0) {
                alert('Tidak ada produk untuk disimpan');
                return;
            }

            try {
                // Generate temporary data for PO
            const orderNumber = document.querySelector('input[name="order_number"]').value || 'PO-' + new Date().toISOString().slice(0,10).replace(/-/g,'');
            const invoiceNumber = document.querySelector('input[name="invoice_number"]').value || this.generateInvoiceNumber();
            const transactionDate = document.querySelector('input[name="transaction_date"]').value || new Date().toISOString().slice(0,10);
                const notes = document.getElementById('notes')?.value || '';
                const paymentTerms = document.getElementById('payment_terms')?.value || '30';
                const deliveryNumber = document.getElementById('delivery_number')?.value || '';
                const bankOption = document.getElementById('bank_option')?.value || 'mandiri_cibubur';
                
                // Create form data
                const formData = new FormData();
                formData.append('cart_data', JSON.stringify(this.productCart));
                formData.append('customer_name', this.selectedCustomer || 'Customer');
                formData.append('customer_id', this.finalCustomerId || this.selectedCustomerId || '');
                formData.append('order_number', orderNumber);
                formData.append('invoice_number', invoiceNumber);
                formData.append('transaction_date', transactionDate);
                formData.append('notes', notes);
                formData.append('payment_terms', paymentTerms);
                formData.append('delivery_number', deliveryNumber);
                formData.append('bank_option', bankOption);
                formData.append('include_tax', this.includeTax ? '1' : '0');
                formData.append('is_draft', '1'); // Mark as draft
                
                // If editing existing draft, add draft ID
                if (this.isEditingDraft && this.draftData) {
                    formData.append('draft_id', this.draftData.id);
                }
                
                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    alert('CSRF token tidak ditemukan');
                    return;
                }
                formData.append('_token', csrfToken);

                // Submit to draft save endpoint
                const endpoint = this.isEditingDraft && this.draftData 
                    ? '{{ route("stock.out.draft.update", ":id") }}'.replace(':id', this.draftData.id)
                    : '{{ route("stock.out.draft.save") }}';
                    
                const response = await fetch(endpoint, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (response.ok && result.success) {
                    alert(this.isEditingDraft ? 'Draft berhasil diupdate!' : 'Draft berhasil disimpan!');
                    // Optionally redirect to drafts list or clear form
                    window.location.href = '{{ route("stock.out.draft.index") }}';
                } else {
                    alert('Gagal menyimpan draft: ' + (result.message || 'Unknown error'));
                }

            } catch (error) {
                console.error('Save draft error:', error);
                alert('Terjadi kesalahan saat menyimpan draft');
            }
        },

        init() {
            if (this.isEditingDraft && this.draftData) {
                this.loadDraftData();
            }
        },

        loadDraftData() {
            // All draft data is now automatically loaded via Blade template initialization
            // This function is kept for any additional processing if needed
            console.log('Draft data loaded successfully');
        },

        getProductName(productId) {
            const product = this.availableProducts.find(p => p.id == productId);
            return product ? product.name : 'Unknown Product';
        },

        // Generate invoice number with format: [CV_CODE][RUNNING_NUMBER][MONTH_YEAR]
        generateInvoiceNumber() {
            const date = new Date();
            const monthYear = date.getFullYear().toString().slice(-2) + String(date.getMonth() + 1).padStart(2, '0');
            
            // For now, generate a simple format - the actual logic will be handled server-side
            // This is just for display purposes before form submission
            return '11985' + monthYear; // Placeholder - server will generate the correct one
        },

    }
}
</script>
@endsection
