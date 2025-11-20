@extends('layouts.app')

@section('title', 'Laporan Gudang')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Laporan Gudang</h1>
    <p class="text-gray-600">Pilih jenis laporan yang ingin Anda lihat</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Stock Report -->
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                <i class="fas fa-boxes text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900">Laporan Stok</h3>
                <p class="text-sm text-gray-600">Stok produk saat ini</p>
            </div>
        </div>
        <a href="{{ route('reports.stock') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 rounded-md">
            Lihat Laporan
        </a>
    </div>

    <!-- Movement Report -->
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-green-100 text-green-500">
                <i class="fas fa-exchange-alt text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900">Pergerakan Stok</h3>
                <p class="text-sm text-gray-600">Riwayat masuk/keluar</p>
            </div>
        </div>
        <a href="{{ route('reports.movement') }}" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center py-2 rounded-md">
            Lihat Laporan
        </a>
    </div>

    <!-- Supplier Report -->
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                <i class="fas fa-truck text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900">Laporan Distributor</h3>
                <p class="text-sm text-gray-600">Data distributor</p>
            </div>
        </div>
        <a href="{{ route('reports.supplier') }}" class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center py-2 rounded-md">
            Lihat Laporan
        </a>
    </div>

    <!-- Customer Report -->
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-orange-100 text-orange-500">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900">Laporan Customer</h3>
                <p class="text-sm text-gray-600">Data customer</p>
            </div>
        </div>
        <a href="{{ route('reports.customer') }}" class="block w-full bg-orange-600 hover:bg-orange-700 text-white text-center py-2 rounded-md">
            Lihat Laporan
        </a>
    </div>
</div>
@endsection
