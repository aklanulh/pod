@extends('layouts.app')

@section('title', 'Detail Produk - ' . $product->name)

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <div class="flex items-center mb-2">
                <a href="{{ route('reports.stock') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Detail Produk</h1>
            </div>
            <p class="text-gray-600">History pergerakan stok dan informasi lengkap untuk {{ $product->name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('reports.export.stock.detail', $product->id) }}" 
               class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-file-excel mr-2"></i>
                Export Excel
            </a>
        </div>
    </div>
</div>

<!-- Product Info Card -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Produk</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-500">Nama Produk</label>
                    <p class="text-gray-900">{{ $product->name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Kode Produk</label>
                    <p class="text-gray-900">{{ $product->code }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Kategori</label>
                    <p class="text-gray-900">{{ $product->category->name ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Satuan</label>
                    <p class="text-gray-900">{{ $product->unit }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Harga Satuan</label>
                    <p class="text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Deskripsi</label>
                    <p class="text-gray-900">{{ $product->description ?? '-' }}</p>
                </div>
            </div>
        </div>
        
        <!-- Statistics -->
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik Stok</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-full">
                            <i class="fas fa-boxes text-blue-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-600">Stok Saat Ini</p>
                            <p class="text-xl font-semibold text-blue-900">{{ number_format($product->current_stock) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-full">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-600">Minimum Stok</p>
                            <p class="text-xl font-semibold text-yellow-900">{{ number_format($product->minimum_stock) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-full">
                            <i class="fas fa-arrow-up text-green-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-600">Total Masuk</p>
                            <p class="text-xl font-semibold text-green-900">{{ number_format($totalStockIn) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-red-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-full">
                            <i class="fas fa-arrow-down text-red-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-600">Total Keluar</p>
                            <p class="text-xl font-semibold text-red-900">{{ number_format($totalStockOut) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 p-4 rounded-lg col-span-2">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-full">
                            <i class="fas fa-calculator text-purple-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-purple-600">Nilai Stok Saat Ini</p>
                            <p class="text-2xl font-semibold text-purple-900">Rp {{ number_format($product->current_stock * $product->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stock Status -->
            <div class="mt-4 p-4 rounded-lg {{ $product->current_stock <= $product->minimum_stock ? 'bg-red-50 border border-red-200' : 'bg-green-50 border border-green-200' }}">
                <div class="flex items-center">
                    <div class="p-2 rounded-full {{ $product->current_stock <= $product->minimum_stock ? 'bg-red-100' : 'bg-green-100' }}">
                        <i class="fas {{ $product->current_stock <= $product->minimum_stock ? 'fa-exclamation-triangle text-red-600' : 'fa-check-circle text-green-600' }}"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium {{ $product->current_stock <= $product->minimum_stock ? 'text-red-600' : 'text-green-600' }}">Status Stok</p>
                        <p class="text-lg font-semibold {{ $product->current_stock <= $product->minimum_stock ? 'text-red-900' : 'text-green-900' }}">
                            {{ $product->current_stock <= $product->minimum_stock ? 'Stok Menipis' : 'Stok Aman' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customer Purchase Chart -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
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

<!-- Stock Movement History -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">History Pergerakan Stok</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Referensi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pemesanan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Invoice</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Partner</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Sebelum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Sesudah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($stockMovements as $movement)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $movement->transaction_date->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($movement->type == 'in')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Masuk
                                </span>
                            @elseif($movement->type == 'out')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Keluar
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Opname
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $movement->reference_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $movement->order_number ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $movement->invoice_number ?? '-' }}
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="{{ $movement->type == 'in' ? 'text-green-600' : ($movement->type == 'out' ? 'text-red-600' : 'text-blue-600') }}">
                                {{ $movement->type == 'in' ? '+' : ($movement->type == 'out' ? '-' : '') }}{{ number_format($movement->quantity) }}
                            </span>
                            {{ $product->unit }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($movement->stock_before) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($movement->stock_after) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ Str::limit($movement->notes ?? '-', 30) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada history pergerakan stok
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($stockMovements->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $stockMovements->links() }}
        </div>
    @endif
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
</script>
@endsection
