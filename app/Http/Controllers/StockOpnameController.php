<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockOpnameController extends Controller
{
    public function index()
    {
        $opnames = StockOpname::with('details')
            ->orderByRaw('opname_date IS NULL, opname_date DESC')
            ->paginate(15);
            
        // Get count of draft opnames for notification
        $draftCount = StockOpname::draft()->count();
        
        return view('stock.opname.index', compact('opnames', 'draftCount'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('stock.opname.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'opname_date' => 'required|date',
            'notes' => 'nullable',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.physical_stock' => 'required|integer|min:0',
            'products.*.difference' => 'nullable|integer',
            'products.*.notes' => 'nullable'
        ]);

        DB::transaction(function () use ($request) {
            // Debug: Log request data
            Log::info('Creating Stock Opname with data:', [
                'opname_date' => $request->opname_date,
                'notes' => $request->notes,
                'products_count' => count($request->products ?? []),
                'products' => $request->products
            ]);

            $opname = StockOpname::create([
                'opname_number' => 'OP-' . date('Ymd') . '-' . str_pad(StockOpname::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT),
                'opname_date' => $request->opname_date,
                'notes' => $request->notes,
                'status' => 'draft'
            ]);

            Log::info('Created Stock Opname:', $opname->toArray());

            foreach ($request->products as $productData) {
                $product = Product::find($productData['product_id']);
                
                // Use the difference from form if provided, otherwise calculate it
                if (isset($productData['difference']) && $productData['difference'] !== null) {
                    $difference = (int) $productData['difference'];
                    $physical_stock = $product->current_stock + $difference;
                } else {
                    $physical_stock = (int) $productData['physical_stock'];
                    $difference = $physical_stock - $product->current_stock;
                }

                $detail = StockOpnameDetail::create([
                    'stock_opname_id' => $opname->id,
                    'product_id' => $productData['product_id'],
                    'system_stock' => $product->current_stock,
                    'physical_stock' => $physical_stock,
                    'difference' => $difference,
                    'notes' => $productData['notes'] ?? null
                ]);

                Log::info('Created Stock Opname Detail:', $detail->toArray());
            }
        });

        return redirect()->route('stock.opname.index')
            ->with('success', 'Stock opname berhasil dibuat');
    }

    public function show($id)
    {
        // Explicitly find the stock opname with details
        $stockOpname = StockOpname::with(['details' => function($query) {
            $query->with('product');
        }])->findOrFail($id);
        
        // Get all products for adding new details
        $products = Product::orderBy('name')->get();
        
        // Debug: Log the data to see what's actually in the database
        Log::info('Stock Opname Data:', [
            'id' => $stockOpname->id,
            'opname_number' => $stockOpname->opname_number,
            'opname_date' => $stockOpname->opname_date,
            'opname_date_raw' => $stockOpname->getRawOriginal('opname_date'),
            'notes' => $stockOpname->notes,
            'status' => $stockOpname->status,
            'details_count' => $stockOpname->details->count(),
            'details_loaded' => $stockOpname->relationLoaded('details')
        ]);
        
        // Debug: Log details data
        if ($stockOpname->details->count() > 0) {
            Log::info('Stock Opname Details:', $stockOpname->details->toArray());
        } else {
            Log::warning('No details found for Stock Opname ID: ' . $id);
        }
        
        return view('stock.opname.show', compact('stockOpname', 'products'));
    }

    public function updateDetail(Request $request, $opnameId, $detailId)
    {
        $request->validate([
            'physical_stock' => 'required|integer|min:0',
            'notes' => 'nullable|string'
        ]);

        $stockOpname = StockOpname::findOrFail($opnameId);
        
        // Only allow editing if status is draft
        if ($stockOpname->status !== 'draft') {
            return redirect()->back()->with('error', 'Tidak dapat mengedit opname yang sudah selesai');
        }

        $detail = StockOpnameDetail::where('stock_opname_id', $opnameId)
                                  ->where('id', $detailId)
                                  ->firstOrFail();

        $difference = $request->physical_stock - $detail->system_stock;

        $detail->update([
            'physical_stock' => $request->physical_stock,
            'difference' => $difference,
            'notes' => $request->notes
        ]);

        return redirect()->back()->with('success', 'Detail opname berhasil diperbarui');
    }

    public function addDetail(Request $request, $opnameId)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'physical_stock' => 'required|integer|min:0',
            'notes' => 'nullable|string'
        ]);

        $stockOpname = StockOpname::findOrFail($opnameId);
        
        // Only allow adding if status is draft
        if ($stockOpname->status !== 'draft') {
            return redirect()->back()->with('error', 'Tidak dapat menambah detail pada opname yang sudah selesai');
        }

        // Check if product already exists in this opname
        $existingDetail = StockOpnameDetail::where('stock_opname_id', $opnameId)
                                          ->where('product_id', $request->product_id)
                                          ->first();

        if ($existingDetail) {
            return redirect()->back()->with('error', 'Produk sudah ada dalam opname ini');
        }

        $product = Product::findOrFail($request->product_id);
        $difference = $request->physical_stock - $product->current_stock;

        StockOpnameDetail::create([
            'stock_opname_id' => $opnameId,
            'product_id' => $request->product_id,
            'system_stock' => $product->current_stock,
            'physical_stock' => $request->physical_stock,
            'difference' => $difference,
            'notes' => $request->notes
        ]);

        return redirect()->back()->with('success', 'Detail opname berhasil ditambahkan');
    }

    public function deleteDetail($opnameId, $detailId)
    {
        $stockOpname = StockOpname::findOrFail($opnameId);
        
        // Only allow deleting if status is draft
        if ($stockOpname->status !== 'draft') {
            return redirect()->back()->with('error', 'Tidak dapat menghapus detail pada opname yang sudah selesai');
        }

        $detail = StockOpnameDetail::where('stock_opname_id', $opnameId)
                                  ->where('id', $detailId)
                                  ->firstOrFail();

        $detail->delete();

        return redirect()->back()->with('success', 'Detail opname berhasil dihapus');
    }

    public function destroy($id)
    {
        $stockOpname = StockOpname::findOrFail($id);
        
        // Only allow deleting if status is draft
        if ($stockOpname->status !== 'draft') {
            return redirect()->back()->with('error', 'Tidak dapat menghapus opname yang sudah selesai');
        }

        DB::transaction(function () use ($stockOpname) {
            // Delete all details first
            $stockOpname->details()->delete();
            
            // Delete the opname itself
            $stockOpname->delete();
        });

        return redirect()->route('stock.opname.index')
            ->with('success', 'Stok opname berhasil dihapus');
    }

    public function complete(StockOpname $stockOpname)
    {
        if ($stockOpname->status === 'completed') {
            return redirect()->back()->with('error', 'Stock opname sudah selesai');
        }

        DB::transaction(function () use ($stockOpname) {
            foreach ($stockOpname->details as $detail) {
                if ($detail->difference != 0) {
                    // Create stock movement for adjustment
                    $type = $detail->difference > 0 ? 'in' : 'out';
                    $quantity = abs($detail->difference);
                    
                    StockMovement::create([
                        'reference_number' => 'ADJ-' . $stockOpname->opname_number . '-' . $detail->product_id,
                        'product_id' => $detail->product_id,
                        'type' => 'opname',
                        'quantity' => $quantity,
                        'stock_before' => $detail->system_stock,
                        'stock_after' => $detail->physical_stock,
                        'notes' => 'Penyesuaian dari stock opname: ' . $stockOpname->opname_number,
                        'transaction_date' => $stockOpname->opname_date
                    ]);

                    // Update product stock
                    $detail->product->update(['current_stock' => $detail->physical_stock]);
                }
            }

            $stockOpname->update(['status' => 'completed']);
        });

        return redirect()->route('stock.opname.index')
            ->with('success', 'Stock opname berhasil diselesaikan dan stok telah disesuaikan');
    }
}
