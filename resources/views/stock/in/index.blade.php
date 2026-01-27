@extends('layouts.app')

@section('title', 'Stok Masuk')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Daftar Stok Masuk</h1>
    <div class="flex space-x-3">
        <a href="{{ url('/stock/in/drafts') }}" class="relative bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-save mr-2"></i>
            Draft
            @if($draftCount > 0)
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center">
                    {{ $draftCount > 99 ? '99+' : $draftCount }}
                </span>
            @endif
        </a>
        <a href="{{ route('stock.in.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Stok Masuk
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" action="{{ route('stock.in.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
            <select name="supplier_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Supplier</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">No. Pemesanan</label>
            <input type="text" name="order_number" value="{{ request('order_number') }}" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Cari nomor pemesanan">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">No. Invoice</label>
            <input type="text" name="invoice_number" value="{{ request('invoice_number') }}" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Cari nomor invoice">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dari</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Sampai</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div class="lg:col-span-5 flex justify-end space-x-2 mt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center">
                <i class="fas fa-filter mr-2"></i>
                Filter
            </button>
            <a href="{{ route('stock.in.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md flex items-center">
                <i class="fas fa-times mr-2"></i>
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Active Filters Info -->
@if(request()->hasAny(['supplier_id', 'order_number', 'invoice_number', 'date_from', 'date_to']))
<div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-filter text-blue-400"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-blue-700">
                <strong>Filter Aktif:</strong>
                @if(request('supplier_id'))
                    Supplier: {{ $suppliers->where('id', request('supplier_id'))->first()->name ?? 'Unknown' }}
                @endif
                @if(request('order_number'))
                    No. Pemesanan: {{ request('order_number') }}
                @endif
                @if(request('invoice_number'))
                    No. Invoice: {{ request('invoice_number') }}
                @endif
                @if(request('date_from'))
                    Tanggal: {{ request('date_from') }}
                @endif
                @if(request('date_to') && request('date_to') != request('date_from'))
                    - {{ request('date_to') }}
                @endif
            </p>
        </div>
    </div>
</div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'order_number', 'sort_order' => request('sort_by') == 'order_number' && request('sort_order') == 'asc' ? 'desc' : 'asc']) }}" 
                           class="flex items-center hover:text-gray-700">
                            No. Pemesanan
                            @if(request('sort_by') == 'order_number')
                                <i class="fas fa-caret-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-400"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'invoice_number', 'sort_order' => request('sort_by') == 'invoice_number' && request('sort_order') == 'asc' ? 'desc' : 'asc']) }}" 
                           class="flex items-center hover:text-gray-700">
                            No. Invoice
                            @if(request('sort_by') == 'invoice_number')
                                <i class="fas fa-caret-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-400"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'transaction_date', 'sort_order' => request('sort_by') == 'transaction_date' && request('sort_order') == 'asc' ? 'desc' : 'asc']) }}" 
                           class="flex items-center hover:text-gray-700">
                            Tanggal Transaksi
                            @if(request('sort_by') == 'transaction_date')
                                <i class="fas fa-caret-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-400"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'supplier_id', 'sort_order' => request('sort_by') == 'supplier_id' && request('sort_order') == 'asc' ? 'desc' : 'asc']) }}" 
                           class="flex items-center hover:text-gray-700">
                            Supplier
                            @if(request('sort_by') == 'supplier_id')
                                <i class="fas fa-caret-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-400"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'items_count', 'sort_order' => request('sort_by') == 'items_count' && request('sort_order') == 'asc' ? 'desc' : 'asc']) }}" 
                           class="flex items-center hover:text-gray-700">
                            Jumlah Item
                            @if(request('sort_by') == 'items_count')
                                <i class="fas fa-caret-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-400"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'total_quantity', 'sort_order' => request('sort_by') == 'total_quantity' && request('sort_order') == 'asc' ? 'desc' : 'asc']) }}" 
                           class="flex items-center hover:text-gray-700">
                            Total Qty
                            @if(request('sort_by') == 'total_quantity')
                                <i class="fas fa-caret-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-400"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'total_amount', 'sort_order' => request('sort_by') == 'total_amount' && request('sort_order') == 'asc' ? 'desc' : 'asc']) }}" 
                           class="flex items-center hover:text-gray-700">
                            Total Amount
                            @if(request('sort_by') == 'total_amount')
                                <i class="fas fa-caret-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-400"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($stockIns as $transaction)
                    <tr class="hover:bg-gray-100 cursor-pointer transition-colors" 
                        onclick="toggleDetails('transaction-{{ $loop->index }}')">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->order_number ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->invoice_number ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->supplier->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $transaction->items_count }} produk
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($transaction->total_quantity) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($transaction->include_tax)
                                <div class="font-semibold">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
                                <div class="text-green-600 text-xs">ppn 11%: Rp {{ number_format($transaction->subtotal_amount * 0.11, 0, ',', '.') }}</div>
                            @else
                                <div class="font-semibold">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button type="button" 
                                    onclick="event.stopPropagation(); printPurchaseOrder('{{ $transaction->order_number }}', '{{ $transaction->invoice_number }}', '{{ $transaction->supplier ? $transaction->supplier->id : '' }}', '{{ $transaction->transaction_date->format('Y-m-d') }}')"
                                    class="text-green-600 hover:text-green-900"
                                    title="Cetak Purchase Order">
                                <i class="fas fa-print"></i> PO
                            </button>
                        </td>
                    </tr>
                    <!-- Detail Row (Hidden by default) -->
                    <tr id="transaction-{{ $loop->index }}" class="hidden bg-gray-50">
                        <td colspan="9" class="px-6 py-4">
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <h4 class="font-semibold text-gray-900 mb-3">Detail Produk Transaksi</h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. Referensi</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Harga Satuan</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($transaction->items as $item)
                                                <tr>
                                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $item->reference_number }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-900">
                                                        <div class="font-medium">{{ $item->product->name }}</div>
                                                        <div class="text-gray-500 text-xs">{{ $item->product->code }}</div>
                                                    </td>
                                                    <td class="px-4 py-2 text-sm text-gray-600">{{ $item->product->description ?? '-' }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $item->quantity }} {{ $item->product->unit }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-900">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-900">
                                                        @php
                                                            $subtotal = $item->quantity * $item->unit_price;
                                                        @endphp
                                                        Rp {{ number_format($subtotal, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($transaction->notes)
                                    <div class="mt-3 p-3 bg-yellow-50 rounded-md">
                                        <p class="text-sm text-gray-700"><strong>Catatan:</strong> {{ $transaction->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            Belum ada data stok masuk
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($stockIns->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $stockIns->links() }}
        </div>
    @endif
</div>

<script>
function toggleDetails(rowId) {
    const detailRow = document.getElementById(rowId);
    
    if (detailRow.classList.contains('hidden')) {
        detailRow.classList.remove('hidden');
    } else {
        detailRow.classList.add('hidden');
    }
}

function printPurchaseOrder(orderNumber, invoiceNumber, supplierId, transactionDate) {
    // Create form data
    const formData = new FormData();
    formData.append('order_number', orderNumber);
    formData.append('invoice_number', invoiceNumber);
    formData.append('supplier_id', supplierId);
    formData.append('transaction_date', transactionDate);
    
    // Open in new window
    const newWindow = window.open('', '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
    
    // Fetch the purchase order
    fetch('/stock/in/export-purchase-order', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.text())
    .then(html => {
        newWindow.document.write(html);
        newWindow.document.close();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mencetak Purchase Order');
        newWindow.close();
    });
}
</script>
@endsection
