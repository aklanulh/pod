@extends('layouts.app')

@section('title', 'Dashboard Error')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-red-50 border border-red-200 rounded-lg p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                    Dashboard Error
                </h3>
                <div class="mt-2 text-sm text-red-700">
                    <p>Terjadi kesalahan saat memuat dashboard. Kemungkinan penyebab:</p>
                    <ul class="list-disc list-inside mt-2">
                        <li>Database belum sepenuhnya ter-setup</li>
                        <li>Beberapa tabel mungkin belum ada</li>
                        <li>Masalah koneksi database</li>
                    </ul>
                </div>
                
                @if(isset($error))
                <div class="mt-4 p-3 bg-red-100 rounded">
                    <p class="text-sm text-red-800 font-mono">{{ $error }}</p>
                </div>
                @endif
                
                <div class="mt-4">
                    <div class="flex space-x-4">
                        <a href="{{ route('login') }}" class="text-sm bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                            Kembali ke Login
                        </a>
                        <button onclick="window.location.reload()" class="text-sm bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                            Refresh Halaman
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h4 class="text-lg font-medium text-blue-800 mb-4">Solusi Sementara:</h4>
        <ol class="list-decimal list-inside text-blue-700 space-y-2">
            <li>Pastikan semua migrasi sudah dijalankan: <code class="bg-blue-100 px-2 py-1 rounded">php artisan migrate --force</code></li>
            <li>Jalankan seeder database: <code class="bg-blue-100 px-2 py-1 rounded">php artisan db:seed --force</code></li>
            <li>Clear cache aplikasi: <code class="bg-blue-100 px-2 py-1 rounded">php artisan cache:clear</code></li>
            <li>Jika masih error, hubungi administrator sistem</li>
        </ol>
    </div>
</div>
@endsection
