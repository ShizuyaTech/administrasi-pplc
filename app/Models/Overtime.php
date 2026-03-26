<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $batch_id
 * @property int $section_id
 * @property \Carbon\Carbon $date
 * @property string $employee_name
 * @property string $start_time
 * @property string $end_time
 * @property float $total_hours
 * @property string $work_description
 * @property string $type
 * @property string $status
 * @property int|null $approved_by
 * @property \Carbon\Carbon|null $approved_at
 * @property string|null $rejection_reason
 * @property int $created_by
 * @property int|null $supervisor_id
 * @property \Carbon\Carbon|null $supervisor_approved_at
 * @property string|null $supervisor_rejection_reason
 * @property int|null $manager_id
 * @property \Carbon\Carbon|null $manager_approved_at
 * @property string|null $manager_rejection_reason
 * @property string|null $supervisor_signature_path
 * @property string|null $manager_signature_path
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Overtime extends Model
{
    protected $fillable = [
        'batch_id',
        'section_id',
        'date',
        'employee_name',
        'start_time',
        'end_time',
        'total_hours',
        'work_description',
        'type',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'created_by',
        // Two-stage approval fields
        'supervisor_id',
        'supervisor_approved_at',
        'supervisor_rejection_reason',
        'manager_id',
        'manager_approved_at',
        'manager_rejection_reason',
    ];

    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
        'supervisor_approved_at' => 'datetime',
        'manager_approved_at' => 'datetime',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Supervisor who approved (Tahap 1)
     */
    public function supervisorApprover()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Manager who approved (Tahap 2)
     */
    public function managerApprover()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Scope: Pending supervisor approval
     */
    public function scopePendingSupervisorApproval($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Pending manager approval (already approved by supervisor)
     */
    public function scopePendingManagerApproval($query)
    {
        return $query->where('status', 'supervisor_approved');
    }

    /**
     * Scope: Fully approved (by both supervisor and manager)
     */
    public function scopeFullyApproved($query)
    {
        return $query->where('status', 'fully_approved');
    }

    /**
     * Scope: Rejected
     */
    public function scopeRejected($query)
    {
        return $query->whereIn('status', ['rejected_by_supervisor', 'rejected_by_manager', 'rejected']);
    }

    /**
     * Scope: Date range filter
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    /**
     * Check if can be approved by supervisor
     */
    public function canBeSupervisorApproved()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if can be approved by manager
     */
    public function canBeManagerApproved()
    {
        return $this->status === 'supervisor_approved';
    }

    /**
     * Check if fully approved
     */
    public function isFullyApproved()
    {
        return $this->status === 'fully_approved';
    }

    /**
     * Check if rejected
     */
    public function isRejected()
    {
        return in_array($this->status, ['rejected_by_supervisor', 'rejected_by_manager', 'rejected']);
    }

    /**
     * Get status label (translated)
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu Approval Supervisor',
            'supervisor_approved' => 'Disetujui Supervisor (Menunggu Manager)',
            'fully_approved' => 'Disetujui Penuh',
            'rejected_by_supervisor' => 'Ditolak Supervisor',
            'rejected_by_manager' => 'Ditolak Manager',
            'rejected' => 'Ditolak',
            'approved' => 'Disetujui', // Legacy status
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get status badge color (Tailwind CSS)
     */
    public function getStatusBadgeColorAttribute()
    {
        $colors = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'supervisor_approved' => 'bg-blue-100 text-blue-800',
            'fully_approved' => 'bg-green-100 text-green-800',
            'rejected_by_supervisor' => 'bg-red-100 text-red-800',
            'rejected_by_manager' => 'bg-red-100 text-red-800',
            'rejected' => 'bg-red-100 text-red-800',
            'approved' => 'bg-green-100 text-green-800', // Legacy status
        ];

        return $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Calculate gross working hours (without break deductions)
     */
    public function getGrossWorkingHours()
    {
        if (!$this->start_time || !$this->end_time) {
            return 0;
        }

        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);

        // Handle overnight overtime
        if ($end->lt($start)) {
            $end->addDay();
        }

        return $start->diffInMinutes($end) / 60;
    }

    /**
     * Get all active break times that overlap with this overtime period
     */
    public function getOverlappingBreakTimes()
    {
        if (!$this->start_time || !$this->end_time) {
            return collect();
        }

        $overtimeStart = \Carbon\Carbon::parse($this->start_time);
        $overtimeEnd = \Carbon\Carbon::parse($this->end_time);

        // Handle overnight overtime
        if ($overtimeEnd->lt($overtimeStart)) {
            $overtimeEnd->addDay();
        }

        // Get all active break times
        $breakTimes = BreakTime::where('is_active', true)->get();

        // Filter break times that overlap with overtime
        return $breakTimes->filter(function ($breakTime) use ($overtimeStart, $overtimeEnd) {
            // Parse break times on the same date as overtime start
            $breakStart = \Carbon\Carbon::parse($this->date->format('Y-m-d') . ' ' . \Carbon\Carbon::parse($breakTime->start_time)->format('H:i:s'));
            $breakEnd = \Carbon\Carbon::parse($this->date->format('Y-m-d') . ' ' . \Carbon\Carbon::parse($breakTime->end_time)->format('H:i:s'));

            // Handle overnight breaks
            if ($breakEnd->lt($breakStart)) {
                $breakEnd->addDay();
            }

            // Check if break overlaps with overtime
            // Break overlaps if: break_start < overtime_end AND break_end > overtime_start
            return $breakStart->lt($overtimeEnd) && $breakEnd->gt($overtimeStart);
        });
    }

    /**
     * Calculate total break time deductions in minutes
     */
    public function getBreakTimeDeductionsInMinutes()
    {
        $overlappingBreaks = $this->getOverlappingBreakTimes();
        $totalDeduction = 0;

        if ($overlappingBreaks->isEmpty()) {
            return 0;
        }

        $overtimeStart = \Carbon\Carbon::parse($this->start_time);
        $overtimeEnd = \Carbon\Carbon::parse($this->end_time);

        // Handle overnight overtime
        if ($overtimeEnd->lt($overtimeStart)) {
            $overtimeEnd->addDay();
        }

        foreach ($overlappingBreaks as $breakTime) {
            // Parse break times on the same date as overtime start
            $breakStart = \Carbon\Carbon::parse($this->date->format('Y-m-d') . ' ' . \Carbon\Carbon::parse($breakTime->start_time)->format('H:i:s'));
            $breakEnd = \Carbon\Carbon::parse($this->date->format('Y-m-d') . ' ' . \Carbon\Carbon::parse($breakTime->end_time)->format('H:i:s'));

            // Handle overnight breaks
            if ($breakEnd->lt($breakStart)) {
                $breakEnd->addDay();
            }

            // Calculate the actual overlap duration
            $overlapStart = $breakStart->gt($overtimeStart) ? $breakStart : $overtimeStart;
            $overlapEnd = $breakEnd->lt($overtimeEnd) ? $breakEnd : $overtimeEnd;

            $deduction = $overlapStart->diffInMinutes($overlapEnd);
            $totalDeduction += $deduction;
        }

        return $totalDeduction;
    }

    /**
     * Calculate net working hours (gross hours minus break time deductions)
     */
    public function getNetWorkingHours()
    {
        $grossHours = $this->getGrossWorkingHours();
        $breakDeductionMinutes = $this->getBreakTimeDeductionsInMinutes();
        
        return $grossHours - ($breakDeductionMinutes / 60);
    }

    /**
     * Get break time deductions in hours
     */
    public function getBreakTimeDeductionsInHours()
    {
        return $this->getBreakTimeDeductionsInMinutes() / 60;
    }

    /**
     * Format hours to readable string (e.g., "8.5 jam")
     */
    public function formatHours($hours)
    {
        return number_format($hours, 2) . ' jam';
    }
}
