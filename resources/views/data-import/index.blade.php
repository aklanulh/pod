@extends('layouts.app')

@section('title', 'Data Import')

@section('content')
<div class="container-fluid p-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Data Import</h1>
            <p class="text-gray-600">Import data dari file Excel ke sistem</p>
        </div>
    </div>

    <!-- Import Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Suppliers Import -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="bg-blue-600 text-white px-6 py-4 rounded-t-lg">
                <h5 class="text-lg font-semibold mb-0">
                    <i class="fas fa-truck mr-2"></i>
                    Import Suppliers
                </h5>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Import data supplier dari file Excel</p>
                
                <!-- Download Template -->
                <div class="mb-4">
                    <a href="{{ route('data-import.template.suppliers') }}" class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-700 text-sm font-medium rounded-md hover:bg-blue-200 transition-colors">
                        <i class="fas fa-download mr-1"></i>
                        Download Template
                    </a>
                    <p class="text-sm text-gray-500 mt-1">Format: .xlsx, .xls, .csv (Max: 10MB)</p>
                </div>

                <!-- Upload Form -->
                <form id="supplierForm" enctype="multipart/form-data" class="space-y-4" onsubmit="return false;">
                    @csrf
                    <div>
                        <label for="supplierFile" class="block text-sm font-medium text-gray-700 mb-2">Pilih File</label>
                        <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" id="supplierFile" name="file" 
                               accept=".xlsx,.xls,.csv" required>
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors" onclick="importData('suppliers', 'supplierFile')">
                            <i class="fas fa-upload mr-2"></i>
                            Import Suppliers
                        </button>
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition-colors" onclick="previewData('suppliers')">
                            <i class="fas fa-eye mr-2"></i>
                            Preview Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Customers Import -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="bg-green-600 text-white px-6 py-4 rounded-t-lg">
                <h5 class="text-lg font-semibold mb-0">
                    <i class="fas fa-users mr-2"></i>
                    Import Customers
                </h5>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Import data customer dari file Excel</p>
                
                <!-- Download Template -->
                <div class="mb-4">
                    <a href="{{ route('data-import.template.customers') }}" class="inline-flex items-center px-3 py-2 bg-green-100 text-green-700 text-sm font-medium rounded-md hover:bg-green-200 transition-colors">
                        <i class="fas fa-download mr-1"></i>
                        Download Template
                    </a>
                    <p class="text-sm text-gray-500 mt-1">Format: .xlsx, .xls, .csv (Max: 10MB)</p>
                </div>

                <!-- Upload Form -->
                <form id="customerForm" enctype="multipart/form-data" class="space-y-4" onsubmit="return false;">
                    @csrf
                    <div>
                        <label for="customerFile" class="block text-sm font-medium text-gray-700 mb-2">Pilih File</label>
                        <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100" id="customerFile" name="file" 
                               accept=".xlsx,.xls,.csv" required>
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 transition-colors" onclick="importData('customers', 'customerFile')">
                            <i class="fas fa-upload mr-2"></i>
                            Import Customers
                        </button>
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition-colors" onclick="previewData('customers')">
                            <i class="fas fa-eye mr-2"></i>
                            Preview Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Import -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="bg-yellow-600 text-white px-6 py-4 rounded-t-lg">
                <h5 class="text-lg font-semibold mb-0">
                    <i class="fas fa-box mr-2"></i>
                    Import Products
                </h5>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Import data produk dari file Excel</p>
                
                <!-- Download Template -->
                <div class="mb-4">
                    <a href="{{ route('data-import.template.products') }}" class="inline-flex items-center px-3 py-2 bg-yellow-100 text-yellow-700 text-sm font-medium rounded-md hover:bg-yellow-200 transition-colors">
                        <i class="fas fa-download mr-1"></i>
                        Download Template
                    </a>
                    <p class="text-sm text-gray-500 mt-1">Format: .xlsx, .xls, .csv (Max: 10MB)</p>
                </div>

                <!-- Upload Form -->
                <form id="productForm" enctype="multipart/form-data" class="space-y-4" onsubmit="return false;">
                    @csrf
                    <div>
                        <label for="productFile" class="block text-sm font-medium text-gray-700 mb-2">Pilih File</label>
                        <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100" id="productFile" name="file" 
                               accept=".xlsx,.xls,.csv" required>
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white font-medium rounded-md hover:bg-yellow-700 transition-colors" onclick="importData('products', 'productFile')">
                            <i class="fas fa-upload mr-2"></i>
                            Import Products
                        </button>
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition-colors" onclick="previewData('products')">
                            <i class="fas fa-eye mr-2"></i>
                            Preview Data
                        </button>
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white font-medium rounded-md hover:bg-purple-700 transition-colors" onclick="testButtonClick()">
                            <i class="fas fa-bug mr-2"></i>
                            Test JS
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stock Movements Import -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="bg-cyan-600 text-white px-6 py-4 rounded-t-lg">
                <h5 class="text-lg font-semibold mb-0">
                    <i class="fas fa-exchange-alt mr-2"></i>
                    Import Stock Movements
                </h5>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Import data stock movement dari file Excel</p>
                
                <!-- Download Template -->
                <div class="mb-4">
                    <a href="{{ route('data-import.template.stock-movements') }}" class="inline-flex items-center px-3 py-2 bg-cyan-100 text-cyan-700 text-sm font-medium rounded-md hover:bg-cyan-200 transition-colors">
                        <i class="fas fa-download mr-1"></i>
                        Download Template
                    </a>
                    <p class="text-sm text-gray-500 mt-1">Format: .xlsx, .xls, .csv (Max: 10MB)</p>
                </div>

                <!-- Upload Form -->
                <form id="stockMovementForm" enctype="multipart/form-data" class="space-y-4" onsubmit="return false;">
                    @csrf
                    <div>
                        <label for="stockMovementFile" class="block text-sm font-medium text-gray-700 mb-2">Pilih File</label>
                        <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100" id="stockMovementFile" name="file" 
                               accept=".xlsx,.xls,.csv" required>
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-cyan-600 text-white font-medium rounded-md hover:bg-cyan-700 transition-colors" onclick="importData('stock-movements', 'stockMovementFile')">
                            <i class="fas fa-upload mr-2"></i>
                            Import Stock Movements
                        </button>
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition-colors" onclick="previewData('stock_movements')">
                            <i class="fas fa-eye mr-2"></i>
                            Preview Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- KSO Items Import -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="bg-purple-600 text-white px-6 py-4 rounded-t-lg">
                <h5 class="text-lg font-semibold mb-0">
                    <i class="fas fa-medkit mr-2"></i>
                    Import KSO Items
                </h5>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Import data KSO items (alat medis & alat pendukung) dari file Excel</p>
                
                <!-- Download Template -->
                <div class="mb-4">
                    <a href="{{ route('data-import.template.kso-items') }}" class="inline-flex items-center px-3 py-2 bg-purple-100 text-purple-700 text-sm font-medium rounded-md hover:bg-purple-200 transition-colors">
                        <i class="fas fa-download mr-1"></i>
                        Download Template
                    </a>
                    <p class="text-sm text-gray-500 mt-1">Format: .xlsx, .xls, .csv (Max: 10MB)</p>
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Template includes main items and support items. Use item_type column to specify.
                    </p>
                </div>

                <!-- Upload Form -->
                <form id="ksoItemsForm" enctype="multipart/form-data" class="space-y-4" onsubmit="return false;">
                    @csrf
                    <div>
                        <label for="ksoItemsFile" class="block text-sm font-medium text-gray-700 mb-2">Pilih File</label>
                        <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100" id="ksoItemsFile" name="file" 
                               accept=".xlsx,.xls,.csv" required>
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white font-medium rounded-md hover:bg-purple-700 transition-colors" onclick="importData('kso-items', 'ksoItemsFile')">
                            <i class="fas fa-upload mr-2"></i>
                            Import KSO Items
                        </button>
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition-colors" onclick="previewData('kso_items')">
                            <i class="fas fa-eye mr-2"></i>
                            Preview Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full" id="previewModal">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h5 class="text-lg font-semibold text-gray-900">Preview Data</h5>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex" onclick="closeModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="mt-2" id="previewContent">
                    <!-- Preview content will be loaded here -->
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition-colors" onclick="closeModal()">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Test if JavaScript is loading
console.log('Data Import JavaScript loaded successfully!');

// Import data function
function importData(type, fileInputId) {
    console.log('=== IMPORT DEBUG START ===');
    console.log('Type:', type);
    console.log('File Input ID:', fileInputId);
    
    const formData = new FormData();
    const fileInput = document.getElementById(fileInputId);
    
    console.log('File Input Element:', fileInput);
    console.log('Files:', fileInput.files);
    console.log('File Selected:', fileInput.files[0]);
    
    if (!fileInput.files[0]) {
        console.error('No file selected');
        showToast('Please select a file', 'error');
        return;
    }
    
    formData.append('file', fileInput.files[0]);
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    console.log('CSRF Token:', csrfToken ? 'found' : 'NOT FOUND');
    
    if (!csrfToken) {
        console.error('CSRF token not found');
        showToast('CSRF token error', 'error');
        return;
    }
    
    formData.append('_token', csrfToken);
    
    // Show loading
    const submitBtn = fileInput.closest('form').querySelector('button[type="button"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Importing...';
    
    const url = `/data-import/${type}`;
    console.log('Request URL:', url);
    console.log('Request Method: POST');
    console.log('FormData contents:');
    for (let pair of formData.entries()) {
        console.log(pair[0] + ':', pair[1]);
    }
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response received');
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            console.error('Response not OK:', response.status, response.statusText);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            showToast(data.message, 'success');
            fileInput.value = ''; // Clear file input
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showToast('Error: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        console.log('=== IMPORT DEBUG END ===');
    });
}

// Test button click
function testButtonClick() {
    console.log('Button clicked!');
    alert('JavaScript is working!');
}

// Preview data function
function previewData(type) {
    const fileInputId = type === 'suppliers' ? 'supplierFile' : 
                       type === 'customers' ? 'customerFile' : 
                       type === 'products' ? 'productFile' : 'stockMovementFile';
    
    const fileInput = document.getElementById(fileInputId);
    
    if (!fileInput.files[0]) {
        showToast('Please select a file first', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('file', fileInput.files[0]);
    formData.append('type', type);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    fetch('/data-import/preview', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayPreview(data);
            document.getElementById('previewModal').classList.remove('hidden');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        showToast('Error: ' + error.message, 'error');
    });
}

// Display preview data
function displayPreview(data) {
    let html = `
        <div class="mb-4">
            <span class="font-semibold">Total Rows:</span> ${data.total_rows}
            <span class="text-gray-500 ml-2">(Showing first 10 rows)</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
    `;
    
    // Add headers
    data.headers.forEach(header => {
        html += `<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">${header}</th>`;
    });
    html += `</tr></thead><tbody class="bg-white divide-y divide-gray-200">`;
    
    // Add data rows
    data.preview.forEach(row => {
        html += '<tr>';
        data.headers.forEach(header => {
            html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${row[header] || ''}</td>`;
        });
        html += '</tr>';
    });
    
    html += `</tbody></table></div>`;
    
    document.getElementById('previewContent').innerHTML = html;
}

// Close modal
function closeModal() {
    document.getElementById('previewModal').classList.add('hidden');
}

// Show toast notification
function showToast(message, type = 'info') {
    const toastHtml = `
        <div class="fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white ${type === 'error' ? 'bg-red-500' : type === 'success' ? 'bg-green-500' : 'bg-blue-500'} z-50">
            <div class="flex items-center">
                <span>${message}</span>
                <button type="button" class="ml-4 text-white hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
    
    const toastContainer = document.createElement('div');
    toastContainer.innerHTML = toastHtml;
    document.body.appendChild(toastContainer);
    
    setTimeout(() => {
        toastContainer.remove();
    }, 5000);
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing...');
    
    // Test if buttons exist
    const productBtn = document.querySelector('button[onclick*="importData(\'products\'"]');
    console.log('Product button found:', productBtn);
    
    if (productBtn) {
        console.log('Product button onclick:', productBtn.getAttribute('onclick'));
    }
});
</script>
@endsection
