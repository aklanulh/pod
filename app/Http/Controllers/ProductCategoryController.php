<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = ProductCategory::withCount('products')->orderBy('name')->get();
            return response()->json($categories);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load categories: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:product_categories,name',
                'description' => 'nullable|string|max:500'
            ]);

            $category = ProductCategory::create([
                'name' => $request->name,
                'description' => $request->description
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kategori berhasil ditambahkan',
                    'category' => $category
                ]);
            }

            return redirect()->back()->with('success', 'Kategori berhasil ditambahkan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all())
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambah kategori: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Gagal menambah kategori: ' . $e->getMessage());
        }
    }

    public function update(Request $request, ProductCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name,' . $category->id,
            'description' => 'nullable|string|max:500'
        ]);

        $category->update($request->all());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diupdate',
                'category' => $category
            ]);
        }

        return redirect()->back()->with('success', 'Kategori berhasil diupdate');
    }

    public function destroy(ProductCategory $category)
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak dapat dihapus karena masih digunakan oleh produk'
                ], 422);
            }
            
            return redirect()->back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh produk');
        }

        $category->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus'
            ]);
        }

        return redirect()->back()->with('success', 'Kategori berhasil dihapus');
    }
}
