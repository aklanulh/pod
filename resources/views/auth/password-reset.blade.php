@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<!-- Reset Password Form -->
<div class="bg-white rounded-lg shadow-xl p-8">
    <h2 class="text-xl font-semibold text-gray-800 mb-6 text-center">Reset Password</h2>
    
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i>
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif

    <form action="{{ route('password.reset.post', $token) }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        
        <!-- Email Display (Readonly) -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-envelope mr-2"></i>Email
            </label>
            <input type="email" 
                   value="{{ $email ?? '' }}" 
                   readonly
                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600">
        </div>

        <!-- Password Field -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-lock mr-2"></i>Password Baru
            </label>
            <input type="password" 
                   id="password" 
                   name="password" 
                   required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                   placeholder="Masukkan password baru (minimal 8 karakter)"
                   autocomplete="new-password">
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Confirmation Field -->
        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-lock mr-2"></i>Konfirmasi Password Baru
            </label>
            <input type="password" 
                   id="password_confirmation" 
                   name="password_confirmation" 
                   required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password_confirmation') border-red-500 @enderror"
                   placeholder="Masukkan kembali password baru"
                   autocomplete="new-password">
            @error('password_confirmation')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 flex items-center justify-center">
            <i class="fas fa-key mr-2"></i>
            Reset Password
        </button>
    </form>

    <!-- Back to Login Link -->
    <div class="text-center mt-6">
        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 text-sm">
            <i class="fas fa-arrow-left mr-1"></i>
            Kembali ke Login
        </a>
    </div>
</div>
@endsection
