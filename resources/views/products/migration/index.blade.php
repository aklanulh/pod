@extends('layouts.app')

@section('title', 'Product Migration')

@section('content')
<div class="container-fluid p-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Product Migration</h1>
            <p class="text-gray-600">Kelola migrasi produk dari nama lama ke nama baru</p>
        </div>
    </div>

    <!-- Migration Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Active Products</p>
                    <p class="text-xl font-semibold text-gray-800">{{ App\Models\Product::active()->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-exchange-alt text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Migrated Products</p>
                    <p class="text-xl font-semibold text-gray-800">{{ App\Models\Product::inactive()->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-history text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Products</p>
                    <p class="text-xl font-semibold text-gray-800">{{ App\Models\Product::count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Need Migration</p>
                    <p class="text-xl font-semibold text-gray-800">{{ App\Models\Product::where('is_active', true)->whereHas('stockMovements')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Produk</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">History</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Migrasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Migrated
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                @if($product->migration_notes)
                                    <div class="text-xs text-gray-500">{{ Str::limit($product->migration_notes, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $product->category->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->current_stock }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $product->stockMovements()->count() }} movements
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($product->migratedToProduct)
                                    <div class="text-xs">
                                        <div class="font-medium">{{ $product->migratedToProduct->name }}</div>
                                        <div class="text-gray-500">{{ $product->migratedToProduct->code }}</div>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($product->is_active && $product->stockMovements()->count() > 0)
                                    <button onclick="openMigrationModal({{ $product->id }}, '{{ $product->name }}', '{{ $product->code }}')" 
                                            class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-exchange-alt"></i> Migrate
                                    </button>
                                @endif
                                
                                <a href="{{ route('products.migration.show', $product->id) }}" 
                                   class="text-gray-600 hover:text-gray-900">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada data produk
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $products->links() }}
        </div>
    </div>
</div>

<!-- Migration Modal -->
<div id="migrationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Migrasi Produk</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex" onclick="closeMigrationModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="migrationForm" onsubmit="performMigration(event)">
                @csrf
                <input type="hidden" id="oldProductId" name="old_product_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Produk Lama</label>
                    <div class="p-3 bg-gray-100 rounded-md">
                        <div class="font-medium" id="oldProductName"></div>
                        <div class="text-sm text-gray-600" id="oldProductCode"></div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="newProductId" class="block text-sm font-medium text-gray-700 mb-2">Produk Baru</label>
                    <select id="newProductId" name="new_product_id" required 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih produk baru...</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="migrationNotes" class="block text-sm font-medium text-gray-700 mb-2">Catatan Migrasi</label>
                    <textarea id="migrationNotes" name="migration_notes" rows="3"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Alasan migrasi produk..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeMigrationModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-exchange-alt mr-2"></i>Migrasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentProductId = null;

function openMigrationModal(productId, productName, productCode) {
    currentProductId = productId;
    document.getElementById('oldProductId').value = productId;
    document.getElementById('oldProductName').textContent = productName;
    document.getElementById('oldProductCode').textContent = productCode;
    
    // Load active products
    fetch(`/products/migration/get-active-products?exclude_id=${productId}`)
        .then(response => response.json())
        .then(products => {
            const select = document.getElementById('newProductId');
            select.innerHTML = '<option value="">Pilih produk baru...</option>';
            
            products.forEach(product => {
                const option = document.createElement('option');
                option.value = product.id;
                option.textContent = `${product.name} (${product.code})`;
                select.appendChild(option);
            });
        });
    
    document.getElementById('migrationModal').classList.remove('hidden');
}

function closeMigrationModal() {
    document.getElementById('migrationModal').classList.add('hidden');
    document.getElementById('migrationForm').reset();
    currentProductId = null;
}

function performMigration(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    // Show loading
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Migrating...';
    
    fetch(`/products/migration/${currentProductId}/migrate`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            closeMigrationModal();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        showToast('Error: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

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
</script>
@endsection
