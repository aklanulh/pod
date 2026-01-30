<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public static function middleware(): array
    {
        return [
            'auth'
        ];
    }
    public function index(Request $request)
    {
        $isActive = $request->get('is_active', 1);
        $search = $request->get('search');

        $query = Customer::orderBy('name');

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

        $customers = $query->get();

        // Get counts for different statuses
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('is_active', true)->count();
        $inactiveCustomers = Customer::where('is_active', false)->count();

        return view('customers.index', compact('customers', 'isActive', 'search', 'totalCustomers', 'activeCustomers', 'inactiveCustomers'));
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
            'address' => 'nullable',
            'is_active' => 'nullable|boolean'
        ]);

        $customer->update([
            'name' => $request->name,
            'contact_person' => $request->contact_person,
            'contact_person_2' => $request->contact_person_2,
            'contact_person_3' => $request->contact_person_3,
            'phone' => $request->phone,
            'phone_2' => $request->phone_2,
            'phone_3' => $request->phone_3,
            'email' => $request->email,
            'address' => $request->address,
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

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
