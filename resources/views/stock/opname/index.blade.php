@extends('layouts.app')

@section('title', 'Stok Opname')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Daftar Stok Opname</h1>
        @if(isset($draftCount) && $draftCount > 0)
            <p class="text-sm text-orange-600 mt-1">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ $draftCount }} opname masih dalam status draft
            </p>
        @endif
    </div>
    <a href="{{ route('stock.opname.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
        <i class="fas fa-plus mr-2"></i>
        Buat Stok Opname
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Opname</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($opnames as $opname)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $opname->opname_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $opname->opname_date ? $opname->opname_date->format('d/m/Y') : 'Tanggal tidak tersedia' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($opname->status === 'draft')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Draft
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Selesai
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php
                                $stockLebih = $opname->details->where('difference', '>', 0)->count();
                                $stockKurang = $opname->details->where('difference', '<', 0)->count();
                                $stockSesuai = $opname->details->where('difference', '=', 0)->count();
                            @endphp
                            
                            <div class="flex flex-wrap gap-1">
                                @if($stockLebih > 0)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-arrow-up mr-1"></i>
                                        {{ $stockLebih }} Lebih
                                    </span>
                                @endif
                                
                                @if($stockKurang > 0)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-arrow-down mr-1"></i>
                                        {{ $stockKurang }} Kurang
                                    </span>
                                @endif
                                
                                @if($stockSesuai > 0)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-check mr-1"></i>
                                        {{ $stockSesuai }} Sesuai
                                    </span>
                                @endif
                                
                                @if($opname->details->count() === 0)
                                    <span class="text-gray-500">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('stock.opname.show', $opname) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye mr-1"></i>
                                    Detail
                                </a>
                                
                                @if($opname->status === 'draft')
                                    <form action="{{ route('stock.opname.destroy', $opname) }}" method="POST" class="inline" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus opname ini? Data tidak dapat dikembalikan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada data stok opname</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($opnames->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $opnames->links() }}
        </div>
    @endif
</div>
@endsection
