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
        } elseif ($user->isSupervisor() || $user->isManager()) {
            // Supervisor/Manager can see data from all sections they manage
            $accessibleSectionIds = $user->getAccessibleSectionIds();
            $query->whereIn('section_id', $accessibleSectionIds);
        } elseif ($user->isLeaderOrForeman()) {
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
        // Special rule: Manager by default only sees supervisor_approved overtimes
        if ($user->isManager() && !$user->canManageAllSections()) {
            // If no status filter, default to supervisor_approved for Manager
            $statusFilter = $request->filled('status') && $request->status !== '' 
                ? $request->status 
                : 'supervisor_approved';
            $query->where('status', $statusFilter);
        } elseif ($request->filled('status') && $request->status !== '') {
            // For other users, only filter if status is explicitly selected (not "Semua")
            $query->where('status', $request->status);
        }
        // If status is empty ("Semua"), no status filter is applied - show all statuses
        
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
        
        // Fetch all overtimes matching criteria
        $allOvertimes = $query
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Group by date AND status for consolidated view
        // This ensures batches with different statuses are displayed separately
        $groupedByDateAndStatus = $allOvertimes->groupBy(function($overtime) {
            return $overtime->date->format('Y-m-d') . '|' . $overtime->status;
        })->map(function($dateOvertimes) {
            // Further group by batch_id within each date+status
            $batchGroups = $dateOvertimes->groupBy('batch_id');
            
            return [
                'date' => $dateOvertimes->first()->date,
                'batch_ids' => $batchGroups->keys()->toArray(),
                'batches' => $batchGroups->map(function($batchOvertimes) {
                    $first = $batchOvertimes->first();
                    return [
                        'batch_id' => $first->batch_id,
                        'section_id' => $first->section_id,
                        'section_name' => $first->section ? $first->section->name : '-',
                        'creator_id' => $first->created_by,
                        'creator_name' => $first->creator ? $first->creator->name : '-',
                        'creator_role' => $first->creator && $first->creator->role ? $first->creator->role->name : '-',
                        'start_time' => $first->start_time,
                        'end_time' => $first->end_time,
                        'time_range' => substr($first->start_time, 0, 5) . ' - ' . substr($first->end_time, 0, 5),
                        'employee_count' => $batchOvertimes->count(),
                        'total_hours' => $batchOvertimes->sum('total_hours'),
                    ];
                })->values(),
                'type' => $dateOvertimes->first()->type,
                'status' => $dateOvertimes->first()->status,
                'total_employees' => $dateOvertimes->count(),
                'total_hours' => $dateOvertimes->sum('total_hours'),
                'sections' => $batchGroups->map(function($batch) {
                    return $batch->first()->section ? $batch->first()->section->name : '-';
                })->unique()->values()->toArray(),
                'creators' => $batchGroups->map(function($batch) {
                    return $batch->first()->creator ? $batch->first()->creator->name : '-';
                })->unique()->values()->toArray(),
            ];
        });
        
        // Paginate the grouped data
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage('page');
        $perPage = 15;
        $currentPageItems = $groupedByDateAndStatus->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $overtimeGroups = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $groupedByDateAndStatus->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
        
        $sections = $user->canManageAllSections() ? Section::all() : collect([$user->section]);
        
        return view('overtimes.index', compact('overtimeGroups', 'sections'));
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
            // Supervisor/Manager can export from all sections they manage
            if ($user->isSupervisor() || $user->isManager()) {
                $accessibleSectionIds = $user->getAccessibleSectionIds();
                $query->whereIn('section_id', $accessibleSectionIds);
            } else {
                $query->where('section_id', $user->section_id);
            }
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
        
        // Check access: can manage all sections, OR can approve overtimes and access this section, OR created this batch
        if (!$user->canManageAllSections()) {
            $canAccess = false;
            
            // Check if user can approve overtimes for this section
            if ($user->canApproveOvertimes() && $user->canAccessSection($firstOvertime->section_id)) {
                $canAccess = true;
            }
            
            // Check if user is leader/foreman for this section
            if ($user->isLeaderOrForeman() && $user->section_id == $firstOvertime->section_id) {
                $canAccess = true;
            }
            
            // Check if user created this batch
            if ($firstOvertime->created_by === $user->id) {
                $canAccess = true;
            }
            
            if (!$canAccess) {
                abort(403, 'Anda tidak memiliki akses ke batch overtime ini.');
            }
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
        
        // Check access: can manage all sections, OR can approve overtimes for this section, OR created this batch
        if (!$user->canManageAllSections()) {
            $canAccess = false;
            
            // Check if user can approve overtimes for this section
            if ($user->canApproveOvertimes() && $user->canAccessSection($firstOvertime->section_id)) {
                $canAccess = true;
            }
            
            // Check if user is leader/foreman for this section
            if ($user->isLeaderOrForeman() && $user->section_id == $firstOvertime->section_id) {
                $canAccess = true;
            }
            
            // Check if user created this batch
            if ($firstOvertime->created_by === $user->id) {
                $canAccess = true;
            }
            
            if (!$canAccess) {
                abort(403, 'Anda tidak memiliki akses untuk menghapus batch overtime ini.');
            }
        }
        
        // Cannot delete if already approved
        if ($firstOvertime->status === 'approved') {
            return redirect()->route('overtimes.index')->with('error', 'Batch overtime yang sudah diapprove tidak dapat dihapus.');
        }
        
        $deletedCount = Overtime::where('batch_id', $batchId)->delete();
        
        return redirect()->route('overtimes.index')->with('success', "Batch overtime berhasil dihapus ({$deletedCount} karyawan).");
    }
    
    /**
     * Bulk action for multiple batches (approve or reject multiple batches at once)
     */
    public function bulkAction(Request $request)
    {
        $user = auth()->user();
        $action = $request->input('_action'); // 'approve' or 'reject'
        $batchIdsString = $request->input('batch_ids'); // comma-separated string
        
        if (!$batchIdsString) {
            return redirect()->back()->with('error', 'Tidak ada batch yang dipilih.');
        }
        
        $batchIds = explode(',', $batchIdsString);
        
        if ($action === 'approve') {
            return $this->bulkApprove($batchIds, $user);
        } elseif ($action === 'reject') {
            $rejectionReason = $request->input('rejection_reason');
            if (!$rejectionReason) {
                return redirect()->back()->with('error', 'Alasan reject harus diisi.');
            }
            return $this->bulkReject($batchIds, $user, $rejectionReason);
        }
        
        return redirect()->back()->with('error', 'Aksi tidak valid.');
    }
    
    /**
     * Approve multiple batches at once
     */
    private function bulkApprove(array $batchIds, $user)
    {
        $totalApproved = 0;
        $errors = [];
        
        foreach ($batchIds as $batchId) {
            $overtimes = Overtime::where('batch_id', $batchId)->get();
            
            if ($overtimes->isEmpty()) {
                $errors[] = "Batch {$batchId} tidak ditemukan.";
                continue;
            }
            
            $firstOvertime = $overtimes->first();
            
            // Check access
            if (!$user->canManageAllSections()) {
                if (!$user->canApproveOvertimes() || !$user->canAccessSection($firstOvertime->section_id)) {
                    $errors[] = "Tidak memiliki akses untuk approve batch {$batchId}.";
                    continue;
                }
            }
            
            // Check if pending
            if ($firstOvertime->status !== 'pending') {
                $errors[] = "Batch {$batchId} sudah tidak pending.";
                continue;
            }
            
            // Approve all in batch
            foreach ($overtimes as $overtime) {
                $overtime->update([
                    'status' => 'approved',
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                ]);
                $totalApproved++;
            }
        }
        
        if ($totalApproved > 0) {
            $message = "Berhasil approve {$totalApproved} karyawan dari " . count($batchIds) . " batch.";
            if (!empty($errors)) {
                $message .= " Catatan: " . implode(' ', $errors);
            }
            return redirect()->route('overtimes.index')->with('success', $message);
        }
        
        return redirect()->route('overtimes.index')->with('error', 'Tidak ada batch yang berhasil diapprove. ' . implode(' ', $errors));
    }
    
    /**
     * Reject multiple batches at once
     */
    private function bulkReject(array $batchIds, $user, string $rejectionReason)
    {
        $totalRejected = 0;
        $errors = [];
        
        foreach ($batchIds as $batchId) {
            $overtimes = Overtime::where('batch_id', $batchId)->get();
            
            if ($overtimes->isEmpty()) {
                $errors[] = "Batch {$batchId} tidak ditemukan.";
                continue;
            }
            
            $firstOvertime = $overtimes->first();
            
            // Check access
            if (!$user->canManageAllSections()) {
                if (!$user->canApproveOvertimes() || !$user->canAccessSection($firstOvertime->section_id)) {
                    $errors[] = "Tidak memiliki akses untuk reject batch {$batchId}.";
                    continue;
                }
            }
            
            // Check if pending
            if ($firstOvertime->status !== 'pending') {
                $errors[] = "Batch {$batchId} sudah tidak pending.";
                continue;
            }
            
            // Reject all in batch
            foreach ($overtimes as $overtime) {
                $overtime->update([
                    'status' => 'rejected',
                    'rejected_by' => $user->id,
                    'rejected_at' => now(),
                    'rejection_reason' => $rejectionReason,
                ]);
                $totalRejected++;
            }
        }
        
        if ($totalRejected > 0) {
            $message = "Berhasil reject {$totalRejected} karyawan dari " . count($batchIds) . " batch.";
            if (!empty($errors)) {
                $message .= " Catatan: " . implode(' ', $errors);
            }
            return redirect()->route('overtimes.index')->with('success', $message);
        }
        
        return redirect()->route('overtimes.index')->with('error', 'Tidak ada batch yang berhasil direject. ' . implode(' ', $errors));
    }
    
    /**
     * Bulk delete multiple batches at once
     */
    public function bulkDelete(Request $request)
    {
        $user = auth()->user();
        $batchIdsString = $request->input('batch_ids');
        
        if (!$batchIdsString) {
            return redirect()->back()->with('error', 'Tidak ada batch yang dipilih.');
        }
        
        $batchIds = explode(',', $batchIdsString);
        $totalDeleted = 0;
        $errors = [];
        
        foreach ($batchIds as $batchId) {
            $firstOvertime = Overtime::where('batch_id', $batchId)->first();
            
            if (!$firstOvertime) {
                $errors[] = "Batch {$batchId} tidak ditemukan.";
                continue;
            }
            
            // Check access
            if (!$user->canManageAllSections()) {
                $canAccess = false;
                
                if ($user->canApproveOvertimes() && $user->canAccessSection($firstOvertime->section_id)) {
                    $canAccess = true;
                }
                
                if ($user->isLeaderOrForeman() && $user->section_id == $firstOvertime->section_id) {
                    $canAccess = true;
                }
                
                if ($firstOvertime->created_by === $user->id) {
                    $canAccess = true;
                }
                
                if (!$canAccess) {
                    $errors[] = "Tidak memiliki akses untuk hapus batch {$batchId}.";
                    continue;
                }
            }
            
            // Cannot delete if already approved
            if ($firstOvertime->status === 'approved') {
                $errors[] = "Batch {$batchId} sudah approved, tidak dapat dihapus.";
                continue;
            }
            
            $deleted = Overtime::where('batch_id', $batchId)->delete();
            $totalDeleted += $deleted;
        }
        
        if ($totalDeleted > 0) {
            $message = "Berhasil menghapus {$totalDeleted} karyawan dari " . count($batchIds) . " batch.";
            if (!empty($errors)) {
                $message .= " Catatan: " . implode(' ', $errors);
            }
            return redirect()->route('overtimes.index')->with('success', $message);
        }
        
        return redirect()->route('overtimes.index')->with('error', 'Tidak ada batch yang berhasil dihapus. ' . implode(' ', $errors));
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
        
        // Check access - hanya leader/foreman/admin atau supervisor/manager yang manage section ini
        if (!$user->canManageAllSections()) {
            $canAccess = false;
            
            // Check if user can approve overtimes for this section
            if ($user->canApproveOvertimes() && $user->canAccessSection($firstOvertime->section_id)) {
                $canAccess = true;
            }
            
            // Check if user is leader/foreman for this section
            if ($user->isLeaderOrForeman() && $user->section_id == $firstOvertime->section_id) {
                $canAccess = true;
            }
            
            if (!$canAccess) {
                abort(403, 'Anda tidak memiliki akses untuk mengedit batch overtime ini.');
            }
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
        if (!$user->canManageAllSections()) {
            $canAccess = false;
            
            // Check if user can approve overtimes for this section
            if ($user->canApproveOvertimes() && $user->canAccessSection($firstOvertime->section_id)) {
                $canAccess = true;
            }
            
            // Check if user is leader/foreman for this section
            if ($user->isLeaderOrForeman() && $user->section_id == $firstOvertime->section_id) {
                $canAccess = true;
            }
            
            if (!$canAccess) {
                abort(403, 'Anda tidak memiliki akses untuk mengedit batch overtime ini.');
            }
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

    /**
     * Supervisor Approval Page - Show all pending overtimes
     */
    public function supervisorApprovalIndex(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isSupervisor() && !$user->canManageAllSections()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        // Default date range (current month if not specified)
        $date_from = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $date_to = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));
        $type = $request->input('type'); // regular, additional, or null for all
        
        // Query pending supervisor approval
        $query = Overtime::with(['section', 'creator', 'supervisorApprover', 'managerApprover'])
            ->pendingSupervisorApproval()
            ->dateRange($date_from, $date_to);
        
        if ($type) {
            $query->where('type', $type);
        }
        
        // If not super admin, filter by accessible sections
        if (!$user->canManageAllSections()) {
            $accessibleSectionIds = $user->getAccessibleSectionIds();
            $query->whereIn('section_id', $accessibleSectionIds);
        }
        
        $overtimes = $query->orderBy('date', 'asc')
            ->orderBy('batch_id')
            ->orderBy('employee_name')
            ->get();
        
        // Group by date, type, then batch_id
        $grouped = $this->groupOvertimesForApproval($overtimes);
        
        return view('overtimes.supervisor-approval', compact('grouped', 'date_from', 'date_to', 'type'));
    }

    /**
     * Manager Approval Page - Show supervisor approved overtimes
     */
    public function managerApprovalIndex(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isManager() && !$user->canManageAllSections()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        // Default date range
        $date_from = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $date_to = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));
        $type = $request->input('type');
        
        // Query supervisor approved (pending manager approval)
        $query = Overtime::with(['section', 'creator', 'supervisorApprover', 'managerApprover'])
            ->pendingManagerApproval()
            ->dateRange($date_from, $date_to);
        
        if ($type) {
            $query->where('type', $type);
        }
        
        // If not super admin, filter by accessible sections
        if (!$user->canManageAllSections()) {
            $accessibleSectionIds = $user->getAccessibleSectionIds();
            $query->whereIn('section_id', $accessibleSectionIds);
        }
        
        $overtimes = $query->orderBy('date', 'asc')
            ->orderBy('batch_id')
            ->orderBy('employee_name')
            ->get();
        
        // Group by date, type, then batch_id
        $grouped = $this->groupOvertimesForApproval($overtimes);
        
        return view('overtimes.manager-approval', compact('grouped', 'date_from', 'date_to', 'type'));
    }

    /**
     * Bulk Approve by Supervisor (Tahap 1)
     */
    public function supervisorBulkApprove(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isSupervisor() && !$user->canManageAllSections()) {
            return response()->json(['error' => 'Tidak memiliki akses'], 403);
        }
        
        $request->validate([
            'overtime_ids' => 'required|array|min:1',
            'overtime_ids.*' => 'required|exists:overtimes,id',
        ]);
        
        $approvedCount = 0;
        
        \DB::transaction(function() use ($request, $user, &$approvedCount) {
            foreach ($request->overtime_ids as $overtimeId) {
                $overtime = Overtime::findOrFail($overtimeId);
                
                // Validate can be approved
                if (!$overtime->canBeSupervisorApproved()) {
                    continue;
                }
                
                // Check section access (for supervisors with multiple sections)
                if (!$user->canAccessSection($overtime->section_id)) {
                    continue;
                }
                
                $overtime->update([
                    'status' => 'supervisor_approved',
                    'supervisor_id' => $user->id,
                    'supervisor_approved_at' => now(),
                    'supervisor_signature_path' => $user->signature_path, // Copy signature
                ]);
                
                $approvedCount++;
            }
        });
        
        return response()->json([
            'success' => true,
            'message' => "{$approvedCount} karyawan berhasil di-approve (Supervisor).",
            'approved_count' => $approvedCount
        ]);
    }

    /**
     * Bulk Reject by Supervisor (Tahap 1)
     */
    public function supervisorBulkReject(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isSupervisor() && !$user->canManageAllSections()) {
            return response()->json(['error' => 'Tidak memiliki akses'], 403);
        }
        
        $request->validate([
            'overtime_ids' => 'required|array|min:1',
            'overtime_ids.*' => 'required|exists:overtimes,id',
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        $rejectedCount = 0;
        
        \DB::transaction(function() use ($request, $user, &$rejectedCount) {
            foreach ($request->overtime_ids as $overtimeId) {
                $overtime = Overtime::findOrFail($overtimeId);
                
                // Validate can be rejected
                if (!$overtime->canBeSupervisorApproved()) {
                    continue;
                }
                
                // Check section access (for supervisors with multiple sections)
                if (!$user->canAccessSection($overtime->section_id)) {
                    continue;
                }
                
                $overtime->update([
                    'status' => 'rejected_by_supervisor',
                    'supervisor_id' => $user->id,
                    'supervisor_approved_at' => now(),
                    'supervisor_rejection_reason' => $request->rejection_reason,
                ]);
                
                $rejectedCount++;
            }
        });
        
        return response()->json([
            'success' => true,
            'message' => "{$rejectedCount} karyawan berhasil di-reject (Supervisor).",
            'rejected_count' => $rejectedCount
        ]);
    }

    /**
     * Update individual overtime record (jam dan deskripsi pekerjaan)
     * Untuk digunakan oleh Supervisor/Manager saat review approval
     */
    public function updateIndividual(Request $request, Overtime $overtime)
    {
        $user = auth()->user();
        
        // Check access - must be supervisor/manager with access to this section
        if (!$user->canManageAllSections()) {
            if (!$user->canApproveOvertimes() || !$user->canAccessSection($overtime->section_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk edit overtime ini.'
                ], 403);
            }
        }
        
        // Validate - cannot edit if already approved by manager
        if ($overtime->status === 'approved_by_manager') {
            return response()->json([
                'success' => false,
                'message' => 'Overtime yang sudah approved oleh manager tidak dapat diedit.'
            ], 400);
        }
        
        $validated = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'work_description' => 'required|string|max:500',
        ]);
        
        // Calculate new total hours
        $start = \Carbon\Carbon::parse($validated['start_time']);
        $end = \Carbon\Carbon::parse($validated['end_time']);
        $totalHours = abs($start->diffInMinutes($end, false)) / 60;
        
        $overtime->update([
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'total_hours' => $totalHours,
            'work_description' => $validated['work_description'],
        ]);
        
        return response()->json([
            'success' => true,
            'message' => "Data overtime untuk {$overtime->employee_name} berhasil diupdate.",
            'data' => [
                'total_hours' => $totalHours,
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'work_description' => $validated['work_description'],
            ]
        ]);
    }

    /**
     * Bulk Approve by Manager (Tahap 2 - Final)
     */
    public function managerBulkApprove(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isManager() && !$user->canManageAllSections()) {
            return response()->json(['error' => 'Tidak memiliki akses'], 403);
        }
        
        $request->validate([
            'overtime_ids' => 'required|array|min:1',
            'overtime_ids.*' => 'required|exists:overtimes,id',
        ]);
        
        $approvedCount = 0;
        
        \DB::transaction(function() use ($request, $user, &$approvedCount) {
            foreach ($request->overtime_ids as $overtimeId) {
                $overtime = Overtime::findOrFail($overtimeId);
                
                // Validate can be approved by manager
                if (!$overtime->canBeManagerApproved()) {
                    continue;
                }
                
                // Check section access (for managers with multiple sections)
                if (!$user->canAccessSection($overtime->section_id)) {
                    continue;
                }
                
                $overtime->update([
                    'status' => 'fully_approved',
                    'manager_id' => $user->id,
                    'manager_approved_at' => now(),
                    'manager_signature_path' => $user->signature_path, // Copy signature
                ]);
                
                $approvedCount++;
            }
        });
        
        return response()->json([
            'success' => true,
            'message' => "{$approvedCount} karyawan berhasil di-approve (Manager - Final Approval).",
            'approved_count' => $approvedCount
        ]);
    }

    /**
     * Bulk Reject by Manager (Tahap 2)
     */
    public function managerBulkReject(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isManager() && !$user->canManageAllSections()) {
            return response()->json(['error' => 'Tidak memiliki akses'], 403);
        }
        
        $request->validate([
            'overtime_ids' => 'required|array|min:1',
            'overtime_ids.*' => 'required|exists:overtimes,id',
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        $rejectedCount = 0;
        
        \DB::transaction(function() use ($request, $user, &$rejectedCount) {
            foreach ($request->overtime_ids as $overtimeId) {
                $overtime = Overtime::findOrFail($overtimeId);
                
                // Validate can be rejected by manager
                if (!$overtime->canBeManagerApproved()) {
                    continue;
                }
                
                // Check section access (for managers with multiple sections)
                if (!$user->canAccessSection($overtime->section_id)) {
                    continue;
                }
                
                $overtime->update([
                    'status' => 'rejected_by_manager',
                    'manager_id' => $user->id,
                    'manager_approved_at' => now(),
                    'manager_rejection_reason' => $request->rejection_reason,
                ]);
                
                $rejectedCount++;
            }
        });
        
        return response()->json([
            'success' => true,
            'message' => "{$rejectedCount} karyawan berhasil di-reject (Manager).",
            'rejected_count' => $rejectedCount
        ]);
    }

    /**
     * Helper: Group overtimes for approval display
     */
    private function groupOvertimesForApproval($overtimes)
    {
        $grouped = [];
        
        foreach ($overtimes as $overtime) {
            $dateKey = $overtime->date->format('Y-m-d');
            $typeKey = $overtime->type;
            $batchKey = $overtime->batch_id;
            
            if (!isset($grouped[$dateKey])) {
                $grouped[$dateKey] = [
                    'date' => $overtime->date,
                    'types' => []
                ];
            }
            
            if (!isset($grouped[$dateKey]['types'][$typeKey])) {
                $grouped[$dateKey]['types'][$typeKey] = [
                    'type' => $typeKey,
                    'batches' => []
                ];
            }
            
            if (!isset($grouped[$dateKey]['types'][$typeKey]['batches'][$batchKey])) {
                $grouped[$dateKey]['types'][$typeKey]['batches'][$batchKey] = [
                    'batch_id' => $batchKey,
                    'start_time' => $overtime->start_time,
                    'end_time' => $overtime->end_time,
                    'creator' => $overtime->creator,
                    'section' => $overtime->section,
                    'overtimes' => collect()
                ];
            }
            
            $grouped[$dateKey]['types'][$typeKey]['batches'][$batchKey]['overtimes']->push($overtime);
        }
        
        return $grouped;
    }

    /**
     * Show PDF Generation Form
     */
    public function showGeneratePDFForm()
    {
        $user = auth()->user();
        
        // Check permission
        if (!$user->hasPermission('print-overtime-report')) {
            abort(403, 'Anda tidak memiliki akses untuk print laporan overtime.');
        }
        
        return view('overtimes.generate-pdf');
    }

    /**
     * Generate PDF Report for Fully Approved Overtimes
     * Now using print-friendly HTML page (Print to PDF from browser)
     */
    public function generatePDF(Request $request)
    {
        $user = auth()->user();
        
        // Check permission
        if (!$user->hasPermission('print-overtime-report')) {
            abort(403, 'Anda tidak memiliki akses untuk print laporan overtime.');
        }
        
        // Validate inputs
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'type' => 'required|in:regular,additional',
        ]);
        
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $type = $request->input('type');
        
        // Query fully approved overtimes
        $query = Overtime::with(['section', 'creator', 'supervisorApprover', 'managerApprover'])
            ->fullyApproved()
            ->where('type', $type)
            ->dateRange($date_from, $date_to);
        
        // Filter by section if not super admin
        if (!$user->canManageAllSections()) {
            $accessibleSectionIds = $user->getAccessibleSectionIds();
            $query->whereIn('section_id', $accessibleSectionIds);
        }
        
        $overtimes = $query->orderBy('date', 'asc')
            ->orderBy('batch_id')
            ->orderBy('employee_name')
            ->get();
        
        if ($overtimes->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data overtime yang fully approved untuk periode ini.');
        }
        
        // Group overtimes for display
        $grouped = $this->groupOvertimesForApproval($overtimes);
        
        // Calculate totals
        $total_employees = $overtimes->count();
        $total_hours = $overtimes->sum('total_hours');
        
        // Get signatures from overtimes (take first record per date)
        $supervisorSignature = null;
        $managerSignature = null;
        $supervisor = null;
        $manager = null;
        
        foreach ($overtimes as $overtime) {
            if (!$supervisor && $overtime->supervisorApprover) {
                $supervisor = $overtime->supervisorApprover;
                $supervisorSignature = $overtime->supervisor_signature_path;
            }
            if (!$manager && $overtime->managerApprover) {
                $manager = $overtime->managerApprover;
                $managerSignature = $overtime->manager_signature_path;
            }
            if ($supervisor && $manager) break;
        }
        
        // Prepare data for print view
        $data = [
            'overtimes' => $overtimes,
            'grouped' => $grouped,
            'type' => $type,
            'type_label' => $type == 'regular' ? 'Overtime Harian' : 'Overtime Susulan',
            'date_from' => \Carbon\Carbon::parse($date_from)->format('d M Y'),
            'date_to' => \Carbon\Carbon::parse($date_to)->format('d M Y'),
            'total_employees' => $total_employees,
            'total_hours' => $total_hours,
            'supervisor' => $supervisor,
            'manager' => $manager,
            'supervisor_signature' => $supervisorSignature,
            'manager_signature' => $managerSignature,
            'company_name' => config('app.name', 'PT. NAMA PERUSAHAAN'),
        ];
        
        // Return print-friendly view
        return view('overtimes.print-report', $data);
    }
}
