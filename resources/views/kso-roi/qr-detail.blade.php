<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail KSO Item</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-4xl mx-auto">
            @if(isset($error))
                <!-- Error State -->
                <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Data Tidak Ditemukan</h1>
                    <p class="text-gray-600 mb-6">{{ $error }}</p>
                    <a href="javascript:history.back()" class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-arrow-left"></i>
                        Kembali
                    </a>
                </div>
            @else
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                                <i class="fas fa-qrcode text-blue-600"></i>
                                Detail KSO Item
                            </h1>
                            <p class="text-gray-600 mt-2">Informasi lengkap peralatan KSO</p>
                        </div>
                        <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-print"></i>
                            Cetak
                        </button>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Main Info -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Peralatan Utama Card -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                                    <i class="fas fa-tools"></i>
                                    Peralatan Utama
                                </h2>
                            </div>
                            <div class="p-6 space-y-4">
                                <!-- Nama Alat -->
                                <div class="border-b pb-4">
                                    <p class="text-sm text-gray-600 font-medium mb-1">Nama Peralatan</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $ksoItem->nama_alat }}</p>
                                </div>

                                <!-- Grid Info -->
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Brand -->
                                    <div>
                                        <p class="text-sm text-gray-600 font-medium mb-1">
                                            <i class="fas fa-tag text-gray-500 mr-2"></i>Brand
                                        </p>
                                        <p class="text-gray-900 font-semibold">{{ $ksoItem->brand ?? '-' }}</p>
                                    </div>

                                    <!-- Model -->
                                    <div>
                                        <p class="text-sm text-gray-600 font-medium mb-1">
                                            <i class="fas fa-cube text-gray-500 mr-2"></i>Model
                                        </p>
                                        <p class="text-gray-900 font-semibold">{{ $ksoItem->model ?? '-' }}</p>
                                    </div>

                                    <!-- Serial Number -->
                                    <div>
                                        <p class="text-sm text-gray-600 font-medium mb-1">
                                            <i class="fas fa-barcode text-gray-500 mr-2"></i>Serial Number
                                        </p>
                                        <p class="text-gray-900 font-semibold">{{ $ksoItem->serial_number ?? '-' }}</p>
                                    </div>

                                    <!-- No. Registrasi -->
                                    <div>
                                        <p class="text-sm text-gray-600 font-medium mb-1">
                                            <i class="fas fa-file-alt text-gray-500 mr-2"></i>No. Registrasi
                                        </p>
                                        <p class="text-gray-900 font-semibold">{{ $ksoItem->no_registrasi ?? '-' }}</p>
                                    </div>

                                    <!-- Kategori -->
                                    <div>
                                        <p class="text-sm text-gray-600 font-medium mb-1">
                                            <i class="fas fa-folder text-gray-500 mr-2"></i>Kategori
                                        </p>
                                        <p class="text-gray-900 font-semibold">{{ $ksoItem->kategori ?? '-' }}</p>
                                    </div>

                                    <!-- Kondisi -->
                                    <div>
                                        <p class="text-sm text-gray-600 font-medium mb-1">
                                            <i class="fas fa-heartbeat text-gray-500 mr-2"></i>Kondisi
                                        </p>
                                        <div class="inline-block">
                                            @if($ksoItem->kondisi === 'baik')
                                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                                    <i class="fas fa-check-circle mr-1"></i>Baik
                                                </span>
                                            @elseif($ksoItem->kondisi === 'rusak')
                                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                                                    <i class="fas fa-times-circle mr-1"></i>Rusak
                                                </span>
                                            @else
                                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $ksoItem->kondisi ?? '-' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Support Items -->
                        @if($ksoItem->supportItems->count() > 0)
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-cyan-600 to-blue-600 px-6 py-4">
                                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                                    <i class="fas fa-laptop"></i>
                                    Peralatan Pendukung ({{ $ksoItem->supportItems->count() }})
                                </h2>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    @foreach($ksoItem->supportItems as $support)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                        <div class="flex items-start justify-between mb-3">
                                            <div>
                                                <h3 class="font-bold text-gray-900 text-lg">{{ $support->nama_item }}</h3>
                                                <p class="text-sm text-gray-600">
                                                    <i class="fas fa-cube text-gray-500 mr-1"></i>
                                                    {{ $support->brand ?? '-' }} {{ $support->model ?? '' }}
                                                </p>
                                            </div>
                                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                                                Qty: {{ $support->jumlah }}
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3 text-sm">
                                            @if($support->serial_number)
                                            <div>
                                                <p class="text-gray-600">Serial Number</p>
                                                <p class="font-semibold text-gray-900">{{ $support->serial_number }}</p>
                                            </div>
                                            @endif

                                            @if($support->no_registrasi)
                                            <div>
                                                <p class="text-gray-600">No. Registrasi</p>
                                                <p class="font-semibold text-gray-900">{{ $support->no_registrasi }}</p>
                                            </div>
                                            @endif

                                            @if($support->kategori)
                                            <div>
                                                <p class="text-gray-600">Kategori</p>
                                                <p class="font-semibold text-gray-900">{{ $support->kategori }}</p>
                                            </div>
                                            @endif

                                            @if($support->kondisi)
                                            <div>
                                                <p class="text-gray-600">Kondisi</p>
                                                <p class="font-semibold text-gray-900">{{ $support->kondisi }}</p>
                                            </div>
                                            @endif

                                            @if($support->tanggal_install)
                                            <div>
                                                <p class="text-gray-600">Tanggal Install</p>
                                                <p class="font-semibold text-gray-900">{{ $support->tanggal_install->format('d M Y') }}</p>
                                            </div>
                                            @endif

                                            @if($support->lokasi_penempatan)
                                            <div>
                                                <p class="text-gray-600">Lokasi</p>
                                                <p class="font-semibold text-gray-900">{{ $support->lokasi_penempatan }}</p>
                                            </div>
                                            @endif

                                            @if($support->garansi_berakhir)
                                            <div>
                                                <p class="text-gray-600">Garansi Berakhir</p>
                                                <p class="font-semibold text-gray-900">{{ $support->garansi_berakhir->format('d M Y') }}</p>
                                            </div>
                                            @endif

                                            @if($support->spesifikasi)
                                            <div class="col-span-2">
                                                <p class="text-gray-600">Spesifikasi</p>
                                                <p class="font-semibold text-gray-900">{{ $support->spesifikasi }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Spesifikasi Teknis -->
                        @if($ksoItem->spesifikasi_teknis)
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-4">
                                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                                    <i class="fas fa-cogs"></i>
                                    Spesifikasi Teknis
                                </h2>
                            </div>
                            <div class="p-6">
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <p class="text-gray-900 whitespace-pre-wrap">{{ $ksoItem->spesifikasi_teknis }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Keterangan -->
                        @if($ksoItem->keterangan)
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-4">
                                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                                    <i class="fas fa-sticky-note"></i>
                                    Keterangan
                                </h2>
                            </div>
                            <div class="p-6">
                                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                    <p class="text-gray-900 whitespace-pre-wrap">{{ $ksoItem->keterangan }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Right Column - Summary -->
                    <div class="space-y-6">
                        <!-- Customer Info Card -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden sticky top-8">
                            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                                    <i class="fas fa-hospital"></i>
                                    Customer
                                </h2>
                            </div>
                            <div class="p-6">
                                <p class="text-2xl font-bold text-gray-900 mb-4">{{ $ksoItem->customer->name ?? 'N/A' }}</p>
                                
                                @if($ksoItem->customer->address)
                                <div class="mb-4 pb-4 border-b">
                                    <p class="text-sm text-gray-600 mb-1">
                                        <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>Alamat
                                    </p>
                                    <p class="text-gray-900 text-sm">{{ $ksoItem->customer->address }}</p>
                                </div>
                                @endif

                                @if($ksoItem->customer->phone)
                                <div class="mb-4 pb-4 border-b">
                                    <p class="text-sm text-gray-600 mb-1">
                                        <i class="fas fa-phone text-blue-600 mr-2"></i>Telepon
                                    </p>
                                    <p class="text-gray-900 font-semibold">{{ $ksoItem->customer->phone }}</p>
                                </div>
                                @endif

                                @if($ksoItem->customer->email)
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">
                                        <i class="fas fa-envelope text-blue-600 mr-2"></i>Email
                                    </p>
                                    <p class="text-gray-900 font-semibold break-all text-sm">{{ $ksoItem->customer->email }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Tanggal & Periode Card -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                                    <i class="fas fa-calendar-alt"></i>
                                    Tanggal & Periode
                                </h2>
                            </div>
                            <div class="p-6 space-y-3">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium mb-1">
                                        <i class="fas fa-calendar-check text-purple-600 mr-2"></i>Tanggal Investasi
                                    </p>
                                    <p class="text-gray-900 font-semibold text-sm">
                                        {{ $ksoItem->tanggal_investasi ? $ksoItem->tanggal_investasi->format('d M Y') : '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-600 font-medium mb-1">
                                        <i class="fas fa-calendar-plus text-purple-600 mr-2"></i>Tanggal Install
                                    </p>
                                    <p class="text-gray-900 font-semibold text-sm">
                                        {{ $ksoItem->tanggal_install ? $ksoItem->tanggal_install->format('d M Y') : '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-600 font-medium mb-1">
                                        <i class="fas fa-play-circle text-purple-600 mr-2"></i>Periode KSO Mulai
                                    </p>
                                    <p class="text-gray-900 font-semibold text-sm">
                                        {{ $ksoItem->periode_kso_mulai ? $ksoItem->periode_kso_mulai->format('d M Y') : '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-600 font-medium mb-1">
                                        <i class="fas fa-stop-circle text-purple-600 mr-2"></i>Periode KSO Berakhir
                                    </p>
                                    <p class="text-gray-900 font-semibold text-sm">
                                        {{ $ksoItem->periode_kso_berakhir ? $ksoItem->periode_kso_berakhir->format('d M Y') : '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-600 font-medium mb-1">
                                        <i class="fas fa-hourglass-half text-purple-600 mr-2"></i>Durasi KSO
                                    </p>
                                    <p class="text-gray-900 font-semibold text-sm">{{ $ksoItem->durasi_kso_bulan }} Bulan</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-600 font-medium mb-1">
                                        <i class="fas fa-shield-alt text-purple-600 mr-2"></i>Garansi Berakhir
                                    </p>
                                    <p class="text-gray-900 font-semibold text-sm">
                                        {{ $ksoItem->garansi_berakhir ? $ksoItem->garansi_berakhir->format('d M Y') : '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Lokasi & PIC Card -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4">
                                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Lokasi & Penanggung Jawab
                                </h2>
                            </div>
                            <div class="p-6 space-y-3">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium mb-1">
                                        <i class="fas fa-map-pin text-green-600 mr-2"></i>Lokasi Penempatan
                                    </p>
                                    <p class="text-gray-900 font-semibold text-sm">{{ $ksoItem->lokasi_penempatan ?? '-' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-600 font-medium mb-1">
                                        <i class="fas fa-user-tie text-green-600 mr-2"></i>PIC Customer
                                    </p>
                                    <p class="text-gray-900 font-semibold text-sm">{{ $ksoItem->pic_customer ?? '-' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-600 font-medium mb-1">
                                        <i class="fas fa-user-tie text-green-600 mr-2"></i>PIC MSA
                                    </p>
                                    <p class="text-gray-900 font-semibold text-sm">{{ $ksoItem->pic_msa ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Summary -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                                    <i class="fas fa-money-bill-wave"></i>
                                    Ringkasan Finansial
                                </h2>
                            </div>
                            <div class="p-6 space-y-3">
                                <div class="flex justify-between items-center pb-3 border-b">
                                    <p class="text-gray-600">Nilai Alat Utama</p>
                                    <p class="font-bold text-gray-900">Rp {{ number_format($ksoItem->nilai_alat_utama, 0, ',', '.') }}</p>
                                </div>

                                @if($ksoItem->total_pendukung > 0)
                                <div class="flex justify-between items-center pb-3 border-b">
                                    <p class="text-gray-600">Nilai Alat Pendukung</p>
                                    <p class="font-bold text-gray-900">Rp {{ number_format($ksoItem->total_pendukung, 0, ',', '.') }}</p>
                                </div>
                                @endif

                                <div class="flex justify-between items-center pt-3 bg-blue-50 p-3 rounded-lg">
                                    <p class="font-bold text-gray-900">Total Investasi</p>
                                    <p class="font-bold text-blue-600 text-lg">Rp {{ number_format($ksoItem->total_investasi, 0, ',', '.') }}</p>
                                </div>

                                @if($ksoItem->nilai_sewa_bulanan > 0)
                                <div class="flex justify-between items-center pt-3 bg-green-50 p-3 rounded-lg">
                                    <p class="font-bold text-gray-900">Sewa Bulanan</p>
                                    <p class="font-bold text-green-600 text-lg">Rp {{ number_format($ksoItem->nilai_sewa_bulanan, 0, ',', '.') }}</p>
                                </div>
                                @endif

                                @if($ksoItem->deposit > 0)
                                <div class="flex justify-between items-center pt-3 bg-orange-50 p-3 rounded-lg">
                                    <p class="font-bold text-gray-900">Deposit</p>
                                    <p class="font-bold text-orange-600 text-lg">Rp {{ number_format($ksoItem->deposit, 0, ',', '.') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Status Card -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                                    <i class="fas fa-info-circle"></i>
                                    Status
                                </h2>
                            </div>
                            <div class="p-6">
                                <div class="flex items-center gap-3">
                                    @if($ksoItem->status === 'active')
                                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                                        <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full font-semibold">
                                            <i class="fas fa-check-circle mr-2"></i>Aktif
                                        </span>
                                    @else
                                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                        <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full font-semibold">
                                            <i class="fas fa-times-circle mr-2"></i>Tidak Aktif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-2">
                            <button onclick="window.print()" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                                <i class="fas fa-print"></i>
                                Cetak Detail
                            </button>
                            <a href="javascript:history.back()" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition font-semibold">
                                <i class="fas fa-arrow-left"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Print Styles -->
    <style media="print">
        body {
            background: white;
        }
        .no-print {
            display: none !important;
        }
        button {
            display: none !important;
        }
    </style>
</body>
</html>
