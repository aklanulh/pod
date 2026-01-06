<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi QR - KSO Item</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Card Container -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header with gradient -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white bg-opacity-20 rounded-full mb-4">
                    <i class="fas fa-qrcode text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Verifikasi QR</h1>
                <p class="text-blue-100 text-sm">Masukkan password untuk melihat detail KSO Item</p>
            </div>

            <!-- Content -->
            <div class="px-8 py-8">
                @if(isset($error))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                        <i class="fas fa-exclamation-circle text-red-600 mt-0.5 flex-shrink-0"></i>
                        <div>
                            <p class="text-red-800 font-medium">Error</p>
                            <p class="text-red-700 text-sm">{{ $error }}</p>
                        </div>
                    </div>
                @endif

                @if(isset($ksoItem))
                    <!-- Info KSO Item -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-sm text-gray-600 mb-1">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <span class="font-medium">KSO Item:</span>
                        </p>
                        <p class="text-lg font-bold text-blue-900">{{ $ksoItem->nama_alat }}</p>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-hospital text-gray-500 mr-2"></i>
                            {{ $ksoItem->customer->name ?? 'N/A' }}
                        </p>
                    </div>
                @endif

                <!-- Password Form -->
                <form action="{{ route('qr.verify', $id) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock text-gray-500 mr-2"></i>
                            Password
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                                placeholder="Masukkan password"
                                required
                                autofocus
                            >
                            <button 
                                type="button"
                                onclick="togglePassword()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                            >
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-3 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition duration-200 flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-unlock"></i>
                        Verifikasi Password
                    </button>
                </form>

                <!-- Info Box -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-600">
                        <i class="fas fa-shield-alt text-gray-500 mr-2"></i>
                        <span class="font-medium">Keamanan:</span> Password diperlukan untuk mengakses informasi detail KSO Item ini.
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-8 py-4 border-t border-gray-200 text-center">
                <p class="text-xs text-gray-500">
                    <i class="fas fa-copyright text-gray-400 mr-1"></i>
                    MSA PT - KSO Management System
                </p>
            </div>
        </div>

        <!-- Back Link -->
        <div class="text-center mt-6">
            <a href="javascript:history.back()" class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Auto-focus on password input
        document.getElementById('password').focus();
    </script>
</body>
</html>
