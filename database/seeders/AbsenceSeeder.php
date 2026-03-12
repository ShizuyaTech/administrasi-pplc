<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Absence;
use App\Models\Section;
use App\Models\User;

class AbsenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = Section::all();
        $users = User::whereNotNull('section_id')->get();
        
        // Create absences for the last 30 days
        for ($i = 0; $i < 30; $i++) {
            $date = now()->subDays($i);
            
            foreach ($sections as $section) {
                $totalMembers = rand(15, 30);
                $present = rand(12, $totalMembers);
                $sick = rand(0, 3);
                $permission = rand(0, 2);
                $leave = $totalMembers - $present - $sick - $permission;
                
                Absence::create([
                    'section_id' => $section->id,
                    'date' => $date,
                    'present' => $present,
                    'sick' => $sick,
                    'permission' => $permission,
                    'leave' => max(0, $leave),
                    'total_members' => $totalMembers,
                    'notes' => rand(0, 100) > 70 ? 'Normal attendance' : null,
                    'created_by' => $users->where('section_id', $section->id)->first()?->id ?? $users->first()->id,
                ]);
            }
        }
    }
}
