@extends('layouts.app')

@section('title', 'KSO Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard KSO ROI</h1>
        <p class="text-gray-600">Analisis Return of Investment untuk Kerja Sama Operasional</p>
    </div>

    <!-- Overall Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
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
                    <p class="text-2xl font-bold">{{ number_format($overallROI, 1) }}%</p>
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

        <!-- Selisih ROI -->
        <div class="bg-gradient-to-r {{ $overallDifferenceData['type'] === 'profit' ? 'from-teal-500 to-teal-600' : 'from-orange-500 to-orange-600' }} rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white text-opacity-90 text-sm font-medium">Selisih ROI</p>
                    <p class="text-xl font-bold">
                        {{ $overallDifferenceData['type'] === 'profit' ? 'Profit' : 'Kurang' }}
                    </p>
                    <p class="text-sm text-white text-opacity-80">
                        Rp {{ number_format($overallDifferenceData['amount'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    @if($overallDifferenceData['type'] === 'profit')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    @else
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                        </svg>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mb-8">
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('kso-roi.kso-items') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Kelola KSO Items
            </a>
            <a href="{{ route('kso-roi.create-kso-item') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah KSO Item
            </a>
        </div>
    </div>

    <!-- Customer ROI Performance Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Performa ROI per Customer</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" colspan="2">Equipment Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Investasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ROI</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selisih</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50 cursor-pointer transition-colors duration-200" onclick="toggleDropdown('equipment-{{ $customer->id }}')">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                        <div class="text-sm text-gray-500">
                                            Alat Medis ({{ $customer->medical_equipment_count }}) & Alat Komputer & Pendukung ({{ $customer->computer_equipment_count }})
                                        </div>
                                    </div>
                                    <div class="text-gray-400">
                                        <svg class="w-5 h-5 transform transition-transform duration-200" id="arrow-equipment-{{ $customer->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4" colspan="2">
                                <div class="flex items-center justify-between">
                                    <div class="flex space-x-4">
                                        <div class="bg-blue-50 px-3 py-2 rounded-lg">
                                            <span class="text-sm font-medium text-blue-800">
                                                {{ $customer->ksoItems->count() }} Alat Medis
                                            </span>
                                            <div class="text-xs text-blue-600">Rp {{ number_format($customer->ksoItems->sum('nilai_alat_utama'), 0, ',', '.') }}</div>
                                        </div>
                                        <div class="bg-green-50 px-3 py-2 rounded-lg">
                                            <span class="text-sm font-medium text-green-800">
                                                {{ $customer->ksoItems->sum(function($item) { return $item->supportItems->count(); }) }} Item Pendukung
                                            </span>
                                            <div class="text-xs text-green-600">Rp {{ number_format($customer->ksoItems->sum('total_pendukung'), 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Rp {{ number_format($customer->total_investment, 0, ',', '.') }}</div>
                                <div class="text-xs text-gray-500">Penjualan: Rp {{ number_format($customer->total_sales, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium {{ $customer->roi_status === 'ROI' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($customer->roi_percentage, 1) }}%
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                            <div class="h-1.5 rounded-full {{ $customer->roi_status === 'ROI' ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ min($customer->roi_percentage, 100) }}%"></div>
                                        </div>
                                    </div>
                                    <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full {{ $customer->roi_status === 'ROI' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $customer->roi_status }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-center">
                                    @if($customer->roi_difference['type'] === 'profit')
                                        <div class="text-sm font-medium text-green-600">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                            </svg>
                                            Profit
                                        </div>
                                        <div class="text-xs text-green-600">Rp {{ number_format($customer->roi_difference['amount'], 0, ',', '.') }}</div>
                                        <div class="text-xs text-green-500">+{{ number_format($customer->roi_difference['percentage_diff'], 1) }}%</div>
                                    @else
                                        <div class="text-sm font-medium text-red-600">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                            </svg>
                                            Kurang
                                        </div>
                                        <div class="text-xs text-red-600">Rp {{ number_format($customer->roi_difference['amount'], 0, ',', '.') }}</div>
                                        <div class="text-xs text-red-500">{{ number_format($customer->roi_difference['percentage_diff'], 1) }}%</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('kso-roi.customer-detail', $customer) }}" class="text-blue-600 hover:text-blue-900 mr-3" onclick="event.stopPropagation()">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        <!-- Equipment Details Row (Hidden by default) -->
                        <tr id="equipment-{{ $customer->id }}" class="hidden bg-gray-50">
                            <td colspan="7" class="px-6 py-4">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <!-- Alat Medis -->
                                    <div class="bg-white rounded-lg p-4 border border-blue-200">
                                        <div class="flex items-center mb-3">
                                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <h3 class="text-lg font-semibold text-blue-800">Alat Medis ({{ $customer->ksoItems->count() }})</h3>
                                        </div>
                                        <div class="space-y-3">
                                            @foreach($customer->ksoItems as $item)
                                                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                                    <div class="flex-1">
                                                        <div class="font-medium text-gray-900">{{ $item->nama_alat }}</div>
                                                        <div class="text-sm text-gray-500">{{ $item->tanggal_investasi->format('d/m/Y') }}</div>
                                                        @if($item->keterangan)
                                                            <div class="text-xs text-gray-400 mt-1">{{ Str::limit($item->keterangan, 50) }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="font-semibold text-gray-900">Rp {{ number_format($item->nilai_alat_utama, 0, ',', '.') }}</div>
                                                        <div class="text-xs px-2 py-1 rounded-full {{ $item->roi_status === 'ROI' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                            {{ $item->roi_status }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Alat Komputer & Pendukung -->
                                    <div class="bg-white rounded-lg p-4 border border-green-200">
                                        <div class="flex items-center mb-3">
                                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                            <h3 class="text-lg font-semibold text-green-800">Alat Komputer & Pendukung ({{ $customer->ksoItems->sum(function($item) { return $item->supportItems->count(); }) }})</h3>
                                        </div>
                                        <div class="space-y-4">
                                            @foreach($customer->ksoItems as $item)
                                                @if($item->supportItems->count() > 0)
                                                    <div class="border-l-4 border-green-300 pl-4">
                                                        <div class="text-sm font-medium text-gray-600 mb-2">{{ $item->nama_alat }}</div>
                                                        <div class="space-y-2">
                                                            @foreach($item->supportItems as $support)
                                                                <div class="flex justify-between items-center p-2 bg-green-50 rounded">
                                                                    <div class="flex-1">
                                                                        <div class="text-sm font-medium text-gray-900">{{ $support->nama_item }}</div>
                                                                        <div class="text-xs text-gray-500">
                                                                            Qty: {{ $support->jumlah }} | Unit: Rp {{ number_format($support->nilai_item, 0, ',', '.') }}
                                                                        </div>
                                                                        @if($support->spesifikasi)
                                                                            <div class="text-xs text-gray-400 mt-1">{{ Str::limit($support->spesifikasi, 40) }}</div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="text-right">
                                                                        <div class="font-semibold text-gray-900">Rp {{ number_format($support->total_value, 0, ',', '.') }}</div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                            @if($customer->ksoItems->sum(function($item) { return $item->supportItems->count(); }) === 0)
                                                <div class="text-center py-4 text-gray-500">
                                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                                    </svg>
                                                    <p class="text-sm">Tidak ada item pendukung</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
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
                                    <p class="text-lg font-medium mb-2">Belum ada data KSO</p>
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
    </div>

    <script>
    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        const arrow = document.getElementById('arrow-' + id);
        
        // Close all other dropdowns
        document.querySelectorAll('[id^="equipment-"]').forEach(function(element) {
            if (element.id !== id) {
                element.classList.add('hidden');
                const otherArrow = document.getElementById('arrow-' + element.id);
                if (otherArrow) {
                    otherArrow.classList.remove('rotate-180');
                }
            }
        });
        
        // Toggle current dropdown
        dropdown.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
        
        // Add smooth animation
        if (!dropdown.classList.contains('hidden')) {
            dropdown.style.opacity = '0';
            dropdown.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                dropdown.style.transition = 'all 0.3s ease-in-out';
                dropdown.style.opacity = '1';
                dropdown.style.transform = 'translateY(0)';
            }, 10);
        }
    }

    // Close dropdowns when clicking outside table
    document.addEventListener('click', function(event) {
        const isTableRow = event.target.closest('tr[onclick^="toggleDropdown"]');
        const isDropdownContent = event.target.closest('[id^="equipment-"]');
        const isActionButton = event.target.closest('a[onclick="event.stopPropagation()"]');
        
        if (!isTableRow && !isDropdownContent && !isActionButton) {
            document.querySelectorAll('[id^="equipment-"]').forEach(function(element) {
                element.classList.add('hidden');
                const arrow = document.getElementById('arrow-' + element.id);
                if (arrow) {
                    arrow.classList.remove('rotate-180');
                }
            });
        }
    });
    </script>
</div>
@endsection
