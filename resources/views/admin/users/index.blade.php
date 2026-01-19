@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg">
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-900">Manajemen User</h3>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>
        
        <div class="p-6">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <p class="text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        <p class="text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $index => $user)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $users->firstItem() + $index }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($user->role)
                                        @case('super_admin')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                Super Admin
                                            </span>
                                            @break
                                        @case('admin')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Admin
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                User
                                            </span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition-colors duration-200">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                        <button onclick="showResetModal('{{ $user->id }}', '{{ $user->name }}', '{{ $user->email }}')" 
                                                class="inline-flex items-center px-3 py-1.5 bg-orange-600 text-white text-xs font-medium rounded hover:bg-orange-700 transition-colors duration-200">
                                            <i class="fas fa-key mr-1"></i> Reset Password
                                        </button>
                                        @if ($user->id !== auth()->id())
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 transition-colors duration-200" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                                    <i class="fas fa-trash mr-1"></i> Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                    <i class="fas fa-users text-4xl text-gray-300 mb-3 block"></i>
                                    Tidak ada data user
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-center">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Reset Password -->
<div id="resetPasswordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative p-4 w-full max-w-md mx-auto mt-20">
        <div class="bg-white rounded-lg shadow-xl">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Link Reset Password Berhasil Dibuat</h3>
                <button onclick="closeResetModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Body -->
            <div class="p-4">
                <!-- Success Icon -->
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600 text-2xl"></i>
                    </div>
                </div>
                
                <!-- Reset Link Field -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Link Reset Password</label>
                    <div class="flex">
                        <input type="text" 
                               id="resetLink" 
                               readonly 
                               value="https://example.com/reset-password/token-here" 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md rounded-r-none bg-gray-50 text-gray-600">
                        <button onclick="copyResetLink()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <!-- WhatsApp Message -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pesan WhatsApp</label>
                    <textarea id="whatsappMessage" 
                              rows="4" 
                              readonly
                              class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600">Halo [Nama User],

Berikut link reset password Anda:
https://example.com/reset-password/token-here

Link ini berlaku sampai: 20 Jan 2026 14:37

Segera reset password Anda untuk keamanan akun.</textarea>
                </div>
                
                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button onclick="copyWhatsAppMessage()" 
                            class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-200">
                        <i class="fab fa-whatsapp mr-2"></i> Salin Pesan WhatsApp
                    </button>
                    <button onclick="copyResetLink()" 
                            class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-copy mr-2"></i> Salin Link Reset Password
                    </button>
                </div>
                
                <!-- Expiry Info -->
                <div class="text-center text-sm text-gray-500">
                    <i class="fas fa-clock mr-1"></i>
                    Berlaku sampai: <span id="expiryTime">20 Jan 2026 14:37</span>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="flex justify-end p-4 border-t border-gray-200">
                <button onclick="closeResetModal()" 
                        class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Function to show reset password modal
function showResetModal(userId, userName, userEmail) {
    // Show loading state
    showNotification('Menghasilkan link reset password...', 'info');
    
    // Call backend to generate reset token
    fetch(`{{ route('users.reset-token', ['user' => ':userId']) }}`.replace(':userId', userId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                           document.querySelector('input[name="_token"]')?.value
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const resetUrl = window.location.origin + '/password-reset/' + data.token;
            const expiryTime = new Date(Date.now() + 2 * 60 * 60 * 1000); // 2 hours from now
            
            // Update modal content
            document.getElementById('resetLink').value = resetUrl;
            document.getElementById('whatsappMessage').value = `Halo ${userName},

Berikut link reset password Anda:
${resetUrl}

Link ini berlaku sampai: ${expiryTime.toLocaleString('id-ID', { 
    day: 'numeric', 
    month: 'long', 
    year: 'numeric', 
    hour: '2-digit', 
    minute: '2-digit' 
})}

Segera reset password Anda untuk keamanan akun.`;
            
            document.getElementById('expiryTime').textContent = expiryTime.toLocaleString('id-ID', { 
                day: 'numeric', 
                month: 'long', 
                year: 'numeric', 
                hour: '2-digit', 
                minute: '2-digit' 
            });
            
            // Show modal
            document.getElementById('resetPasswordModal').classList.remove('hidden');
            showNotification('Link reset password berhasil dibuat!', 'success');
        } else {
            showNotification('Gagal membuat link reset password', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan, silakan coba lagi', 'error');
    });
}

// Function to close modal
function closeResetModal() {
    document.getElementById('resetPasswordModal').classList.add('hidden');
}

// Function to copy reset link
function copyResetLink() {
    const resetLink = document.getElementById('resetLink');
    resetLink.select();
    document.execCommand('copy');
    
    // Show feedback
    showNotification('Link reset password berhasil disalin!', 'success');
}

// Function to copy WhatsApp message
function copyWhatsAppMessage() {
    const whatsappMessage = document.getElementById('whatsappMessage');
    whatsappMessage.select();
    document.execCommand('copy');
    
    // Show feedback
    showNotification('Pesan WhatsApp berhasil disalin!', 'success');
    
    // Tidak otomatis membuka WhatsApp, hanya menyalin pesan
}

// Function to show notification
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification-toast fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${
                type === 'success' ? 'fa-check-circle' : 
                type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'
            } mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add to body
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Close modal when clicking outside
document.getElementById('resetPasswordModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeResetModal();
    }
});
</script>
@endsection
