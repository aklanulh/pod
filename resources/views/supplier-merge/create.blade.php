@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Merge Supplier</h1>
            <p class="text-gray-600">Pilih supplier yang akan di-merge</p>
        </div>
        <a href="{{ route('supplier-merge.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <ul class="text-red-800 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Source Supplier -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                </svg>
                Supplier Source (Akan Dihapus)
            </h2>

            @if($sourceSupplier)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-medium text-gray-900">{{ $sourceSupplier->name }}</h3>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $sourceSupplier->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $sourceSupplier->is_active ? '✓ Aktif' : '✗ Inaktif' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">ID: {{ $sourceSupplier->id }}</p>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600 space-y-1">
                        @if($sourceSupplier->phone)
                            <div><span class="font-medium">Telepon:</span> {{ $sourceSupplier->phone }}</div>
                        @endif
                        @if($sourceSupplier->email)
                            <div><span class="font-medium">Email:</span> {{ $sourceSupplier->email }}</div>
                        @endif
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        <div>Transaksi: {{ $sourceSupplier->stockMovements()->count() }}</div>
                    </div>
                </div>
                <button onclick="clearSource()" class="text-red-600 hover:text-red-800 text-sm">
                    <i class="fas fa-times mr-1"></i> Hapus Pilihan
                </button>
            @else
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Supplier Source</label>
                    <div class="relative">
                        <input type="text" id="sourceSearch" placeholder="Ketik nama supplier..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <div id="sourceSearchResults" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto hidden"></div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Target Supplier -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Supplier Target (Akan Dipertahankan)
            </h2>

            @if($targetSupplier)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-medium text-gray-900">{{ $targetSupplier->name }}</h3>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $targetSupplier->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $targetSupplier->is_active ? '✓ Aktif' : '✗ Inaktif' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">ID: {{ $targetSupplier->id }}</p>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600 space-y-1">
                        @if($targetSupplier->phone)
                            <div><span class="font-medium">Telepon:</span> {{ $targetSupplier->phone }}</div>
                        @endif
                        @if($targetSupplier->email)
                            <div><span class="font-medium">Email:</span> {{ $targetSupplier->email }}</div>
                        @endif
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        <div>Transaksi: {{ $targetSupplier->stockMovements()->count() }}</div>
                    </div>
                </div>
                <button onclick="clearTarget()" class="text-red-600 hover:text-red-800 text-sm">
                    <i class="fas fa-times mr-1"></i> Hapus Pilihan
                </button>
            @else
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Supplier Target</label>
                    <div class="relative">
                        <input type="text" id="targetSearch" placeholder="Ketik nama supplier..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <div id="targetSearchResults" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto hidden"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Preview Merge -->
    @if($sourceSupplier && $targetSupplier)
        <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Preview Merge
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-medium text-blue-800 mb-2">Data yang akan dipindahkan:</h4>
                    <div class="text-sm text-blue-700 space-y-1">
                        <div>Stock Movements: <span class="font-bold" id="stockMovementsCount">-</span></div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h4 class="font-medium text-yellow-800 mb-2">Impact setelah merge:</h4>
                    <div class="text-sm text-yellow-700">
                        <div>Total Transaksi Target: <span class="font-bold" id="totalTransactions">-</span></div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('supplier-merge.merge') }}" onsubmit="return confirmMerge()">
                @csrf
                <input type="hidden" name="source_supplier_id" value="{{ $sourceSupplier->id }}">
                <input type="hidden" name="target_supplier_id" value="{{ $targetSupplier->id }}">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Merge <span class="text-gray-400 text-xs">(Opsional)</span></label>
                    <textarea name="reason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Jelaskan alasan merge ini (opsional)..."></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"></path>
                        </svg>
                        Execute Merge - Konfirmasi!
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

<script>
let sourceSearchTimeout;
let targetSearchTimeout;

// Source search
document.getElementById('sourceSearch').addEventListener('input', function(e) {
    clearTimeout(sourceSearchTimeout);
    const query = e.target.value.trim();
    
    if (query.length < 2) {
        document.getElementById('sourceSearchResults').classList.add('hidden');
        return;
    }
    
    sourceSearchTimeout = setTimeout(() => {
        searchSuppliers(query, 'source');
    }, 300);
});

// Target search
document.getElementById('targetSearch').addEventListener('input', function(e) {
    clearTimeout(targetSearchTimeout);
    const query = e.target.value.trim();
    
    if (query.length < 2) {
        document.getElementById('targetSearchResults').classList.add('hidden');
        return;
    }
    
    targetSearchTimeout = setTimeout(() => {
        searchSuppliers(query, 'target');
    }, 300);
});

function searchSuppliers(query, type) {
    fetch(`/supplier-merge/search?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            const resultsDiv = document.getElementById(type + 'SearchResults');
            
            if (data.length === 0) {
                resultsDiv.innerHTML = '<div class="p-3 text-gray-500 text-sm">Tidak ada supplier ditemukan</div>';
            } else {
                resultsDiv.innerHTML = data.map(supplier => `
                    <div class="p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0" onclick="selectSupplier(${supplier.id}, '${type}')">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-medium text-gray-900">${supplier.name}</div>
                                <div class="text-xs text-gray-500">ID: ${supplier.id}</div>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full ${supplier.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${supplier.is_active ? 'Aktif' : 'Inaktif'}
                            </span>
                        </div>
                        ${supplier.phone ? `<div class="text-sm text-gray-600 mt-1">${supplier.phone}</div>` : ''}
                        ${supplier.email ? `<div class="text-sm text-gray-600">${supplier.email}</div>` : ''}
                    </div>
                `).join('');
            }
            
            resultsDiv.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Search error:', error);
        });
}

function selectSupplier(supplierId, type) {
    const url = new URL(window.location);
    url.searchParams.set(type + '_supplier_id', supplierId);
    
    // Remove the other type if it exists to avoid conflicts
    if (type === 'source') {
        url.searchParams.delete('target_supplier_id');
    } else {
        url.searchParams.delete('source_supplier_id');
    }
    
    window.location.href = url.toString();
}

function clearSource() {
    const url = new URL(window.location);
    url.searchParams.delete('source_supplier_id');
    window.location.href = url.toString();
}

function clearTarget() {
    const url = new URL(window.location);
    url.searchParams.delete('target_supplier_id');
    window.location.href = url.toString();
}

function confirmMerge() {
    return confirm('Apakah Anda yakin ingin melakukan merge ini? Source supplier akan dihapus permanen dan semua datanya akan dipindahkan ke target supplier.');
}

// Load preview data if both suppliers are selected
@if($sourceSupplier && $targetSupplier)
    loadPreviewData();
    
    function loadPreviewData() {
        fetch(`/supplier-merge/preview?source_supplier_id={{ $sourceSupplier->id }}&target_supplier_id={{ $targetSupplier->id }}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('stockMovementsCount').textContent = data.stock_movements_count || 0;
                
                const sourceCount = {{ $sourceSupplier->stockMovements()->count() }};
                const targetCount = {{ $targetSupplier->stockMovements()->count() }};
                document.getElementById('totalTransactions').textContent = sourceCount + targetCount;
            })
            .catch(error => {
                console.error('Preview error:', error);
                document.getElementById('stockMovementsCount').textContent = 'Error';
                document.getElementById('totalTransactions').textContent = 'Error';
            });
    }
@endif

// Close search results when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#sourceSearch') && !e.target.closest('#sourceSearchResults')) {
        document.getElementById('sourceSearchResults').classList.add('hidden');
    }
    if (!e.target.closest('#targetSearch') && !e.target.closest('#targetSearchResults')) {
        document.getElementById('targetSearchResults').classList.add('hidden');
    }
});
</script>
@endsection
