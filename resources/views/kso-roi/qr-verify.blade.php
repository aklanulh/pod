@extends('layouts.qr-public')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Card Container -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-8">
                <div class="text-center">
                    <svg class="w-16 h-16 mx-auto text-white mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M6.343 17.657l1.414-1.414m2.828 2.828l1.414-1.414m5.656 0l1.414 1.414m2.828-2.828l1.414 1.414M9 11a3 3 0 11-6 0 3 3 0 016 0zm6 0a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <h1 class="text-3xl font-bold text-white mb-2">Verifikasi QR Code</h1>
                    <p class="text-blue-100">Masukkan password untuk melihat detail KSO</p>
                </div>
            </div>

            <!-- Body -->
            <div class="px-8 py-8">
                <!-- Error Message -->
                @if($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h3 class="font-semibold text-red-800">Error</h3>
                                <ul class="mt-2 text-sm text-red-700 space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h3 class="font-semibold text-red-800">Error</h3>
                                <p class="mt-1 text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- KSO Item Found -->
                @if($ksoItem)
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h3 class="font-semibold text-green-800">KSO Item Ditemukan</h3>
                                <p class="mt-1 text-sm text-green-700">
                                    <strong>{{ $ksoItem->nama_alat }}</strong> - {{ $ksoItem->customer->name }}
                                </p>
                                <p class="text-xs text-green-600 mt-1">ID: {{ $ksoItem->unique_id }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Password Form -->
                <form action="{{ route('qr.password', $uniqueId) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password Verifikasi
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                            required
                            autofocus
                        >
                        <p class="mt-2 text-xs text-gray-500">
                            💡 Hubungi administrator jika Anda lupa password
                        </p>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-3 rounded-lg transition-all duration-200 transform hover:scale-105 active:scale-95"
                    >
                        Verifikasi & Lihat Detail
                    </button>
                </form>

                <!-- Divider -->
                <div class="my-6 flex items-center">
                    <div class="flex-1 border-t border-gray-300"></div>
                    <div class="px-3 text-sm text-gray-500">atau</div>
                    <div class="flex-1 border-t border-gray-300"></div>
                </div>

                <!-- Manual Search Form (untuk barcode rusak) -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-semibold text-blue-900 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8 16a3 3 0 11-6 0 3 3 0 016 0zM9 2a4 4 0 100 8 4 4 0 000-8zM17 16a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Pencarian Manual
                    </h3>
                    <p class="text-xs text-blue-700 mb-3">
                        Jika barcode rusak atau tidak terbaca, masukkan ID KSO secara manual
                    </p>

                    <form action="{{ route('qr.search', $uniqueId) }}" method="POST" class="space-y-3">
                        @csrf

                        <div>
                            <label for="search_id" class="block text-xs font-medium text-blue-900 mb-1">
                                ID KSO (6-8 digit)
                            </label>
                            <input
                                type="text"
                                id="search_id"
                                name="search_id"
                                placeholder="Contoh: 12345678"
                                maxlength="8"
                                pattern="\d{6,8}"
                                class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition text-sm"
                            >
                            <p class="mt-1 text-xs text-blue-600">
                                Masukkan 6-8 digit angka ID KSO
                            </p>
                        </div>

                        <button
                            type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-lg transition-colors text-sm"
                        >
                            Cari KSO
                        </button>
                    </form>
                </div>

                <!-- Info Box -->
                <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 text-sm mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 8a1 1 0 000 2h6a1 1 0 000-2H8zm0 3a1 1 0 000 2h3a1 1 0 000-2H8z" clip-rule="evenodd"></path>
                        </svg>
                        Informasi Penting
                    </h4>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li>✓ Halaman ini aman dan tidak memerlukan login</li>
                        <li>✓ Data Anda dilindungi dengan password</li>
                        <li>✓ Setiap sesi berakhir setelah browser ditutup</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-sm text-gray-600">
            <p>© 2025 WMS System - MSA PT</p>
        </div>
    </div>
</div>
@endsection
