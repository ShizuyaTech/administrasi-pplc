<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Section;
use App\Models\Role;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = Section::all();
        $roles = Role::all();
        
        if ($sections->isEmpty() || $roles->isEmpty()) {
            $this->command->warn('Please run RoleSeeder and SectionSeeder first!');
            return;
        }
        
        // Get specific roles
        $superAdmin = $roles->where('name', 'Super Admin')->first();
        $groupLeader = $roles->where('name', 'Group Leader')->first();
        $foreman = $roles->where('name', 'Foreman')->first();
        $staff = $roles->where('name', 'Staff')->first();
        
        // Sample employees for each section
        $employees = [
            // Material Control
            [
                'nrp' => 'NRP001',
                'name' => 'Ahmad Susanto',
                'section_id' => $sections->where('name', 'Material Control')->first()->id,
                'position' => 'Group Leader',
                'shift' => 'Non Shift',
                'role_id' => $groupLeader->id,
                'is_active' => true,
            ],
            [
                'nrp' => 'NRP002',
                'name' => 'Budi Prasetyo',
                'section_id' => $sections->where('name', 'Material Control')->first()->id,
                'position' => 'Foreman',
                'shift' => 'Non Shift',
                'role_id' => $foreman->id,
                'is_active' => true,
            ],
            [
                'nrp' => 'NRP003',
                'name' => 'Citra Dewi',
                'section_id' => $sections->where('name', 'Material Control')->first()->id,
                'position' => 'Operator',
                'shift' => 'Shift A',
                'role_id' => $staff->id,
                'is_active' => true,
            ],
            [
                'nrp' => 'NRP004',
                'name' => 'Dedi Setiawan',
                'section_id' => $sections->where('name', 'Material Control')->first()->id,
                'position' => 'Operator',
                'shift' => 'Shift B',
                'role_id' => $staff->id,
                'is_active' => true,
            ],
            
            // PPC + Toolroom
            [
                'nrp' => 'NRP005',
                'name' => 'Eko Wahyudi',
                'section_id' => $sections->where('name', 'PPC + Toolroom')->first()->id,
                'position' => 'Group Leader',
                'shift' => 'Non Shift',
                'role_id' => $groupLeader->id,
                'is_active' => true,
            ],
            [
                'nrp' => 'NRP006',
                'name' => 'Fitri Handayani',
                'section_id' => $sections->where('name', 'PPC + Toolroom')->first()->id,
                'position' => 'Foreman',
                'shift' => 'Non Shift',
                'role_id' => $foreman->id,
                'is_active' => true,
            ],
            [
                'nrp' => 'NRP007',
                'name' => 'Gatot Supriyanto',
                'section_id' => $sections->where('name', 'PPC + Toolroom')->first()->id,
                'position' => 'Technician',
                'shift' => 'Shift A',
                'role_id' => $staff->id,
                'is_active' => true,
            ],
            
            // Logistik
            [
                'nrp' => 'NRP008',
                'name' => 'Hendra Saputra',
                'section_id' => $sections->where('name', 'Logistik')->first()->id,
                'position' => 'Group Leader',
                'shift' => 'Non Shift',
                'role_id' => $groupLeader->id,
                'is_active' => true,
            ],
            [
                'nrp' => 'NRP009',
                'name' => 'Indah Permatasari',
                'section_id' => $sections->where('name', 'Logistik')->first()->id,
                'position' => 'Foreman',
                'shift' => 'Non Shift',
                'role_id' => $foreman->id,
                'is_active' => true,
            ],
            [
                'nrp' => 'NRP010',
                'name' => 'Joko Widodo',
                'section_id' => $sections->where('name', 'Logistik')->first()->id,
                'position' => 'Driver',
                'shift' => 'Shift A',
                'role_id' => $staff->id,
                'is_active' => true,
            ],
            
            // Delivery
            [
                'nrp' => 'NRP011',
                'name' => 'Kurniawan',
                'section_id' => $sections->where('name', 'Delivery')->first()->id,
                'position' => 'Group Leader',
                'shift' => 'Non Shift',
                'role_id' => $groupLeader->id,
                'is_active' => true,
            ],
            [
                'nrp' => 'NRP012',
                'name' => 'Lina Marlina',
                'section_id' => $sections->where('name', 'Delivery')->first()->id,
                'position' => 'Foreman',
                'shift' => 'Non Shift',
                'role_id' => $foreman->id,
                'is_active' => true,
            ],
            [
                'nrp' => 'NRP013',
                'name' => 'Maman Sulaiman',
                'section_id' => $sections->where('name', 'Delivery')->first()->id,
                'position' => 'Driver',
                'shift' => 'Shift B',
                'role_id' => $staff->id,
                'is_active' => true,
            ],
            [
                'nrp' => 'NRP014',
                'name' => 'Nina Sari',
                'section_id' => $sections->where('name', 'Delivery')->first()->id,
                'position' => 'Admin',
                'shift' => 'Non Shift',
                'role_id' => $staff->id,
                'is_active' => true,
            ],
            
            // Inactive Employee Example
            [
                'nrp' => 'NRP999',
                'name' => 'Zulkifli (Non-Aktif)',
                'section_id' => $sections->first()->id,
                'position' => 'Former Employee',
                'shift' => 'Non Shift',
                'role_id' => $staff->id,
                'is_active' => false,
            ],
        ];
        
        foreach ($employees as $employee) {
            Employee::create($employee);
        }
        
        $this->command->info('Employee seeder completed! Created ' . count($employees) . ' employees.');
    }
}

