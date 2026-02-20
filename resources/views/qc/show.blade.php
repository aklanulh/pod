@extends('layouts.app')

@section('title', 'Detail ' . ($qcRecord->type === 'qc' ? 'QC' : 'Kalibrasi') . ' - ' . $qcRecord->ksoItem->nama_alat)

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    {{ $qcRecord->type === 'qc' ? '🔍 Quality Control' : '🔧 Kalibrasi' }}
                </h1>
                <p class="text-gray-600 mt-2">{{ $qcRecord->ksoItem->nama_alat }} - {{ $qcRecord->ksoItem->customer->name }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('kso-roi.qc.edit', $qcRecord) }}" 
                   class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('kso-roi.technician-dashboard') }}" 
                   class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Status Card -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Status: {{ $qcRecord->status_text }}</h2>
                    <p class="text-gray-600 mt-1">{{ $qcRecord->date->format('d F Y') }}</p>
                </div>
                <div class="text-6xl">
                    {{ $qcRecord->status === 'pass' ? '✅' : ($qcRecord->status === 'fail' ? '❌' : '⏳') }}
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="h-4 rounded-full {{ $qcRecord->status === 'pass' ? 'bg-green-500' : ($qcRecord->status === 'fail' ? 'bg-red-500' : 'bg-yellow-500') }}" 
                     style="width: {{ $qcRecord->status === 'pass' ? '100' : ($qcRecord->status === 'fail' ? '100' : '50') }}%"></div>
            </div>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Informasi Dasar</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tipe</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $qcRecord->type === 'qc' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $qcRecord->type_text }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $qcRecord->date->format('d F Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Teknisi</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $qcRecord->technician_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jatuh Tempo Berikutnya</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($qcRecord->next_due_date)
                                    {{ $qcRecord->next_due_date->format('d F Y') }}
                                    @if($qcRecord->next_due_date->isPast())
                                        <span class="ml-2 text-red-600 font-medium">(TERLAMBAT)</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Equipment Details -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Detail Peralatan</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama Alat</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $qcRecord->ksoItem->nama_alat }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Brand/Model</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $qcRecord->ksoItem->brand }} {{ $qcRecord->ksoItem->model }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Serial Number</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $qcRecord->ksoItem->serial_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Customer</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $qcRecord->ksoItem->customer->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Lokasi</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $qcRecord->ksoItem->lokasi_penempatan }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">PIC Customer</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $qcRecord->ksoItem->pic_customer }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Notes & Findings -->
            @if($qcRecord->notes)
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Catatan & Temuan</h3>
                </div>
                <div class="p-6">
                    <div class="prose max-w-none">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $qcRecord->notes }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Parameters -->
            @if($qcRecord->parameters && count($qcRecord->parameters) > 0)
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Parameter Pengukuran</h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parameter</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Standar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($qcRecord->parameters as $param => $value)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $param }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $value['value'] ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $value['standard'] ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ ($value['status'] ?? 'ok') === 'ok' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $value['status'] ?? 'OK' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Aksi</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('kso-roi.qc.create', [$qcRecord->ksoItem, $qcRecord->type]) }}" 
                       class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm text-center">
                        <i class="fas fa-plus mr-2"></i> {{ $qcRecord->type === 'qc' ? 'QC' : 'Kalibrasi' }} Baru
                    </a>
                    <button onclick="window.print()" 
                            class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm">
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                    <button onclick="window.open('https://wa.me/?text={{ urlencode("Hasil " . ($qcRecord->type === 'qc' ? 'QC' : 'kalibrasi') . " untuk " . $qcRecord->ksoItem->nama_alat . " - Status: " . $qcRecord->status_text) }}')"
                            class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-sm">
                        <i class="fab fa-whatsapp mr-2"></i> Share via WhatsApp
                    </button>
                </div>
            </div>

            <!-- Certificate -->
            @if($qcRecord->certificate_file)
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Sertifikat</h3>
                </div>
                <div class="p-6">
                    <a href="{{ asset('storage/' . $qcRecord->certificate_file) }}" 
                       target="_blank"
                       class="flex items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 transition-colors">
                        <div class="text-center">
                            <i class="fas fa-file-pdf text-4xl text-red-500 mb-2"></i>
                            <p class="text-sm text-gray-600">Lihat Certificate</p>
                        </div>
                    </a>
                </div>
            </div>
            @endif

            <!-- History -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Riwayat</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dibuat oleh</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $qcRecord->creator->name ?? 'System' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dibuat pada</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $qcRecord->created_at->format('d M Y H:i') }}</dd>
                        </div>
                        @if($qcRecord->updated_at != $qcRecord->created_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Diperbarui</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $qcRecord->updated_at->format('d M Y H:i') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
