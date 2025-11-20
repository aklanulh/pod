@extends('layouts.app')

@section('title', 'Data Supplier')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Daftar Supplier</h1>
    <a href="{{ route('suppliers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
        <i class="fas fa-plus mr-2"></i>
        Tambah Supplier
    </a>
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
    
    @if($suppliers->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $suppliers->links() }}
        </div>
    @endif
</div>
@endsection
