@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Detail Produk</h1>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $product->name }}</h2>
                    <p class="text-sm text-gray-600">Kode: {{ $product->code }}</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('products.edit', $product) }}" 
                       class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
                    </a>
                </div>
            </div>
        </div>

        <div class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kategori</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->category->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Satuan</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->unit }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Harga</label>
                        <p class="mt-1 text-sm text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Stok Saat Ini</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->current_stock }} {{ $product->unit }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Stok Minimum</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->minimum_stock }} {{ $product->unit }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status Stok</label>
                        <div class="mt-1">
                            @if($product->isLowStock())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Stok Menipis
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Normal
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($product->description)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $product->description }}</p>
                </div>
            @endif

            <!-- Regulatory Information -->
            @if($product->lot_number || $product->expired_date || $product->distribution_permit)
                <div class="mt-8 border-t pt-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Informasi Regulasi & Kedaluwarsa</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Lot</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->lot_number ?? '-' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Kedaluwarsa</label>
                            @if($product->expired_date)
                                <div class="mt-1">
                                    <p class="text-sm {{ $product->isExpired() ? 'text-red-600 font-semibold' : ($product->isExpiringSoon() ? 'text-yellow-600 font-medium' : 'text-gray-900') }}">
                                        {{ $product->expired_date->format('d/m/Y') }}
                                        @if($product->isExpired())
                                            <i class="fas fa-exclamation-triangle ml-1 text-red-600"></i>
                                        @elseif($product->isExpiringSoon())
                                            <i class="fas fa-clock ml-1 text-yellow-600"></i>
                                        @endif
                                    </p>
                                    @if($product->isExpired())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                                            Kedaluwarsa
                                        </span>
                                    @elseif($product->isExpiringSoon())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                            Akan Kedaluwarsa
                                        </span>
                                    @endif
                                </div>
                            @else
                                <p class="mt-1 text-sm text-gray-900">-</p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Izin Edar</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->distribution_permit ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Customer Purchase Chart -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Grafik Pembelian Customer per Bulan</h3>
                <p class="text-sm text-gray-600">Jumlah produk yang dibeli setiap customer per bulan dalam tahun {{ $selectedYear }}</p>
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
            <canvas id="productCustomerChart"></canvas>
        </div>
    </div>

    @if($product->stockMovements->count() > 0)
        <div class="mt-8 bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Pergerakan Stok</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Partner</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($product->stockMovements->sortByDesc('transaction_date') as $movement)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $movement->transaction_date->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($movement->type === 'in')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Masuk
                                        </span>
                                    @elseif($movement->type === 'out')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Keluar
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Opname
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $movement->quantity }} {{ $product->unit }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($movement->supplier)
                                        {{ $movement->supplier->name }}
                                    @elseif($movement->customer)
                                        {{ $movement->customer->name }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @if($movement->notes && strlen($movement->notes) > 50)
                                        <span class="truncate block max-w-xs">{{ Str::limit($movement->notes, 50) }}</span>
                                        <button onclick="showNoteModal('{{ addslashes($movement->notes) }}')" 
                                                class="text-blue-600 hover:text-blue-800 cursor-pointer inline-flex items-center mt-1">
                                            <i class="fas fa-eye text-xs mr-1"></i>
                                            <span class="text-xs">Lihat lengkap</span>
                                        </button>
                                    @else
                                        {{ $movement->notes ?? '-' }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<!-- Modal untuk menampilkan catatan lengkap -->
<div id="noteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Catatan Lengkap</h3>
                <button onclick="closeNoteModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="mt-2 px-2 py-2">
                <p id="noteContent" class="text-sm text-gray-700 whitespace-pre-wrap"></p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeNoteModal()" 
                        class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart configuration
    const ctx = document.getElementById('productCustomerChart').getContext('2d');
    const chartData = @json($chartData);
    
    const chart = new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Pembelian per Customer - {{ $product->name }} ({{ $selectedYear }})',
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
                        text: 'Jumlah Pembelian'
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
});

function showNoteModal(note) {
    document.getElementById('noteContent').textContent = note;
    document.getElementById('noteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeNoteModal() {
    document.getElementById('noteModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('noteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeNoteModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeNoteModal();
    }
});
</script>
@endsection
