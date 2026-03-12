<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call seeders in order
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            CardPermissionSeeder::class,
            SectionSeeder::class,
        ]);

        // Seed employee master data first
        $this->call(EmployeeSeeder::class);

        // Create Super Admin user (not linked to employee)
        User::factory()->create([
            'employee_id' => null,
            'name' => 'Super Admin',
            'email' => 'admin@iuse-ippi.com',
            'password' => Hash::make('ippi54321'),
            // 'password' => bcrypt('password'),
            'role_id' => 1, // Super Admin
            'section_id' => null,
        ]);

        // Create user for Ahmad Susanto (GL Material Control, employee_id = 1)
        User::factory()->create([
            'employee_id' => 1,
            'name' => 'Ahmad Susanto',
            'email' => 'gl.mc@iuse-ippi.com',
            'password' => Hash::make('ippi54321'),
            'role_id' => 2, // Group Leader
            'section_id' => 1, // Material Control
        ]);

        // Create user for Eko Wahyudi (GL PPC+Toolroom, employee_id = 5)
        User::factory()->create([
            'employee_id' => 5,
            'name' => 'Eko Wahyudi',
            'email' => 'gl.ppc@iuse-ippi.com',
            'password' => Hash::make('ippi54321'),
            'role_id' => 2, // Group Leader
            'section_id' => 2, // PPC + Toolroom
        ]);
        
        // Seed consumable items
        $this->call(ConsumableSeeder::class);
        
        // Seed absences
        $this->call(AbsenceSeeder::class);
        
        // Seed overtimes
        $this->call(OvertimeSeeder::class);
        
        // Seed business trips
        $this->call(BusinessTripSeeder::class);
    }
}
