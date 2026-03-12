<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Overtime;
use App\Models\Section;
use App\Models\User;

class OvertimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = Section::all();
        $users = User::all();
        $approvers = User::whereIn('role_id', [1, 2, 3])->get(); // Super Admin, GL, Foreman
        
        $names = ['Ahmad', 'Budi', 'Citra', 'Dedi', 'Eko', 'Fajar', 'Gita', 'Hadi', 'Indra', 'Joko', 'Kurnia', 'Lina', 'Mega', 'Nina'];
        $descriptions = [
            'Quality check produk',
            'Setting mesin produksi',
            'Perbaikan tool rusak',
            'Delivery urgent ke customer',
            'Stock opname material',
            'Training operator baru',
            'Meeting koordinasi shift',
            'Maintenance preventive',
            'Packing produk urgent',
            'Inspeksi material incoming',
        ];
        
        // Create batch overtimes for the last 30 days
        // Each batch contains 1-5 employees
        for ($i = 0; $i < 20; $i++) {
            $date = now()->subDays(rand(0, 30));
            $section = $sections->random();
            $creator = $users->where('section_id', $section->id)->first() ?? $users->first();
            $approver = $approvers->where('section_id', $section->id)->first() ?? $approvers->first();
            
            // Generate batch_id
            $batchId = 'OT-' . $date->timestamp . '-' . $creator->id . '-' . uniqid();
            
            $startTime = sprintf('%02d:%02d', rand(17, 20), [0, 15, 30, 45][rand(0, 3)]);
            $endHour = rand(19, 23);
            $endTime = sprintf('%02d:%02d', $endHour, [0, 15, 30, 45][rand(0, 3)]);
            
            $start = \Carbon\Carbon::parse($startTime);
            $end = \Carbon\Carbon::parse($endTime);
            $totalHours = $end->diffInMinutes($start) / 60;
            
            $status = ['pending', 'approved', 'approved', 'approved', 'rejected'][rand(0, 4)];
            $type = ['regular', 'additional'][rand(0, 1)]; // Same type for all employees in batch
            
            // Create 1-5 employees in this batch
            $employeeCount = rand(1, 5);
            $usedNames = [];
            
            for ($j = 0; $j < $employeeCount; $j++) {
                // Get unique name for this batch
                do {
                    $employeeName = $names[rand(0, count($names) - 1)];
                } while (in_array($employeeName, $usedNames));
                $usedNames[] = $employeeName;
                
                Overtime::create([
                    'batch_id' => $batchId,
                    'section_id' => $section->id,
                    'date' => $date,
                    'employee_name' => $employeeName,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'total_hours' => $totalHours,
                    'type' => $type, // Use same type for all employees in batch
                    'work_description' => $descriptions[rand(0, count($descriptions) - 1)],
                    'status' => $status,
                    'approved_by' => $status !== 'pending' ? $approver->id : null,
                    'approved_at' => $status !== 'pending' ? now()->subDays(rand(0, 5)) : null,
                    'rejection_reason' => $status === 'rejected' ? 'Tidak ada work order untuk OT ini' : null,
                    'created_by' => $creator->id,
                ]);
            }
        }
    }
}
