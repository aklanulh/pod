@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Supplier Merge Management</h1>
            <p class="text-gray-600">Kelola dan merge supplier duplikat</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('supplier-merge.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"></path>
                </svg>
                Merge Supplier
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

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

    <!-- Potential Duplicates -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            Supplier Duplikat Potensial
        </h2>

        @if($potentialDuplicates->count() > 0)
            <div class="space-y-6">
                @foreach($potentialDuplicates as $group)
                    <div class="border border-yellow-200 rounded-lg p-4 bg-yellow-50">
                        <div class="mb-3">
                            <p class="text-sm font-medium text-yellow-800 mb-1">{{ $group['reason'] }}</p>
                            <p class="text-xs text-yellow-600">Tipe: {{ $group['group_type'] }}</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($group['suppliers'] as $supplier)
                                <div class="bg-white rounded-lg p-3 border @if($supplier->is_active) border-green-300 @else border-red-300 @endif">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-medium text-gray-900">{{ $supplier->name }}</h4>
                                        <div class="flex flex-col gap-1">
                                            <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">ID: {{ $supplier->id }}</span>
                                            <span class="text-xs px-2 py-1 rounded-full @if($supplier->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                                @if($supplier->is_active) Aktif @else Inaktif @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-600 space-y-1">
                                        @if($supplier->phone)
                                            <div><span class="font-medium">Telepon:</span> {{ $supplier->phone }}</div>
                                        @endif
                                        @if($supplier->email)
                                            <div><span class="font-medium">Email:</span> {{ $supplier->email }}</div>
                                        @endif
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500">
                                        <div>Transaksi: {{ $supplier->stockMovements()->count() }}</div>
                                        <div class="font-medium">Total: {{ $supplier->stockMovements()->count() }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Merge Buttons Inside Container -->
                        <div class="flex gap-2 flex-wrap mt-4 pt-3 border-t border-yellow-300">
                            @php
                                $activeSuppliers = $group['suppliers']->where('is_active', true);
                                $bestTarget = null;
                                if($activeSuppliers->count() > 0) {
                                    $bestTarget = $activeSuppliers->sortByDesc(function($supplier) {
                                        return $supplier->stockMovements()->count();
                                    })->first();
                                }
                            @endphp
                            
                            @if($bestTarget)
                                @foreach($group['suppliers'] as $supplier)
                                    @if($supplier->id != $bestTarget->id)
                                        <button onclick="showMergeModal({{ $supplier->id }}, {{ $bestTarget->id }})" 
                                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                            {{ $supplier->name }} → {{ $bestTarget->name }}
                                        </button>
                                    @endif
                                @endforeach
                            @else
                                <span class="text-xs text-red-600 bg-red-50 px-2 py-1 rounded">
                                    Tidak ada supplier aktif
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 bg-gray-50 rounded-lg">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-500">Tidak ada supplier duplikat potensial yang ditemukan</p>
            </div>
        @endif
    </div>

    <!-- Manual Merge -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
            </svg>
            Merge Manual
        </h2>
        
        <div class="text-center py-8">
            <a href="{{ route('supplier-merge.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                </svg>
                Mulai Merge Supplier
            </a>
        </div>
    </div>
</div>

<!-- Merge Modal -->
<div id="mergeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Konfirmasi Merge Supplier</h3>
            <div id="mergeContent"></div>
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeMergeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Batal
                </button>
                <button id="confirmMergeBtn" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Merge
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showMergeModal(sourceId, targetId) {
    const modal = document.getElementById('mergeModal');
    const content = document.getElementById('mergeContent');
    const confirmBtn = document.getElementById('confirmMergeBtn');
    
    content.innerHTML = `
        <div class="space-y-3">
            <div class="p-3 bg-red-50 rounded border border-red-200">
                <p class="text-sm font-medium text-red-800">Source (Akan Dihapus):</p>
                <p class="text-sm text-red-600">ID: ${sourceId}</p>
            </div>
            <div class="text-center text-gray-500">
                <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </div>
            <div class="p-3 bg-green-50 rounded border border-green-200">
                <p class="text-sm font-medium text-green-800">Target (Akan Dipertahankan):</p>
                <p class="text-sm text-green-600">ID: ${targetId}</p>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-4">Source supplier akan dihapus dan semua datanya dipindahkan ke target supplier.</p>
    `;
    
    confirmBtn.onclick = function() {
        performMerge(sourceId, targetId);
    };
    
    modal.classList.remove('hidden');
}

function closeMergeModal() {
    document.getElementById('mergeModal').classList.add('hidden');
}

function performMerge(sourceId, targetId) {
    const confirmBtn = document.getElementById('confirmMergeBtn');
    const originalText = confirmBtn.innerHTML;
    
    // Show loading state
    confirmBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 inline" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Merging...';
    confirmBtn.disabled = true;
    
    // Perform merge via AJAX
    fetch('/supplier-merge/merge', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            source_supplier_id: sourceId,
            target_supplier_id: targetId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            closeMergeModal();
            showSuccessMessage('Supplier berhasil di-merge!');
            
            // Reload page after 2 seconds
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            // Show error message
            showErrorMessage(data.message || 'Terjadi kesalahan saat merge supplier');
            confirmBtn.innerHTML = originalText;
            confirmBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage('Terjadi kesalahan saat merge supplier');
        confirmBtn.innerHTML = originalText;
        confirmBtn.disabled = false;
    });
}

function showSuccessMessage(message) {
    // Create success alert
    const alert = document.createElement('div');
    alert.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50';
    alert.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            ${message}
        </div>
    `;
    document.body.appendChild(alert);
    
    // Remove after 3 seconds
    setTimeout(() => {
        alert.remove();
    }, 3000);
}

function showErrorMessage(message) {
    // Create error alert
    const alert = document.createElement('div');
    alert.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50';
    alert.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            ${message}
        </div>
    `;
    document.body.appendChild(alert);
    
    // Remove after 3 seconds
    setTimeout(() => {
        alert.remove();
    }, 3000);
}
</script>
@endsection
