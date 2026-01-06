@extends('layouts.app')

@section('title', 'Detail Aktivitas - Super Admin')

@section('content')
<!-- Page Header -->
<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Detail Aktivitas Admin</h1>
            <p class="text-gray-600">Informasi lengkap aktivitas {{ $activity->user->name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.history.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

<!-- Activity Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Activity Info -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Informasi Aktivitas</h2>
        
        <div class="space-y-6">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal & Waktu</label>
                    <div class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md">
                        {{ $activity->created_at->format('d/m/Y H:i:s') }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">User</label>
                    <div class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md">
                        {{ $activity->user->name }}
                        <div class="text-xs text-gray-500">{{ $activity->user->email }}</div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Aksi</label>
                    <div class="bg-gray-50 p-3 rounded-md">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $activity->action_badge }}">
                            <i class="{{ $activity->action_icon }} mr-1"></i>
                            {{ ucfirst($activity->action) }}
                        </span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Modul</label>
                    <div class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md">
                        {{ ucfirst($activity->module) }}
                    </div>
                </div>
            </div>
            
            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <div class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md">
                    {{ $activity->description }}
                </div>
            </div>
            
            <!-- Technical Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">IP Address</label>
                    <div class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md font-mono">
                        {{ $activity->ip_address ?? 'N/A' }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">User Agent</label>
                    <div class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md break-all">
                        {{ $activity->user_agent ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Additional Data -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Data Tambahan</h2>
        
        @if($activity->data)
            <div class="space-y-4">
                @foreach($activity->data as $key => $value)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
                        <div class="text-sm text-gray-900 bg-gray-50 p-2 rounded-md">
                            @if(is_array($value))
                                <pre class="text-xs">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                            @else
                                {{ $value }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-400">
                    <i class="fas fa-info-circle text-2xl mb-2"></i>
                    <p class="text-sm">Tidak ada data tambahan</p>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Related Activities -->
<div class="bg-white rounded-lg shadow-lg p-6 mt-8">
    <h2 class="text-lg font-semibold text-gray-900 mb-6">Aktivitas Terkait dari {{ $activity->user->name }}</h2>
    
    @php
        $relatedActivities = App\Models\AdminActivityLog::where('user_id', $activity->user_id)
            ->where('id', '!=', $activity->id)
            ->whereDate('created_at', $activity->created_at->toDateString())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    @endphp
    
    @if($relatedActivities->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modul</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($relatedActivities as $related)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $related->created_at->format('H:i:s') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $related->action_badge }}">
                                <i class="{{ $related->action_icon }} mr-1"></i>
                                {{ ucfirst($related->action) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ ucfirst($related->module) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $related->description }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8">
            <div class="text-gray-400">
                <i class="fas fa-clock text-2xl mb-2"></i>
                <p class="text-sm">Tidak ada aktivitas lain pada hari yang sama</p>
            </div>
        </div>
    @endif
</div>
@endsection
