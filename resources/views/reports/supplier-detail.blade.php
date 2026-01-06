@extends('layouts.app')

@section('title', 'Detail Distributor - ' . $supplier->name)

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <div class="flex items-center mb-2">
                <a href="{{ route('reports.supplier') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Detail Distributor</h1>
            </div>
            <p class="text-gray-600">History transaksi dan statistik untuk {{ $supplier->name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('reports.export.supplier.detail', $supplier->id) }}" 
               class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-file-excel mr-2"></i>
                Export Excel
            </a>
        </div>
    </div>
</div>

<!-- Distributor Info Card -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Distributor</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-500">Nama Distributor</label>
                    <p class="text-gray-900">{{ $supplier->name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Kontak</label>
                    <div class="space-y-1">
                        @if($supplier->contact_person)
                            <p class="text-gray-900">{{ $supplier->contact_person }}</p>
                        @endif
                        @if($supplier->contact_person_2)
                            <p class="text-gray-600">{{ $supplier->contact_person_2 }}</p>
                        @endif
                        @if($supplier->contact_person_3)
                            <p class="text-gray-600">{{ $supplier->contact_person_3 }}</p>
                        @endif
                        @if(!$supplier->contact_person && !$supplier->contact_person_2 && !$supplier->contact_person_3)
                            <p class="text-gray-400">-</p>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Telepon</label>
                    <div class="space-y-1">
                        @if($supplier->phone)
                            <p class="text-gray-900">{{ $supplier->phone }}</p>
                        @endif
                        @if($supplier->phone_2)
                            <p class="text-gray-600">{{ $supplier->phone_2 }}</p>
                        @endif
                        @if($supplier->phone_3)
                            <p class="text-gray-600">{{ $supplier->phone_3 }}</p>
                        @endif
                        @if(!$supplier->phone && !$supplier->phone_2 && !$supplier->phone_3)
                            <p class="text-gray-400">-</p>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Email</label>
                    <p class="text-gray-900">{{ $supplier->email ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Alamat</label>
                    <p class="text-gray-900">{{ $supplier->address ?? '-' }}</p>
                </div>
            </div>
        </div>
        
        <!-- Statistics -->
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik Transaksi</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-full">
                            <i class="fas fa-shopping-cart text-blue-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-600">Total Transaksi</p>
                            <p class="text-xl font-semibold text-blue-900">{{ $totalTransactions }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-full">
                            <i class="fas fa-boxes text-green-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-600">Total Quantity</p>
                            <p class="text-xl font-semibold text-green-900">{{ number_format($totalQuantity) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 p-4 rounded-lg col-span-2">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-full">
                            <i class="fas fa-calculator text-purple-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-purple-600">Total Nilai Pembelian</p>
                            <p class="text-2xl font-semibold text-purple-900">Rp {{ number_format($totalValue, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transaction History -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">History Transaksi</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Referensi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pemesanan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Invoice</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Nilai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($stockMovements as $movement)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $movement->transaction_date->format('d/m/Y H:i') }}
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
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $movement->product->name ?? 'Produk Dihapus' }}</div>
                            <div class="text-sm text-gray-500">{{ $movement->product->code ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($movement->quantity) }} {{ $movement->product->unit ?? 'pcs' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($movement->unit_price ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($movement->quantity * ($movement->unit_price ?? 0), 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ Str::limit($movement->notes ?? '-', 30) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada history transaksi
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
@endsection
