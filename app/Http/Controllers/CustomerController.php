<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('name')->paginate(15);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
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
            'email' => 'nullable|email|unique:customers',
            'address' => 'nullable'
        ]);

        $customer = Customer::create($request->all());

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'message' => 'Customer berhasil ditambahkan'
            ]);
        }

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil ditambahkan');
    }

    public function show(Customer $customer)
    {
        $customer->load('stockMovements.product');
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required',
            'contact_person' => 'nullable',
            'contact_person_2' => 'nullable',
            'contact_person_3' => 'nullable',
            'phone' => 'nullable',
            'phone_2' => 'nullable',
            'phone_3' => 'nullable',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'address' => 'nullable'
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil diupdate');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->stockMovements()->count() > 0) {
            return redirect()->route('customers.index')
                ->with('error', 'Customer tidak dapat dihapus karena memiliki riwayat transaksi');
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil dihapus');
    }
}
