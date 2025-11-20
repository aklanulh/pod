@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit KSO Item</h1>
            <p class="text-gray-600">Edit Alat Medis & Alat Komputer & Pendukung: {{ $ksoItem->nama_alat }}</p>
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
        <form action="{{ route('kso-roi.update-kso-item', $ksoItem) }}" method="POST" id="ksoItemForm">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Informasi Dasar</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">Customer *</label>
                    <select name="customer_id" id="customer_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <option value="">Pilih Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ (old('customer_id', $ksoItem->customer_id) == $customer->id) ? 'selected' : '' }}>
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
                    <input type="text" name="nama_alat" id="nama_alat" value="{{ old('nama_alat', $ksoItem->nama_alat) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    @error('nama_alat')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="brand" class="block text-sm font-medium text-gray-700 mb-2">Brand/Merk</label>
                    <input type="text" name="brand" id="brand" value="{{ old('brand', $ksoItem->brand) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('brand')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="model" class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                    <input type="text" name="model" id="model" value="{{ old('model', $ksoItem->model) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('model')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-2">Serial Number</label>
                    <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number', $ksoItem->serial_number) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('serial_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_registrasi" class="block text-sm font-medium text-gray-700 mb-2">No. Registrasi</label>
                    <input type="text" name="no_registrasi" id="no_registrasi" value="{{ old('no_registrasi', $ksoItem->no_registrasi) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('no_registrasi')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <input type="text" name="kategori" id="kategori" value="{{ old('kategori', $ksoItem->kategori) }}" list="kategori-list" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Pilih atau ketik kategori">
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
                        <option value="baik" {{ old('kondisi', $ksoItem->kondisi) == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak ringan" {{ old('kondisi', $ksoItem->kondisi) == 'rusak ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="rusak berat" {{ old('kondisi', $ksoItem->kondisi) == 'rusak berat' ? 'selected' : '' }}>Rusak Berat</option>
                        <option value="maintenance" {{ old('kondisi', $ksoItem->kondisi) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
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
                    <input type="number" name="nilai_alat_utama" id="nilai_alat_utama" value="{{ old('nilai_alat_utama', $ksoItem->nilai_alat_utama) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" min="0" step="0.01" required>
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
                    <input type="date" name="tanggal_investasi" id="tanggal_investasi" value="{{ old('tanggal_investasi', $ksoItem->tanggal_investasi?->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <p class="text-xs text-gray-500 mt-1">Tanggal pembelian alat (untuk perhitungan ROI)</p>
                    @error('tanggal_investasi')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_deployment" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Deployment *</label>
                    <input type="date" name="tanggal_deployment" id="tanggal_deployment" value="{{ old('tanggal_deployment', $ksoItem->tanggal_install?->format('Y-m-d') ?? $ksoItem->periode_kso_mulai?->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
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
                    <input type="number" name="durasi_kso_bulan" id="durasi_kso_bulan" value="{{ old('durasi_kso_bulan', $ksoItem->durasi_kso_bulan) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" min="1" required>
                    <p class="text-xs text-gray-500 mt-1">Otomatis menghitung tanggal berakhir</p>
                    @error('durasi_kso_bulan')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="garansi_berakhir" class="block text-sm font-medium text-gray-700 mb-2">Garansi Berakhir</label>
                    <input type="date" name="garansi_berakhir" id="garansi_berakhir" value="{{ old('garansi_berakhir', $ksoItem->garansi_berakhir?->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('garansi_berakhir')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-end">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 w-full">
                        <p class="text-sm text-blue-700 font-medium">Kontrak Berakhir:</p>
                        <p class="text-sm text-blue-600" id="kontrak_berakhir_display">
                            @if($ksoItem->periode_kso_berakhir)
                                {{ $ksoItem->periode_kso_berakhir->format('d F Y') }}
                            @else
                                Akan dihitung otomatis
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Location & PIC -->
            <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Lokasi & PIC</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="lokasi_penempatan" class="block text-sm font-medium text-gray-700 mb-2">Lokasi Penempatan</label>
                    <input type="text" name="lokasi_penempatan" id="lokasi_penempatan" value="{{ old('lokasi_penempatan', $ksoItem->lokasi_penempatan) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('lokasi_penempatan')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pic_customer" class="block text-sm font-medium text-gray-700 mb-2">PIC Customer</label>
                    <input type="text" name="pic_customer" id="pic_customer" value="{{ old('pic_customer', $ksoItem->pic_customer) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('pic_customer')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pic_msa" class="block text-sm font-medium text-gray-700 mb-2">PIC MSA</label>
                    <input type="text" name="pic_msa" id="pic_msa" value="{{ old('pic_msa', $ksoItem->pic_msa) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                    <textarea name="keterangan" id="keterangan" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Keterangan umum tentang Alat Medis & Alat Komputer & Pendukung">{{ old('keterangan', $ksoItem->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="spesifikasi_teknis" class="block text-sm font-medium text-gray-700 mb-2">Spesifikasi Teknis</label>
                    <textarea name="spesifikasi_teknis" id="spesifikasi_teknis" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Detail spesifikasi teknis alat">{{ old('spesifikasi_teknis', $ksoItem->spesifikasi_teknis) }}</textarea>
                    @error('spesifikasi_teknis')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Status -->
            <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" id="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <option value="active" {{ old('status', $ksoItem->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $ksoItem->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center mt-6">
                        <input type="checkbox" name="butuh_komputer" id="butuh_komputer" value="1" {{ old('butuh_komputer', $ksoItem->butuh_komputer) || $ksoItem->supportItems->count() > 0 ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="butuh_komputer" class="ml-2 block text-sm text-gray-900">Membutuhkan komputer pendukung</label>
                    </div>
                </div>
            </div>

            <!-- Support Items Section -->
            <div id="supportItemsSection" class="mb-8" style="display: {{ old('butuh_komputer', $ksoItem->butuh_komputer) || $ksoItem->supportItems->count() > 0 ? 'block' : 'none' }};">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Item Pendukung</h3>
                    <button type="button" id="addSupportItem" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-plus mr-2"></i>Tambah Item
                    </button>
                </div>
                
                <div id="supportItemsContainer" class="space-y-4">
                    @foreach($ksoItem->supportItems as $supportItem)
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
                                    <input type="text" name="support_items[{{ $loop->index }}][nama_item]" value="{{ $supportItem->nama_item }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                                    <input type="text" name="support_items[{{ $loop->index }}][brand]" value="{{ $supportItem->brand }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                                    <input type="text" name="support_items[{{ $loop->index }}][model]" value="{{ $supportItem->model }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Serial Number</label>
                                    <input type="text" name="support_items[{{ $loop->index }}][serial_number]" value="{{ $supportItem->serial_number }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Registrasi</label>
                                    <input type="text" name="support_items[{{ $loop->index }}][no_registrasi]" value="{{ $supportItem->no_registrasi }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                    <select name="support_items[{{ $loop->index }}][kategori]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Pilih Kategori</option>
                                        <option value="komputer" {{ $supportItem->kategori == 'komputer' ? 'selected' : '' }}>Komputer</option>
                                        <option value="monitor" {{ $supportItem->kategori == 'monitor' ? 'selected' : '' }}>Monitor</option>
                                        <option value="ups" {{ $supportItem->kategori == 'ups' ? 'selected' : '' }}>UPS</option>
                                        <option value="printer" {{ $supportItem->kategori == 'printer' ? 'selected' : '' }}>Printer</option>
                                        <option value="keyboard" {{ $supportItem->kategori == 'keyboard' ? 'selected' : '' }}>Keyboard</option>
                                        <option value="mouse" {{ $supportItem->kategori == 'mouse' ? 'selected' : '' }}>Mouse</option>
                                        <option value="network" {{ $supportItem->kategori == 'network' ? 'selected' : '' }}>Network</option>
                                        <option value="storage" {{ $supportItem->kategori == 'storage' ? 'selected' : '' }}>Storage</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                                    <input type="number" name="support_items[{{ $loop->index }}][jumlah]" value="{{ $supportItem->jumlah }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" min="1">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Item (Rp)</label>
                                    <input type="number" name="support_items[{{ $loop->index }}][nilai_item]" value="{{ $supportItem->nilai_item ?? 0 }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" min="0" placeholder="0">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi</label>
                                    <select name="support_items[{{ $loop->index }}][kondisi]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="baik" {{ $supportItem->kondisi == 'baik' ? 'selected' : '' }}>Baik</option>
                                        <option value="cukup" {{ $supportItem->kondisi == 'cukup' ? 'selected' : '' }}>Cukup</option>
                                        <option value="rusak ringan" {{ $supportItem->kondisi == 'rusak ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                        <option value="rusak berat" {{ $supportItem->kondisi == 'rusak berat' ? 'selected' : '' }}>Rusak Berat</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Install</label>
                                    <input type="date" name="support_items[{{ $loop->index }}][tanggal_install]" value="{{ $supportItem->tanggal_install?->format('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Penempatan</label>
                                    <input type="text" name="support_items[{{ $loop->index }}][lokasi_penempatan]" value="{{ $supportItem->lokasi_penempatan }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Garansi Berakhir</label>
                                    <input type="date" name="support_items[{{ $loop->index }}][garansi_berakhir]" value="{{ $supportItem->garansi_berakhir?->format('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select name="support_items[{{ $loop->index }}][status]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="active" {{ $supportItem->status == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $supportItem->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="maintenance" {{ $supportItem->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode KSO Mulai</label>
                                    <input type="date" name="support_items[{{ $loop->index }}][periode_kso_mulai]" value="{{ $supportItem->periode_kso_mulai?->format('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode KSO Berakhir</label>
                                    <input type="date" name="support_items[{{ $loop->index }}][periode_kso_berakhir]" value="{{ $supportItem->periode_kso_berakhir?->format('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Spesifikasi</label>
                                <textarea name="support_items[{{ $loop->index }}][spesifikasi]" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Detail spesifikasi item pendukung">{{ $supportItem->spesifikasi }}</textarea>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('kso-roi.kso-items') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                    Update KSO Item
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
    const supportItemTemplate = document.getElementById('supportItemTemplate');

    // Add event listeners to existing remove buttons
    document.querySelectorAll('.remove-support-item').forEach(function(btn) {
        btn.addEventListener('click', function() {
            this.closest('.support-item').remove();
        });
    });

    addSupportItemBtn.addEventListener('click', function() {
        const template = supportItemTemplate.content.cloneNode(true);
        
        // Get current index for proper naming
        const currentIndex = document.querySelectorAll('.support-item').length;
        console.log('Adding support item with index:', currentIndex);
        
        // Update all name attributes with correct index
        const inputs = template.querySelectorAll('input, select, textarea');
        inputs.forEach(function(input) {
            if (input.name) {
                const oldName = input.name;
                input.name = input.name.replace('support_items[]', 'support_items[' + currentIndex + ']');
                console.log('Updated name from', oldName, 'to', input.name);
            }
        });
        
        supportItemsContainer.appendChild(template);
        
        // Add event listener to remove button
        const removeBtn = supportItemsContainer.lastElementChild.querySelector('.remove-support-item');
        removeBtn.addEventListener('click', function() {
            this.closest('.support-item').remove();
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
            // Keep existing date if available
            if (!display.textContent || display.textContent === 'Akan dihitung otomatis') {
                display.textContent = 'Akan dihitung otomatis';
                display.classList.remove('text-green-600', 'font-medium');
                display.classList.add('text-blue-600');
            }
        }
    }
    
    // Add event listeners for auto calculation
    document.getElementById('tanggal_deployment').addEventListener('change', calculateContractEndDate);
    document.getElementById('durasi_kso_bulan').addEventListener('input', calculateContractEndDate);

    // Handle checkbox "Membutuhkan komputer pendukung"
    const butuhKomputerCheckbox = document.getElementById('butuh_komputer');
    const supportItemsSection = document.getElementById('supportItemsSection');
    
    butuhKomputerCheckbox.addEventListener('change', function() {
        if (this.checked) {
            supportItemsSection.style.display = 'block';
            // Auto add first support item when checkbox is checked and no items exist
            if (document.querySelectorAll('.support-item').length === 0) {
                document.getElementById('addSupportItem').click();
            }
        } else {
            supportItemsSection.style.display = 'none';
            // Clear all support items when unchecked
            const supportItems = document.querySelectorAll('.support-item');
            supportItems.forEach(item => item.remove());
        }
    });

    // Handle form submission
    document.getElementById('ksoItemForm').addEventListener('submit', function(e) {
        console.log('Form submission started (edit)');
        
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
