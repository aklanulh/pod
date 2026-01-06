@extends('layouts.app')

@section('title', $title ?? 'Dashboard Teknisi - Coming Soon')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full mb-6">
            <i class="fas fa-tools text-blue-600 text-3xl"></i>
        </div>
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Dashboard Teknisi</h1>
        <div class="inline-flex items-center bg-yellow-100 text-yellow-800 px-4 py-2 rounded-full text-lg font-semibold mb-4">
            <i class="fas fa-clock mr-2"></i>
            Coming Soon
        </div>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
            Dashboard Teknisi sedang dalam pengembangan. Segera hadir dengan fitur-fitur lengkap untuk monitoring dan maintenance peralatan KSO.
        </p>
    </div>

    <!-- Features Preview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="text-blue-600 mb-4">
                <i class="fas fa-wrench text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Maintenance Schedule</h3>
            <p class="text-gray-600 text-sm">Jadwal maintenance rutin untuk semua peralatan KSO</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="text-green-600 mb-4">
                <i class="fas fa-chart-line text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Equipment Monitoring</h3>
            <p class="text-gray-600 text-sm">Real-time monitoring status dan kinerja peralatan</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <div class="text-purple-600 mb-4">
                <i class="fas fa-calendar-check text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Calendar View</h3>
            <p class="text-gray-600 text-sm">Kalender interaktif untuk jadwal maintenance</p>
        </div>
    </div>

    <!-- Progress Indicator -->
    <div class="bg-gray-50 rounded-lg p-8 text-center">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Pengembangan</h3>
        <div class="max-w-md mx-auto">
            <div class="flex justify-between text-sm text-gray-600 mb-2">
                <span>Planning</span>
                <span>Development</span>
                <span>Testing</span>
                <span>Release</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-blue-600 h-3 rounded-full" style="width: 60%"></div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="text-center mt-8">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
