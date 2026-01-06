<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminActivityLog;
use App\Models\User;
use Carbon\Carbon;

class AdminHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->paginate(20);

        // Get filter options
        $users = User::orderBy('name')->get();
        $modules = AdminActivityLog::distinct()->pluck('module')->sort();
        $actions = AdminActivityLog::distinct()->pluck('action')->sort();

        // Get statistics
        $stats = $this->getStatistics();

        return view('admin.history.index', compact('activities', 'users', 'modules', 'actions', 'stats'));
    }

    public function show($id)
    {
        $activity = AdminActivityLog::with('user')->findOrFail($id);
        return view('admin.history.show', compact('activity'));
    }

    public function export(Request $request)
    {
        $query = AdminActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->get();

        $filename = 'admin_history_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Tanggal',
                'Waktu',
                'User',
                'Aksi',
                'Modul',
                'Deskripsi',
                'IP Address',
                'User Agent'
            ]);

            // CSV data
            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->created_at->format('d/m/Y'),
                    $activity->created_at->format('H:i:s'),
                    $activity->user->name,
                    ucfirst($activity->action),
                    ucfirst($activity->module),
                    $activity->description,
                    $activity->ip_address,
                    $activity->user_agent
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getStatistics()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total_activities' => AdminActivityLog::count(),
            'today_activities' => AdminActivityLog::whereDate('created_at', $today)->count(),
            'week_activities' => AdminActivityLog::where('created_at', '>=', $thisWeek)->count(),
            'month_activities' => AdminActivityLog::where('created_at', '>=', $thisMonth)->count(),
            'active_users_today' => AdminActivityLog::whereDate('created_at', $today)
                ->distinct('user_id')->count(),
            'most_active_user' => AdminActivityLog::selectRaw('user_id, COUNT(*) as activity_count')
                ->with('user')
                ->groupBy('user_id')
                ->orderBy('activity_count', 'desc')
                ->first(),
            'top_modules' => AdminActivityLog::selectRaw('module, COUNT(*) as count')
                ->groupBy('module')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get()
        ];
    }
}
