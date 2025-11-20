@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
<!-- Page Header -->
<div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg p-6 mb-8 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold mb-2">Log Aktivitas Admin</h1>
            <p class="text-blue-100">Monitor dan tracking aktivitas semua admin di sistem</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.history.export', request()->query()) }}" 
               class="inline-flex items-center px-4 py-2 border border-white border-opacity-30 text-sm font-medium rounded-md text-white bg-white bg-opacity-20 hover:bg-opacity-30 transition-colors duration-200">
                <i class="fas fa-download mr-2"></i>
                Export CSV
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <!-- Total Activities -->
    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Total Aktivitas</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_activities']) }}</p>
            </div>
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-chart-line text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Today Activities -->
    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Hari Ini</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['today_activities']) }}</p>
            </div>
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-calendar-day text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Week Activities -->
    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Minggu Ini</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['week_activities']) }}</p>
            </div>
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i class="fas fa-calendar-week text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Month Activities -->
    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Bulan Ini</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['month_activities']) }}</p>
            </div>
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-calendar-alt text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Active Users Today -->
    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">User Aktif Hari Ini</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_users_today']) }}</p>
            </div>
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <i class="fas fa-users text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Aktivitas</h3>
    
    <form method="GET" action="{{ route('admin.history.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
        <!-- User Filter -->
        <div>
            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">User</label>
            <select name="user_id" id="user_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Semua User</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Module Filter -->
        <div>
            <label for="module" class="block text-sm font-medium text-gray-700 mb-2">Modul</label>
            <select name="module" id="module" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Semua Modul</option>
                @foreach($modules as $module)
                    <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                        {{ ucfirst($module) }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Action Filter -->
        <div>
            <label for="action" class="block text-sm font-medium text-gray-700 mb-2">Aksi</label>
            <select name="action" id="action" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Semua Aksi</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                        {{ ucfirst($action) }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Date From -->
        <div>
            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <!-- Date To -->
        <div>
            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <!-- Filter Actions -->
        <div class="flex items-end space-x-2">
            <button type="submit" 
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                <i class="fas fa-search mr-2"></i>
                Filter
            </button>
            <a href="{{ route('admin.history.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                <i class="fas fa-times"></i>
            </a>
        </div>
    </form>
</div>

<!-- Activities Table -->
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Riwayat Aktivitas Admin</h2>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modul</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($activities as $activity)
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $activity->created_at->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8">
                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $activity->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $activity->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $activity->action_badge }}">
                            <i class="{{ $activity->action_icon }} mr-1"></i>
                            {{ ucfirst($activity->action) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ ucfirst($activity->module) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $activity->description }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $activity->ip_address }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.history.show', $activity->id) }}" 
                           class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                            <i class="fas fa-eye mr-1"></i>
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-4"></i>
                            <p class="text-sm">Belum ada aktivitas admin</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($activities->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $activities->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<!-- Most Active User & Top Modules -->
@if($stats['most_active_user'] || $stats['top_modules']->count() > 0)
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
    <!-- Most Active User -->
    @if($stats['most_active_user'])
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">User Paling Aktif</h3>
        <div class="flex items-center">
            <div class="flex-shrink-0 h-12 w-12">
                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-crown text-blue-600 text-lg"></i>
                </div>
            </div>
            <div class="ml-4">
                <div class="text-lg font-medium text-gray-900">{{ $stats['most_active_user']->user->name }}</div>
                <div class="text-sm text-gray-500">{{ number_format($stats['most_active_user']->activity_count) }} aktivitas</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Top Modules -->
    @if($stats['top_modules']->count() > 0)
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Modul Terpopuler</h3>
        <div class="space-y-3">
            @foreach($stats['top_modules'] as $module)
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-900">{{ ucfirst($module->module) }}</span>
                <span class="text-sm text-gray-500">{{ number_format($module->count) }} aktivitas</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif
@endsection
