@extends('layouts.app')

@section('title', 'Data Customer')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Daftar Customer</h1>
    <a href="{{ route('customers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
        <i class="fas fa-plus mr-2"></i>
        Tambah Customer
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($customers as $customer)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="space-y-1">
                                @if($customer->contact_person)
                                    <div>{{ $customer->contact_person }}</div>
                                @endif
                                @if($customer->contact_person_2)
                                    <div class="text-gray-600">{{ $customer->contact_person_2 }}</div>
                                @endif
                                @if($customer->contact_person_3)
                                    <div class="text-gray-600">{{ $customer->contact_person_3 }}</div>
                                @endif
                                @if(!$customer->contact_person && !$customer->contact_person_2 && !$customer->contact_person_3)
                                    <div>-</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="space-y-1">
                                @if($customer->phone)
                                    <div>{{ $customer->phone }}</div>
                                @endif
                                @if($customer->phone_2)
                                    <div class="text-gray-600">{{ $customer->phone_2 }}</div>
                                @endif
                                @if($customer->phone_3)
                                    <div class="text-gray-600">{{ $customer->phone_3 }}</div>
                                @endif
                                @if(!$customer->phone && !$customer->phone_2 && !$customer->phone_3)
                                    <div>-</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $customer->email ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ Str::limit($customer->address ?? '-', 50) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('customers.edit', $customer) }}" class="text-yellow-600 hover:text-yellow-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus customer ini?')">
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
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data customer</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($customers->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $customers->links() }}
        </div>
    @endif
</div>
@endsection
