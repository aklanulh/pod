@extends('layouts.app')

@section('title', 'Draft Stok Masuk')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <a href="{{ route('stock.in.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="text-xl font-semibold text-gray-800">
                        <i class="fas fa-save mr-2"></i>
                        Draft Stok Masuk
                    </h2>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('stock.in.index') }}" 
                       class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        <i class="fas fa-list mr-2"></i>
                        Daftar Stok Masuk
                    </a>
                    <a href="{{ route('stock.in.create') }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Stok Masuk
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mx-6 mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mx-6 mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="p-6">
            @if($drafts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No. Draft
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Supplier
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No. Order
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Amount
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Items
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($drafts as $draft)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $draft->draft_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $draft->supplier ? $draft->supplier->name : $draft->supplier_name }}
                                        @if($draft->supplier)
                                            <br><small class="text-gray-400">{{ $draft->supplier->phone }}</small>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $draft->order_number ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $draft->transaction_date ? $draft->transaction_date->format('d/m/Y') : $draft->created_at->format('d/m/Y') }}
                                        <br><small class="text-gray-400">{{ $draft->created_at->format('H:i') }}</small>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Rp {{ number_format($draft->total_amount, 0, ',', '.') }}
                                        @if($draft->include_tax)
                                            <br><small class="text-green-600">+ PPN 11%</small>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ count($draft->cart_data) }} produk
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button type="button" onclick="printPurchaseOrder({{ $draft->id }})"
                                                    class="text-green-600 hover:text-green-900" title="Cetak PO">
                                                <i class="fas fa-print"></i>
                                            </button>
                                            <a href="{{ route('stock.in.draft.edit', $draft->id) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <form action="{{ route('stock.in.draft.process', $draft->id) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Yakin ingin memproses draft ini menjadi transaksi? Stok akan bertambah.')">
                                                @csrf
                                                <button type="submit" 
                                                        class="text-green-600 hover:text-green-900"
                                                        title="Proses Draft">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('stock.in.draft.delete', $draft->id) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus draft ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900"
                                                        title="Hapus Draft">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $drafts->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-save text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500 text-lg">Belum ada draft stok masuk</p>
                    <p class="text-gray-400 text-sm mt-2">Draft akan muncul di sini ketika Anda menyimpan transaksi sementara</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function printPurchaseOrder(draftId) {
    // Fetch draft data
    fetch(`/stock/in/draft/${draftId}/data`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Create form data for PO
            const formData = new FormData();
            formData.append('order_number', data.draft.order_number || 'PO-' + new Date().toISOString().slice(0,10).replace(/-/g,''));
            formData.append('invoice_number', data.draft.invoice_number || 'INV-' + new Date().toISOString().slice(0,10).replace(/-/g,''));
            formData.append('supplier_id', data.draft.supplier_id);
            formData.append('transaction_date', data.draft.transaction_date || new Date().toISOString().slice(0,10));
            
            // Add product data from draft
            data.draft.cart_data.forEach((item, index) => {
                formData.append(`products[${index}][product_id]`, item.product_id);
                formData.append(`products[${index}][quantity]`, item.quantity);
                formData.append(`products[${index}][unit_price]`, item.unit_price);
            });
            
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
        } else {
            alert('Error: ' + (data.message || 'Terjadi kesalahan'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengambil data draft');
    });
}
</script>
@endsection
