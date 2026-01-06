<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AdminActivityLog;
use Illuminate\Support\Facades\Auth;

class LogAdminActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for authenticated users
        if (Auth::check()) {
            $this->logActivity($request, $response);
        }

        return $response;
    }

    private function logActivity(Request $request, Response $response): void
    {
        $method = $request->method();
        $path = $request->path();
        $user = Auth::user();

        // Skip logging for certain routes
        if (!$this->shouldLog($request)) {
            return;
        }

        // Determine action based on HTTP method and path
        $action = $this->determineAction($method, $path);
        $module = $this->determineModule($path);
        $description = $this->generateDescription($method, $path, $user);

        // Get additional data
        $data = [
            'method' => $method,
            'path' => $path,
            'status_code' => $response->getStatusCode(),
            'request_data' => $this->sanitizeRequestData($request)
        ];

        AdminActivityLog::logActivity($action, $module, $description, $data);
    }

    /**
     * Determine if the request should be logged
     */
    private function shouldLog(Request $request): bool
    {
        $path = $request->path();
        $method = $request->method();
        
        // Only log important actions - CREATE, UPDATE, DELETE operations
        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return false;
        }
        
        // Skip certain paths that don't need logging
        $skipPaths = [
            'admin/dashboard',
            'admin/history',
            'reports',
            '_debugbar',
            'favicon.ico',
            'up',
            'register'
        ];
        
        foreach ($skipPaths as $skipPath) {
            if (str_starts_with($path, $skipPath)) {
                return false;
            }
        }
        
        // Always log login/logout activities
        if (str_contains($path, 'login') || str_contains($path, 'logout')) {
            return true;
        }
        
        // Only log specific important operations
        $importantPaths = [
            'stock/in',           // Stock In transactions
            'stock/out',          // Stock Out transactions  
            'stock/opname',       // Stock Opname operations
            'products',           // Product CRUD
            'suppliers',          // Supplier CRUD
            'customers',          // Customer CRUD
            'categories',         // Category CRUD
            'reports/export',     // Excel export operations
            'export'              // General export operations
        ];
        
        foreach ($importantPaths as $importantPath) {
            if (str_starts_with($path, $importantPath)) {
                return true;
            }
        }
        
        return false;
    }

    private function determineAction(string $method, string $path): string
    {
        if (str_contains($path, 'login')) return 'login';
        if (str_contains($path, 'logout')) return 'logout';

        return match($method) {
            'GET' => 'view',
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'unknown'
        };
    }

    private function determineModule(string $path): string
    {
        if (str_contains($path, 'products')) return 'products';
        if (str_contains($path, 'stock')) return 'stock';
        if (str_contains($path, 'suppliers')) return 'suppliers';
        if (str_contains($path, 'customers')) return 'customers';
        if (str_contains($path, 'reports')) return 'reports';
        if (str_contains($path, 'dashboard')) return 'dashboard';
        if (str_contains($path, 'opname')) return 'opname';

        return 'system';
    }

    private function generateDescription(string $method, string $path, $user): string
    {
        $userName = $user ? $user->name : 'Unknown User';
        
        if (str_contains($path, 'login')) {
            return "{$userName} melakukan login ke sistem dari halaman login";
        }
        
        if (str_contains($path, 'logout')) {
            return "{$userName} melakukan logout dari sistem";
        }
        
        // Get specific action and location details
        $actionDetails = $this->getDetailedAction($method, $path);
        $locationDetails = $this->getLocationDetails($path);
        
        return "{$userName} {$actionDetails['action']} {$actionDetails['target']} di {$locationDetails}";
    }
    
    private function getDetailedAction(string $method, string $path): array
    {
        // Stock operations
        if (str_contains($path, 'stock/in')) {
            return [
                'action' => $method === 'POST' ? 'melakukan transaksi stok masuk' : 'mengakses halaman stok masuk',
                'target' => 'barang'
            ];
        }
        
        if (str_contains($path, 'stock/out')) {
            return [
                'action' => $method === 'POST' ? 'melakukan transaksi stok keluar' : 'mengakses halaman stok keluar',
                'target' => 'barang'
            ];
        }
        
        if (str_contains($path, 'stock/opname')) {
            if (str_contains($path, 'complete')) {
                return ['action' => 'membuat complete stock opname', 'target' => ''];
            }
            
            $action = match($method) {
                'POST' => 'membuat stock opname baru',
                'PUT', 'PATCH' => 'mengupdate stock opname',
                'DELETE' => 'menghapus stock opname',
                default => 'mengakses stock opname'
            };
            return ['action' => $action, 'target' => ''];
        }
        
        // Product operations
        if (str_contains($path, 'products')) {
            $action = match($method) {
                'POST' => 'menambah produk baru',
                'PUT', 'PATCH' => 'mengedit data produk',
                'DELETE' => 'menghapus produk',
                default => 'melihat daftar produk'
            };
            return ['action' => $action, 'target' => ''];
        }
        
        // Supplier operations
        if (str_contains($path, 'suppliers')) {
            $action = match($method) {
                'POST' => 'menambah supplier baru',
                'PUT', 'PATCH' => 'mengedit data supplier',
                'DELETE' => 'menghapus supplier',
                default => 'melihat daftar supplier'
            };
            return ['action' => $action, 'target' => ''];
        }
        
        // Customer operations
        if (str_contains($path, 'customers')) {
            $action = match($method) {
                'POST' => 'menambah customer baru',
                'PUT', 'PATCH' => 'mengedit data customer',
                'DELETE' => 'menghapus customer',
                default => 'melihat daftar customer'
            };
            return ['action' => $action, 'target' => ''];
        }
        
        // Category operations
        if (str_contains($path, 'categories')) {
            $action = match($method) {
                'POST' => 'menambah kategori baru',
                'PUT', 'PATCH' => 'mengedit kategori',
                'DELETE' => 'menghapus kategori',
                default => 'melihat daftar kategori'
            };
            return ['action' => $action, 'target' => ''];
        }
        
        // Export operations
        if (str_contains($path, 'export') || str_contains($path, 'reports/export')) {
            $exportType = 'data';
            if (str_contains($path, 'supplier')) {
                $exportType = 'laporan supplier';
            } elseif (str_contains($path, 'customer')) {
                $exportType = 'laporan customer';
            } elseif (str_contains($path, 'stock')) {
                $exportType = 'laporan stok';
            } elseif (str_contains($path, 'history')) {
                $exportType = 'history admin';
            }
            
            return ['action' => "mengekspor {$exportType} ke Excel", 'target' => ''];
        }
        
        // Default fallback
        $action = match($method) {
            'POST' => 'menambah data',
            'PUT', 'PATCH' => 'mengedit data',
            'DELETE' => 'menghapus data',
            default => 'mengakses'
        };
        
        return ['action' => $action, 'target' => ''];
    }
    
    private function getLocationDetails(string $path): string
    {
        if (str_contains($path, 'stock/in')) {
            return 'halaman Stok Masuk';
        }
        
        if (str_contains($path, 'stock/out')) {
            return 'halaman Stok Keluar';
        }
        
        if (str_contains($path, 'stock/opname')) {
            if (str_contains($path, 'complete')) {
                return 'halaman detail Stock Opname';
            }
            return 'halaman Stock Opname';
        }
        
        if (str_contains($path, 'products')) {
            return 'halaman Master Produk';
        }
        
        if (str_contains($path, 'suppliers')) {
            return 'halaman Master Supplier';
        }
        
        if (str_contains($path, 'customers')) {
            return 'halaman Master Customer';
        }
        
        if (str_contains($path, 'categories')) {
            return 'halaman Master Kategori';
        }
        
        if (str_contains($path, 'export') || str_contains($path, 'reports/export')) {
            return 'halaman Export/Download';
        }
        
        if (str_contains($path, 'dashboard')) {
            return 'halaman Dashboard';
        }
        
        if (str_contains($path, 'reports')) {
            return 'halaman Laporan';
        }
        
        return 'sistem';
    }

    private function sanitizeRequestData(Request $request): array
    {
        $data = $request->except(['password', 'password_confirmation', '_token']);
        
        // Limit data size to prevent large logs
        if (count($data) > 20) {
            $data = array_slice($data, 0, 20);
            $data['_truncated'] = true;
        }

        return $data;
    }
}
