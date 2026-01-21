@extends('layouts.auth')

@section('title', 'Reset Password Error')

@section('content')
<!-- Error Message -->
<div class="bg-white rounded-lg shadow-xl p-8">
    <div class="text-center mb-6">
        <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
        </div>
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Link Reset Password Tidak Valid</h2>
        <p class="text-gray-600">{{ $error }}</p>
    </div>
    
    <!-- Information Box -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-yellow-600 mt-0.5 mr-3"></i>
            <div>
                <h3 class="text-sm font-medium text-yellow-800 mb-1">Informasi</h3>
                <p class="text-sm text-yellow-700">
                    Link reset password mungkin telah kadaluarsa atau tidak valid. 
                    Silakan hubungi administrator untuk mendapatkan link reset password yang baru.
                </p>
            </div>
        </div>
    </div>

    <!-- Back to Login Button -->
    <a href="{{ route('login') }}" 
       class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 flex items-center justify-center">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Login
    </a>
</div>
@endsection
