@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Detail ROI Customer</h1>
            <p class="text-gray-600">{{ $customer->name }}</p>
        </div>
        <a href="{{ route('kso-roi.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    <!-- Customer Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Informasi Dasar Customer -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Informasi Dasar Customer
            </h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-500">Nama Customer</p>
                    <p class="text-gray-900 font-medium">{{ $customer->name }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Kode Customer</p>
                    <p class="text-gray-900">{{ $customer->code ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Tipe Customer</p>
                    <p class="text-gray-900">{{ $customer->type ?? 'Regular' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Status</p>
                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $customer->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($customer->status ?? 'active') }}
                    </span>
                </div>
                @if($customer->tax_number)
                <div>
                    <p class="text-sm font-medium text-gray-500">NPWP</p>
                    <p class="text-gray-900">{{ $customer->tax_number }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Alamat & Kontak Customer -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Alamat & Kontak
            </h2>
            <div class="space-y-3">
                @if($customer->address)
                <div>
                    <p class="text-sm font-medium text-gray-500">Alamat</p>
                    <p class="text-gray-900">{{ $customer->address }}</p>
                </div>
                @endif
                @if($customer->city)
                <div>
                    <p class="text-sm font-medium text-gray-500">Kota</p>
                    <p class="text-gray-900">{{ $customer->city }}</p>
                </div>
                @endif
                @if($customer->phone)
                <div>
                    <p class="text-sm font-medium text-gray-500">Telepon</p>
                    <p class="text-gray-900">{{ $customer->phone }}</p>
                </div>
                @endif
                @if($customer->email)
                <div>
                    <p class="text-sm font-medium text-gray-500">Email</p>
                    <p class="text-gray-900">{{ $customer->email }}</p>
                </div>
                @endif
                @if($customer->contact_person)
                <div>
                    <p class="text-sm font-medium text-gray-500">Contact Person</p>
                    <p class="text-gray-900">{{ $customer->contact_person }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Informasi KSO -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Informasi KSO
            </h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-500">Jumlah KSO Items</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $customer->ksoItems->count() }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">KSO Items Aktif</p>
                    <p class="text-xl font-semibold text-green-600">{{ $customer->ksoItems->where('status', 'active')->count() }}</p>
                </div>
                @if($customer->ksoItems->count() > 0)
                <div>
                    <p class="text-sm font-medium text-gray-500">Tanggal KSO Pertama</p>
                    <p class="text-gray-900">{{ $customer->ksoItems->min('tanggal_investasi')?->format('d/m/Y') ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">KSO Terbaru</p>
                    <p class="text-gray-900">{{ $customer->ksoItems->max('tanggal_investasi')?->format('d/m/Y') ?? '-' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ROI Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Investment -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Investasi KSO</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($totalInvestment, 0, ',', '.') }}</p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Sales -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Penjualan WMS</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- ROI Percentage -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">ROI Percentage</p>
                    <p class="text-2xl font-bold">{{ number_format($roiPercentage, 1) }}%</p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- ROI Status -->
        <div class="bg-gradient-to-r {{ $roiStatus === 'ROI' ? 'from-emerald-500 to-emerald-600' : 'from-red-500 to-red-600' }} rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white text-opacity-90 text-sm font-medium">Status ROI</p>
                    <p class="text-2xl font-bold">{{ $roiStatus }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    @if($roiStatus === 'ROI')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @else
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- KSO Items -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">KSO Items</h2>
        <div class="space-y-4">
            @forelse($customer->ksoItems as $item)
                    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-4 shadow-sm">
                        <!-- Header with Equipment Name and Status -->
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $item->nama_alat }}</h3>
                                @if($item->kategori)
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                        {{ $item->category_display }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <span class="px-3 py-2 rounded-full text-sm font-medium {{ $item->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                                @if($item->nilai_sewa_bulanan)
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-green-600">Rp {{ number_format($item->nilai_sewa_bulanan, 0, ',', '.') }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->kondisi ?? 'excellent' }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- QR Code Section -->
                        <div class="mb-4 p-4 bg-purple-50 rounded-lg border border-purple-200">
                            <button class="w-full text-left focus:outline-none" onclick="toggleQrCode({{ $item->id }})">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        <span class="font-medium text-purple-900">QR Code untuk Alat Ini</span>
                                    </div>
                                    <svg id="qr-chevron-{{ $item->id }}" class="w-5 h-5 text-purple-600 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </button>
                            
                            <div id="qr-code-{{ $item->id }}" class="hidden mt-4 pt-4 border-t border-purple-200">
                                <div class="flex flex-col items-center gap-4">
                                    <!-- QR Code Image -->
                                    <div class="p-4 bg-white rounded-lg border-2 border-purple-300">
                                        <img 
                                            src="{{ \App\Helpers\QrCodeHelper::generateKsoItemQrCode($item->id, 250) }}" 
                                            alt="QR Code - {{ $item->nama_alat }}"
                                            class="w-64 h-64"
                                        />
                                    </div>
                                    
                                    <!-- QR Code Info -->
                                    <div class="w-full text-center">
                                        <p class="text-sm text-gray-600 mb-4">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Scan QR Code dengan smartphone untuk melihat detail lengkap
                                        </p>

                                        <!-- Manual Access Code -->
                                        <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                            <p class="text-xs text-gray-600 mb-2">
                                                <i class="fas fa-keyboard mr-1"></i>
                                                Atau gunakan kode akses manual:
                                            </p>
                                            <div class="flex gap-2 items-center justify-center">
                                                <input 
                                                    type="text" 
                                                    value="{{ route('qr.verify', $item->unique_id, false) }}" 
                                                    readonly 
                                                    class="flex-1 px-3 py-2 text-xs bg-white border border-blue-300 rounded-lg font-mono text-blue-600"
                                                    id="manual-code-{{ $item->id }}"
                                                />
                                                <button 
                                                    onclick="copyManualCode({{ $item->id }})"
                                                    class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-xs font-medium"
                                                >
                                                    <i class="fas fa-copy mr-1"></i>Salin
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Action Buttons -->
                                        <div class="flex gap-2 justify-center flex-wrap">
                                            <a 
                                                href="{{ route('qr.verify', $item->unique_id) }}" 
                                                target="_blank"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm font-medium"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                                Buka
                                            </a>
                                            
                                            <button 
                                                onclick="downloadQrCode({{ $item->id }}, '{{ $item->nama_alat }}')"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                </svg>
                                                Download
                                            </button>
                                            
                                            <button 
                                                onclick="copyQrUrl({{ $item->id }})"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                                Salin URL
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Equipment Details in Two Columns -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <!-- Left Column -->
                            <div class="space-y-3">
                                @if($item->brand && $item->model)
                                    <div class="flex">
                                        <span class="text-sm font-medium text-gray-500 w-24">Brand/Model:</span>
                                        <span class="text-sm text-blue-600 font-medium">{{ $item->brand }} - {{ $item->model }}</span>
                                    </div>
                                @endif
                                @if($item->serial_number)
                                    <div class="flex">
                                        <span class="text-sm font-medium text-gray-500 w-24">Serial Number:</span>
                                        <span class="text-sm text-gray-900">{{ $item->serial_number }}</span>
                                    </div>
                                @endif
                                @if($item->no_registrasi)
                                    <div class="flex">
                                        <span class="text-sm font-medium text-gray-500 w-24">No. Registrasi:</span>
                                        <span class="text-sm text-gray-900">{{ $item->no_registrasi }}</span>
                                    </div>
                                @endif
                                @if($item->tanggal_install)
                                    <div class="flex">
                                        <span class="text-sm font-medium text-gray-500 w-24">Tanggal Install:</span>
                                        <span class="text-sm text-gray-900">{{ $item->tanggal_install->format('d M Y') }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-3">
                                @if($item->kategori)
                                    <div class="flex">
                                        <span class="text-sm font-medium text-gray-500 w-24">Kategori:</span>
                                        <span class="text-sm text-gray-900">{{ $item->category_display }}</span>
                                    </div>
                                @endif
                                @if($item->garansi_berakhir)
                                    <div class="flex">
                                        <span class="text-sm font-medium text-gray-500 w-24">Garansi s/d:</span>
                                        <span class="text-sm {{ $item->warranty_status_color }}">{{ $item->garansi_berakhir->format('d M Y') }} ({{ $item->warranty_status }})</span>
                                    </div>
                                @endif
                                @if($item->periode_kso_mulai && $item->periode_kso_berakhir)
                                    <div class="flex">
                                        <span class="text-sm font-medium text-gray-500 w-24">Periode KSO:</span>
                                        <div class="text-sm">
                                            <div class="text-gray-900">{{ $item->periode_kso_mulai->format('d M Y') }} - {{ $item->periode_kso_berakhir->format('d M Y') }}</div>
                                            <div class="{{ $item->kso_period_status_color }} text-xs">{{ $item->kso_period_status }}@if($item->durasi_kso_bulan) ({{ $item->durasi_kso_bulan }} bulan)@endif</div>
                                        </div>
                                    </div>
                                @elseif($item->periode_kso_berakhir)
                                    <div class="flex">
                                        <span class="text-sm font-medium text-gray-500 w-24">Periode KSO:</span>
                                        <span class="text-sm {{ $item->kso_period_status_color }}">{{ $item->periode_kso_berakhir->format('d M Y') }} ({{ $item->kso_period_status }})</span>
                                    </div>
                                @endif
                                @if($item->lokasi_penempatan)
                                    <div class="flex items-start">
                                        <span class="text-sm font-medium text-gray-500 w-24 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            Ruangan:
                                        </span>
                                        <span class="text-sm text-gray-900">{{ $item->lokasi_penempatan }}</span>
                                    </div>
                                @endif
                                @if($item->no_registrasi)
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-500 w-24"></span>
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h4"></path>
                                            </svg>
                                            QR-{{ strtoupper(substr($item->no_registrasi, -6)) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Support Items (Alat Komputer & Pendukung) -->
                        @if($item->supportItems && $item->supportItems->count() > 0)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <button class="w-full text-left focus:outline-none" onclick="toggleSupportItems({{ $item->id }})">
                                    <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center justify-between hover:text-blue-600 transition-colors">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 002 2z"></path>
                                            </svg>
                                            Alat Komputer & Pendukung ({{ $item->supportItems->sum('jumlah') }} units)
                                        </div>
                                        <svg id="chevron-{{ $item->id }}" class="w-4 h-4 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </h4>
                                </button>
                                
                                <div id="support-items-{{ $item->id }}" class="hidden">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($item->supportItems as $support)
                                            @for($i = 1; $i <= $support->jumlah; $i++)
                                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                                    <!-- Header with Equipment Name and Status -->
                                                    <div class="flex justify-between items-start mb-3">
                                                        <div class="flex-1">
                                                            <h5 class="text-sm font-medium text-gray-900 mb-1">{{ $support->nama_item }}</h5>
                                                            @if($support->kategori)
                                                                <span class="inline-block px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                                                    {{ $support->category_display }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="text-right ml-3">
                                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">
                                                                {{ ucfirst($support->status ?? 'active') }}
                                                            </span>
                                                            <div class="text-right mt-1">
                                                                <p class="text-sm font-bold text-green-600">Rp {{ number_format($support->nilai_item, 0, ',', '.') }}</p>
                                                                <p class="text-xs text-gray-500">{{ $support->kondisi ?? 'excellent' }}</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Equipment Details in Two Columns -->
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                                        <!-- Left Column -->
                                                        <div class="space-y-2">
                                                            @if($support->brand && $support->model)
                                                                <div class="flex">
                                                                    <span class="text-xs font-medium text-gray-500 w-20">Brand/Model:</span>
                                                                    <span class="text-xs text-blue-600 font-medium">{{ $support->brand }} - {{ $support->model }}</span>
                                                                </div>
                                                            @endif
                                                            @if($support->serial_number)
                                                                <div class="flex">
                                                                    <span class="text-xs font-medium text-gray-500 w-20">Serial Number:</span>
                                                                    <span class="text-xs text-gray-900">{{ $support->serial_number }}</span>
                                                                </div>
                                                            @endif
                                                            @if($support->no_registrasi)
                                                                <div class="flex">
                                                                    <span class="text-xs font-medium text-gray-500 w-20">No. Registrasi:</span>
                                                                    <span class="text-xs text-gray-900">{{ $support->no_registrasi }}</span>
                                                                </div>
                                                            @endif
                                                            @if($support->tanggal_install)
                                                                <div class="flex">
                                                                    <span class="text-xs font-medium text-gray-500 w-20">Tanggal Install:</span>
                                                                    <span class="text-xs text-gray-900">{{ $support->tanggal_install->format('d M Y') }}</span>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- Right Column -->
                                                        <div class="space-y-2">
                                                            @if($support->kategori)
                                                                <div class="flex">
                                                                    <span class="text-xs font-medium text-gray-500 w-20">Kategori:</span>
                                                                    <span class="text-xs text-gray-900">{{ $support->category_display }}</span>
                                                                </div>
                                                            @endif
                                                            @if($support->garansi_berakhir)
                                                                <div class="flex">
                                                                    <span class="text-xs font-medium text-gray-500 w-20">Garansi s/d:</span>
                                                                    <span class="text-xs {{ $support->warranty_status_color }}">{{ $support->garansi_berakhir->format('d M Y') }} ({{ $support->warranty_status }})</span>
                                                                </div>
                                                            @endif
                                                            @if($support->periode_kso_mulai && $support->periode_kso_berakhir)
                                                                <div class="flex">
                                                                    <span class="text-xs font-medium text-gray-500 w-20">Periode KSO:</span>
                                                                    <div class="text-xs">
                                                                        <div class="text-gray-900">{{ $support->periode_kso_mulai->format('d M Y') }} - {{ $support->periode_kso_berakhir->format('d M Y') }}</div>
                                                                        <div class="{{ $support->kso_period_status_color }} text-xs">{{ $support->kso_period_status }}@if($support->durasi_kso_bulan) ({{ $support->durasi_kso_bulan }} bulan)@endif</div>
                                                                    </div>
                                                                </div>
                                                            @elseif($support->periode_kso_berakhir)
                                                                <div class="flex">
                                                                    <span class="text-xs font-medium text-gray-500 w-20">Periode KSO:</span>
                                                                    <span class="text-xs {{ $support->kso_period_status_color }}">{{ $support->periode_kso_berakhir->format('d M Y') }} ({{ $support->kso_period_status }})</span>
                                                                </div>
                                                            @endif
                                                            @if($support->lokasi_penempatan)
                                                                <div class="flex items-start">
                                                                    <span class="text-xs font-medium text-gray-500 w-20 flex items-center">
                                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                        </svg>
                                                                        Ruangan:
                                                                    </span>
                                                                    <span class="text-xs text-gray-900">{{ $support->lokasi_penempatan }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    @if($support->spesifikasi)
                                                        <div class="pt-2 border-t border-gray-200">
                                                            <p class="text-xs text-gray-600"><strong>Spesifikasi:</strong> {{ $support->spesifikasi }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endfor
                                        @endforeach
                                    </div>
                                    
                                    <!-- Support Items Summary -->
                                    <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium text-blue-900">Total Alat Pendukung:</span>
                                            <span class="text-sm font-bold text-blue-900">Rp {{ number_format($item->total_pendukung, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <p>Belum ada KSO items untuk customer ini</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Product Purchase Chart -->
    <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Grafik Pembelian Produk per Bulan</h3>
                <p class="text-sm text-gray-600">Jumlah produk yang dibeli setiap bulan dalam tahun {{ $selectedYear }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <label for="yearSelect" class="text-sm font-medium text-gray-700">Tahun:</label>
                <select id="yearSelect" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="relative" style="height: 400px;">
            <canvas id="customerProductChart"></canvas>
        </div>
    </div>

    <!-- Monthly Sales Chart -->
    @if($monthlySales->count() > 0)
        <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Trend Penjualan Bulanan</h2>
            <div class="h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Product Purchase Chart
            const productCtx = document.getElementById('customerProductChart').getContext('2d');
            const chartData = @json($chartData);
            
            const productChart = new Chart(productCtx, {
                type: 'line',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Pembelian Produk per Bulan - {{ $customer->name }} ({{ $selectedYear }})',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah Produk'
                            },
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Bulan'
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    elements: {
                        point: {
                            radius: 4,
                            hoverRadius: 6
                        }
                    }
                }
            });

            // Year selection change handler
            document.getElementById('yearSelect').addEventListener('change', function() {
                const selectedYear = this.value;
                const currentUrl = new URL(window.location);
                currentUrl.searchParams.set('year', selectedYear);
                window.location.href = currentUrl.toString();
            });

            // Monthly Sales Chart
            const ctx = document.getElementById('salesChart').getContext('2d');
            const monthlySales = @json($monthlySales->reverse()->values());
            
            const labels = monthlySales.map(item => {
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                return months[item.month - 1] + ' ' + item.year;
            });
            
            const data = monthlySales.map(item => item.total);
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Penjualan Bulanan',
                        data: data,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Penjualan: Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });

        });
        </script>
    @endif

    <script>
        // Support Items Dropdown Toggle Function
        window.toggleSupportItems = function(itemId) {
            const content = document.getElementById('support-items-' + itemId);
            const chevron = document.getElementById('chevron-' + itemId);
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                chevron.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                chevron.style.transform = 'rotate(0deg)';
            }
        };

        // QR Code Toggle Function
        window.toggleQrCode = function(itemId) {
            const content = document.getElementById('qr-code-' + itemId);
            const chevron = document.getElementById('qr-chevron-' + itemId);
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                chevron.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                chevron.style.transform = 'rotate(0deg)';
            }
        };

        // Download QR Code Function
        window.downloadQrCode = function(itemId, itemName) {
            const qrImage = document.querySelector(`#qr-code-${itemId} img`);
            if (!qrImage) return;
            
            const link = document.createElement('a');
            link.href = qrImage.src;
            link.download = `QR-${itemName.replace(/\s+/g, '-')}-${itemId}.png`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };

        // Copy QR URL Function
        window.copyQrUrl = function(itemId) {
            const url = `{{ config('app.url') }}/qr/kso/${itemId}/password`;
            navigator.clipboard.writeText(url).then(() => {
                alert('URL QR Code berhasil disalin ke clipboard!');
            }).catch(err => {
                console.error('Gagal menyalin:', err);
                alert('Gagal menyalin URL');
            });
        };

        // Copy Manual Code Function
        window.copyManualCode = function(itemId) {
            const input = document.getElementById('manual-code-' + itemId);
            if (!input) return;
            
            input.select();
            navigator.clipboard.writeText(input.value).then(() => {
                alert('Kode akses manual berhasil disalin ke clipboard!');
            }).catch(err => {
                console.error('Gagal menyalin:', err);
                alert('Gagal menyalin kode akses');
            });
        };
    </script>
</div>
@endsection
