@extends('layouts.app')

@section('title', 'Laporan Gudang')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Laporan Gudang</h1>
    <p class="text-gray-600">Pilih jenis laporan yang ingin Anda lihat</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Stock Report -->
    <a href="{{ route('reports.stock') }}" class="block bg-white rounded-lg shadow p-6 hover:shadow-lg transition-all hover:scale-105 cursor-pointer group">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-blue-100 text-blue-500 group-hover:bg-blue-200 transition-colors">
                <i class="fas fa-boxes text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">Lihat Stok</h3>
                <p class="text-sm text-gray-600">Stok produk saat ini</p>
            </div>
        </div>
    </a>

    <!-- Movement Report -->
    <a href="{{ route('reports.movement') }}" class="block bg-white rounded-lg shadow p-6 hover:shadow-lg transition-all hover:scale-105 cursor-pointer group">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-green-100 text-green-500 group-hover:bg-green-200 transition-colors">
                <i class="fas fa-exchange-alt text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-green-600 transition-colors">Lihat Pergerakan</h3>
                <p class="text-sm text-gray-600">Riwayat masuk/keluar</p>
            </div>
        </div>
    </a>

    <!-- Supplier Report -->
    <a href="{{ route('reports.supplier') }}" class="block bg-white rounded-lg shadow p-6 hover:shadow-lg transition-all hover:scale-105 cursor-pointer group">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-purple-100 text-purple-500 group-hover:bg-purple-200 transition-colors">
                <i class="fas fa-truck text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-purple-600 transition-colors">Lihat Distributor</h3>
                <p class="text-sm text-gray-600">Data distributor</p>
            </div>
        </div>
    </a>

    <!-- Customer Report -->
    <a href="{{ route('reports.customer') }}" class="block bg-white rounded-lg shadow p-6 hover:shadow-lg transition-all hover:scale-105 cursor-pointer group">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-orange-100 text-orange-500 group-hover:bg-orange-200 transition-colors">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-orange-600 transition-colors">Lihat Customer</h3>
                <p class="text-sm text-gray-600">Data customer</p>
            </div>
        </div>
    </a>
</div>
@endsection
