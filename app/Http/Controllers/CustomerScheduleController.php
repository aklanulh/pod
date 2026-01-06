<?php

namespace App\Http\Controllers;

use App\Models\CustomerSchedule;
use App\Models\Customer;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerScheduleController extends Controller
{
    public function index()
    {
        $schedules = CustomerSchedule::with(['customer', 'product'])
            ->orderBy('scheduled_date', 'asc')
            ->paginate(15);

        $stats = [
            'total' => CustomerSchedule::count(),
            'pending' => CustomerSchedule::pending()->count(),
            'overdue' => CustomerSchedule::overdue()->count(),
            'due_today' => CustomerSchedule::dueToday()->count(),
            'due_this_week' => CustomerSchedule::dueThisWeek()->count()
        ];

        return view('customer-schedules.index', compact('schedules', 'stats'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        return view('customer-schedules.create', compact('customers'));
    }

    public function getCustomerLastPurchases($customerId)
    {
        $lastPurchases = StockMovement::with(['product.category'])
            ->where('customer_id', $customerId)
            ->where('type', 'out')
            ->select('product_id', DB::raw('MAX(transaction_date) as last_purchase_date'), 
                    DB::raw('SUM(quantity) as total_quantity'), 
                    DB::raw('AVG(unit_price) as avg_price'))
            ->groupBy('product_id')
            ->orderBy('last_purchase_date', 'desc')
            ->get();

        return response()->json($lastPurchases);
    }

    public function store(Request $request)
    {
        try {
            // Debug: Log what's being submitted
            Log::info('Customer Schedule Form Data:', $request->all());
            
            $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'product_id' => 'required|exists:products,id',
                'scheduled_date' => 'required|date|after_or_equal:today',
                'is_recurring' => 'boolean',
                'recurring_days' => 'nullable|integer|min:1|required_if:is_recurring,1',
                'notes' => 'nullable|string'
            ]);

            // Create schedule with auto-generated title
            $data = $request->all();
            $data['title'] = 'Jadwal Customer - ' . now()->format('d/m/Y H:i');
            
            CustomerSchedule::create($data);

            return redirect()->route('customer-schedules.index')
                ->with('success', 'Jadwal customer berhasil ditambahkan!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error:', $e->errors());
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Error creating customer schedule: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan jadwal: ' . $e->getMessage());
        }
    }

    public function show(CustomerSchedule $customerSchedule)
    {
        $customerSchedule->load(['customer', 'product.category']);
        
        // Get customer's purchase history for this product
        $purchaseHistory = StockMovement::with('product')
            ->where('customer_id', $customerSchedule->customer_id)
            ->where('product_id', $customerSchedule->product_id)
            ->where('type', 'out')
            ->orderBy('transaction_date', 'desc')
            ->take(10)
            ->get();

        return view('customer-schedules.show', compact('customerSchedule', 'purchaseHistory'));
    }

    public function edit(CustomerSchedule $customerSchedule)
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        
        return view('customer-schedules.edit', compact('customerSchedule', 'customers', 'products'));
    }

    public function update(Request $request, CustomerSchedule $customerSchedule)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'scheduled_date' => 'required|date',
            'status' => 'required|in:pending,notified,completed,cancelled',
            'is_recurring' => 'boolean',
            'recurring_days' => 'nullable|integer|min:1|required_if:is_recurring,1',
            'notes' => 'nullable|string'
        ]);

        // Update schedule, keep existing title if not provided
        $data = $request->all();
        if (!isset($data['title']) || empty($data['title'])) {
            $data['title'] = $customerSchedule->title ?? 'Jadwal Customer - ' . now()->format('d/m/Y H:i');
        }
        
        $customerSchedule->update($data);

        return redirect()->route('customer-schedules.index')
            ->with('success', 'Jadwal customer berhasil diupdate!');
    }

    public function destroy(CustomerSchedule $customerSchedule)
    {
        $customerSchedule->delete();

        return redirect()->route('customer-schedules.index')
            ->with('success', 'Jadwal customer berhasil dihapus!');
    }

    public function markAsNotified(CustomerSchedule $customerSchedule)
    {
        $customerSchedule->markAsNotified();

        return back()->with('success', 'Jadwal ditandai sebagai sudah dinotifikasi!');
    }

    public function markAsCompleted(CustomerSchedule $customerSchedule)
    {
        $customerSchedule->markAsCompleted();

        return back()->with('success', 'Jadwal ditandai sebagai selesai!');
    }

}
