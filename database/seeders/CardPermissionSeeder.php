<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CardPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'view-card',
                'slug' => 'view-card',
                'description' => 'View kartu e-money',
                'group' => 'cards',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'create-card',
                'slug' => 'create-card',
                'description' => 'Create kartu e-money',
                'group' => 'cards',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'edit-card',
                'slug' => 'edit-card',
                'description' => 'Edit kartu e-money',
                'group' => 'cards',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'delete-card',
                'slug' => 'delete-card',
                'description' => 'Delete kartu e-money',
                'group' => 'cards',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($permissions as $permission) {
            // Check if permission already exists
            $exists = DB::table('permissions')
                ->where('name', $permission['name'])
                ->exists();

            if (!$exists) {
                DB::table('permissions')->insert($permission);
                $this->command->info("✓ Permission '{$permission['name']}' created successfully.");
            } else {
                $this->command->warn("⚠ Permission '{$permission['name']}' already exists.");
            }
        }

        // Auto-assign card permissions to Super Admin, Group Leader, and Foreman roles
        $roles = DB::table('roles')
            ->whereIn('name', ['Super Admin', 'Group Leader', 'Foreman'])
            ->get();
        
        if ($roles->isNotEmpty()) {
            $cardPermissions = DB::table('permissions')
                ->whereIn('name', ['view-card', 'create-card', 'edit-card', 'delete-card'])
                ->get();

            foreach ($roles as $role) {
                foreach ($cardPermissions as $permission) {
                    $exists = DB::table('permission_role')
                        ->where('permission_id', $permission->id)
                        ->where('role_id', $role->id)
                        ->exists();

                    if (!$exists) {
                        DB::table('permission_role')->insert([
                            'permission_id' => $permission->id,
                            'role_id' => $role->id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    }
                }
                $this->command->info("✓ Card permissions assigned to {$role->name} role.");
            }
        }
    }
}
