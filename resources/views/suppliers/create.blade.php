@extends('layouts.app')

@section('title', 'Tambah Distributor')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('suppliers.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Tambah Distributor Baru</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('suppliers.store') }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Distributor</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contact Persons Section -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-4">Nama Kontak (Maksimal 3)</label>
                
                <div class="space-y-3">
                    <div>
                        <label for="contact_person" class="block text-xs text-gray-600 mb-1">Kontak 1</label>
                        <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="contact_person_2" class="block text-xs text-gray-600 mb-1">Kontak 2 (Opsional)</label>
                        <input type="text" name="contact_person_2" id="contact_person_2" value="{{ old('contact_person_2') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="contact_person_3" class="block text-xs text-gray-600 mb-1">Kontak 3 (Opsional)</label>
                        <input type="text" name="contact_person_3" id="contact_person_3" value="{{ old('contact_person_3') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Phone Numbers Section -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-4">Nomor Telepon (Maksimal 3)</label>
                
                <div class="space-y-3">
                    <div>
                        <label for="phone" class="block text-xs text-gray-600 mb-1">Telepon 1</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="phone_2" class="block text-xs text-gray-600 mb-1">Telepon 2 (Opsional)</label>
                        <input type="text" name="phone_2" id="phone_2" value="{{ old('phone_2') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="phone_3" class="block text-xs text-gray-600 mb-1">Telepon 3 (Opsional)</label>
                        <input type="text" name="phone_3" id="phone_3" value="{{ old('phone_3') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                <textarea name="address" id="address" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('suppliers.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Simpan Supplier
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
