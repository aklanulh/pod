@extends('layouts.app')

@section('title', 'Edit ' . ($qcRecord->type === 'qc' ? 'QC' : 'Kalibrasi') . ' - ' . $qcRecord->ksoItem->nama_alat)

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    Edit {{ $qcRecord->type === 'qc' ? '🔍 Quality Control' : '🔧 Kalibrasi' }}
                </h1>
                <p class="text-gray-600 mt-2">{{ $qcRecord->ksoItem->nama_alat }} - {{ $qcRecord->ksoItem->customer->name }}</p>
            </div>
            <a href="{{ route('kso-roi.qc.show', $qcRecord) }}" 
               class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Current Record Info -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Record saat ini:</strong> 
                    {{ $qcRecord->date->format('d M Y') }} - 
                    Status: <span class="font-semibold">{{ $qcRecord->status_text }}</span>
                    oleh {{ $qcRecord->technician_name }}
                </p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                Edit Form {{ $qcRecord->type === 'qc' ? 'Quality Control' : 'Kalibrasi' }}
            </h2>
        </div>
        
        <form action="{{ route('kso-roi.qc.update', $qcRecord) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Date -->
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal {{ $qcRecord->type === 'qc' ? 'QC' : 'Kalibrasi' }} <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="date" 
                               name="date" 
                               value="{{ old('date', $qcRecord->date->format('Y-m-d')) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Technician Name -->
                    <div>
                        <label for="technician_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Teknisi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="technician_name" 
                               name="technician_name" 
                               value="{{ old('technician_name', $qcRecord->technician_name) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('technician_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="radio" name="status" value="pass" required
                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                       {{ old('status', $qcRecord->status) == 'pass' ? 'checked' : '' }}>
                                <span class="ml-2 text-green-700 font-medium">✅ Lulus (Pass)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="status" value="fail" required
                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
                                       {{ old('status', $qcRecord->status) == 'fail' ? 'checked' : '' }}>
                                <span class="ml-2 text-red-700 font-medium">❌ Gagal (Fail)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="status" value="pending" required
                                       class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300"
                                       {{ old('status', $qcRecord->status) == 'pending' ? 'checked' : '' }}>
                                <span class="ml-2 text-yellow-700 font-medium">⏳ Pending</span>
                            </label>
                        </div>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Next Due Date - Dual Input Option -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Due Date Berikutnya
                        </label>
                        
                        <!-- Input Method Selection -->
                        <div class="mb-3">
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="due_date_method" value="days" checked
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                           onchange="toggleDueDateMethod('days')">
                                    <span class="ml-2 text-sm">Input jumlah hari</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="due_date_method" value="date"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                           onchange="toggleDueDateMethod('date')">
                                    <span class="ml-2 text-sm">Pilih tanggal spesifik</span>
                                </label>
                            </div>
                        </div>

                        <!-- Days Input Method -->
                        <div id="daysMethod" class="space-y-2">
                            <div>
                                <label for="next_due_days" class="block text-sm font-medium text-gray-700 mb-1">
                                    Jumlah Hari Sampai Jatuh Tempo
                                    <small class="text-gray-500">
                                        ({{ $qcRecord->type === 'qc' ? 'Default: 14 hari' : 'Default: 30 hari' }})
                                    </small>
                                </label>
                                @if($qcRecord->next_due_date)
                                    <p class="text-xs text-gray-600 mb-2">
                                        Jatuh tempo saat ini: {{ $qcRecord->next_due_date->format('d M Y') }}
                                        ({{ $qcRecord->date->diffInDays($qcRecord->next_due_date) }} hari dari tanggal {{ $qcRecord->type === 'qc' ? 'QC' : 'kalibrasi' }})
                                    </p>
                                @endif
                                <input type="number" 
                                       id="next_due_days" 
                                       name="next_due_days" 
                                       value="{{ old('next_due_days', $qcRecord->next_due_date ? $qcRecord->date->diffInDays($qcRecord->next_due_date) : ($qcRecord->type === 'qc' ? 14 : 30)) }}"
                                       min="1"
                                       max="365"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="mt-1 text-xs text-gray-500">
                                    Masukkan jumlah hari dari tanggal {{ $qcRecord->type === 'qc' ? 'QC' : 'kalibrasi' }} hingga jatuh tempo berikutnya
                                </p>
                                <div id="dueDateDisplay" class="text-green-600 font-medium mt-1"></div>
                                @error('next_due_days')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Date Picker Method -->
                        <div id="dateMethod" class="space-y-2 hidden">
                            <div>
                                <label for="next_due_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    Pilih Tanggal Jatuh Tempo Spesifik
                                    <small class="text-gray-500">
                                        (Opsional - akan menghitung jumlah hari otomatis)
                                    </small>
                                </label>
                                <input type="date" 
                                       id="next_due_date" 
                                       name="next_due_date" 
                                       value="{{ old('next_due_date', $qcRecord->next_due_date ? $qcRecord->next_due_date->format('Y-m-d') : '') }}"
                                       min="{{ now()->format('Y-m-d') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <div id="daysDisplay" class="text-blue-600 font-medium mt-1"></div>
                                @error('next_due_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan & Temuan
                        </label>
                        <textarea id="notes" 
                                  name="notes" 
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Deskripsi hasil {{ $qcRecord->type === 'qc' ? 'QC' : 'kalibrasi' }}, temuan, rekomendasi...">{{ old('notes', $qcRecord->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Certificate File -->
                    <div>
                        <label for="certificate_file" class="block text-sm font-medium text-gray-700 mb-2">
                            Upload Certificate/Sertifikat
                            <small class="text-gray-500">(PDF, JPG, PNG - Max 2MB)</small>
                        </label>
                        @if($qcRecord->certificate_file)
                            <div class="mb-2">
                                <p class="text-sm text-gray-600">Current file:</p>
                                <a href="{{ asset('storage/' . $qcRecord->certificate_file) }}" 
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-800 text-sm">
                                    📄 View Current Certificate
                                </a>
                            </div>
                        @endif
                        <input type="file" 
                               id="certificate_file" 
                               name="certificate_file" 
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('certificate_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Equipment Info -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-medium text-gray-800 mb-3">Informasi Alat</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nama Alat:</span>
                                <span class="font-medium">{{ $qcRecord->ksoItem->nama_alat }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Brand/Model:</span>
                                <span class="font-medium">{{ $qcRecord->ksoItem->brand }} {{ $qcRecord->ksoItem->model }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Serial Number:</span>
                                <span class="font-medium">{{ $qcRecord->ksoItem->serial_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Lokasi:</span>
                                <span class="font-medium">{{ $qcRecord->ksoItem->lokasi_penempatan }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                <a href="{{ route('kso-roi.qc.show', $qcRecord) }}" 
                   class="inline-flex items-center px-6 py-2.5 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <i class="fas fa-save mr-2"></i> Update {{ $qcRecord->type === 'qc' ? 'QC' : 'Kalibrasi' }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap Tooltip JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('date');
    const daysInput = document.getElementById('next_due_days');
    const datePickerInput = document.getElementById('next_due_date');
    const dueDateDisplay = document.getElementById('dueDateDisplay');
    const daysDisplay = document.getElementById('daysDisplay');
    
    function calculateDueDate() {
        const date = dateInput.value;
        const days = parseInt(daysInput.value) || 0;
        
        if (date && days > 0) {
            const dueDate = new Date(date);
            dueDate.setDate(dueDate.getDate() + days);
            
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = dueDate.toLocaleDateString('id-ID', options);
            
            dueDateDisplay.textContent = `Jatuh tempo: ${formattedDate}`;
            dueDateDisplay.className = 'text-green-600 font-medium mt-1 block';
        } else {
            dueDateDisplay.textContent = '';
        }
    }
    
    function calculateDays() {
        const date = dateInput.value;
        const dueDate = datePickerInput.value;
        
        if (date && dueDate) {
            const startDate = new Date(date);
            const endDate = new Date(dueDate);
            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays >= 0) {
                daysDisplay.textContent = `Jumlah hari: ${diffDays} hari`;
                daysDisplay.className = 'text-blue-600 font-medium mt-1 block';
            } else {
                daysDisplay.textContent = `Perhatian: Tanggal jatuh tempo harus setelah tanggal {{ $qcRecord->type === 'qc' ? 'QC' : 'kalibrasi' }}`;
                daysDisplay.className = 'text-red-600 font-medium mt-1 block';
            }
        } else {
            daysDisplay.textContent = '';
        }
    }
    
    // Toggle between input methods
    window.toggleDueDateMethod = function(method) {
        const daysMethod = document.getElementById('daysMethod');
        const dateMethod = document.getElementById('dateMethod');
        
        if (method === 'days') {
            daysMethod.classList.remove('hidden');
            dateMethod.classList.add('hidden');
            calculateDueDate();
        } else {
            daysMethod.classList.add('hidden');
            dateMethod.classList.remove('hidden');
            calculateDays();
        }
    };
    
    // Event listeners
    dateInput.addEventListener('change', function() {
        calculateDueDate();
        calculateDays();
    });
    
    daysInput.addEventListener('input', calculateDueDate);
    datePickerInput.addEventListener('change', calculateDays);
    
    // Calculate on load
    calculateDueDate();
});
</script>
@endsection
