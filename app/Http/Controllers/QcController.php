<?php

namespace App\Http\Controllers;

use App\Models\KsoItem;
use App\Models\QcRecord;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class QcController extends Controller
{
    public static function middleware(): array
    {
        return [
            'auth',
            'super_admin'
        ];
    }

    /**
     * Show QC form for KSO item
     */
    public function create(KsoItem $ksoItem, $type)
    {
        if (!in_array($type, ['qc', 'calibration'])) {
            abort(404);
        }

        $lastRecord = $ksoItem->qcRecords()
            ->where('type', $type)
            ->latest('date')
            ->first();

        return view('qc.create', compact('ksoItem', 'type', 'lastRecord'));
    }

    /**
     * Store QC/Calibration record
     */
    public function store(Request $request, KsoItem $ksoItem, $type)
    {
        $request->validate([
            'date' => 'required|date|date_format:Y-m-d',
            'status' => 'required|in:pass,fail',
            'technician_name' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'parameters' => 'nullable|array',
            'next_due_days' => 'nullable|integer|min:1|max:365|required_without:next_due_date',
            'next_due_date' => 'nullable|date|after:date|required_without:next_due_days'
        ]);

        $data = $request->except('certificate_file', 'next_due_days', 'next_due_date');
        $data['type'] = $type;
        $data['created_by'] = auth()->id();
        $data['kso_item_id'] = $ksoItem->id;

        // Handle next due date calculation
        if ($request->has('next_due_days')) {
            // Calculate from days input
            $days = (int) $request->next_due_days;
            $nextDueDate = Carbon::parse($request->date)->addDays($days);
            $data['next_due_date'] = $nextDueDate->toDateString();
        } elseif ($request->has('next_due_date')) {
            // Use specific date
            $data['next_due_date'] = $request->next_due_date;
        } else {
            // Default calculation - 14 days for QC, 30 days for calibration
            $nextDueDate = Carbon::parse($request->date);
            $nextDueDate = $type === 'qc' ? $nextDueDate->addDays(14) : $nextDueDate->addDays(30);
            $data['next_due_date'] = $nextDueDate->toDateString();
        }

        // Handle certificate file upload
        if ($request->hasFile('certificate_file')) {
            $file = $request->file('certificate_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('certificates', $filename, 'public');
            $data['certificate_file'] = $path;
        }

        $qcRecord = QcRecord::create($data);

        // Log activity
        $typeText = $type === 'qc' ? 'QC' : 'Kalibrasi';
        AdminActivityLog::logActivity(
            'create',
            'qc_record',
            "Melakukan {$typeText} untuk '{$ksoItem->nama_alat}' - Status: {$qcRecord->status}",
            [
                'qc_record_id' => $qcRecord->id,
                'kso_item_id' => $ksoItem->id,
                'type' => $type,
                'status' => $qcRecord->status,
                'technician_name' => $qcRecord->technician_name,
                'next_due_days' => $request->next_due_days ?? null,
                'next_due_date' => $qcRecord->next_due_date,
                'input_method' => $request->has('next_due_days') ? 'days' : ($request->has('next_due_date') ? 'date' : 'default')
            ]
        );

        return redirect()
            ->route('kso-roi.technician-dashboard')
            ->with('success', "{$typeText} berhasil disimpan!");
    }

    /**
     * Show QC record details
     */
    public function show(QcRecord $qcRecord)
    {
        $qcRecord->load(['ksoItem.customer', 'creator']);
        return view('qc.show', compact('qcRecord'));
    }

    /**
     * Edit QC record
     */
    public function edit(QcRecord $qcRecord)
    {
        $qcRecord->load(['ksoItem']);
        return view('qc.edit', compact('qcRecord'));
    }

    /**
     * Update QC record
     */
    public function update(Request $request, QcRecord $qcRecord)
    {
        $request->validate([
            'date' => 'required|date|date_format:Y-m-d',
            'status' => 'required|in:pass,fail',
            'technician_name' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'parameters' => 'nullable|array',
            'next_due_days' => 'nullable|integer|min:1|max:365|required_without:next_due_date',
            'next_due_date' => 'nullable|date|after:date|required_without:next_due_days'
        ]);

        $data = $request->except('certificate_file', 'next_due_days', 'next_due_date');

        // Handle next due date calculation
        if ($request->has('next_due_days')) {
            // Calculate from days input
            $days = (int) $request->next_due_days;
            $nextDueDate = Carbon::parse($request->date)->addDays($days);
            $data['next_due_date'] = $nextDueDate->toDateString();
        } elseif ($request->has('next_due_date')) {
            // Use specific date
            $data['next_due_date'] = $request->next_due_date;
        }

        // Handle certificate file upload
        if ($request->hasFile('certificate_file')) {
            // Delete old file if exists
            if ($qcRecord->certificate_file) {
                Storage::disk('public')->delete($qcRecord->certificate_file);
            }

            $file = $request->file('certificate_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('certificates', $filename, 'public');
            $data['certificate_file'] = $path;
        }

        $qcRecord->update($data);

        // Log activity
        $typeText = $qcRecord->type === 'qc' ? 'QC' : 'Kalibrasi';
        AdminActivityLog::logActivity(
            'update',
            'qc_record',
            "Memperbarui {$typeText} untuk '{$qcRecord->ksoItem->nama_alat}'",
            [
                'qc_record_id' => $qcRecord->id,
                'kso_item_id' => $qcRecord->ksoItem->id,
                'type' => $qcRecord->type,
                'status' => $qcRecord->status,
                'technician_name' => $qcRecord->technician_name,
                'next_due_days' => $request->next_due_days ?? ($qcRecord->type === 'qc' ? 14 : 30),
                'next_due_date' => $qcRecord->next_due_date,
                'input_method' => $request->has('next_due_days') ? 'days' : ($request->has('next_due_date') ? 'date' : 'default')
            ]
        );

        return redirect()
            ->route('kso-roi.qc.show', $qcRecord)
            ->with('success', "{$typeText} berhasil diperbarui!");
    }

    /**
     * Get QC calendar data for dashboard
     */
    public function calendarData(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $qcRecords = QcRecord::with(['ksoItem.customer'])
            ->whereBetween('date', [$start, $end])
            ->get();

        $events = $qcRecords->map(function ($record) {
            return [
                'id' => $record->id,
                'title' => $record->type_text . ' - ' . $record->ksoItem->nama_alat,
                'start' => $record->date,
                'backgroundColor' => $record->type === 'qc' ? '#3B82F6' : '#8B5CF6',
                'borderColor' => $record->type === 'qc' ? '#1D4ED8' : '#6D28D9',
                'url' => route('qc.show', $record->id),
                'extendedProps' => [
                    'type' => $record->type,
                    'status' => $record->status,
                    'technician' => $record->technician_name,
                    'customer' => $record->ksoItem->customer->name,
                    'icon' => $record->status === 'pass' ? '✅' : ($record->status === 'fail' ? '❌' : '⏳')
                ]
            ];
        });

        return response()->json($events);
    }
}
