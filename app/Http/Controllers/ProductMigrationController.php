<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductMigrationController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'migratedToProduct', 'migratedFromProducts'])
            ->orderBy('name')
            ->paginate(20);

        return view('products.migration.index', compact('products'));
    }

    public function migrate(Request $request, $oldProductId)
    {
        $request->validate([
            'new_product_id' => 'required|exists:products,id|different:' . $oldProductId,
            'migration_notes' => 'nullable|string|max:1000'
        ]);

        $oldProduct = Product::findOrFail($oldProductId);
        $newProduct = Product::findOrFail($request->new_product_id);

        // Check if old product has stock movements
        if ($oldProduct->stockMovements()->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Produk lama tidak memiliki history stock movement. Tidak perlu migrasi.'
            ], 400);
        }

        // Check if old product is already migrated
        if ($oldProduct->migrated_to_product_id) {
            return response()->json([
                'success' => false,
                'message' => 'Produk ini sudah dimigrasikan ke produk lain.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Update old product
            $oldProduct->update([
                'is_active' => false,
                'migrated_to_product_id' => $newProduct->id,
                'migration_notes' => $request->migration_notes
            ]);

            // Update current stock (optional: combine stocks)
            $newProduct->update([
                'current_stock' => $newProduct->current_stock + $oldProduct->current_stock
            ]);

            DB::commit();

            Log::info('Product migrated', [
                'old_product_id' => $oldProduct->id,
                'old_product_name' => $oldProduct->name,
                'new_product_id' => $newProduct->id,
                'new_product_name' => $newProduct->name,
                'notes' => $request->migration_notes
            ]);

            return response()->json([
                'success' => true,
                'message' => "Produk '{$oldProduct->name}' berhasil dimigrasikan ke '{$newProduct->name}'"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product migration failed', [
                'error' => $e->getMessage(),
                'old_product_id' => $oldProductId,
                'new_product_id' => $request->new_product_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Galat saat migrasi produk: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showMigrationHistory($productId)
    {
        $product = Product::with([
            'migratedFromProducts' => function ($query) {
                $query->with('category');
            },
            'stockMovements' => function ($query) {
                $query->with(['supplier', 'customer'])
                    ->orderBy('transaction_date', 'desc')
                    ->limit(10);
            }
        ])->findOrFail($productId);

        return view('products.migration.show', compact('product'));
    }

    public function getActiveProducts()
    {
        $products = Product::active()
            ->where('id', '!=', request('exclude_id'))
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return response()->json($products);
    }
}
