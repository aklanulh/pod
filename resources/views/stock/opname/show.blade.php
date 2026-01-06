@extends('layouts.app')

@section('title', 'Detail Stok Opname')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Detail Stok Opname</h1>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('stock.opname.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
            
            @if($stockOpname->status === 'draft')
                <form action="{{ route('stock.opname.destroy', $stockOpname) }}" method="POST" class="inline" 
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh opname ini? Data tidak dapat dikembalikan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md transition-colors duration-200">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus Opname
                    </button>
                </form>
                
                <form action="{{ route('stock.opname.complete', $stockOpname) }}" method="POST" class="inline" 
                      onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan opname ini? Stok akan disesuaikan secara otomatis.')">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors duration-200">
                        <i class="fas fa-check mr-2"></i>
                        Selesaikan Opname
                    </button>
                </form>
            @else
                <span class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-600 font-medium rounded-md">
                    <i class="fas fa-check-circle mr-2"></i>
                    Opname Telah Selesai
                </span>
            @endif
        </div>
    </div>

    <!-- Header Information -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Stok Opname #{{ $stockOpname->id }}</h2>
                    <p class="text-sm text-gray-600">{{ $stockOpname->opname_date ? $stockOpname->opname_date->format('d F Y') : 'Tanggal tidak tersedia' }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800">
                        {{ $stockOpname->details->count() }} Produk
                    </span>
                </div>
            </div>
        </div>

        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Opname</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $stockOpname->opname_date ? $stockOpname->opname_date->format('d F Y') : 'Tanggal tidak tersedia' }}</p>
                </div>

                @if($stockOpname->notes)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Catatan</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $stockOpname->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Product Details Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Detail Produk</h3>
            @if($stockOpname->status === 'draft')
                <button onclick="showAddModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Produk
                </button>
            @endif
        </div>
        
        @if($stockOpname->details && $stockOpname->details->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Sistem</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Fisik</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selisih</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                            @if($stockOpname->status === 'draft')
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($stockOpname->details as $detail)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $detail->product->code ?? 'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $detail->product->name ?? 'Produk tidak ditemukan' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($detail->system_stock) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($detail->physical_stock) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($detail->difference > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            +{{ number_format($detail->difference) }}
                                        </span>
                                    @elseif($detail->difference < 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ number_format($detail->difference) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            0
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    @if($detail->notes && strlen($detail->notes) > 50)
                                        <span class="cursor-pointer text-blue-600 hover:text-blue-800" 
                                              onclick="showModal('{{ addslashes($detail->notes) }}')">
                                            {{ Str::limit($detail->notes, 50) }}
                                        </span>
                                    @else
                                        {{ $detail->notes ?? '-' }}
                                    @endif
                                </td>
                                @if($stockOpname->status === 'draft')
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button onclick="showEditModal({{ $detail->id }}, {{ $detail->physical_stock }}, '{{ addslashes($detail->notes ?? '') }}')" 
                                                    class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('stock.opname.detail.delete', [$stockOpname->id, $detail->id]) }}" 
                                                  method="POST" class="inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus detail ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-4 text-center text-gray-500">
                Belum ada detail produk
            </div>
        @endif
    </div>

    <!-- Summary Statistics -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-arrow-up text-green-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Stok Lebih</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $stockOpname->details->where('difference', '>', 0)->count() }} Produk
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-arrow-down text-red-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Stok Kurang</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $stockOpname->details->where('difference', '<', 0)->count() }} Produk
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-gray-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Stok Sesuai</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $stockOpname->details->where('difference', '=', 0)->count() }} Produk
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk menampilkan catatan lengkap -->
<div id="notesModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Catatan Lengkap</h3>
            <div class="mt-2 px-7 py-3">
                <p id="modalContent" class="text-sm text-gray-500 text-left"></p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeModal()" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for editing detail -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4 text-center">Edit Detail Opname</h3>
            <form id="editForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stok Fisik</label>
                    <input type="number" id="editPhysicalStock" name="physical_stock" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea id="editNotes" name="notes" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeEditModal()" 
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for adding new detail -->
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4 text-center">Tambah Detail Opname</h3>
            <form action="{{ route('stock.opname.detail.add', $stockOpname->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Produk</label>
                    <select name="product_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Pilih Produk</option>
                        @foreach($products as $product)
                            @if(!$stockOpname->details->contains('product_id', $product->id))
                                <option value="{{ $product->id }}" data-stock="{{ $product->current_stock }}">
                                    {{ $product->code }} - {{ $product->name }} (Stok: {{ $product->current_stock }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stok Fisik</label>
                    <input type="number" name="physical_stock" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea name="notes" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeAddModal()" 
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showModal(content) {
    document.getElementById('modalContent').innerText = content;
    document.getElementById('notesModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('notesModal').classList.add('hidden');
}

function showEditModal(detailId, physicalStock, notes) {
    document.getElementById('editPhysicalStock').value = physicalStock;
    document.getElementById('editNotes').value = notes;
    document.getElementById('editForm').action = `/stock/opname/{{ $stockOpname->id }}/detail/${detailId}/update`;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

function showAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
}
</script>
@endsection
