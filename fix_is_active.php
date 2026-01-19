<?php

use Illuminate\Support\Facades\DB;
use App\Models\Product;

// Script to fix is_active values for existing products
// This should be run after fixing the import logic

echo "Starting to fix is_active values for existing products...\n";

try {
    // Check if we can identify which products should be inactive
    // Since the original import forced all to true, we need to determine 
    // which ones should be false based on business logic or re-import

    // For now, let's check if there are any products that should logically be inactive
    // For example, products with no stock, expired products, etc.

    $totalProducts = Product::count();
    echo "Total products in database: {$totalProducts}\n";

    // Example: Mark expired products as inactive
    $expiredProducts = Product::where('expired_date', '<', now())
        ->where('is_active', true)
        ->update(['is_active' => false]);

    echo "Marked {$expiredProducts} expired products as inactive\n";

    // Example: Mark products with zero stock and no recent activity as inactive
    $zeroStockProducts = Product::where('current_stock', 0)
        ->where('is_active', true)
        ->whereDoesntHave('stockMovements', function ($query) {
            $query->where('created_at', '>', now()->subMonths(6));
        })
        ->update(['is_active' => false]);

    echo "Marked {$zeroStockProducts} zero stock products (no recent activity) as inactive\n";

    $activeCount = Product::where('is_active', true)->count();
    $inactiveCount = Product::where('is_active', false)->count();

    echo "After fixing:\n";
    echo "Active products: {$activeCount}\n";
    echo "Inactive products: {$inactiveCount}\n";

    echo "Fix completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
