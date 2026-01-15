@extends('layouts.app')

@section('title', 'Stok Keluar')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Daftar Stok Keluar</h1>
    <div class="flex space-x-3">
        <a href="{{ route('stock.out.draft.index') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center relative">
            <i class="fas fa-save mr-2"></i>
            Draft
            @if($draftCount > 0)
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center min-w-[1.5rem]">
                    {{ $draftCount > 99 ? '99+' : $draftCount }}
                </span>
            @endif
        </a>
        <a href="{{ route('stock.out.create') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Stok Keluar
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" action="{{ route('stock.out.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
            <select name="customer_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Customer</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }}
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
            <a href="{{ route('stock.out.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md flex items-center">
                <i class="fas fa-times mr-2"></i>
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Active Filters Info -->
@if(request()->hasAny(['customer_id', 'order_number', 'invoice_number', 'date_from', 'date_to']))
<div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-filter text-blue-400"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-blue-700">
                <strong>Filter Aktif:</strong>
                @if(request('customer_id'))
                    Customer: {{ $customers->where('id', request('customer_id'))->first()->name ?? 'Unknown' }}
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pemesanan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Invoice</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Transaksi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Qty</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($stockOuts as $transaction)
                    <tr class="hover:bg-gray-50">
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
                            {{ $transaction->customer->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
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
                            <div class="flex space-x-2">
                                <button type="button" 
                                        onclick="toggleDetails('transaction-{{ $loop->index }}')"
                                        class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                                <button type="button" 
                                        onclick="openInvoice('{{ $transaction->order_number }}', '{{ $transaction->invoice_number }}', '{{ $transaction->customer->id ?? '' }}', '{{ $transaction->transaction_date->format('Y-m-d') }}')"
                                        class="text-green-600 hover:text-green-900 mr-2"
                                        title="Cetak Faktur">
                                    <i class="fas fa-print"></i> Faktur
                                </button>
                                <button type="button" 
                                        onclick="openDeliveryNote('{{ $transaction->order_number }}', '{{ $transaction->invoice_number }}', '{{ $transaction->customer->id ?? '' }}', '{{ $transaction->transaction_date->format('Y-m-d') }}')"
                                        class="text-purple-600 hover:text-purple-900"
                                        title="Cetak Surat Jalan">
                                    <i class="fas fa-truck"></i> Surat Jalan
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Detail Row (Hidden by default) -->
                    <tr id="transaction-{{ $loop->index }}" class="hidden bg-gray-50">
                        <td colspan="8" class="px-6 py-4">
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <h4 class="font-semibold text-gray-900 mb-3">Detail Produk Transaksi</h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. Referensi</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Harga Satuan</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Diskon</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($transaction->items as $item)
                                                <tr>
                                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $item->reference_number }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $item->product->name }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $item->quantity }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-900">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-900">
                                                        @if($item->discount_percent > 0)
                                                            {{ $item->discount_percent }}%
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-2 text-sm text-gray-900">
                                                        @php
                                                            $subtotal = $item->quantity * $item->unit_price;
                                                            if($item->discount_percent > 0) {
                                                                $subtotal = $subtotal - ($subtotal * ($item->discount_percent / 100));
                                                            }
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
                                
                                <!-- Action buttons in detail -->
                                <div class="mt-4 flex justify-end space-x-2">
                                    <button type="button" 
                                            onclick="openInvoice('{{ $transaction->order_number }}', '{{ $transaction->invoice_number }}', '{{ $transaction->customer->id ?? '' }}', '{{ $transaction->transaction_date->format('Y-m-d') }}')"
                                            class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 mr-2">
                                        <i class="fas fa-print mr-2"></i>
                                        Cetak Faktur
                                    </button>
                                    <button type="button" 
                                            onclick="openDeliveryNote('{{ $transaction->order_number }}', '{{ $transaction->invoice_number }}', '{{ $transaction->customer->id ?? '' }}', '{{ $transaction->transaction_date->format('Y-m-d') }}')"
                                            class="inline-flex items-center px-3 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700">
                                        <i class="fas fa-truck mr-2"></i>
                                        Cetak Surat Jalan
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data stok keluar
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($stockOuts->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $stockOuts->links() }}
        </div>
    @endif
</div>

<script>
function toggleDetails(rowId) {
    const detailRow = document.getElementById(rowId);
    const button = event.target.closest('button');
    const icon = button.querySelector('i');
    
    if (detailRow.classList.contains('hidden')) {
        detailRow.classList.remove('hidden');
        icon.className = 'fas fa-eye-slash';
        button.innerHTML = '<i class="fas fa-eye-slash"></i> Tutup';
    } else {
        detailRow.classList.add('hidden');
        icon.className = 'fas fa-eye';
        button.innerHTML = '<i class="fas fa-eye"></i> Detail';
    }
}

function openInvoice(orderNumber, invoiceNumber, customerId, transactionDate) {
    // Create a temporary form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("stock.out.export.invoice") }}';
    form.target = '_blank'; // Open in new tab
    form.style.display = 'none';
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    // Add order number
    const orderInput = document.createElement('input');
    orderInput.type = 'hidden';
    orderInput.name = 'order_number';
    orderInput.value = orderNumber || '';
    form.appendChild(orderInput);
    
    // Add invoice number
    const invoiceInput = document.createElement('input');
    invoiceInput.type = 'hidden';
    invoiceInput.name = 'invoice_number';
    invoiceInput.value = invoiceNumber || '';
    form.appendChild(invoiceInput);
    
    // Add customer ID
    const customerInput = document.createElement('input');
    customerInput.type = 'hidden';
    customerInput.name = 'customer_id';
    customerInput.value = customerId || '';
    form.appendChild(customerInput);
    
    // Add transaction date
    const dateInput = document.createElement('input');
    dateInput.type = 'hidden';
    dateInput.name = 'transaction_date';
    dateInput.value = transactionDate || '';
    form.appendChild(dateInput);
    
    // Append form to body, submit, and remove
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function openDeliveryNote(orderNumber, invoiceNumber, customerId, transactionDate) {
    // Create a temporary form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("stock.out.export.delivery") }}';
    form.target = '_blank'; // Open in new tab
    form.style.display = 'none';
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    // Add delivery number (use order number as base)
    const deliveryInput = document.createElement('input');
    deliveryInput.type = 'hidden';
    deliveryInput.name = 'delivery_number';
    deliveryInput.value = orderNumber ? 'SJ/' + orderNumber.replace(/[^0-9]/g, '') + '/IX/MSA/25' : '';
    form.appendChild(deliveryInput);
    
    // Add customer ID
    const customerInput = document.createElement('input');
    customerInput.type = 'hidden';
    customerInput.name = 'customer_id';
    customerInput.value = customerId || '';
    form.appendChild(customerInput);
    
    // Add transaction date
    const dateInput = document.createElement('input');
    dateInput.type = 'hidden';
    dateInput.name = 'transaction_date';
    dateInput.value = transactionDate || '';
    form.appendChild(dateInput);
    
    // Append form to body, submit, and remove
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>
@endsection
