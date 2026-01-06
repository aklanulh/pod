@extends('layouts.app')

@section('title', 'Manajemen Produk')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Daftar Produk</h1>
    <div class="flex space-x-3">
        <button onclick="openCategoryModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-tags mr-2"></i>
            Kelola Kategori
        </button>
        <a href="{{ route('products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Produk
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exp/Lot/NIE</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $product->code }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                @if(strlen($product->name) > 30)
                                    <div class="text-sm font-medium text-gray-900">
                                        <span class="truncate block max-w-xs">{{ Str::limit($product->name, 30) }}</span>
                                        <button onclick="showNameModal('{{ addslashes($product->name) }}')" 
                                                class="text-blue-600 hover:text-blue-800 cursor-pointer inline-flex items-center mt-1">
                                            <i class="fas fa-eye text-xs mr-1"></i>
                                            <span class="text-xs">Lihat lengkap</span>
                                        </button>
                                    </div>
                                @else
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                @endif
                                @if($product->description)
                                    @if(strlen($product->description) > 30)
                                        <div class="text-sm text-gray-500 max-w-xs mt-1">
                                            <span class="truncate block">{{ Str::limit($product->description, 30) }}</span>
                                            <button onclick="showDescriptionModal('{{ addslashes($product->description) }}')" 
                                                    class="text-blue-600 hover:text-blue-800 cursor-pointer inline-flex items-center mt-1">
                                                <i class="fas fa-eye text-xs mr-1"></i>
                                                <span class="text-xs">Lihat lengkap</span>
                                            </button>
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500 mt-1">{{ $product->description }}</div>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $product->category->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 font-medium">{{ $product->current_stock }} {{ $product->unit }}</div>
                            <div class="text-sm text-gray-500">Min: {{ $product->minimum_stock }}</div>
                            
                            <!-- Stock Status Badge -->
                            <div class="mt-1">
                                @if($product->current_stock == 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Habis
                                    </span>
                                @elseif($product->isLowStock())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Stok Menipis
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Stok Aman
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                @if($product->expired_date)
                                    @php
                                        $isExpired = $product->expired_date->isPast();
                                        $daysUntilExpiry = $isExpired ? 0 : now()->diffInDays($product->expired_date);
                                        $isExpiringIn1Month = !$isExpired && $daysUntilExpiry <= 30;
                                        $isExpiringIn2Months = !$isExpired && $daysUntilExpiry > 30 && $daysUntilExpiry <= 60;
                                        $isGood = !$isExpired && $daysUntilExpiry > 60;
                                    @endphp
                                    <div class="text-xs {{ $isExpired || $isExpiringIn1Month ? 'text-red-600 font-semibold' : ($isExpiringIn2Months ? 'text-yellow-600 font-medium' : 'text-green-600 font-medium') }}">
                                        {{ $product->expired_date->format('d/m/Y') }}
                                        @if($isExpired || $isExpiringIn1Month)
                                            <i class="fas fa-exclamation-triangle ml-1"></i>
                                        @elseif($isExpiringIn2Months)
                                            <i class="fas fa-clock ml-1"></i>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-xs text-gray-400 italic">-</div>
                                @endif
                                
                                @if($product->lot_number)
                                    <div class="text-gray-900 font-medium">{{ $product->lot_number }}</div>
                                @else
                                    <div class="text-gray-400 italic">-</div>
                                @endif
                                
                                @if($product->distribution_permit)
                                    <div class="text-xs text-gray-500 truncate max-w-xs" title="{{ $product->distribution_permit }}">
                                        {{ Str::limit($product->distribution_permit, 15) }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('products.edit', $product) }}" class="text-yellow-600 hover:text-yellow-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
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
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada produk</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
</div>

<!-- Modal untuk menampilkan deskripsi lengkap -->
<div id="descriptionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Deskripsi Produk</h3>
                <button onclick="closeDescriptionModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="mt-2 px-2 py-2">
                <p id="descriptionContent" class="text-sm text-gray-700 whitespace-pre-wrap"></p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeDescriptionModal()" 
                        class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk menampilkan nama lengkap produk -->
<div id="nameModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Nama Produk Lengkap</h3>
                <button onclick="closeNameModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="mt-2 px-2 py-2">
                <p id="nameContent" class="text-sm text-gray-700 whitespace-pre-wrap font-medium"></p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeNameModal()" 
                        class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk manajemen kategori -->
<div id="categoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/5 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Manajemen Kategori Produk</h3>
                <button onclick="closeCategoryModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <!-- Form tambah kategori -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="text-md font-medium text-gray-900 mb-3">Tambah Kategori Baru</h4>
                <form id="addCategoryForm" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="categoryName" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                            <input type="text" id="categoryName" name="name" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="categoryDescription" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <input type="text" id="categoryDescription" name="description"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-plus mr-2"></i>Tambah Kategori
                    </button>
                </form>
            </div>
            
            <!-- Daftar kategori -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="categoryTableBody" class="bg-white divide-y divide-gray-200">
                        <!-- Categories will be loaded here -->
                    </tbody>
                </table>
            </div>
            
            <div class="flex justify-end mt-6">
                <button onclick="closeCategoryModal()" 
                        class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal edit kategori -->
<div id="editCategoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit Kategori</h3>
                <button onclick="closeEditCategoryModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <form id="editCategoryForm" class="space-y-4">
                <input type="hidden" id="editCategoryId">
                <div>
                    <label for="editCategoryName" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                    <input type="text" id="editCategoryName" name="name" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="editCategoryDescription" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <input type="text" id="editCategoryDescription" name="description"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditCategoryModal()" 
                            class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Description Modal Functions
function showDescriptionModal(description) {
    document.getElementById('descriptionContent').textContent = description;
    document.getElementById('descriptionModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDescriptionModal() {
    document.getElementById('descriptionModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Name Modal Functions
function showNameModal(name) {
    document.getElementById('nameContent').textContent = name;
    document.getElementById('nameModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeNameModal() {
    document.getElementById('nameModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Category Modal Functions
function openCategoryModal() {
    document.getElementById('categoryModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    loadCategories();
}

function closeCategoryModal() {
    document.getElementById('categoryModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    // Reset form
    document.getElementById('addCategoryForm').reset();
}

function closeEditCategoryModal() {
    document.getElementById('editCategoryModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function loadCategories() {
    fetch('/product-categories', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            console.log('Load categories response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(categories => {
            console.log('Categories loaded:', categories);
            const tbody = document.getElementById('categoryTableBody');
            tbody.innerHTML = '';
            
            if (Array.isArray(categories) && categories.length > 0) {
                categories.forEach(category => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            ${category.name}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${category.description || '-'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${category.products_count || 0} produk
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="editCategory(${category.id}, '${category.name}', '${category.description || ''}')" 
                                        class="text-yellow-600 hover:text-yellow-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteCategory(${category.id}, '${category.name}', ${category.products_count || 0})" 
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada kategori</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error loading categories:', error);
            const tbody = document.getElementById('categoryTableBody');
            tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-red-500">Gagal memuat kategori: ' + error.message + '</td></tr>';
        });
}

function editCategory(id, name, description) {
    document.getElementById('editCategoryId').value = id;
    document.getElementById('editCategoryName').value = name;
    document.getElementById('editCategoryDescription').value = description;
    document.getElementById('editCategoryModal').classList.remove('hidden');
}

function deleteCategory(id, name, productCount) {
    if (productCount > 0) {
        alert(`Kategori "${name}" tidak dapat dihapus karena masih digunakan oleh ${productCount} produk.`);
        return;
    }
    
    if (confirm(`Apakah Anda yakin ingin menghapus kategori "${name}"?`)) {
        // Check if CSRF token exists
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            alert('CSRF token tidak ditemukan. Silakan refresh halaman.');
            return;
        }
        
        fetch(`/product-categories/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Delete response status:', response.status);
            
            if (!response.ok) {
                return response.text().then(text => {
                    console.log('Delete error response text:', text);
                    throw new Error(`HTTP error! status: ${response.status}, response: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Delete response data:', data);
            if (data.success) {
                alert(data.message);
                loadCategories();
                // Reload page to update product list if needed
                location.reload();
            } else {
                alert(data.message || 'Gagal menghapus kategori');
            }
        })
        .catch(error => {
            console.error('Error deleting category:', error);
            alert('Gagal menghapus kategori: ' + error.message);
        });
    }
}

// Form Submissions
document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate form
    const nameInput = document.getElementById('categoryName');
    const name = nameInput.value.trim();
    
    if (!name) {
        alert('Nama kategori harus diisi');
        nameInput.focus();
        return;
    }
    
    const formData = new FormData(this);
    const data = {
        name: name,
        description: formData.get('description') || ''
    };
    
    // Debug log
    console.log('Sending data:', data);
    console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
    
    // Check if CSRF token exists
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        alert('CSRF token tidak ditemukan. Silakan refresh halaman.');
        return;
    }
    
    fetch('/product-categories', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            return response.text().then(text => {
                console.log('Error response text:', text);
                throw new Error(`HTTP error! status: ${response.status}, response: ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            alert(data.message);
            this.reset();
            loadCategories();
            // Reload page to update product form dropdowns
            location.reload();
        } else {
            alert(data.message || 'Gagal menambah kategori');
        }
    })
    .catch(error => {
        console.error('Error adding category:', error);
        alert('Gagal menambah kategori: ' + error.message);
    });
});

document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const id = document.getElementById('editCategoryId').value;
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Check if CSRF token exists
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        alert('CSRF token tidak ditemukan. Silakan refresh halaman.');
        return;
    }
    
    fetch(`/product-categories/${id}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Edit response status:', response.status);
        
        if (!response.ok) {
            return response.text().then(text => {
                console.log('Edit error response text:', text);
                throw new Error(`HTTP error! status: ${response.status}, response: ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Edit response data:', data);
        if (data.success) {
            alert(data.message);
            closeEditCategoryModal();
            loadCategories();
            // Reload page to update product list if needed
            location.reload();
        } else {
            alert(data.message || 'Gagal mengupdate kategori');
        }
    })
    .catch(error => {
        console.error('Error updating category:', error);
        alert('Gagal mengupdate kategori: ' + error.message);
    });
});

// Close modal when clicking outside
document.getElementById('descriptionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDescriptionModal();
    }
});

document.getElementById('nameModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeNameModal();
    }
});

document.getElementById('categoryModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCategoryModal();
    }
});

document.getElementById('editCategoryModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditCategoryModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDescriptionModal();
        closeNameModal();
        closeCategoryModal();
        closeEditCategoryModal();
    }
});
</script>
@endsection
