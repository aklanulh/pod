@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Customer Merge Management</h1>
            <p class="text-gray-600">Kelola dan merge customer duplikat</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('customer-merge.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"></path>
                </svg>
                Merge Customer
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
            Customer Duplikat Potensial
        </h2>

        @if($potentialDuplicates->count() > 0)
            <div class="space-y-6">
                @foreach($potentialDuplicates as $group)
                    <div class="border border-yellow-200 rounded-lg p-4 bg-yellow-50">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="text-sm font-medium text-yellow-800 mb-1">{{ $group['reason'] }}</p>
                                <p class="text-xs text-yellow-600">Tipe: {{ $group['group_type'] }}</p>
                            </div>
                            <div class="flex gap-2 flex-wrap">
                                @php
                                    $activeCustomers = $group['customers']->where('is_active', true);
                                    $bestTarget = null;
                                    if($activeCustomers->count() > 0) {
                                        $bestTarget = $activeCustomers->sortByDesc(function($customer) {
                                            return $customer->ksoItems()->count() + $customer->stockMovements()->count();
                                        })->first();
                                    }
                                @endphp
                                
                                @if($bestTarget)
                                    @foreach($group['customers'] as $customer)
                                        @if($customer->id != $bestTarget->id)
                                            <button onclick="showMergeModal({{ $customer->id }}, {{ $bestTarget->id }})" 
                                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                                {{ $customer->name }} → {{ $bestTarget->name }}
                                            </button>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="text-xs text-red-600 bg-red-50 px-2 py-1 rounded">
                                        Tidak ada customer aktif
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($group['customers'] as $customer)
                                <div class="bg-white rounded-lg p-3 border @if($customer->is_active) border-green-300 @else border-red-300 @endif">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-medium text-gray-900">{{ $customer->name }}</h4>
                                        <div class="flex flex-col gap-1">
                                            <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">ID: {{ $customer->id }}</span>
                                            <span class="text-xs px-2 py-1 rounded-full @if($customer->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                                @if($customer->is_active) Aktif @else Inaktif @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-600 space-y-1">
                                        @if($customer->phone)
                                            <div><span class="font-medium">Telepon:</span> {{ $customer->phone }}</div>
                                        @endif
                                        @if($customer->email)
                                            <div><span class="font-medium">Email:</span> {{ $customer->email }}</div>
                                        @endif
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500">
                                        <div>KSO: {{ $customer->ksoItems()->count() }} | Transaksi: {{ $customer->stockMovements()->count() }}</div>
                                        <div class="font-medium">Total: {{ $customer->ksoItems()->count() + $customer->stockMovements()->count() }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 bg-gray-50 rounded-lg">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-500">Tidak ada customer duplikat yang terdeteksi</p>
                <p class="text-sm text-gray-400 mt-1">Sistem akan menampilkan customer dengan nama atau telepon yang mirip</p>
            </div>
        @endif
    </div>
</div>

<!-- Merge Modal -->
<div id="mergeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Konfirmasi Merge Customer</h3>
            <button onclick="closeMergeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="mergeForm" method="POST" action="{{ route('customer-merge.merge') }}">
            @csrf
            <input type="hidden" name="source_customer_id" id="sourceCustomerId">
            <input type="hidden" name="target_customer_id" id="targetCustomerId">
            
            <div class="mb-4">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h4 class="font-medium text-yellow-800 mb-2">Perhatian!</h4>
                    <p class="text-sm text-yellow-700">Merge akan memindahkan semua data dari customer source ke target dan menghapus customer source. Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Merge (opsional)</label>
                <textarea name="reason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Jelaskan alasan merge ini..."></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeMergeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Merge Customer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showMergeModal(sourceId, targetId) {
    document.getElementById('sourceCustomerId').value = sourceId;
    document.getElementById('targetCustomerId').value = targetId;
    document.getElementById('mergeModal').classList.remove('hidden');
}

function closeMergeModal() {
    document.getElementById('mergeModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('mergeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeMergeModal();
    }
});
</script>
@endsection
