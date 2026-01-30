@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Merge Customer</h1>
            <p class="text-gray-600">Pilih customer yang akan di-merge</p>
        </div>
        <a href="{{ route('customer-merge.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Source Customer -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                </svg>
                Customer Source (Akan Dihapus)
            </h2>

            @if($sourceCustomer)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-medium text-gray-900">{{ $sourceCustomer->name }}</h3>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $sourceCustomer->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $sourceCustomer->is_active ? '✓ Aktif' : '✗ Inaktif' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">ID: {{ $sourceCustomer->id }}</p>
                        </div>
                        <button onclick="clearSourceCustomer()" class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="text-sm text-gray-600 space-y-1">
                        @if($sourceCustomer->phone)
                            <div><span class="font-medium">Telepon:</span> {{ $sourceCustomer->phone }}</div>
                        @endif
                        @if($sourceCustomer->email)
                            <div><span class="font-medium">Email:</span> {{ $sourceCustomer->email }}</div>
                        @endif
                        @if($sourceCustomer->address)
                            <div><span class="font-medium">Alamat:</span> {{ Str::limit($sourceCustomer->address, 100) }}</div>
                        @endif
                    </div>
                    
                    <div class="mt-3 pt-3 border-t border-red-200">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">KSO Items:</span>
                                <span class="ml-2 text-red-600 font-bold">{{ $sourceCustomer->ksoItems()->count() }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Transaksi:</span>
                                <span class="ml-2 text-red-600 font-bold">{{ $sourceCustomer->stockMovements()->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Customer Source</label>
                    <div class="relative">
                        <input type="text" 
                               id="sourceSearch" 
                               placeholder="Ketik nama, telepon, atau email..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <div id="sourceSearchResults" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden"></div>
                    </div>
                </div>

                @if(!$sourceCustomer)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                        </svg>
                        <p class="text-gray-500">Pilih customer yang akan di-merge</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Target Customer -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                </svg>
                Customer Target (Akan Disimpan)
            </h2>

            @if($targetCustomer)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-medium text-gray-900">{{ $targetCustomer->name }}</h3>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $targetCustomer->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $targetCustomer->is_active ? '✓ Aktif' : '✗ Inaktif' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">ID: {{ $targetCustomer->id }}</p>
                        </div>
                        <button onclick="clearTargetCustomer()" class="text-green-600 hover:text-green-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="text-sm text-gray-600 space-y-1">
                        @if($targetCustomer->phone)
                            <div><span class="font-medium">Telepon:</span> {{ $targetCustomer->phone }}</div>
                        @endif
                        @if($targetCustomer->email)
                            <div><span class="font-medium">Email:</span> {{ $targetCustomer->email }}</div>
                        @endif
                        @if($targetCustomer->address)
                            <div><span class="font-medium">Alamat:</span> {{ Str::limit($targetCustomer->address, 100) }}</div>
                        @endif
                    </div>
                    
                    <div class="mt-3 pt-3 border-t border-green-200">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">KSO Items:</span>
                                <span class="ml-2 text-green-600 font-bold">{{ $targetCustomer->ksoItems()->count() }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Transaksi:</span>
                                <span class="ml-2 text-green-600 font-bold">{{ $targetCustomer->stockMovements()->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Customer Target</label>
                    <div class="relative">
                        <input type="text" 
                               id="targetSearch" 
                               placeholder="Ketik nama, telepon, atau email..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <div id="targetSearchResults" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden"></div>
                    </div>
                </div>

                @if(!$targetCustomer)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500">Pilih customer target</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Merge Preview & Action -->
    @if($sourceCustomer && $targetCustomer)
        <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Preview Merge</h3>
            
            <div id="mergePreview" class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-medium text-blue-800 mb-2">Data yang akan dipindahkan:</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="font-medium">KSO Items:</span>
                            <span class="ml-2 font-bold text-blue-600" id="ksoItemsCount">-</span>
                        </div>
                        <div>
                            <span class="font-medium">Transaksi:</span>
                            <span class="ml-2 font-bold text-blue-600" id="stockMovementsCount">-</span>
                        </div>
                        <div>
                            <span class="font-medium">Schedules:</span>
                            <span class="ml-2 font-bold text-blue-600" id="schedulesCount">-</span>
                        </div>
                        <div>
                            <span class="font-medium">Drafts:</span>
                            <span class="ml-2 font-bold text-blue-600" id="draftsCount">-</span>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h4 class="font-medium text-yellow-800 mb-2">Impact setelah merge:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium">Total Investasi KSO:</span>
                            <span class="ml-2 font-bold text-yellow-600" id="totalInvestment">-</span>
                        </div>
                        <div>
                            <span class="font-medium">Total Sales:</span>
                            <span class="ml-2 font-bold text-yellow-600" id="totalSales">-</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('customer-merge.merge') }}" onsubmit="return confirmMerge()">
                    @csrf
                    <input type="hidden" name="source_customer_id" value="{{ $sourceCustomer->id }}">
                    <input type="hidden" name="target_customer_id" value="{{ $targetCustomer->id }}">
                    
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
        </div>
    @endif
</div>

<script>
let sourceCustomerId = {{ $sourceCustomer?->id ?? 'null' }};
let targetCustomerId = {{ $targetCustomer?->id ?? 'null' }};

// Search functionality
document.getElementById('sourceSearch').addEventListener('input', function(e) {
    searchCustomers(e.target.value, 'source');
});

document.getElementById('targetSearch').addEventListener('input', function(e) {
    searchCustomers(e.target.value, 'target');
});

function searchCustomers(query, type) {
    if (query.length < 2) {
        document.getElementById(type + 'SearchResults').classList.add('hidden');
        return;
    }

    fetch(`{{ route('customer-merge.search-similar') }}?search=${query}`)
        .then(response => response.json())
        .then(customers => {
            const resultsDiv = document.getElementById(type + 'SearchResults');
            resultsDiv.innerHTML = '';
            
            if (customers.length === 0) {
                resultsDiv.innerHTML = '<div class="p-3 text-gray-500 text-sm">Tidak ada hasil</div>';
            } else {
                customers.forEach(customer => {
                    const div = document.createElement('div');
                    div.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0';
                    div.innerHTML = `
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">${customer.name}</div>
                                <div class="text-sm text-gray-600 mt-1">
                                    ${customer.phone ? '<div class="flex items-center"><span class="mr-2">📞</span>' + customer.phone + '</div>' : ''}
                                    ${customer.email ? '<div class="flex items-center"><span class="mr-2">✉️</span>' + customer.email + '</div>' : ''}
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-1 ml-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    ID: ${customer.id}
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${customer.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${customer.is_active ? '✓ Aktif' : '✗ Inaktif'}
                                </span>
                            </div>
                        </div>
                    `;
                    div.onclick = () => selectCustomer(customer, type);
                    resultsDiv.appendChild(div);
                });
            }
            
            resultsDiv.classList.remove('hidden');
        });
}

function selectCustomer(customer, type) {
    if (type === 'source') {
        sourceCustomerId = customer.id;
        let url = `{{ route('customer-merge.create') }}?source_id=${customer.id}`;
        if (targetCustomerId && targetCustomerId !== 'null') {
            url += `&target_id=${targetCustomerId}`;
        }
        window.location.href = url;
    } else {
        targetCustomerId = customer.id;
        let url = `{{ route('customer-merge.create') }}?target_id=${customer.id}`;
        if (sourceCustomerId && sourceCustomerId !== 'null') {
            url += `&source_id=${sourceCustomerId}`;
        }
        window.location.href = url;
    }
}

function clearSourceCustomer() {
    let url = `{{ route('customer-merge.create') }}`;
    if (targetCustomerId && targetCustomerId !== 'null') {
        url += `?target_id=${targetCustomerId}`;
    }
    window.location.href = url;
}

function clearTargetCustomer() {
    let url = `{{ route('customer-merge.create') }}`;
    if (sourceCustomerId && sourceCustomerId !== 'null') {
        url += `?source_id=${sourceCustomerId}`;
    }
    window.location.href = url;
}

function confirmMerge() {
    return confirm('⚠️ PERINGATATAN: Merge akan menghapus customer source dan memindahkan semua data ke target. Tindakan ini tidak dapat dibatalkan. Apakah Anda yakin?');
}

function loadPreviewData() {
    if (sourceCustomerId && targetCustomerId && sourceCustomerId !== 'null' && targetCustomerId !== 'null') {
        // Show loading state
        document.getElementById('ksoItemsCount').textContent = 'Loading...';
        document.getElementById('stockMovementsCount').textContent = 'Loading...';
        document.getElementById('schedulesCount').textContent = 'Loading...';
        document.getElementById('draftsCount').textContent = 'Loading...';
        document.getElementById('totalInvestment').textContent = 'Loading...';
        document.getElementById('totalSales').textContent = 'Loading...';
        
        fetch('{{ route('customer-merge.preview') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                source_customer_id: sourceCustomerId,
                target_customer_id: targetCustomerId
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Preview data:', data); // Debug log
            document.getElementById('ksoItemsCount').textContent = data.kso_items_to_move || 0;
            document.getElementById('stockMovementsCount').textContent = data.stock_movements_to_move || 0;
            document.getElementById('schedulesCount').textContent = data.schedules_to_move || 0;
            document.getElementById('draftsCount').textContent = data.drafts_to_move || 0;
            document.getElementById('totalInvestment').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.total_investment_impact || 0);
            document.getElementById('totalSales').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.total_sales_impact || 0);
        })
        .catch(error => {
            console.error('Error loading preview data:', error);
            // Show error state
            document.getElementById('ksoItemsCount').textContent = 'Error';
            document.getElementById('stockMovementsCount').textContent = 'Error';
            document.getElementById('schedulesCount').textContent = 'Error';
            document.getElementById('draftsCount').textContent = 'Error';
            document.getElementById('totalInvestment').textContent = 'Error';
            document.getElementById('totalSales').textContent = 'Error';
        });
    } else {
        // Clear data if customers not selected
        document.getElementById('ksoItemsCount').textContent = '-';
        document.getElementById('stockMovementsCount').textContent = '-';
        document.getElementById('schedulesCount').textContent = '-';
        document.getElementById('draftsCount').textContent = '-';
        document.getElementById('totalInvestment').textContent = '-';
        document.getElementById('totalSales').textContent = '-';
    }
}

// Load preview data if both customers are selected
@if($sourceCustomer && $targetCustomer)
loadPreviewData();
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
