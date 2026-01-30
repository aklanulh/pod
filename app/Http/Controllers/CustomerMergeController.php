<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerMerge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerMergeController extends Controller
{
    public static function middleware(): array
    {
        return [
            'auth',
            'super_admin'
        ];
    }

    /**
     * Show merge dashboard
     */
    public function index()
    {
        // Get potential duplicates
        $potentialDuplicates = Customer::getPotentialDuplicates();

        return view('customer-merge.index', compact('potentialDuplicates'));
    }

    /**
     * Show merge form for specific customers
     */
    public function create(Request $request)
    {
        $sourceId = $request->get('source_id');
        $targetId = $request->get('target_id');

        $sourceCustomer = $sourceId ? Customer::findOrFail($sourceId) : null;
        $targetCustomer = $targetId ? Customer::findOrFail($targetId) : null;

        $customers = Customer::orderBy('name')->get();

        return view('customer-merge.create', compact('customers', 'sourceCustomer', 'targetCustomer'));
    }

    /**
     * Perform the merge
     */
    public function merge(Request $request)
    {
        $request->validate([
            'source_customer_id' => 'required|exists:customers,id|different:target_customer_id',
            'target_customer_id' => 'required|exists:customers,id',
            'reason' => 'nullable|string|max:500'
        ]);

        $sourceCustomer = Customer::findOrFail($request->source_customer_id);
        $targetCustomer = Customer::findOrFail($request->target_customer_id);

        try {
            $result = $sourceCustomer->mergeInto(
                $targetCustomer,
                $request->reason,
                auth()->user()
            );

            return redirect()->route('customer-merge.index')
                ->with('success', "Customer '{$sourceCustomer->name}' berhasil di-merge ke '{$targetCustomer->name}'");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal merge customer: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint to search for similar customers
     */
    public function searchSimilar(Request $request)
    {
        $search = $request->get('search');

        if (empty($search)) {
            return response()->json([]);
        }

        $customers = Customer::where('name', 'LIKE', "%{$search}%")
            ->orWhere('phone', 'LIKE', "%{$search}%")
            ->orWhere('email', 'LIKE', "%{$search}%")
            ->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json($customers);
    }

    /**
     * Preview merge impact
     */
    public function preview(Request $request)
    {
        $request->validate([
            'source_customer_id' => 'required|exists:customers,id|different:target_customer_id',
            'target_customer_id' => 'required|exists:customers,id'
        ]);

        $sourceCustomer = Customer::with(['ksoItems', 'stockMovements', 'customerSchedules', 'stockOutDrafts'])
            ->findOrFail($request->source_customer_id);

        $targetCustomer = Customer::with(['ksoItems', 'stockMovements', 'customerSchedules', 'stockOutDrafts'])
            ->findOrFail($request->target_customer_id);

        $impact = [
            'source_customer' => $sourceCustomer,
            'target_customer' => $targetCustomer,
            'kso_items_to_move' => $sourceCustomer->ksoItems->count(),
            'stock_movements_to_move' => $sourceCustomer->stockMovements->count(),
            'schedules_to_move' => $sourceCustomer->customerSchedules->count(),
            'drafts_to_move' => $sourceCustomer->stockOutDrafts->count(),
            'total_investment_impact' => $sourceCustomer->getTotalKsoInvestment() + $targetCustomer->getTotalKsoInvestment(),
            'total_sales_impact' => $sourceCustomer->getTotalSalesValue() + $targetCustomer->getTotalSalesValue()
        ];

        return response()->json($impact);
    }
}
