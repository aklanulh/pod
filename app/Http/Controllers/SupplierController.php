<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $isActive = $request->get('is_active', 1);
        $search = $request->get('search');

        $query = Supplier::orderBy('name');

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('contact_person', 'LIKE', '%' . $search . '%')
                    ->orWhere('contact_person_2', 'LIKE', '%' . $search . '%')
                    ->orWhere('contact_person_3', 'LIKE', '%' . $search . '%')
                    ->orWhere('phone', 'LIKE', '%' . $search . '%')
                    ->orWhere('phone_2', 'LIKE', '%' . $search . '%')
                    ->orWhere('phone_3', 'LIKE', '%' . $search . '%')
                    ->orWhere('email', 'LIKE', '%' . $search . '%')
                    ->orWhere('address', 'LIKE', '%' . $search . '%');
            });
        }

        $suppliers = $query->get();

        // Get counts for different statuses
        $totalSuppliers = Supplier::count();
        $activeSuppliers = Supplier::where('is_active', true)->count();
        $inactiveSuppliers = Supplier::where('is_active', false)->count();

        return view('suppliers.index', compact('suppliers', 'isActive', 'search', 'totalSuppliers', 'activeSuppliers', 'inactiveSuppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'contact_person' => 'nullable',
            'contact_person_2' => 'nullable',
            'contact_person_3' => 'nullable',
            'phone' => 'nullable',
            'phone_2' => 'nullable',
            'phone_3' => 'nullable',
            'email' => 'nullable|email|unique:suppliers',
            'address' => 'nullable'
        ]);

        $supplier = Supplier::create($request->all());

        // Handle AJAX request
        if ($request->expectsJson()) {
            return response()->json([
                'id' => $supplier->id,
                'name' => $supplier->name,
                'contact_person' => $supplier->contact_person,
                'message' => 'Supplier berhasil ditambahkan'
            ]);
        }

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('stockMovements.product');
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required',
            'contact_person' => 'nullable',
            'contact_person_2' => 'nullable',
            'contact_person_3' => 'nullable',
            'phone' => 'nullable',
            'phone_2' => 'nullable',
            'phone_3' => 'nullable',
            'email' => 'nullable|email|unique:suppliers,email,' . $supplier->id,
            'address' => 'nullable'
        ]);

        $supplier->update($request->all());

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil diupdate');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->stockMovements()->count() > 0) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Supplier tidak dapat dihapus karena memiliki riwayat transaksi');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil dihapus');
    }
}
