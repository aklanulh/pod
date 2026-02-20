@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Kelola KSO Items</h1>
            <p class="text-gray-600">Manajemen Alat Medis & Alat Komputer & Pendukung dan perhitungan ROI</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('kso-roi.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Dashboard
            </a>
            <a href="{{ route('kso-roi.create-kso-item') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah KSO Item
            </a>
        </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('kso-roi.kso-items', ['status' => 'active']) }}" 
                   class="{{ $status === 'active' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} 
                   whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Aktif
                        <span class="ml-2 bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full text-xs">{{ $activeCount }}</span>
                    </div>
                </a>
                <a href="{{ route('kso-roi.kso-items', ['status' => 'inactive']) }}" 
                   class="{{ $status === 'inactive' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} 
                   whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Inactive
                        <span class="ml-2 bg-gray-100 text-gray-800 px-2 py-0.5 rounded-full text-xs">{{ $inactiveCount }}</span>
                    </div>
                </a>
                <a href="{{ route('kso-roi.kso-items', ['status' => 'all']) }}" 
                   class="{{ $status === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} 
                   whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        Semua
                        <span class="ml-2 bg-purple-100 text-purple-800 px-2 py-0.5 rounded-full text-xs">{{ $allCount }}</span>
                    </div>
                </a>
            </nav>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- KSO Items Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alat Medis & Alat Komputer & Pendukung</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Investasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ROI</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($ksoItems as $item)
                        <tr class="hover:bg-gray-50 {{ $item->status === 'inactive' ? 'bg-gray-50 opacity-75' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 {{ $item->status === 'inactive' ? 'line-through text-gray-500' : '' }}">
                                        {{ $item->nama_alat }}
                                        @if($item->status === 'inactive')
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Inactive
                                            </span>
                                        @endif
                                    </div>
                                    @if($item->butuh_komputer)
                                        <div class="text-xs {{ $item->status === 'inactive' ? 'text-gray-400' : 'text-blue-600' }}">Membutuhkan komputer</div>
                                    @endif
                                    @if($item->supportItems->count() > 0)
                                        <div class="text-xs {{ $item->status === 'inactive' ? 'text-gray-400' : 'text-gray-500' }}">{{ $item->supportItems->count() }} item pendukung</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm {{ $item->status === 'inactive' ? 'text-gray-500' : 'text-gray-900' }}">{{ $item->customer->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm {{ $item->status === 'inactive' ? 'text-gray-500' : 'text-gray-900' }}">
                                    <div>Alat Utama: Rp {{ number_format($item->nilai_alat_utama, 0, ',', '.') }}</div>
                                    @if($item->total_pendukung > 0)
                                        <div class="text-xs {{ $item->status === 'inactive' ? 'text-gray-400' : 'text-gray-500' }}">Pendukung: Rp {{ number_format($item->total_pendukung, 0, ',', '.') }}</div>
                                    @endif
                                    <div class="font-medium">Total: Rp {{ number_format($item->total_investasi, 0, ',', '.') }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->status === 'active')
                                    <div class="text-sm">
                                        <div class="font-medium {{ $item->roi_status_color }}">{{ number_format($item->calculateItemROI(), 1) }}%</div>
                                        <div class="text-xs {{ $item->roi_status_color }}">{{ $item->roi_status }}</div>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-400">
                                        <div class="font-medium">-</div>
                                        <div class="text-xs">Tidak aktif</div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $item->status === 'inactive' ? 'text-gray-400' : 'text-gray-500' }}">
                                {{ $item->tanggal_investasi->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('qr.verify', $item->unique_id) }}" target="_blank" title="Buka QR Code" class="text-purple-600 hover:text-purple-900">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                            <rect x="6" y="10" width="4" height="4" rx="1"></rect>
                                            <rect x="14" y="10" width="4" height="4" rx="1"></rect>
                                            <rect x="10" y="14" width="4" height="4" rx="1"></rect>
                                        </svg>
                                    </a>
                                    <a href="{{ route('kso-roi.edit-kso-item', $item) }}" class="text-blue-600 hover:text-blue-900">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @if($item->status === 'inactive')
                                        <form action="{{ route('kso-roi.update-kso-item', $item) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin mengaktifkan kembali KSO item ini?')">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="active">
                                            <input type="hidden" name="customer_id" value="{{ $item->customer_id }}">
                                            <input type="hidden" name="nama_alat" value="{{ $item->nama_alat }}">
                                            <input type="hidden" name="brand" value="{{ $item->brand }}">
                                            <input type="hidden" name="model" value="{{ $item->model }}">
                                            <input type="hidden" name="serial_number" value="{{ $item->serial_number }}">
                                            <input type="hidden" name="no_registrasi" value="{{ $item->no_registrasi }}">
                                            <input type="hidden" name="kategori" value="{{ $item->kategori }}">
                                            <input type="hidden" name="kondisi" value="{{ $item->kondisi }}">
                                            <input type="hidden" name="nilai_alat_utama" value="{{ $item->nilai_alat_utama }}">
                                            <input type="hidden" name="tanggal_investasi" value="{{ $item->tanggal_investasi?->format('Y-m-d') }}">
                                            <input type="hidden" name="tanggal_deployment" value="{{ $item->tanggal_install?->format('Y-m-d') ?? $item->periode_kso_mulai?->format('Y-m-d') }}">
                                            <input type="hidden" name="durasi_kso_bulan" value="{{ $item->durasi_kso_bulan }}">
                                            <input type="hidden" name="lokasi_penempatan" value="{{ $item->lokasi_penempatan }}">
                                            <input type="hidden" name="pic_customer" value="{{ $item->pic_customer }}">
                                            <input type="hidden" name="pic_msa" value="{{ $item->pic_msa }}">
                                            <input type="hidden" name="butuh_komputer" value="{{ $item->butuh_komputer ? '1' : '' }}">
                                            <input type="hidden" name="keterangan" value="{{ $item->keterangan }}">
                                            <input type="hidden" name="spesifikasi_teknis" value="{{ $item->spesifikasi_teknis }}">
                                            <button type="submit" title="Aktifkan Kembali" class="text-green-600 hover:text-green-900">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('kso-roi.destroy-kso-item', $item) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus KSO item ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-lg font-medium mb-2">Belum ada KSO Items</p>
                                    <p class="mb-4">Mulai dengan menambahkan KSO item pertama</p>
                                    <a href="{{ route('kso-roi.create-kso-item') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                        Tambah KSO Item
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($ksoItems->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $ksoItems->links() }}
            </div>
        @endif
    </div>

    <!-- Summary Statistics -->
    @if($ksoItems->count() > 0)
        <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $ksoItems->count() }}</div>
                    <div class="text-sm text-gray-500">
                        {{ $status === 'active' ? 'KSO Items Aktif' : ($status === 'inactive' ? 'KSO Items Inactive' : 'Total KSO Items') }}
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">Rp {{ number_format($ksoItems->sum('total_investasi'), 0, ',', '.') }}</div>
                    <div class="text-sm text-gray-500">
                        Total Investasi {{ $status === 'active' ? 'Aktif' : ($status === 'inactive' ? 'Inactive' : '') }}
                    </div>
                </div>
            </div>
            @if($status === 'active')
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $ksoItems->filter(function($item) { return $item->hasAchievedROI(); })->count() }}</div>
                        <div class="text-sm text-gray-500">Items dengan ROI</div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ number_format($ksoItems->avg(function($item) { return $item->calculateItemROI(); }), 1) }}%</div>
                        <div class="text-sm text-gray-500">Rata-rata ROI</div>
                    </div>
                </div>
            @elseif($status === 'inactive')
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $ksoItems->where('butuh_komputer', true)->count() }}</div>
                        <div class="text-sm text-gray-500">Butuh Komputer</div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $ksoItems->sum(function($item) { return $item->supportItems->count(); }) }}</div>
                        <div class="text-sm text-gray-500">Total Item Pendukung</div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $activeCount }}</div>
                        <div class="text-sm text-gray-500">Items Aktif</div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-600">{{ $inactiveCount }}</div>
                        <div class="text-sm text-gray-500">Items Inactive</div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
