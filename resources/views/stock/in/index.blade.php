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

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pemesanan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Invoice</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Transaksi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Qty</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($stockIns as $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->order_number ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->invoice_number ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->transaction_date->format('d/m/Y H:i') }}
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
                            <div class="flex space-x-2">
                                <button type="button" 
                                        onclick="toggleDetails('transaction-{{ $loop->index }}')"
                                        class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                                <button type="button" 
                                        onclick="printPurchaseOrder('{{ $transaction->order_number }}', '{{ $transaction->invoice_number }}', '{{ $transaction->supplier->id }}', '{{ $transaction->transaction_date->format('Y-m-d') }}')"
                                        class="text-green-600 hover:text-green-900"
                                        title="Cetak Purchase Order">
                                    <i class="fas fa-print"></i> PO
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
