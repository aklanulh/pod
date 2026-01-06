<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductCategory;

class AdminProductController extends Controller
{
    /**
     * Display a listing of products for admin (without financial data)
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->has('category') && $request->category) {
            $query->where('product_category_id', $request->category);
        }

        // Stock status filter
        if ($request->has('stock_status') && $request->stock_status) {
            if ($request->stock_status === 'low') {
                $query->whereColumn('current_stock', '<=', 'minimum_stock');
            } elseif ($request->stock_status === 'out') {
                $query->where('current_stock', 0);
            } elseif ($request->stock_status === 'available') {
                $query->whereColumn('current_stock', '>', 'minimum_stock');
            }
        }

        $products = $query->orderBy('name')->paginate(15);
        $categories = ProductCategory::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Display the specified product (without financial data)
     */
    public function show(Product $product)
    {
        $product->load('category');
        return view('admin.products.show', compact('product'));
    }
}
