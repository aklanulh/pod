<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceSchedule;
use App\Models\KsoItem;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MaintenanceScheduleController extends Controller
{
    public static function middleware(): array
    {
        return [
            'auth',
            'super_admin'
        ];
    }

    /**
     * Display a listing of maintenance schedules
     */
    public function index(Request $request)
    {
        $schedules = MaintenanceSchedule::with(['ksoItem.customer'])
            ->when($request->date, function ($query, $date) {
                return $query->whereDate('scheduled_date', $date);
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time')
            ->paginate(20);

        return view('maintenance-schedules.index', compact('schedules'));
    }

    /**
     * Show the form for creating a new maintenance schedule
     */
    public function create()
    {
        $ksoItems = KsoItem::with(['customer'])->where('status', 'active')->get();
        return view('maintenance-schedules.create', compact('ksoItems'));
    }

    /**
     * Store a newly created maintenance schedule
     */
    public function store(Request $request)
    {
        $request->validate([
            'kso_item_id' => 'required|exists:kso_items,id',
            'scheduled_date' => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'scheduled_time' => 'nullable|string|max:255',
            'maintenance_type' => 'required|in:routine,repair,calibration,qc',
            'description' => 'nullable|string',
            'estimated_duration' => 'nullable|integer|min:1',
            'priority' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string'
        ]);

        $schedule = MaintenanceSchedule::create($request->all());

        // Log activity
        AdminActivityLog::logActivity(
            'create',
            'maintenance_schedule',
            "Menambah jadwal maintenance untuk '{$schedule->ksoItem->nama_alat}'",
            [
                'schedule_id' => $schedule->id,
                'kso_item_id' => $schedule->kso_item_id,
                'scheduled_date' => $schedule->scheduled_date,
                'maintenance_type' => $schedule->maintenance_type,
                'priority' => $schedule->priority
            ]
        );

        return redirect()
            ->route('maintenance-schedules.index')
            ->with('success', 'Jadwal maintenance berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified maintenance schedule
     */
    public function edit(MaintenanceSchedule $maintenanceSchedule)
    {
        $ksoItems = KsoItem::with(['customer'])->where('status', 'active')->get();
        return view('maintenance-schedules.edit', compact('maintenanceSchedule', 'ksoItems'));
    }

    /**
     * Update the specified maintenance schedule
     */
    public function update(Request $request, MaintenanceSchedule $maintenanceSchedule)
    {
        $request->validate([
            'kso_item_id' => 'required|exists:kso_items,id',
            'scheduled_date' => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'scheduled_time' => 'nullable|string|max:255',
            'maintenance_type' => 'required|in:routine,repair,calibration,qc',
            'description' => 'nullable|string',
            'estimated_duration' => 'nullable|integer|min:1',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        $maintenanceSchedule->update($request->all());

        // Log activity
        AdminActivityLog::logActivity(
            'update',
            'maintenance_schedule',
            "Memperbarui jadwal maintenance untuk '{$maintenanceSchedule->ksoItem->nama_alat}'",
            [
                'schedule_id' => $maintenanceSchedule->id,
                'kso_item_id' => $maintenanceSchedule->kso_item_id,
                'status' => $maintenanceSchedule->status,
                'maintenance_type' => $maintenanceSchedule->maintenance_type
            ]
        );

        return redirect()
            ->route('maintenance-schedules.index')
            ->with('success', 'Jadwal maintenance berhasil diperbarui!');
    }

    /**
     * Remove the specified maintenance schedule
     */
    public function destroy(MaintenanceSchedule $maintenanceSchedule)
    {
        $maintenanceSchedule->delete();

        // Log activity
        AdminActivityLog::logActivity(
            'delete',
            'maintenance_schedule',
            "Menghapus jadwal maintenance untuk '{$maintenanceSchedule->ksoItem->nama_alat}'",
            [
                'schedule_id' => $maintenanceSchedule->id,
                'kso_item_id' => $maintenanceSchedule->kso_item_id
            ]
        );

        return redirect()
            ->route('maintenance-schedules.index')
            ->with('success', 'Jadwal maintenance berhasil dihapus!');
    }

    /**
     * Complete maintenance schedule
     */
    public function complete(MaintenanceSchedule $maintenanceSchedule, Request $request)
    {
        $request->validate([
            'completion_notes' => 'nullable|string',
            'actual_duration' => 'nullable|integer|min:1',
            'next_maintenance_date' => 'nullable|date|after:today'
        ]);

        $maintenanceSchedule->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completion_notes' => $request->completion_notes,
            'actual_duration' => $request->actual_duration,
            'next_maintenance_date' => $request->next_maintenance_date
        ]);

        // Log activity
        AdminActivityLog::logActivity(
            'complete',
            'maintenance_schedule',
            "Menyelesaikan maintenance untuk '{$maintenanceSchedule->ksoItem->nama_alat}'",
            [
                'schedule_id' => $maintenanceSchedule->id,
                'kso_item_id' => $maintenanceSchedule->kso_item_id,
                'actual_duration' => $request->actual_duration
            ]
        );

        return redirect()
            ->route('maintenance-schedules.index')
            ->with('success', 'Maintenance berhasil diselesaikan!');
    }

    /**
     * Get calendar data for technician dashboard
     */
    public function calendarData(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $schedules = MaintenanceSchedule::with(['ksoItem.customer'])
            ->whereBetween('scheduled_date', [$start, $end])
            ->get();

        $events = $schedules->map(function ($schedule) {
            $colors = [
                'routine' => '#3B82F6',
                'repair' => '#EF4444',
                'calibration' => '#8B5CF6',
                'qc' => '#10B981'
            ];

            $priorityIcons = [
                'low' => '🟢',
                'medium' => '🟡',
                'high' => '🟠',
                'urgent' => '🔴'
            ];

            return [
                'id' => $schedule->id,
                'title' => $priorityIcons[$schedule->priority] . ' ' . $schedule->ksoItem->nama_alat,
                'start' => $schedule->scheduled_date . ($schedule->scheduled_time ? 'T' . $schedule->scheduled_time . ':00' : ''),
                'backgroundColor' => $colors[$schedule->maintenance_type] ?? '#6B7280',
                'borderColor' => $this->darkenColor($colors[$schedule->maintenance_type] ?? '#6B7280'),
                'url' => route('maintenance-schedules.edit', $schedule),
                'extendedProps' => [
                    'type' => $schedule->maintenance_type,
                    'priority' => $schedule->priority,
                    'status' => $schedule->status,
                    'customer' => $schedule->ksoItem->customer->name,
                    'time' => $schedule->scheduled_time,
                    'description' => $schedule->description
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * Get today's schedule for technician
     */
    public function todaySchedule()
    {
        $schedules = MaintenanceSchedule::with(['ksoItem.customer'])
            ->whereDate('scheduled_date', today())
            ->where('status', 'scheduled')
            ->orderBy('scheduled_time')
            ->get();

        return response()->json([
            'count' => $schedules->count(),
            'schedules' => $schedules->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'equipment' => $schedule->ksoItem->nama_alat,
                    'customer' => $schedule->ksoItem->customer->name,
                    'time' => $schedule->scheduled_time,
                    'type' => $schedule->maintenance_type,
                    'priority' => $schedule->priority,
                    'location' => $schedule->ksoItem->lokasi_penempatan,
                    'customer_address' => $schedule->ksoItem->customer->address
                ];
            })
        ]);
    }

    private function darkenColor($color)
    {
        $colorMap = [
            '#3B82F6' => '#1D4ED8',
            '#EF4444' => '#991B1B',
            '#8B5CF6' => '#6D28D9',
            '#10B981' => '#047857',
            '#6B7280' => '#374151'
        ];
        return $colorMap[$color] ?? '#374151';
    }
}
