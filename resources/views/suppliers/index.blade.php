@extends('layouts.app')

@section('title', 'Data Supplier')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Daftar Supplier</h1>
        <div class="mt-2 text-sm text-gray-600">
            Menampilkan {{ $suppliers->count() }} dari {{ $totalSuppliers }} supplier total
            @if($isActive == 1)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <i class="fas fa-user-check mr-1"></i> Aktif ({{ $activeSuppliers }})
                </span>
            @elseif($isActive == 0)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    <i class="fas fa-user-times mr-1"></i> Tidak Aktif ({{ $inactiveSuppliers }})
                </span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <i class="fas fa-users mr-1"></i> Semua ({{ $totalSuppliers }})
                </span>
            @endif
            @if($search)
                untuk pencarian "{{ $search }}"
            @endif
        </div>
        <div class="mt-2 flex space-x-2">
            <a href="{{ route('suppliers.index', ['is_active' => 1]) }}" 
               class="px-3 py-1 text-sm rounded-lg @if($isActive == 1) bg-green-600 text-white @else bg-gray-200 text-gray-700 hover:bg-gray-300 @endif">
                <i class="fas fa-user-check mr-1"></i> Aktif
            </a>
            <a href="{{ route('suppliers.index', ['is_active' => 0]) }}" 
               class="px-3 py-1 text-sm rounded-lg @if($isActive == 0) bg-red-600 text-white @else bg-gray-200 text-gray-700 hover:bg-gray-300 @endif">
                <i class="fas fa-user-times mr-1"></i> Tidak Aktif
            </a>
            <a href="{{ route('suppliers.index') }}" 
               class="px-3 py-1 text-sm rounded-lg @if($isActive === null) bg-blue-600 text-white @else bg-gray-200 text-gray-700 hover:bg-gray-300 @endif">
                <i class="fas fa-users mr-1"></i> Semua
            </a>
        </div>
    </div>
    <div class="flex items-center space-x-3">
        <form method="GET" action="{{ route('suppliers.index') }}" class="flex">
            @if($isActive !== null)
                <input type="hidden" name="is_active" value="{{ $isActive }}">
            @endif
            <input type="text" name="search" value="{{ $search ?? '' }}" 
                   placeholder="Cari supplier..." 
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <a href="{{ route('suppliers.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Supplier
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Supplier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($suppliers as $supplier)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $supplier->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="space-y-1">
                                @if($supplier->contact_person)
                                    <div>{{ $supplier->contact_person }}</div>
                                @endif
                                @if($supplier->contact_person_2)
                                    <div class="text-gray-600">{{ $supplier->contact_person_2 }}</div>
                                @endif
                                @if($supplier->contact_person_3)
                                    <div class="text-gray-600">{{ $supplier->contact_person_3 }}</div>
                                @endif
                                @if(!$supplier->contact_person && !$supplier->contact_person_2 && !$supplier->contact_person_3)
                                    <div>-</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="space-y-1">
                                @if($supplier->phone)
                                    <div>{{ $supplier->phone }}</div>
                                @endif
                                @if($supplier->phone_2)
                                    <div class="text-gray-600">{{ $supplier->phone_2 }}</div>
                                @endif
                                @if($supplier->phone_3)
                                    <div class="text-gray-600">{{ $supplier->phone_3 }}</div>
                                @endif
                                @if(!$supplier->phone && !$supplier->phone_2 && !$supplier->phone_3)
                                    <div>-</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $supplier->email ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ Str::limit($supplier->address ?? '-', 50) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                @if($supplier->phone)
                                    <a href="https://wa.me/62{{ preg_replace('/[^0-9]/', '', $supplier->phone) }}" 
                                       target="_blank" class="text-green-600 hover:text-green-900" 
                                       title="WhatsApp {{ $supplier->phone }}">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                @endif
                                @if($supplier->phone_2)
                                    <a href="https://wa.me/62{{ preg_replace('/[^0-9]/', '', $supplier->phone_2) }}" 
                                       target="_blank" class="text-green-600 hover:text-green-900" 
                                       title="WhatsApp {{ $supplier->phone_2 }}">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                @endif
                                @if($supplier->phone_3)
                                    <a href="https://wa.me/62{{ preg_replace('/[^0-9]/', '', $supplier->phone_3) }}" 
                                       target="_blank" class="text-green-600 hover:text-green-900" 
                                       title="WhatsApp {{ $supplier->phone_3 }}">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                @endif
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="text-yellow-600 hover:text-yellow-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data supplier</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
</div>
@endsection
