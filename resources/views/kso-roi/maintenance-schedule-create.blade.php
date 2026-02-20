@extends('layouts.app')

@section('title', 'Tambah Jadwal Maintenance')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
            <a href="{{ route('kso-roi.technician-dashboard') }}" class="hover:text-blue-600 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Dashboard Teknisi
            </a>
            <span>/</span>
            <span>Tambah Jadwal Maintenance</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Tambah Jadwal Maintenance</h1>
        <p class="text-gray-600 mt-1">Jadwalkan maintenance untuk peralatan KSO</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('kso-roi.maintenance-schedules.store') }}" method="POST">
            @csrf
            
            <!-- Equipment Type Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Maintenance</label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="radio" name="maintenance_category" value="kso" class="mr-2" checked
                               onchange="toggleMaintenanceCategory(this.value)">
                        <span class="text-sm">KSO Item</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="maintenance_category" value="personal" class="mr-2"
                               onchange="toggleMaintenanceCategory(this.value)">
                        <span class="text-sm">Barang Pribadi</span>
                    </label>
                </div>
            </div>

            <!-- KSO Item Selection -->
            <div id="kso_item_section">
                <div class="mb-6">
                    <label for="kso_item_id" class="block text-sm font-medium text-gray-700 mb-2">
                        KSO Item <span class="text-red-500">*</span>
                    </label>
                    <select id="kso_item_id" name="kso_item_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih KSO Item</option>
                        @foreach($ksoItems as $ksoItem)
                            <option value="{{ $ksoItem->id }}" 
                                {{ old('kso_item_id') == $ksoItem->id || $ksoItem->id == $selectedKsoItemId ? 'selected' : '' }}>
                                {{ $ksoItem->nama_alat }} - {{ $ksoItem->customer->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('kso_item_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Personal Item Section -->
            <div id="personal_item_section" class="hidden mb-6">
                <label for="personal_item_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Barang Pribadi <span class="text-red-500">*</span>
                </label>
                <input type="text" id="personal_item_name" name="personal_item_name"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Masukkan nama barang pribadi">
                @error('personal_item_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Maintenance Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="maintenance_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Maintenance <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="maintenance_type" name="maintenance_type" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Masukkan tipe maintenance"
                           value="{{ old('maintenance_type') }}">
                    @error('maintenance_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="last_maintenance_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Maintenance Dilakukan
                    </label>
                    <input type="date" id="last_maintenance_date" name="last_maintenance_date"
                           value="{{ old('last_maintenance_date') ?: now()->format('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('last_maintenance_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Next Maintenance Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="next_maintenance_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Maintenance Berikutnya <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="next_maintenance_date" name="next_maintenance_date" required
                           value="{{ old('next_maintenance_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('next_maintenance_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">
                        Biaya Maintenance
                    </label>
                    <input type="number" id="cost" name="cost" step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Masukkan biaya maintenance (opsional)"
                           value="{{ old('cost') }}">
                    @error('cost')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Maintenance Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi Maintenance
                </label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Deskripsi maintenance yang dilakukan (opsional)">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Technician -->
            <div class="mb-6">
                <label for="technician" class="block text-sm font-medium text-gray-700 mb-2">
                    Teknisi
                </label>
                <input type="text" id="technician" name="technician"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Masukkan nama teknisi"
                       value="{{ old('technician') }}">
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan Tambahan
                </label>
                <textarea id="notes" name="notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Hidden fields -->
            <input type="hidden" name="equipment_type" value="main">
            <input type="hidden" name="status" value="completed">

            <!-- Form Actions -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('kso-roi.technician-dashboard') }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Store KSO items data
const ksoItems = @json($ksoItems);

function toggleMaintenanceCategory(category) {
    const ksoItemSection = document.getElementById('kso_item_section');
    const personalItemSection = document.getElementById('personal_item_section');
    const ksoItemSelect = document.getElementById('kso_item_id');
    
    if (category === 'kso') {
        ksoItemSection.classList.remove('hidden');
        personalItemSection.classList.add('hidden');
        ksoItemSelect.setAttribute('required', 'required');
        document.getElementById('personal_item_name').removeAttribute('required');
    } else {
        ksoItemSection.classList.add('hidden');
        personalItemSection.classList.remove('hidden');
        ksoItemSelect.removeAttribute('required');
        document.getElementById('personal_item_name').setAttribute('required', 'required');
        
        // Clear KSO selection
        ksoItemSelect.value = '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('last_maintenance_date').setAttribute('max', today);
    document.getElementById('next_maintenance_date').setAttribute('min', today);
    
    // Initialize with KSO category selected
    toggleMaintenanceCategory('kso');
    
    // Auto-fill equipment name when KSO item is selected
    const ksoItemSelect = document.getElementById('kso_item_id');
    ksoItemSelect.addEventListener('change', function() {
        const selectedKsoItemId = this.value;
        if (selectedKsoItemId) {
            const selectedItem = ksoItems.find(item => item.id == selectedKsoItemId);
            if (selectedItem) {
                // Auto-fill maintenance type if maintenance_type is empty
                const maintenanceTypeField = document.getElementById('maintenance_type');
                if (!maintenanceTypeField.value) {
                    maintenanceTypeField.value = 'Maintenance Rutin - ' + selectedItem.nama_alat;
                }
                
                // Auto-fill equipment name in description if empty
                const descriptionField = document.getElementById('description');
                if (!descriptionField.value) {
                    descriptionField.value = 'Perawatan rutin untuk ' + selectedItem.nama_alat + ' - ' + selectedItem.customer.name;
                }
            }
        }
    });
    
    // Auto-set next maintenance date when last maintenance date is selected
    const lastMaintenanceDateField = document.getElementById('last_maintenance_date');
    lastMaintenanceDateField.addEventListener('change', function() {
        const lastDate = this.value;
        if (lastDate) {
            const nextMaintenanceField = document.getElementById('next_maintenance_date');
            if (!nextMaintenanceField.value) {
                // Default next maintenance is 30 days from last maintenance
                const lastDateObj = new Date(lastDate);
                const nextDate = new Date(lastDateObj);
                nextDate.setDate(nextDate.getDate() + 30);
                nextMaintenanceField.value = nextDate.toISOString().split('T')[0];
            }
        }
    });
    
    // Trigger change event if there's a pre-selected item
    if (ksoItemSelect.value) {
        ksoItemSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
