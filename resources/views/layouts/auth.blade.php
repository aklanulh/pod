<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Warehouse Management System') }} - @yield('title', 'Authentication')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ url('public/images/favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('public/images/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ url('public/images/logopod.png') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <!-- Logo and Company Info -->
        <div class="text-center mb-8">
            <div class="bg-white rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center shadow-lg p-2">
                <img src="{{ url('public/images/logopod.png') }}" alt="POD Logo" class="w-full h-full object-contain">
            </div>
            <h1 class="text-2xl font-bold text-gray-800">PT. Mitrajaya Selaras Abadi</h1>
            <p class="text-gray-600 mt-1">Platform Operating Digital</p>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <div class="text-center mt-8 text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} PT. Mitrajaya Selaras Abadi. All rights reserved.</p>
        </div>
    </div>
    
    @yield('scripts')
</body>
</html>
