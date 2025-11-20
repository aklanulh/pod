<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminHistoryController;
use App\Http\Controllers\CustomerScheduleController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\KsoRoiController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// QR Routes - Tanpa Login (Public)
Route::prefix('qr')->name('qr.')->group(function () {
    Route::get('kso/{uniqueId}', [KsoRoiController::class, 'qrVerify'])->name('verify');
    Route::post('kso/{uniqueId}/search', [KsoRoiController::class, 'qrSearch'])->name('search');
    Route::post('kso/{uniqueId}/password', [KsoRoiController::class, 'verifyQrPassword'])->name('password');
    Route::get('kso/{uniqueId}/detail', [KsoRoiController::class, 'qrDetail'])->name('detail');
});

// Root route - redirect based on authentication and role
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    if (Auth::user()->isSuperAdmin()) {
        return redirect()->route('dashboard');
    } else {
        return redirect()->route('admin.dashboard');
    }
})->name('home');

// Protected Routes - Super Admin Only (Dashboard, Products, Reports)
Route::middleware(['auth', 'super_admin'])->group(function () {
    // Dashboard - Super Admin Only (with fallback)
    Route::get('/dashboard', function () {
        try {
            return app(DashboardController::class)->index(request());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Dashboard error: ' . $e->getMessage());
            return view('dashboard-error', ['error' => $e->getMessage()]);
        }
    })->name('dashboard');

    // Products - Super Admin Only
    Route::resource('products', ProductController::class);
    Route::post('products/ajax', [ProductController::class, 'store'])->name('products.ajax.store');

    // Reports - Super Admin Only
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('stock', [ReportController::class, 'stockReport'])->name('stock');
        Route::get('stock/{id}', [ReportController::class, 'stockDetail'])->name('stock.detail');
        Route::get('movement', [ReportController::class, 'movementReport'])->name('movement');
        Route::get('supplier', [ReportController::class, 'supplierReport'])->name('supplier');
        Route::get('supplier/{id}', [ReportController::class, 'supplierDetail'])->name('supplier.detail');
        Route::get('customer', [ReportController::class, 'customerReport'])->name('customer');
        Route::get('customer/{id}', [ReportController::class, 'customerDetail'])->name('customer.detail');

        // Export routes
        Route::get('export/stock', [ReportController::class, 'exportStockReport'])->name('export.stock');
        Route::get('export/stock/{id}', [ReportController::class, 'exportStockDetail'])->name('export.stock.detail');
        Route::get('export/movement', [ReportController::class, 'exportMovementReport'])->name('export.movement');
        Route::get('export/supplier', [ReportController::class, 'exportSupplierReport'])->name('export.supplier');
        Route::get('export/supplier/{id}', [ReportController::class, 'exportSupplierDetail'])->name('export.supplier.detail');
        Route::get('export/customer', [ReportController::class, 'exportCustomerReport'])->name('export.customer');
        Route::get('export/customer/{id}', [ReportController::class, 'exportCustomerDetail'])->name('export.customer.detail');
    });

    // Admin History - Super Admin Only
    Route::prefix('admin/history')->name('admin.history.')->group(function () {
        Route::get('/', [AdminHistoryController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminHistoryController::class, 'show'])->name('show');
        Route::get('/export/csv', [AdminHistoryController::class, 'export'])->name('export');
    });

    // Super Admin can also access all regular admin features
    // Stock Movements - Super Admin has full access
    Route::prefix('stock')->group(function () {
        Route::get('in', [StockMovementController::class, 'stockInIndex'])->name('stock.in.index');
        Route::get('in/create', [StockMovementController::class, 'stockInCreate'])->name('stock.in.create');
        Route::post('in', [StockMovementController::class, 'stockInStore'])->name('stock.in.store');

        Route::get('out', [StockMovementController::class, 'stockOutIndex'])->name('stock.out.index');
        Route::get('out/create', [StockMovementController::class, 'stockOutCreate'])->name('stock.out.create');
        Route::post('out', [StockMovementController::class, 'stockOutStore'])->name('stock.out.store');
        Route::post('out/export-invoice', [StockMovementController::class, 'exportStockOutExcel'])->name('stock.out.export.invoice');
        Route::post('out/export-xlsx', [StockMovementController::class, 'exportStockOutToExcel'])->name('stock.out.export.xlsx');
        Route::post('out/export-delivery-note', [StockMovementController::class, 'exportDeliveryNote'])->name('stock.out.export.delivery');

        // Draft routes
        Route::get('out/drafts', [StockMovementController::class, 'draftIndex'])->name('stock.out.draft.index');
        Route::post('out/draft/save', [StockMovementController::class, 'saveDraft'])->name('stock.out.draft.save');
        Route::get('out/draft/{id}/edit', [StockMovementController::class, 'editDraft'])->name('stock.out.draft.edit');
        Route::post('out/draft/{id}/update', [StockMovementController::class, 'updateDraft'])->name('stock.out.draft.update');
        Route::delete('out/draft/{id}', [StockMovementController::class, 'deleteDraft'])->name('stock.out.draft.delete');
        Route::post('out/draft/{id}/process', [StockMovementController::class, 'processDraft'])->name('stock.out.draft.process');

        Route::resource('opname', StockOpnameController::class)->names([
            'index' => 'stock.opname.index',
            'create' => 'stock.opname.create',
            'store' => 'stock.opname.store',
            'show' => 'stock.opname.show',
            'edit' => 'stock.opname.edit',
            'update' => 'stock.opname.update',
            'destroy' => 'stock.opname.destroy'
        ]);
        Route::post('opname/{stockOpname}/complete', [StockOpnameController::class, 'complete'])->name('stock.opname.complete');
        Route::post('opname/{opnameId}/detail/{detailId}/update', [StockOpnameController::class, 'updateDetail'])->name('stock.opname.detail.update');
        Route::post('opname/{opnameId}/detail/add', [StockOpnameController::class, 'addDetail'])->name('stock.opname.detail.add');
        Route::delete('opname/{opnameId}/detail/{detailId}', [StockOpnameController::class, 'deleteDetail'])->name('stock.opname.detail.delete');
    });

    // Suppliers - Super Admin has full access
    Route::resource('suppliers', SupplierController::class);
    Route::post('suppliers/ajax', [SupplierController::class, 'store'])->name('suppliers.ajax.store');

    // Customers - Super Admin has full access
    Route::resource('customers', CustomerController::class);
    Route::post('customers/ajax', [CustomerController::class, 'store'])->name('customers.ajax.store');

    // Customer Schedules - Super Admin Only
    Route::resource('customer-schedules', CustomerScheduleController::class);
    Route::get('customers/{customer}/last-purchases', [CustomerScheduleController::class, 'getCustomerLastPurchases'])->name('customers.last-purchases');
    Route::post('customer-schedules/{customerSchedule}/notify', [CustomerScheduleController::class, 'markAsNotified'])->name('customer-schedules.notify');
    Route::post('customer-schedules/{customerSchedule}/complete', [CustomerScheduleController::class, 'markAsCompleted'])->name('customer-schedules.complete');

    // KSO ROI Routes - Super Admin Only
    Route::prefix('kso-roi')->name('kso-roi.')->group(function () {
        Route::get('/', [KsoRoiController::class, 'index'])->name('index');
        Route::get('/kso-items', [KsoRoiController::class, 'ksoItems'])->name('kso-items');
        Route::get('/kso-items/create', [KsoRoiController::class, 'createKsoItem'])->name('create-kso-item');
        Route::post('/kso-items', [KsoRoiController::class, 'storeKsoItem'])->name('store-kso-item');
        Route::get('/kso-items/{ksoItem}/edit', [KsoRoiController::class, 'editKsoItem'])->name('edit-kso-item');
        Route::put('/kso-items/{ksoItem}', [KsoRoiController::class, 'updateKsoItem'])->name('update-kso-item');
        Route::delete('/kso-items/{ksoItem}', [KsoRoiController::class, 'destroyKsoItem'])->name('destroy-kso-item');
        Route::get('/customer/{customer}', [KsoRoiController::class, 'customerDetail'])->name('customer-detail');
        Route::get('/analytics', [KsoRoiController::class, 'analytics'])->name('analytics');
    });
});

// Protected Routes - Regular Admin Only (Dashboard, Products, Stock, Suppliers, Customers)
Route::middleware('auth')->group(function () {
    // Admin Dashboard - Available to regular admins only
    Route::get('/admin-dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Admin Products - Available to regular admins (without financial data)
    Route::prefix('admin/products')->name('admin.products.')->group(function () {
        Route::get('/', [AdminProductController::class, 'index'])->name('index');
        Route::get('/{product}', [AdminProductController::class, 'show'])->name('show');
    });

    // Admin Stock Routes
    Route::prefix('admin/stock')->name('admin.stock.')->group(function () {
        Route::get('movements', [StockMovementController::class, 'adminMovements'])->name('movements');

        Route::get('in', [StockMovementController::class, 'stockInIndex'])->name('in.index');
        Route::get('in/create', [StockMovementController::class, 'stockInCreate'])->name('in.create');
        Route::post('in', [StockMovementController::class, 'stockInStore'])->name('in.store');

        Route::get('out', [StockMovementController::class, 'stockOutIndex'])->name('out.index');
        Route::get('out/create', [StockMovementController::class, 'stockOutCreate'])->name('out.create');
        Route::post('out', [StockMovementController::class, 'stockOutStore'])->name('out.store');

        Route::resource('opname', StockOpnameController::class)->names([
            'index' => 'opname.index',
            'create' => 'opname.create',
            'store' => 'opname.store',
            'show' => 'opname.show',
            'edit' => 'opname.edit',
            'update' => 'opname.update',
            'destroy' => 'opname.destroy'
        ]);
        Route::post('opname/{stockOpname}/complete', [StockOpnameController::class, 'complete'])->name('opname.complete');
        Route::post('opname/{opnameId}/detail/{detailId}/update', [StockOpnameController::class, 'updateDetail'])->name('opname.detail.update');
        Route::post('opname/{opnameId}/detail/add', [StockOpnameController::class, 'addDetail'])->name('opname.detail.add');
        Route::delete('opname/{opnameId}/detail/{detailId}', [StockOpnameController::class, 'deleteDetail'])->name('opname.detail.delete');
    });

    // Stock Movements - Available to regular admins only (legacy routes for compatibility)
    Route::prefix('stock')->group(function () {
        Route::get('in', [StockMovementController::class, 'stockInIndex'])->name('stock.in.index');
        Route::get('in/create', [StockMovementController::class, 'stockInCreate'])->name('stock.in.create');
        Route::post('in', [StockMovementController::class, 'stockInStore'])->name('stock.in.store');

        Route::get('out', [StockMovementController::class, 'stockOutIndex'])->name('stock.out.index');
        Route::get('out/create', [StockMovementController::class, 'stockOutCreate'])->name('stock.out.create');
        Route::post('out', [StockMovementController::class, 'stockOutStore'])->name('stock.out.store');
        Route::post('out/export-invoice', [StockMovementController::class, 'exportStockOutExcel'])->name('stock.out.export.invoice');
        Route::post('out/export-xlsx', [StockMovementController::class, 'exportStockOutToExcel'])->name('stock.out.export.xlsx');

        Route::resource('opname', StockOpnameController::class)->names([
            'index' => 'stock.opname.index',
            'create' => 'stock.opname.create',
            'store' => 'stock.opname.store',
            'show' => 'stock.opname.show',
            'edit' => 'stock.opname.edit',
            'update' => 'stock.opname.update',
            'destroy' => 'stock.opname.destroy'
        ]);
        Route::post('opname/{stockOpname}/complete', [StockOpnameController::class, 'complete'])->name('stock.opname.complete');
        Route::post('opname/{opnameId}/detail/{detailId}/update', [StockOpnameController::class, 'updateDetail'])->name('stock.opname.detail.update');
        Route::post('opname/{opnameId}/detail/add', [StockOpnameController::class, 'addDetail'])->name('stock.opname.detail.add');
        Route::delete('opname/{opnameId}/detail/{detailId}', [StockOpnameController::class, 'deleteDetail'])->name('stock.opname.detail.delete');
    });

    // Suppliers - Available to regular admins
    Route::resource('suppliers', SupplierController::class);
    Route::post('suppliers/ajax', [SupplierController::class, 'store'])->name('suppliers.ajax.store');

    // Customers - Available to regular admins
    Route::resource('customers', CustomerController::class);
    Route::post('customers/ajax', [CustomerController::class, 'store'])->name('customers.ajax.store');

    // Product Categories - Available to all authenticated users
    Route::prefix('product-categories')->name('product-categories.')->group(function () {
        Route::get('/', [ProductCategoryController::class, 'index'])->name('index');
        Route::post('/', [ProductCategoryController::class, 'store'])->name('store');
        Route::put('/{category}', [ProductCategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [ProductCategoryController::class, 'destroy'])->name('destroy');
    });
});
