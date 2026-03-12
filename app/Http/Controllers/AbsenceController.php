<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Section;
use App\Http\Requests\StoreAbsenceRequest;
use App\Http\Requests\UpdateAbsenceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $query = Absence::with(['section', 'creator']);
        
        // Admin/Leader can see all data in their section or all sections
        if ($user->canManageAllSections()) {
            // Super admin can see all
        } elseif ($user->isLeaderOrForeman()) {
            // Leader/Foreman can see all data in their section
            $query->where('section_id', $user->section_id);
        } else {
            // Regular users only see their own data
            $query->where('created_by', $user->id);
        }
        
        // Filter by date range if provided
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        
        // Filter by section for users who can manage all sections
        if ($request->filled('section_id') && $user->canManageAllSections()) {
            $query->where('section_id', $request->section_id);
        }
        
        $absences = $query->orderBy('date', 'desc')->paginate(15);
        
        $sections = $user->canManageAllSections() ? Section::all() : collect([$user->section]);
        
        return view('absences.index', compact('absences', 'sections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $sections = $user->canManageAllSections() ? Section::all() : collect([$user->section]);
        
        // Get shift from employee data (for non-admin users)
        $shift = $user->employee ? $user->employee->shift : null;
        
        // Get current user's section for auto-selection
        $currentSectionId = $user->section_id;
        
        // Get employees data grouped by section and shift for auto-fill
        $employeeData = \App\Models\Employee::active()
            ->select('section_id', 'shift', DB::raw('count(*) as count'))
            ->groupBy('section_id', 'shift')
            ->get()
            ->groupBy('section_id')
            ->map(function($group) {
                return $group->keyBy('shift')->map(function($item) {
                    return $item->count;
                });
            });
        
        // Get available shifts
        $shifts = \App\Models\Employee::select('shift')
            ->distinct()
            ->whereNotNull('shift')
            ->orderBy('shift')
            ->pluck('shift');
        
        return view('absences.create', compact('sections', 'shift', 'currentSectionId', 'employeeData', 'shifts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAbsenceRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $data = $request->validated();
        $data['created_by'] = $user->id;
        
        // Users without manage-all-sections permission can only create for their section
        if (!$user->canManageAllSections()) {
            $data['section_id'] = $user->section_id;
        }
        
        Absence::create($data);
        
        return redirect()->route('absences.index')->with('success', 'Data absensi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Absence $absence)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        // Check access: user can only see their own data unless they are leader/admin
        if (!$user->canManageAllSections() && 
            !$user->isLeaderOrForeman() &&
            $absence->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke data absensi ini.');
        }
        
        $absence->load(['section', 'creator']);
        return view('absences.show', compact('absence'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Absence $absence)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        // Check access: user can only edit their own data unless they are leader/admin
        if (!$user->canManageAllSections() && 
            !$user->isLeaderOrForeman() &&
            $absence->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data absensi ini.');
        }
        
        $sections = $user->canManageAllSections() ? Section::all() : collect([$user->section]);
        
        // Get shift from employee data
        $shift = $user->employee ? $user->employee->shift : null;
        
        return view('absences.edit', compact('absence', 'sections', 'shift'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAbsenceRequest $request, Absence $absence)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        // Check access: user can only update their own data unless they are leader/admin
        if (!$user->canManageAllSections() && 
            !$user->isLeaderOrForeman() &&
            $absence->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate data absensi ini.');
        }
        
        $data = $request->validated();
        
        // Users without manage-all-sections permission cannot change section
        /** @var \App\Models\User $authUser */
        $authUser = auth()->user();
        if (!$authUser->canManageAllSections()) {
            unset($data['section_id']);
        }
        
        $absence->update($data);
        
        return redirect()->route('absences.index')->with('success', 'Data absensi berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Absence $absence)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        // Check access: user can only delete their own data unless they are leader/admin
        if (!$user->canManageAllSections() && 
            !$user->isLeaderOrForeman() &&
            $absence->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data absensi ini.');
        }
        
        $absence->delete();
        
        return redirect()->route('absences.index')->with('success', 'Data absensi berhasil dihapus.');
    }
    
    /**
     * Export absences to CSV
     */
    public function export(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $query = Absence::with(['section', 'creator']);
        
        // Apply same filter logic as index
        if ($user->canManageAllSections()) {
            // Super admin can see all
        } elseif ($user->isLeaderOrForeman()) {
            // Leader/Foreman can see all data in their section
            $query->where('section_id', $user->section_id);
        } else {
            // Regular users only see their own data
            $query->where('created_by', $user->id);
        }
        
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        if ($request->filled('section_id') && $user->canManageAllSections()) {
            $query->where('section_id', $request->section_id);
        }
        
        $absences = $query->orderBy('date', 'desc')->get();
        
        $filename = 'absensi_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($absences) {
            $file = fopen('php://output', 'w');
            
            // BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, ['Tanggal', 'Seksi', 'Hadir', 'Sakit', 'Izin', 'Cuti', 'Total Member', 'Catatan', 'Dibuat Oleh', 'Dibuat Pada']);
            
            // Data
            foreach ($absences as $absence) {
                fputcsv($file, [
                    $absence->date->format('d/m/Y'),
                    $absence->section->name,
                    $absence->present,
                    $absence->sick,
                    $absence->permission,
                    $absence->leave,
                    $absence->total_members,
                    $absence->notes ?? '',
                    $absence->creator->name,
                    $absence->created_at->format('d/m/Y H:i'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
