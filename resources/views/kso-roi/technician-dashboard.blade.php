@extends('layouts.app')

@section('title', 'Dashboard Teknisi')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header with Actions -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Dashboard Teknisi</h1>
                <p class="text-gray-600">QC, Kalibrasi & Jadwal Rutinitas Customer</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('kso-roi.maintenance-schedules.create') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i> Tambah Jadwal
                </a>
                <a href="{{ route('kso-roi.kso-items') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-list mr-2"></i> Lihat Alat
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-cog text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Alat</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_equipment'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">QC Overdue</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['overdue_qc'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-full">
                    <i class="fas fa-tools text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Kalibrasi Overdue</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['overdue_calibration'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">QC Due Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['qc_due_this_month'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-calendar-check text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Kalibrasi Due Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['calibration_due_this_month'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipment Monitoring Table -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">🔍 Monitoring QC & Kalibrasi Semua Alat</h2>
                <div class="flex space-x-2">
                    <input type="text" id="searchEquipment" placeholder="Cari alat..." 
                           class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <select id="filterStatus" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                        <option value="">Semua Status</option>
                        <option value="terlambat">Terlambat</option>
                        <option value="akan_jatuh_tempo">Akan Jatuh Tempo</option>
                        <option value="baik">Baik</option>
                        <option value="perlu_perhatian">Perlu Perhatian</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alat</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QC</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kalibrasi</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Maintenance</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="equipmentTableBody">
                    @if(count($ksoItems) > 0)
                        @foreach($ksoItems as $item)
                            <tr class="hover:bg-gray-50 equipment-row" 
                                data-customer="{{ $item['customer'] }}"
                                data-equipment="{{ $item['nama_alat'] }}"
                                data-qc-status="{{ $item['qc_status'] }}"
                                data-calibration-status="{{ $item['calibration_status'] }}">
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $item['nama_alat'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $item['brand'] }} {{ $item['model'] }}</div>
                                        <div class="text-xs text-gray-400">SN: {{ $item['serial_number'] }}</div>
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm">{{ $item['customer'] }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm">{{ $item['lokasi_penempatan'] }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 rounded-full mr-1 bg-{{ $item['qc_status_color'] }}-500"></div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $item['qc_status_text'] }}</div>
                                            @if($item['last_qc'])
                                                <div class="text-xs text-gray-500">Terakhir: {{ $item['last_qc']->format('d M Y') }}</div>
                                            @endif
                                            @if(isset($item['next_qc_due']) && $item['next_qc_due'])
                                                <div class="text-xs text-blue-600">Berikutnya: {{ $item['next_qc_due']->format('d M Y') }}</div>
                                            @endif
                                            @if($item['qc_days_overdue'] > 0)
                                                <div class="text-xs text-red-600 font-medium">{{ $item['qc_days_overdue'] }} hari terlambat</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 rounded-full mr-1 bg-{{ $item['calibration_status_color'] }}-500"></div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $item['calibration_status_text'] }}</div>
                                            @if($item['last_calibration'])
                                                <div class="text-xs text-gray-500">Terakhir: {{ $item['last_calibration']->format('d M Y') }}</div>
                                            @endif
                                            @if(isset($item['next_calibration_due']) && $item['next_calibration_due'])
                                                <div class="text-xs text-blue-600">Berikutnya: {{ $item['next_calibration_due']->format('d M Y') }}</div>
                                            @endif
                                            @if($item['calibration_days_overdue'] > 0)
                                                <div class="text-xs text-red-600 font-medium">{{ $item['calibration_days_overdue'] }} hari terlambat</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm">
                                    @if($item['maintenance'])
                                        <div class="flex items-center">
                                            <div class="w-2 h-2 rounded-full mr-1 bg-blue-500"></div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $item['maintenance']->next_maintenance_date->format('d M Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $item['maintenance']->maintenance_type ?? 'Maintenance Terjadwal' }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-400">-</div>
                                    @endif
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm">
                                    <div class="flex space-x-1">
                                        <a href="{{ route('kso-roi.qc.create', [$item['id'], 'qc']) }}" 
                                           class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs">
                                            QC
                                        </a>
                                        <a href="{{ route('kso-roi.qc.create', [$item['id'], 'calibration']) }}" 
                                           class="bg-purple-500 hover:bg-purple-600 text-white px-2 py-1 rounded text-xs">
                                            Kalibrasi
                                        </a>
                                        <a href="{{ route('kso-roi.maintenance-schedules.create', $item['id']) }}" 
                                           class="bg-orange-500 hover:bg-orange-600 text-white px-2 py-1 rounded text-xs">
                                            Maint
                                        </a>
                                        @if($item['last_qc_record'])
                                            <a href="{{ route('kso-roi.qc.show', $item['last_qc_record']->id) }}" 
                                               class="bg-gray-500 hover:bg-gray-600 text-white px-2 py-1 rounded text-xs">
                                                Rincian
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-cog text-4xl mb-4"></i>
                                    <p class="mb-4">Tidak ada alat tersedia</p>
                                    <button onclick="createDummyKSO()" 
                                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-sm">
                                        <i class="fas fa-magic mr-2"></i> Buat Alat Contoh
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions Bar -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-blue-800">Aksi Cepat</h3>
                <p class="text-blue-700 text-sm">Lakukan QC/Kalibrasi cepat untuk alat yang tersedia</p>
            </div>
            <div class="flex space-x-2">
                @if(count($ksoItems) > 0)
                    <select id="quickEquipment" class="px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Pilih Alat...</option>
                        @foreach($ksoItems as $item)
                            <option value="{{ $item['id'] }}">{{ $item['nama_alat'] }} - {{ $item['customer'] }}</option>
                        @endforeach
                    </select>
                    <button onclick="quickQC()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                        <i class="fas fa-clipboard-check mr-1"></i> Quick QC
                    </button>
                    <button onclick="quickCalibration()" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded text-sm">
                        <i class="fas fa-tools mr-1"></i> Quick Kalibrasi
                    </button>
                @else
                    <div class="text-blue-800">
                        <p class="text-sm">Tidak ada alat tersedia. Silakan buat KSO item terlebih dahulu.</p>
                        <button onclick="createDummyKSO()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-sm mt-2">
                            <i class="fas fa-magic mr-1"></i> Buat Alat Contoh
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Instructions Panel -->
    @if($stats['total_equipment'] == 0)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Cara Memulai Dashboard Teknisi</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ol class="list-decimal list-inside space-y-1">
                        <li><strong>Buat Alat Contoh:</strong> Klik tombol "Buat Alat Contoh" di atas untuk membuat data dummy</li>
                        <li><strong>Buat Jadwal:</strong> Klik "Tambah Jadwal" untuk membuat jadwal maintenance</li>
                        <li><strong>Lakukan QC/Kalibrasi:</strong> Gunakan tombol QC/Kalibrasi di setiap jadwal</li>
                        <li><strong>Monitoring:</strong> Lihat hasil di bagian Overdue dan Recent Records</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Today's Schedule -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">📅 Jadwal Hari Ini</h2>
            </div>
            <div class="p-6">
                @if($todaySchedule->count() > 0)
                    <div class="space-y-4">
                        @foreach($todaySchedule as $schedule)
                            <div class="border-l-4 border-blue-500 bg-blue-50 p-4 rounded">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-semibold text-gray-800">{{ $schedule->ksoItem->nama_alat }}</h4>
                                        <p class="text-sm text-gray-600">{{ $schedule->ksoItem->customer->name }}</p>
                                        <p class="text-sm text-gray-500">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $schedule->scheduled_time ?? 'Seharian' }}
                                        </p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                                            <i class="fas fa-play mr-1"></i> Mulai
                                        </button>
                                        <a href="{{ route('kso-roi.qc.create', [$schedule->ksoItem, 'qc']) }}" 
                                           class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm inline-block">
                                            <i class="fas fa-clipboard-check mr-1"></i> QC
                                        </a>
                                        <a href="{{ route('kso-roi.qc.create', [$schedule->ksoItem, 'calibration']) }}" 
                                           class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded text-sm inline-block">
                                            <i class="fas fa-tools mr-1"></i> Kalibrasi
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-calendar-times text-4xl mb-4"></i>
                        <p class="mb-4">Tidak ada jadwal hari ini</p>
                        <div class="space-x-2">
                            <a href="{{ route('kso-roi.maintenance-schedules.create') }}" 
                               class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-sm inline-block">
                                <i class="fas fa-plus mr-2"></i> Buat Jadwal Baru
                            </a>
                            <button onclick="createDummySchedule()" 
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                                <i class="fas fa-magic mr-2"></i> Buat Data Contoh
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Weekly Schedule -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">📆 Jadwal Minggu Ini</h2>
            </div>
            <div class="p-6">
                @if($weeklySchedule->count() > 0)
                    <div class="space-y-3">
                        @foreach($weeklySchedule as $schedule)
                            <div class="flex items-center justify-between p-3 border rounded hover:bg-gray-50">
                                <div class="flex items-center space-x-3">
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($schedule->scheduled_date)->format('D') }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $schedule->ksoItem->nama_alat }}</p>
                                        <p class="text-sm text-gray-600">{{ $schedule->ksoItem->customer->name }}</p>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $schedule->scheduled_time ?? 'All Day' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-calendar-alt text-4xl mb-4"></i>
                        <p>Tidak ada jadwal minggu ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- QC & Calibration Status -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">🔍 Status QC & Kalibrasi</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Overdue QC -->
                <div>
                    <h3 class="text-lg font-medium text-red-600 mb-4">QC Terlambat ({{ $overdueQc->count() }})</h3>
                    <div class="space-y-3">
                        @if($overdueQc->count() > 0)
                            @foreach($overdueQc->take(5) as $item)
                                <div class="border-l-4 border-red-500 bg-red-50 p-3 rounded">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $item['nama_alat'] }}</p>
                                            <p class="text-sm text-gray-600">{{ $item['customer'] }}</p>
                                            @if($item['last_qc'])
                                                <div class="text-xs text-gray-500">Terakhir: {{ $item['last_qc']->format('d M Y') }}</div>
                                            @endif
                                            <div class="text-xs text-red-600">
                                                Jatuh Tempo: {{ $item['next_qc_due']->format('d M Y') }}
                                            </div>
                                        </div>
                                        <a href="{{ route('kso-roi.qc.create', [$item['id'], 'qc']) }}" 
                                           class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm inline-block">
                                            QC Sekarang
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4 text-gray-500">
                                <p class="text-sm mb-3">Tidak ada QC yang terlambat</p>
                                <button onclick="createDummyQC()" 
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs">
                                    <i class="fas fa-magic mr-1"></i> Buat Data Contoh
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Overdue Calibration -->
                <div>
                    <h3 class="text-lg font-medium text-orange-600 mb-4">Kalibrasi Terlambat ({{ $overdueCalibration->count() }})</h3>
                    <div class="space-y-3">
                        @if($overdueCalibration->count() > 0)
                            @foreach($overdueCalibration->take(5) as $item)
                                <div class="border-l-4 border-orange-500 bg-orange-50 p-3 rounded">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $item['nama_alat'] }}</p>
                                            <p class="text-sm text-gray-600">{{ $item['customer'] }}</p>
                                            @if($item['last_calibration'])
                                                <div class="text-xs text-gray-500">Terakhir: {{ $item['last_calibration']->format('d M Y') }}</div>
                                            @endif
                                            <div class="text-xs text-orange-600">
                                                Jatuh Tempo: {{ $item['next_calibration_due']->format('d M Y') }}
                                            </div>
                                        </div>
                                        <a href="{{ route('kso-roi.qc.create', [$item['id'], 'calibration']) }}" 
                                           class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded text-sm inline-block">
                                            Kalibrasi
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4 text-gray-500">
                                <p class="text-sm mb-3">Tidak ada kalibrasi yang terlambat</p>
                                <button onclick="createDummyCalibration()" 
                                        class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded text-xs">
                                    <i class="fas fa-magic mr-1"></i> Buat Data Contoh
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent QC/Calibration Records -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">📋 Riwayat QC & Kalibrasi Terakhir</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teknisi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if($recentQcRecords->count() > 0)
                            @foreach($recentQcRecords as $record)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $record->date->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $record->type === 'qc' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ $record->type_text }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $record->ksoItem->nama_alat }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $record->ksoItem->customer->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $record->status_badge }}">
                                            {{ $record->status_text }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $record->technician_name ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    Belum ada data QC/Kalibrasi
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Calendar View -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">📅 Kalender QC & Kalibrasi</h2>
                <div class="flex space-x-2">
                    <button onclick="window.open('/kso-roi/qc/calendar-data', '_blank')" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                        <i class="fas fa-sync mr-1"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">⚡ Quick Actions</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('kso-roi.kso-items') }}" 
                   class="flex items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="p-3 bg-blue-100 rounded-full mr-4">
                        <i class="fas fa-list text-blue-600"></i>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-800">Lihat Semua Alat</h4>
                        <p class="text-sm text-gray-600">Lihat daftar KSO items</p>
                    </div>
                </a>

                <button onclick="window.print()" 
                        class="flex items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors w-full">
                    <div class="p-3 bg-green-100 rounded-full mr-4">
                        <i class="fas fa-print text-green-600"></i>
                    </div>
                    <div class="text-left">
                        <h4 class="font-medium text-gray-800">Cetak Jadwal</h4>
                        <p class="text-sm text-gray-600">Cetak jadwal hari ini</p>
                    </div>
                </button>

                <a href="{{ route('admin.history.index') }}" 
                   class="flex items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="p-3 bg-purple-100 rounded-full mr-4">
                        <i class="fas fa-history text-purple-600"></i>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-800">Lihat Riwayat</h4>
                        <p class="text-sm text-gray-600">Lihat aktivitas</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '/kso-roi/qc/calendar-data',
        eventClick: function(info) {
            if (info.event.url) {
                window.open(info.event.url, '_blank');
                info.jsEvent.preventDefault(); // don't let the browser navigate
            }
        },
        eventDidMount: function(info) {
            // Add tooltip
            const tooltip = new Tooltip(info.el, {
                title: info.event.extendedProps.icon + ' ' + 
                       info.event.extendedProps.type.toUpperCase() + 
                       ' - ' + info.event.extendedProps.customer,
                placement: 'top',
                trigger: 'hover',
                container: 'body'
            });
        },
        height: 'auto',
        contentHeight: 400
    });

    calendar.render();
});
</script>

<!-- Bootstrap Tooltip JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->
<script>
// Search and Filter Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchEquipment');
    const filterSelect = document.getElementById('filterStatus');
    const tableBody = document.getElementById('equipmentTableBody');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const filterValue = filterSelect.value;
        const rows = tableBody.querySelectorAll('.equipment-row');

        rows.forEach(row => {
            const customer = row.dataset.customer.toLowerCase();
            const equipment = row.dataset.equipment.toLowerCase();
            const qcStatus = row.dataset.qcStatus;
            const calibrationStatus = row.dataset.calibrationStatus;

            const matchesSearch = customer.includes(searchTerm) || equipment.includes(searchTerm);
            const matchesFilter = !filterValue || 
                filterValue === 'terlambat' && (qcStatus === 'overdue' || calibrationStatus === 'overdue') ||
                filterValue === 'akan_jatuh_tempo' && (qcStatus === 'due_soon' || calibrationStatus === 'due_soon') ||
                filterValue === 'baik' && (qcStatus === 'good' || calibrationStatus === 'good') ||
                filterValue === 'perlu_perhatian' && (qcStatus === 'problem' || calibrationStatus === 'problem') ||
                filterValue === 'pending' && (qcStatus === 'pending' || calibrationStatus === 'pending') ||
                filterValue === qcStatus || filterValue === calibrationStatus;

            row.style.display = matchesSearch && matchesFilter ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterTable);
    filterSelect.addEventListener('change', filterTable);

    // Auto-refresh dashboard every 30 seconds
    setInterval(() => {
        fetch('/kso-roi/technician-dashboard')
            .then(response => response.text())
            .then(html => {
                // Update statistics only
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                
                // Update stats cards
                const newStats = tempDiv.querySelectorAll('.text-2xl');
                const currentStats = document.querySelectorAll('.text-2xl');
                
                newStats.forEach((newStat, index) => {
                    if (currentStats[index]) {
                        currentStats[index].textContent = newStat.textContent;
                    }
                });
            })
            .catch(error => {
                console.log('Auto-refresh failed:', error);
            });
    }, 30000);

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+F for search focus
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            searchInput.focus();
        }
        // Ctrl+Q for Quick QC
        if (e.ctrlKey && e.key === 'q') {
            e.preventDefault();
            quickQC();
        }
        // Ctrl+K for Quick Calibration  
        if (e.ctrlKey && e.key === 'k') {
            e.preventDefault();
            quickCalibration();
        }
        // Ctrl+N for New Schedule
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            window.location.href = '/kso-roi/maintenance-schedules/create';
        }
    });
});

// Quick Actions
function quickQC() {
    const equipmentId = document.getElementById('quickEquipment').value;
    if (!equipmentId) {
        alert('Silakan pilih alat terlebih dahulu');
        return;
    }
    window.location.href = `/kso-roi/qc/${equipmentId}/qc/create`;
}

function quickCalibration() {
    const equipmentId = document.getElementById('quickEquipment').value;
    if (!equipmentId) {
        alert('Silakan pilih alat terlebih dahulu');
        return;
    }
    window.location.href = `/kso-roi/qc/${equipmentId}/calibration/create`;
}

// Create Dummy Data Functions
function createDummyKSO() {
    if (confirm('Buat data KSO item contoh? Ini akan membuat alat dan customer dummy.')) {
        fetch('/kso-roi/create-dummy-kso', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Data KSO item berhasil dibuat!');
                location.reload();
            } else {
                alert('Gagal membuat data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    }
}

function createDummySchedule() {
    if (confirm('Buat data jadwal contoh untuk hari ini?')) {
        fetch('/kso-roi/create-dummy-schedule', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Data jadwal berhasil dibuat!');
                location.reload();
            } else {
                alert('Gagal membuat data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    }
}

function createDummyQC() {
    if (confirm('Buat data QC contoh yang overdue?')) {
        fetch('/kso-roi/create-dummy-qc', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Data QC berhasil dibuat!');
                location.reload();
            } else {
                alert('Gagal membuat data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    }
}

function createDummyCalibration() {
    if (confirm('Buat data kalibrasi contoh yang overdue?')) {
        fetch('/kso-roi/create-dummy-calibration', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Data kalibrasi berhasil dibuat!');
                location.reload();
            } else {
                alert('Gagal membuat data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    }
}

// Show loading state
function showLoading(button) {
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Loading...';
}

// Hide loading state
function hideLoading(button, originalText) {
    button.disabled = false;
    button.innerHTML = originalText;
}
</script>
@endsection
