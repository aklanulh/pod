@extends('layouts.app')

@section('title', 'Edit Jadwal Customer - PT. Mitrajaya Selaras Abadi')

@section('content')
<div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-lg p-6 mb-8 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold mb-2">Edit Jadwal Customer</h2>
            <p class="text-blue-100">Edit jadwal pembelian dan reminder untuk customer</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('customer-schedules.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-2 rounded-lg transition-all duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-lg p-8" x-data="editScheduleForm()">
    <form action="{{ route('customer-schedules.update', $customerSchedule) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Step 1: Customer Selection -->
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">1</span>
                Customer
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                    <select name="customer_id" id="customer_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Pilih Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $customerSchedule->customer_id == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} - {{ $customer->phone }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Step 2: Product Selection -->
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">2</span>
                Produk
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2">Produk</label>
                    <select name="product_id" id="product_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Pilih Produk</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ $customerSchedule->product_id == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} ({{ $product->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Step 3: Schedule Details -->
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">3</span>
                Detail Jadwal
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Jadwal</label>
                    <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date', $customerSchedule->scheduled_date) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    @error('scheduled_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>


                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="pending" {{ old('status', $customerSchedule->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="notified" {{ old('status', $customerSchedule->status) == 'notified' ? 'selected' : '' }}>Notified</option>
                        <option value="completed" {{ old('status', $customerSchedule->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $customerSchedule->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>


                <div class="flex items-center">
                    <input type="checkbox" name="is_recurring" id="is_recurring" value="1" {{ old('is_recurring', $customerSchedule->is_recurring) ? 'checked' : '' }} class="rounded border-gray-300 mr-2" x-model="isRecurring">
                    <label for="is_recurring" class="text-sm font-medium text-gray-700">Jadwal Berulang</label>
                </div>
            </div>

            <div class="mt-4" x-show="isRecurring">
                <label for="recurring_days" class="block text-sm font-medium text-gray-700 mb-2">Interval Hari</label>
                <input type="number" name="recurring_days" id="recurring_days" value="{{ old('recurring_days', $customerSchedule->recurring_days) }}" min="1" class="w-full md:w-1/3 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Berapa hari sekali">
                @error('recurring_days')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>


            <div class="mt-4">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea name="notes" id="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Catatan internal">{{ old('notes', $customerSchedule->notes) }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('customer-schedules.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-save mr-2"></i>Update Jadwal
            </button>
        </div>
    </form>
</div>

<script>
function editScheduleForm() {
    return {
        isRecurring: {{ $customerSchedule->is_recurring ? 'true' : 'false' }}
    }
}
</script>
@endsection
