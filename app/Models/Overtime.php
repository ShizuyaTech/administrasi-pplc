<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
    ];

    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
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
     * Calculate gross working hours (without break deductions)
     */
    public function getGrossWorkingHours()
    {
        if (!$this->start_time || !$this->end_time) {
            return 0;
        }

        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);

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

        $overtimeStart = Carbon::parse($this->start_time);
        $overtimeEnd = Carbon::parse($this->end_time);

        // Handle overnight overtime
        if ($overtimeEnd->lt($overtimeStart)) {
            $overtimeEnd->addDay();
        }

        // Get all active break times
        $breakTimes = BreakTime::where('is_active', true)->get();

        // Filter break times that overlap with overtime
        return $breakTimes->filter(function ($breakTime) use ($overtimeStart, $overtimeEnd) {
            // Parse break times on the same date as overtime start
            $breakStart = Carbon::parse($this->date->format('Y-m-d') . ' ' . Carbon::parse($breakTime->start_time)->format('H:i:s'));
            $breakEnd = Carbon::parse($this->date->format('Y-m-d') . ' ' . Carbon::parse($breakTime->end_time)->format('H:i:s'));

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

        $overtimeStart = Carbon::parse($this->start_time);
        $overtimeEnd = Carbon::parse($this->end_time);

        // Handle overnight overtime
        if ($overtimeEnd->lt($overtimeStart)) {
            $overtimeEnd->addDay();
        }

        foreach ($overlappingBreaks as $breakTime) {
            // Parse break times on the same date as overtime start
            $breakStart = Carbon::parse($this->date->format('Y-m-d') . ' ' . Carbon::parse($breakTime->start_time)->format('H:i:s'));
            $breakEnd = Carbon::parse($this->date->format('Y-m-d') . ' ' . Carbon::parse($breakTime->end_time)->format('H:i:s'));

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
