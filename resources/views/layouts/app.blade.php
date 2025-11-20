<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Warehouse Management System') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="/images/favicon.ico">
    <link rel="apple-touch-icon" href="/images/logopod.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen">
        <!-- Mobile menu button -->
        <div class="lg:hidden fixed top-4 left-4 z-50">
            <button @click="sidebarOpen = !sidebarOpen" 
                    class="bg-blue-800 text-white p-2 rounded-md shadow-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-bars text-lg"></i>
            </button>
        </div>

        <!-- Mobile overlay -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="lg:hidden fixed inset-0 bg-gray-600 bg-opacity-75 z-30"></div>

        <!-- Sidebar -->
        <div class="w-64 bg-blue-800 text-white flex-shrink-0 fixed lg:static inset-y-0 left-0 z-40 transform lg:transform-none transition-transform duration-300 ease-in-out"
             :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
             x-show="sidebarOpen || window.innerWidth >= 1024"
             x-init="sidebarOpen = window.innerWidth >= 1024"
             @resize.window="sidebarOpen = window.innerWidth >= 1024">
            <div class="p-4 flex items-center space-x-3">
                <img src="/images/logopod.png" alt="POD Logo" class="w-10 h-10 object-contain">
                <div>
                    <h1 class="text-xl font-bold">POD - MSA</h1>
                    <p class="text-sm text-blue-200">Platform Operating Digital</p>
                </div>
            </div>
            
            <nav class="mt-8">
                @if(Auth::user()->isSuperAdmin())
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 {{ request()->routeIs('dashboard') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard Super Admin
                    </a>
                    
                    <div class="mt-2">
                        <p class="px-4 py-1 text-xs font-semibold text-blue-300 uppercase tracking-wider">Inventory</p>
                        <a href="{{ route('products.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 {{ request()->routeIs('products.*') ? 'bg-blue-700' : '' }}">
                            <i class="fas fa-boxes mr-3"></i>
                            Produk
                        </a>
                    </div>
                @else
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard Admin
                    </a>
                    
                    <div class="mt-2">
                        <p class="px-4 py-1 text-xs font-semibold text-blue-300 uppercase tracking-wider">Inventory</p>
                        <a href="{{ route('admin.products.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 {{ request()->routeIs('admin.products.*') ? 'bg-blue-700' : '' }}">
                            <i class="fas fa-boxes mr-3"></i>
                            Produk
                        </a>
                    </div>
                @endif

                <div class="mt-2">
                    <p class="px-4 py-1 text-xs font-semibold text-blue-300 uppercase tracking-wider">Stock Movement</p>
                    <a href="{{ route('stock.in.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 {{ request()->routeIs('stock.in.*') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-arrow-down mr-3"></i>
                        Stok Masuk
                    </a>
                    <a href="{{ route('stock.out.index') }}" class="flex items-center justify-between px-4 py-2 text-white hover:bg-blue-700 {{ request()->routeIs('stock.out.*') ? 'bg-blue-700' : '' }}">
                        <div class="flex items-center">
                            <i class="fas fa-arrow-up mr-3"></i>
                            Stok Keluar
                        </div>
                        @if(isset($sidebarDraftCount) && $sidebarDraftCount > 0)
                            <span class="bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center min-w-[1.25rem]">
                                {{ $sidebarDraftCount > 99 ? '99+' : $sidebarDraftCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('stock.opname.index') }}" class="flex items-center justify-between px-4 py-2 text-white hover:bg-blue-700 {{ request()->routeIs('stock.opname.*') ? 'bg-blue-700' : '' }}">
                        <div class="flex items-center">
                            <i class="fas fa-clipboard-check mr-3"></i>
                            Stok Opname
                        </div>
                        @if(isset($sidebarOpnameDraftCount) && $sidebarOpnameDraftCount > 0)
                            <span class="bg-orange-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center min-w-[1.25rem]">
                                {{ $sidebarOpnameDraftCount > 99 ? '99+' : $sidebarOpnameDraftCount }}
                            </span>
                        @endif
                    </a>
                </div>

                <div class="mt-2">
                    <p class="px-4 py-1 text-xs font-semibold text-blue-300 uppercase tracking-wider">Master Data</p>
                    <a href="{{ route('suppliers.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 {{ request()->routeIs('suppliers.*') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-truck mr-3"></i>
                        Supplier
                    </a>
                    <a href="{{ route('customers.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 {{ request()->routeIs('customers.*') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-users mr-3"></i>
                        Customer
                    </a>
                </div>


                @if(Auth::user()->isSuperAdmin())
                    <div class="mt-2">
                        <p class="px-4 py-1 text-xs font-semibold text-blue-300 uppercase tracking-wider">KSO Management</p>
                        <a href="{{ route('kso-roi.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 {{ request()->routeIs('kso-roi.*') ? 'bg-blue-700' : '' }}">
                            <i class="fas fa-handshake mr-3"></i>
                            KSO Management
                        </a>
                    </div>

                    <div class="mt-2">
                        <p class="px-4 py-1 text-xs font-semibold text-blue-300 uppercase tracking-wider">Reports</p>
                        <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 {{ request()->routeIs('reports.*') ? 'bg-blue-700' : '' }}">
                            <i class="fas fa-chart-bar mr-3"></i>
                            Laporan Gudang
                        </a>
                    </div>

                    <div class="mt-2">
                        <p class="px-4 py-1 text-xs font-semibold text-blue-300 uppercase tracking-wider">Administration</p>
                        <a href="{{ route('admin.history.index') }}" class="flex items-center px-4 py-2 text-white hover:bg-blue-700 {{ request()->routeIs('admin.history.*') ? 'bg-blue-700' : '' }}">
                            <i class="fas fa-history mr-3"></i>
                            Log Aktivitas
                        </a>
                    </div>
                @endif
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0" 
             :class="{ 'ml-0': !sidebarOpen || window.innerWidth < 1024, 'lg:ml-0': true }">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <!-- Mobile close button when sidebar is open -->
                        <button @click="sidebarOpen = false" 
                                x-show="sidebarOpen && window.innerWidth < 1024"
                                class="lg:hidden mr-4 text-gray-600 hover:text-gray-800">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                        <h2 class="text-xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h2>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">{{ date('d M Y') }}</span>
                        @auth
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-600">
                                    <i class="fas fa-user mr-1"></i>
                                    {{ Auth::user()->name }}
                                </span>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800 flex items-center">
                                        <i class="fas fa-sign-out-alt mr-1"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        @endauth
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>
