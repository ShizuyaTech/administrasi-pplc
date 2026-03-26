<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'description' => 'Full access to all sections and features'],
            ['name' => 'Supervisor', 'description' => 'Can approve overtimes at supervisor stage'],
            ['name' => 'Manager', 'description' => 'Can approve overtimes at manager stage'],
            ['name' => 'Group Leader', 'description' => 'Access to their assigned section only'],
            ['name' => 'Foreman', 'description' => 'Access to their assigned section only'],
            ['name' => 'Staff', 'description' => 'Access to their assigned section only'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'name' => $role['name'],
                'description' => $role['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
