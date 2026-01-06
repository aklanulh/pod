@extends('layouts.app')

@section('title', 'Jadwal Customer - PT. Mitrajaya Selaras Abadi')

@section('content')
<div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-lg p-6 mb-8 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold mb-2">Jadwal & Alert Customer</h2>
            <p class="text-blue-100">Kelola jadwal pembelian dan reminder untuk customer</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('customer-schedules.create') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-2 rounded-lg transition-all duration-200 flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Jadwal
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Total Jadwal</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-calendar-alt text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Pending</p>
                <p class="text-3xl font-bold text-yellow-600">{{ number_format($stats['pending']) }}</p>
            </div>
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i class="fas fa-clock text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Overdue</p>
                <p class="text-3xl font-bold text-red-600">{{ number_format($stats['overdue']) }}</p>
            </div>
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Hari Ini</p>
                <p class="text-3xl font-bold text-green-600">{{ number_format($stats['due_today']) }}</p>
            </div>
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-bell text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Minggu Ini</p>
                <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['due_this_week']) }}</p>
            </div>
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-calendar-week text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Schedules Table -->
<div class="bg-white rounded-xl shadow-lg">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-list text-blue-600 mr-3"></i>
            Daftar Jadwal Customer
        </h3>
    </div>


    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Jadwal</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Pembelian Terakhir</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($schedules as $schedule)
                    <tr class="hover:bg-blue-50 transition-colors duration-150 {{ $schedule->is_overdue ? 'bg-red-50' : ($schedule->is_due_today ? 'bg-yellow-50' : '') }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">{{ $schedule->customer->name }}</div>
                            <div class="text-xs text-gray-500">{{ $schedule->customer->email ?? $schedule->customer->phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">{{ $schedule->product->name }}</div>
                            <div class="text-xs text-gray-500">{{ $schedule->product->code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">{{ $schedule->scheduled_date->format('d/m/Y') }}</div>
                            <div class="text-xs {{ $schedule->is_overdue ? 'text-red-600' : ($schedule->is_due_today ? 'text-yellow-600' : 'text-gray-500') }}">
                                @if($schedule->is_overdue)
                                    <i class="fas fa-exclamation-triangle mr-1"></i>Terlambat {{ abs($schedule->days_until_due) }} hari yang lalu
                                @elseif($schedule->is_due_today)
                                    <i class="fas fa-bell mr-1"></i>Jatuh tempo hari ini
                                @else
                                    @if($schedule->days_until_due < 0)
                                        <i class="fas fa-exclamation-triangle mr-1"></i>{{ abs($schedule->days_until_due) }} hari yang lalu
                                    @else
                                        <i class="fas fa-clock mr-1"></i>{{ $schedule->days_until_due }} hari lagi
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $lastPurchase = \App\Models\StockMovement::where('customer_id', $schedule->customer_id)
                                    ->where('product_id', $schedule->product_id)
                                    ->where('type', 'out')
                                    ->orderBy('transaction_date', 'desc')
                                    ->first();
                            @endphp
                            @if($lastPurchase)
                                <div class="text-sm font-bold text-gray-900">{{ number_format($lastPurchase->quantity) }} {{ $schedule->product->unit ?? 'pcs' }}</div>
                                <div class="text-xs text-gray-500">{{ $lastPurchase->transaction_date->format('d/m/Y') }}</div>
                                <div class="text-xs text-green-600">Rp {{ number_format($lastPurchase->unit_price ?? 0) }}</div>
                            @else
                                <div class="text-sm text-gray-500">Belum pernah beli</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $schedule->status_badge }}">
                                {{ ucfirst($schedule->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('reports.customer.detail', $schedule->customer_id) }}" class="text-blue-600 hover:text-blue-900" title="Lihat Detail Customer">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('customer-schedules.edit', $schedule) }}" class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($schedule->status === 'pending')
                                    <form action="{{ route('customer-schedules.notify', $schedule) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900" title="Tandai Dinotifikasi">
                                            <i class="fas fa-bell"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($schedule->status === 'pending' || $schedule->status === 'notified')
                                    <form action="{{ route('customer-schedules.complete', $schedule) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-purple-600 hover:text-purple-900" title="Tandai Selesai">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('customer-schedules.destroy', $schedule) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
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
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-calendar-alt text-3xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 font-medium">Belum ada jadwal customer</p>
                            <p class="text-gray-400 text-sm mt-1">Tambahkan jadwal pertama untuk customer Anda</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($schedules->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $schedules->links() }}
        </div>
    @endif
</div>

@endsection
