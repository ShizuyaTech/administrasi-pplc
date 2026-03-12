<?php

namespace App\Http\Controllers;

use App\Models\Overtime;
use App\Models\Section;
use App\Http\Requests\StoreOvertimeRequest;
use App\Http\Requests\UpdateOvertimeRequest;
use Illuminate\Http\Request;

class OvertimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $query = Overtime::with(['section', 'creator', 'approver']);
        
        // Admin/Leader can see all data in their section or all sections
        if ($user->canManageAllSections()) {
            // Super admin can see all
        } elseif ($user->canApproveOvertimes() || $user->isLeaderOrForeman()) {
            // Leader/Foreman can see all data in their section
            $query->where('section_id', $user->section_id);
        } else {
            // Regular users only see their own data
            $query->where('created_by', $user->id);
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by section for users who can manage all sections
        if ($request->filled('section_id') && $user->canManageAllSections()) {
            $query->where('section_id', $request->section_id);
        }
        
        // Only show batched overtimes (filter out old data without batch_id)
        $query->whereNotNull('batch_id');
        
        // Group by batch_id and get summary data
        $overtimeBatches = $query
            ->selectRaw('batch_id, section_id, date, start_time, end_time, type, status, created_by, created_at,
                         COUNT(*) as employee_count,
                         SUM(total_hours) as total_hours,
                         MIN(id) as first_id')
            ->groupBy('batch_id', 'section_id', 'date', 'start_time', 'end_time', 'type', 'status', 'created_by', 'created_at')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $sections = $user->canManageAllSections() ? Section::all() : collect([$user->section]);
        
        return view('overtimes.index', compact('overtimeBatches', 'sections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $sections = $user->canManageAllSections() ? Section::all() : collect([$user->section]);
        
        return view('overtimes.create', compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOvertimeRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $data = $request->validated();
        
        // Users without manage-all-sections permission can only create for their section
        $sectionId = $user->canManageAllSections() ? $data['section_id'] : $user->section_id;
        
        // Generate unique batch_id for grouping employees entered together
        $batchId = 'OT-' . time() . '-' . $user->id . '-' . uniqid();
        
        // Calculate total hours from start and end time
        $start = \Carbon\Carbon::parse($data['start_time']);
        $end = \Carbon\Carbon::parse($data['end_time']);
        $totalHours = abs($start->diffInMinutes($end, false)) / 60;
        
        // Create overtime record for each employee
        $createdCount = 0;
        foreach ($data['employees'] as $employee) {
            Overtime::create([
                'batch_id' => $batchId,
                'section_id' => $sectionId,
                'date' => $data['date'],
                'employee_name' => $employee['name'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'total_hours' => $totalHours,
                'work_description' => $employee['work_description'],
                'type' => $data['type'],
                'created_by' => $user->id,
                'status' => 'pending',
            ]);
            $createdCount++;
        }
        
        return redirect()->route('overtimes.index')->with('success', "Data overtime untuk {$createdCount} karyawan berhasil ditambahkan.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Overtime $overtime)
    {
        $user = auth()->user();
        
        // Check access: user can only see their own data unless they are leader/admin
        if (!$user->canManageAllSections() && 
            !$user->canApproveOvertimes() && 
            !$user->isLeaderOrForeman() &&
            $overtime->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke data overtime ini.');
        }
        
        $overtime->load(['section', 'creator', 'approver']);
        return view('overtimes.show', compact('overtime'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Overtime $overtime)
    {
        $user = auth()->user();
        
        // Check access: user can only edit their own data unless they are leader/admin
        if (!$user->canManageAllSections() && 
            !$user->canApproveOvertimes() && 
            !$user->isLeaderOrForeman() &&
            $overtime->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data overtime ini.');
        }
        
        // Cannot edit if already approved
        if ($overtime->status === 'approved') {
            return redirect()->route('overtimes.index')->with('error', 'Overtime yang sudah diapprove tidak dapat diedit.');
        }
        
        $sections = $user->canManageAllSections() ? Section::all() : collect([$user->section]);
        
        return view('overtimes.edit', compact('overtime', 'sections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOvertimeRequest $request, Overtime $overtime)
    {
        $user = auth()->user();
        
        // Check access: user can only update their own data unless they are leader/admin
        if (!$user->canManageAllSections() && 
            !$user->canApproveOvertimes() && 
            !$user->isLeaderOrForeman() &&
            $overtime->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate data overtime ini.');
        }
        
        // Cannot update if already approved
        if ($overtime->status === 'approved') {
            return redirect()->route('overtimes.index')->with('error', 'Overtime yang sudah diapprove tidak dapat diupdate.');
        }
        
        $data = $request->validated();
        
        // Users without manage-all-sections permission cannot change section
        if (!$user->canManageAllSections()) {
            unset($data['section_id']);
        }
        
        // Recalculate total hours
        $start = \Carbon\Carbon::parse($data['start_time']);
        $end = \Carbon\Carbon::parse($data['end_time']);
        $data['total_hours'] = abs($start->diffInMinutes($end, false)) / 60;
        
        $overtime->update($data);
        
        return redirect()->route('overtimes.index')->with('success', 'Data overtime berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Overtime $overtime)
    {
        $user = auth()->user();
        
        // Check access: user can only delete their own data unless they are leader/admin
        if (!$user->canManageAllSections() && 
            !$user->canApproveOvertimes() && 
            !$user->isLeaderOrForeman() &&
            $overtime->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data overtime ini.');
        }
        
        // Cannot delete if already approved
        if ($overtime->status === 'approved') {
            return redirect()->route('overtimes.index')->with('error', 'Overtime yang sudah diapprove tidak dapat dihapus.');
        }
        
        $overtime->delete();
        
        return redirect()->route('overtimes.index')->with('success', 'Data overtime berhasil dihapus.');
    }

    /**
     * Approve overtime
     */
    public function approve(Overtime $overtime)
    {
        $user = auth()->user();
        
        // Check if user has permission to approve overtimes
        if (!$user->canApproveOvertimes()) {
            return redirect()->route('overtimes.index')->with('error', 'Anda tidak memiliki akses untuk approve overtime.');
        }
        
        // Check section access
        if (!$user->canAccessSection($overtime->section_id)) {
            abort(403, 'You do not have access to this resource.');
        }
        
        $overtime->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);
        
        return redirect()->route('overtimes.index')->with('success', 'Overtime berhasil diapprove.');
    }

    /**
     * Reject overtime
     */
    public function reject(Request $request, Overtime $overtime)
    {
        $user = auth()->user();
        
        // Check if user has permission to reject overtimes
        if (!$user->canApproveOvertimes()) {
            return redirect()->route('overtimes.index')->with('error', 'Anda tidak memiliki akses untuk reject overtime.');
        }
        
        // Check section access
        if (!$user->canAccessSection($overtime->section_id)) {
            abort(403, 'You do not have access to this resource.');
        }
        
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        $overtime->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);
        
        return redirect()->route('overtimes.index')->with('success', 'Overtime berhasil direject.');
    }
    
    /**
     * Export overtimes to CSV
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $query = Overtime::with(['section', 'creator', 'approver']);
        
        if (!$user->canManageAllSections()) {
            $query->where('section_id', $user->section_id);
        }
        
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('section_id') && $user->canManageAllSections()) {
            $query->where('section_id', $request->section_id);
        }
        
        $overtimes = $query->orderBy('date', 'desc')->get();
        
        $filename = 'overtime_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($overtimes) {
            $file = fopen('php://output', 'w');
            
            // BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, ['Tanggal', 'Seksi', 'Nama Pegawai', 'Jam Mulai', 'Jam Selesai', 'Total Jam', 'Tipe', 'Deskripsi', 'Status', 'Disetujui Oleh', 'Dibuat Oleh', 'Dibuat Pada']);
            
            // Data
            foreach ($overtimes as $overtime) {
                fputcsv($file, [
                    $overtime->date->format('d/m/Y'),
                    $overtime->section->name,
                    $overtime->employee_name,
                    $overtime->start_time,
                    $overtime->end_time,
                    number_format($overtime->total_hours, 2),
                    ucfirst($overtime->type),
                    $overtime->work_description,
                    ucfirst($overtime->status),
                    $overtime->approver ? $overtime->approver->name : '',
                    $overtime->creator->name,
                    $overtime->created_at->format('d/m/Y H:i'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Show batch detail - list of employees in the batch
     */
    public function batchDetail($batchId)
    {
        $user = auth()->user();
        
        // Get first record to check access
        $firstOvertime = Overtime::where('batch_id', $batchId)->first();
        
        if (!$firstOvertime) {
            abort(404, 'Batch tidak ditemukan.');
        }
        
        // Check access
        if (!$user->canManageAllSections() && 
            !$user->canApproveOvertimes() && 
            !$user->isLeaderOrForeman() &&
            $firstOvertime->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke batch overtime ini.');
        }
        
        // Get all overtimes in this batch
        $overtimes = Overtime::with(['section', 'creator', 'approver'])
            ->where('batch_id', $batchId)
            ->orderBy('employee_name')
            ->get();
        
        return view('overtimes.batch-detail', compact('overtimes', 'batchId'));
    }
    
    /**
     * Approve all overtimes in a batch
     */
    public function batchApprove($batchId)
    {
        $user = auth()->user();
        
        // Check if user has permission to approve overtimes
        if (!$user->canApproveOvertimes()) {
            return redirect()->route('overtimes.index')->with('error', 'Anda tidak memiliki akses untuk approve overtime.');
        }
        
        // Get first record to check section access
        $firstOvertime = Overtime::where('batch_id', $batchId)->first();
        
        if (!$firstOvertime) {
            return redirect()->route('overtimes.index')->with('error', 'Batch tidak ditemukan.');
        }
        
        // Check section access
        if (!$user->canAccessSection($firstOvertime->section_id)) {
            abort(403, 'You do not have access to this resource.');
        }
        
        // Update all overtimes in the batch
        $updatedCount = Overtime::where('batch_id', $batchId)
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
        
        return redirect()->route('overtimes.index')->with('success', "Batch overtime berhasil diapprove ({$updatedCount} karyawan).");
    }
    
    /**
     * Reject all overtimes in a batch
     */
    public function batchReject(Request $request, $batchId)
    {
        $user = auth()->user();
        
        // Check if user has permission to reject overtimes
        if (!$user->canApproveOvertimes()) {
            return redirect()->route('overtimes.index')->with('error', 'Anda tidak memiliki akses untuk reject overtime.');
        }
        
        // Get first record to check section access
        $firstOvertime = Overtime::where('batch_id', $batchId)->first();
        
        if (!$firstOvertime) {
            return redirect()->route('overtimes.index')->with('error', 'Batch tidak ditemukan.');
        }
        
        // Check section access
        if (!$user->canAccessSection($firstOvertime->section_id)) {
            abort(403, 'You do not have access to this resource.');
        }
        
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        // Update all overtimes in the batch
        $updatedCount = Overtime::where('batch_id', $batchId)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
        
        return redirect()->route('overtimes.index')->with('success', "Batch overtime berhasil direject ({$updatedCount} karyawan).");
    }
    
    /**
     * Delete all overtimes in a batch
     */
    public function batchDelete($batchId)
    {
        $user = auth()->user();
        
        // Get first record to check access
        $firstOvertime = Overtime::where('batch_id', $batchId)->first();
        
        if (!$firstOvertime) {
            return redirect()->route('overtimes.index')->with('error', 'Batch tidak ditemukan.');
        }
        
        // Check access
        if (!$user->canManageAllSections() && 
            !$user->canApproveOvertimes() && 
            !$user->isLeaderOrForeman() &&
            $firstOvertime->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus batch overtime ini.');
        }
        
        // Cannot delete if already approved
        if ($firstOvertime->status === 'approved') {
            return redirect()->route('overtimes.index')->with('error', 'Batch overtime yang sudah diapprove tidak dapat dihapus.');
        }
        
        $deletedCount = Overtime::where('batch_id', $batchId)->delete();
        
        return redirect()->route('overtimes.index')->with('success', "Batch overtime berhasil dihapus ({$deletedCount} karyawan).");
    }
    
    /**
     * Edit batch overtime - untuk update jam dan pekerjaan
     */
    public function batchEdit($batchId)
    {
        $user = auth()->user();
        
        // Get first record to check access
        $firstOvertime = Overtime::where('batch_id', $batchId)->first();
        
        if (!$firstOvertime) {
            abort(404, 'Batch tidak ditemukan.');
        }
        
        // Check access - hanya leader/foreman/admin yang bisa edit
        if (!$user->canManageAllSections() && 
            !$user->canApproveOvertimes() && 
            !$user->isLeaderOrForeman()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit batch overtime ini.');
        }
        
        // Cannot edit if already approved
        if ($firstOvertime->status !== 'pending') {
            return redirect()->route('overtimes.index')->with('error', 'Batch overtime yang sudah diapprove/reject tidak dapat diedit.');
        }
        
        // Get all overtimes in this batch
        $overtimes = Overtime::with(['section', 'creator'])
            ->where('batch_id', $batchId)
            ->orderBy('employee_name')
            ->get();
        
        $sections = $user->canManageAllSections() ? Section::all() : collect([$user->section]);
        
        return view('overtimes.batch-edit', compact('overtimes', 'batchId', 'sections'));
    }
    
    /**
     * Update batch overtime
     */
    public function batchUpdate(Request $request, $batchId)
    {
        $user = auth()->user();
        
        // Get first record to check access
        $firstOvertime = Overtime::where('batch_id', $batchId)->first();
        
        if (!$firstOvertime) {
            return redirect()->route('overtimes.index')->with('error', 'Batch tidak ditemukan.');
        }
        
        // Check access
        if (!$user->canManageAllSections() && 
            !$user->canApproveOvertimes() && 
            !$user->isLeaderOrForeman()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit batch overtime ini.');
        }
        
        // Cannot edit if already approved
        if ($firstOvertime->status !== 'pending') {
            return redirect()->route('overtimes.index')->with('error', 'Batch overtime yang sudah diapprove/reject tidak dapat diedit.');
        }
        
        $validated = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'employees' => 'required|array|min:1',
            'employees.*.id' => 'required|exists:overtimes,id',
            'employees.*.work_description' => 'required|string|max:500',
        ]);
        
        // Calculate new total hours
        $start = \Carbon\Carbon::parse($validated['start_time']);
        $end = \Carbon\Carbon::parse($validated['end_time']);
        $totalHours = abs($start->diffInMinutes($end, false)) / 60;
        
        $updatedCount = 0;
        foreach ($validated['employees'] as $employeeData) {
            $overtime = Overtime::where('id', $employeeData['id'])
                ->where('batch_id', $batchId)
                ->first();
            
            if ($overtime) {
                $overtime->update([
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                    'total_hours' => $totalHours,
                    'work_description' => $employeeData['work_description'],
                ]);
                $updatedCount++;
            }
        }
        
        return redirect()->route('overtimes.index')->with('success', "Batch overtime berhasil diupdate ({$updatedCount} karyawan).");
    }
}
