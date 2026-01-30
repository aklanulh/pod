<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierMerge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SupplierMergeController extends Controller
{
    public static function middleware(): array
    {
        return [
            'auth',
            'super_admin'
        ];
    }

    public function index()
    {
        // Get potential duplicates
        $potentialDuplicates = Supplier::getPotentialDuplicates();

        return view('supplier-merge.index', compact('potentialDuplicates'));
    }

    public function create(Request $request)
    {
        $sourceId = $request->get('source_supplier_id');
        $targetId = $request->get('target_supplier_id');

        $sourceSupplier = $sourceId ? Supplier::findOrFail($sourceId) : null;
        $targetSupplier = $targetId ? Supplier::findOrFail($targetId) : null;

        $suppliers = Supplier::orderBy('name')->get();

        return view('supplier-merge.create', compact('suppliers', 'sourceSupplier', 'targetSupplier'));
    }

    public function merge(Request $request)
    {
        $request->validate([
            'source_supplier_id' => 'required|exists:suppliers,id',
            'target_supplier_id' => 'required|exists:suppliers,id|different:source_supplier_id'
        ]);

        $sourceSupplier = Supplier::findOrFail($request->source_supplier_id);
        $targetSupplier = Supplier::findOrFail($request->target_supplier_id);

        try {
            DB::transaction(function () use ($sourceSupplier, $targetSupplier) {
                // 1. Move stock movements from source to target
                DB::table('stock_movements')
                    ->where('supplier_id', $sourceSupplier->id)
                    ->update([
                        'supplier_id' => $targetSupplier->id,
                        'supplier_name' => $targetSupplier->name
                    ]);

                // 2. Record the merge
                SupplierMerge::create([
                    'source_supplier_id' => $sourceSupplier->id,
                    'source_supplier_name' => $sourceSupplier->name,
                    'target_supplier_id' => $targetSupplier->id,
                    'target_supplier_name' => $targetSupplier->name,
                    'merged_by' => Auth::user()->id,
                    'merge_reason' => 'Automatic duplicate merge',
                    'stock_movements_count' => $sourceSupplier->stockMovements()->count()
                ]);

                // 3. Delete the source supplier
                $sourceSupplier->delete();
            });

            // Return JSON response for AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Supplier berhasil di-merge!'
                ]);
            }

            return redirect()->route('supplier-merge.index')
                ->with('success', 'Supplier berhasil di-merge!');
        } catch (\Exception $e) {
            // Return JSON response for AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function preview(Request $request)
    {
        $request->validate([
            'source_supplier_id' => 'required|exists:suppliers,id',
            'target_supplier_id' => 'required|exists:suppliers,id|different:source_supplier_id'
        ]);

        $sourceSupplier = Supplier::findOrFail($request->source_supplier_id);
        $targetSupplier = Supplier::findOrFail($request->target_supplier_id);

        $stockMovementsCount = $sourceSupplier->stockMovements()->count();

        return response()->json([
            'stock_movements_count' => $stockMovementsCount,
            'total_items' => $stockMovementsCount,
        ]);
    }

    public function searchSimilar(Request $request)
    {
        $query = $request->get('q');

        if (empty($query)) {
            return response()->json([]);
        }

        $suppliers = Supplier::where('name', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'phone', 'email', 'is_active']);

        return response()->json($suppliers);
    }
}
