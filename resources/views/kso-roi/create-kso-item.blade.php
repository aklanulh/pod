@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Tambah KSO Item</h1>
            <p class="text-gray-600">Tambahkan Alat Medis & Alat Komputer & Pendukung baru untuk tracking ROI</p>
        </div>
        <a href="{{ route('kso-roi.kso-items') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('kso-roi.store-kso-item') }}" method="POST" id="ksoItemForm">
            @csrf
            
            <!-- Basic Information -->
            <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Informasi Dasar</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">Customer *</label>
                    <select name="customer_id" id="customer_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <option value="">Pilih Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama_alat" class="block text-sm font-medium text-gray-700 mb-2">Nama Alat *</label>
                    <input type="text" name="nama_alat" id="nama_alat" value="{{ old('nama_alat') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    @error('nama_alat')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="brand" class="block text-sm font-medium text-gray-700 mb-2">Brand/Merk</label>
                    <input type="text" name="brand" id="brand" value="{{ old('brand') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('brand')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="model" class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                    <input type="text" name="model" id="model" value="{{ old('model') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('model')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-2">Serial Number</label>
                    <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('serial_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_registrasi" class="block text-sm font-medium text-gray-700 mb-2">No. Registrasi</label>
                    <input type="text" name="no_registrasi" id="no_registrasi" value="{{ old('no_registrasi') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('no_registrasi')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <input type="text" name="kategori" id="kategori" value="{{ old('kategori') }}" list="kategori-list" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Pilih atau ketik kategori">
                    <datalist id="kategori-list">
                        <option value="hematologi">Hematologi</option>
                        <option value="kimia_klinik">Kimia Klinik</option>
                        <option value="gas_darah">Gas Darah</option>
                        <option value="koagulasi">Koagulasi</option>
                        <option value="mikrobiologi">Mikrobiologi</option>
                        <option value="preparasi_sampel">Preparasi Sampel</option>
                        <option value="imaging">Imaging</option>
                        <option value="monitoring">Monitoring</option>
                        <option value="lainnya">Lainnya</option>
                    </datalist>
                    <p class="text-xs text-gray-500 mt-1">Pilih dari daftar atau ketik kategori baru</p>
                    @error('kategori')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="kondisi" class="block text-sm font-medium text-gray-700 mb-2">Kondisi</label>
                    <select name="kondisi" id="kondisi" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="baik" {{ old('kondisi', 'baik') == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak ringan" {{ old('kondisi') == 'rusak ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="rusak berat" {{ old('kondisi') == 'rusak berat' ? 'selected' : '' }}>Rusak Berat</option>
                        <option value="maintenance" {{ old('kondisi') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                    @error('kondisi')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Asset Information -->
            <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Informasi Aset</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="nilai_alat_utama" class="block text-sm font-medium text-gray-700 mb-2">Nilai Aset (Rp) *</label>
                    <input type="number" name="nilai_alat_utama" id="nilai_alat_utama" value="{{ old('nilai_alat_utama') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" min="0" step="0.01" required>
                    <p class="text-xs text-gray-500 mt-1">Nilai alat untuk keperluan inventaris</p>
                    @error('nilai_alat_utama')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tanggal Penting -->
            <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Tanggal Penting</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="tanggal_investasi" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Investasi *</label>
                    <input type="date" name="tanggal_investasi" id="tanggal_investasi" value="{{ old('tanggal_investasi') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <p class="text-xs text-gray-500 mt-1">Tanggal pembelian alat (untuk perhitungan ROI)</p>
                    @error('tanggal_investasi')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_deployment" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Deployment *</label>
                    <input type="date" name="tanggal_deployment" id="tanggal_deployment" value="{{ old('tanggal_deployment') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <p class="text-xs text-gray-500 mt-1">Tanggal alat dipasang dan mulai beroperasi</p>
                    @error('tanggal_deployment')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Periode Kontrak & Garansi -->
            <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Periode Kontrak & Garansi</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div>
                    <label for="durasi_kso_bulan" class="block text-sm font-medium text-gray-700 mb-2">Durasi Kontrak (Bulan) *</label>
                    <input type="number" name="durasi_kso_bulan" id="durasi_kso_bulan" value="{{ old('durasi_kso_bulan') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" min="1" required>
                    <p class="text-xs text-gray-500 mt-1">Otomatis menghitung tanggal berakhir</p>
                    @error('durasi_kso_bulan')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="garansi_berakhir" class="block text-sm font-medium text-gray-700 mb-2">Garansi Berakhir</label>
                    <input type="date" name="garansi_berakhir" id="garansi_berakhir" value="{{ old('garansi_berakhir') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('garansi_berakhir')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-end">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 w-full">
                        <p class="text-sm text-blue-700 font-medium">Kontrak Berakhir:</p>
                        <p class="text-sm text-blue-600" id="kontrak_berakhir_display">Akan dihitung otomatis</p>
                    </div>
                </div>
            </div>

            <!-- Location & PIC -->
            <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Lokasi & PIC</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="lokasi_penempatan" class="block text-sm font-medium text-gray-700 mb-2">Lokasi Penempatan</label>
                    <input type="text" name="lokasi_penempatan" id="lokasi_penempatan" value="{{ old('lokasi_penempatan') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('lokasi_penempatan')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center mt-6">
                        <input type="checkbox" name="butuh_komputer" id="butuh_komputer" value="1" {{ old('butuh_komputer') ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="butuh_komputer" class="ml-2 block text-sm text-gray-900">Membutuhkan komputer pendukung</label>
                    </div>
                </div>

                <div>
                    <label for="pic_customer" class="block text-sm font-medium text-gray-700 mb-2">PIC Customer</label>
                    <input type="text" name="pic_customer" id="pic_customer" value="{{ old('pic_customer') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('pic_customer')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pic_msa" class="block text-sm font-medium text-gray-700 mb-2">PIC MSA</label>
                    <input type="text" name="pic_msa" id="pic_msa" value="{{ old('pic_msa') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('pic_msa')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description & Technical Specifications -->
            <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Deskripsi & Spesifikasi</h3>
            <div class="grid grid-cols-1 gap-6 mb-8">
                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Keterangan umum tentang Alat Medis & Alat Komputer & Pendukung">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="spesifikasi_teknis" class="block text-sm font-medium text-gray-700 mb-2">Spesifikasi Teknis</label>
                    <textarea name="spesifikasi_teknis" id="spesifikasi_teknis" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Detail spesifikasi teknis alat">{{ old('spesifikasi_teknis') }}</textarea>
                    @error('spesifikasi_teknis')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Support Items -->
            <div id="supportItemsSection" class="mb-8" style="display: none;">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Item Pendukung</h3>
                    <button type="button" id="addSupportItem" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-plus mr-2"></i>Tambah Item
                    </button>
                </div>
                <div id="supportItemsContainer">
                    <!-- Support items will be added here dynamically -->
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('kso-roi.kso-items') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                    Simpan KSO Item
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Support Item Template -->
<template id="supportItemTemplate">
    <div class="support-item border border-gray-200 rounded-lg p-4">
        <div class="flex justify-between items-start mb-4">
            <h4 class="font-medium text-gray-900">Item Pendukung</h4>
            <button type="button" class="remove-support-item text-red-600 hover:text-red-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Item</label>
                <input type="text" name="support_items[][nama_item]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                <input type="text" name="support_items[][brand]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                <input type="text" name="support_items[][model]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Serial Number</label>
                <input type="text" name="support_items[][serial_number]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. Registrasi</label>
                <input type="text" name="support_items[][no_registrasi]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="support_items[][kategori]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Pilih Kategori</option>
                    <option value="komputer">Komputer</option>
                    <option value="monitor">Monitor</option>
                    <option value="ups">UPS</option>
                    <option value="printer">Printer</option>
                    <option value="keyboard">Keyboard</option>
                    <option value="mouse">Mouse</option>
                    <option value="network">Network</option>
                    <option value="storage">Storage</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                <input type="number" name="support_items[][jumlah]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" min="1" value="1">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Item (Rp)</label>
                <input type="number" name="support_items[][nilai_item]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" min="0" value="0" placeholder="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi</label>
                <select name="support_items[][kondisi]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="baik">Baik</option>
                    <option value="cukup">Cukup</option>
                    <option value="rusak ringan">Rusak Ringan</option>
                    <option value="rusak berat">Rusak Berat</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Install</label>
                <input type="date" name="support_items[][tanggal_install]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Penempatan</label>
                <input type="text" name="support_items[][lokasi_penempatan]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Garansi Berakhir</label>
                <input type="date" name="support_items[][garansi_berakhir]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="support_items[][status]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Periode KSO Mulai</label>
                <input type="date" name="support_items[][periode_kso_mulai]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Periode KSO Berakhir</label>
                <input type="date" name="support_items[][periode_kso_berakhir]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Spesifikasi</label>
            <textarea name="support_items[][spesifikasi]" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Detail spesifikasi item pendukung"></textarea>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addSupportItemBtn = document.getElementById('addSupportItem');
    const supportItemsContainer = document.getElementById('supportItemsContainer');
    // Add support item functionality
    document.getElementById('addSupportItem').addEventListener('click', function() {
        const template = document.getElementById('supportItemTemplate');
        const clone = template.content.cloneNode(true);
        
        // Get current index for proper naming
        const currentIndex = document.querySelectorAll('.support-item').length;
        
        // Update all name attributes with correct index
        const inputs = clone.querySelectorAll('input, select, textarea');
        inputs.forEach(function(input) {
            if (input.name) {
                input.name = input.name.replace('support_items[]', 'support_items[' + currentIndex + ']');
            }
        });
        
        document.getElementById('supportItemsContainer').appendChild(clone);
        
        // Add remove functionality to the new item
        const newItem = document.getElementById('supportItemsContainer').lastElementChild;
        newItem.querySelector('.remove-support-item').addEventListener('click', function() {
            newItem.remove();
        });
    });

    // Auto calculate contract end date
    function calculateContractEndDate() {
        const deploymentDate = document.getElementById('tanggal_deployment').value;
        const duration = document.getElementById('durasi_kso_bulan').value;
        const display = document.getElementById('kontrak_berakhir_display');
        
        if (deploymentDate && duration) {
            const startDate = new Date(deploymentDate);
            const endDate = new Date(startDate);
            endDate.setMonth(endDate.getMonth() + parseInt(duration));
            
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            display.textContent = endDate.toLocaleDateString('id-ID', options);
            display.classList.remove('text-blue-600');
            display.classList.add('text-green-600', 'font-medium');
        } else {
            display.textContent = 'Akan dihitung otomatis';
            display.classList.remove('text-green-600', 'font-medium');
            display.classList.add('text-blue-600');
        }
    }
    
    // Add event listeners for auto calculation
    document.getElementById('tanggal_deployment').addEventListener('change', calculateContractEndDate);
    document.getElementById('durasi_kso_bulan').addEventListener('input', calculateContractEndDate);

    // Handle checkbox "Membutuhkan komputer pendukung"
    const butuhKomputerCheckbox = document.getElementById('butuh_komputer');
    const supportItemsSection = document.getElementById('supportItemsSection');
    
    butuhKomputerCheckbox.addEventListener('change', function() {
        console.log('Checkbox changed:', this.checked);
        if (this.checked) {
            supportItemsSection.style.display = 'block';
            console.log('Support items section shown');
            // Auto add first support item when checkbox is checked
            if (document.querySelectorAll('.support-item').length === 0) {
                console.log('Adding first support item');
                document.getElementById('addSupportItem').click();
            }
        } else {
            supportItemsSection.style.display = 'none';
            console.log('Support items section hidden');
            // Clear all support items when unchecked
            const supportItems = document.querySelectorAll('.support-item');
            console.log('Clearing', supportItems.length, 'support items');
            supportItems.forEach(item => item.remove());
        }
    });

    // Check initial state
    if (butuhKomputerCheckbox.checked) {
        supportItemsSection.style.display = 'block';
    }

    // Handle form submission
    document.getElementById('ksoItemForm').addEventListener('submit', function(e) {
        console.log('Form submission started');
        
        // Only remove completely empty support items (all fields empty)
        const supportItems = document.querySelectorAll('.support-item');
        console.log('Found support items:', supportItems.length);
        
        let removedCount = 0;
        supportItems.forEach(function(item) {
            const namaItem = item.querySelector('input[name*="nama_item"]').value;
            const brand = item.querySelector('input[name*="brand"]').value;
            const model = item.querySelector('input[name*="model"]').value;
            
            console.log('Support item data:', {namaItem, brand, model});
            
            // Only remove if ALL main fields are empty
            if (!namaItem.trim() && !brand.trim() && !model.trim()) {
                console.log('Removing empty support item');
                item.remove();
                removedCount++;
            }
        });
        
        console.log('Removed', removedCount, 'empty support items');
        console.log('Remaining support items:', document.querySelectorAll('.support-item').length);
    });
});
</script>
@endsection
