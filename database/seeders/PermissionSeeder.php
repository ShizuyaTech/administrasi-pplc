<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $permissions = [
            // Absences
            [
                'name' => 'View Absences',
                'slug' => 'view-absences',
                'description' => 'Can view absence records',
                'group' => 'absences',
            ],
            [
                'name' => 'Create Absence',
                'slug' => 'create-absence',
                'description' => 'Can create new absence record',
                'group' => 'absences',
            ],
            [
                'name' => 'Edit Absence',
                'slug' => 'edit-absence',
                'description' => 'Can edit absence record',
                'group' => 'absences',
            ],
            [
                'name' => 'Delete Absence',
                'slug' => 'delete-absence',
                'description' => 'Can delete absence record',
                'group' => 'absences',
            ],
            [
                'name' => 'Export Absences',
                'slug' => 'export-absences',
                'description' => 'Can export absence data',
                'group' => 'absences',
            ],

            // Overtimes
            [
                'name' => 'View Overtimes',
                'slug' => 'view-overtimes',
                'description' => 'Can view overtime records',
                'group' => 'overtimes',
            ],
            [
                'name' => 'Create Overtime',
                'slug' => 'create-overtime',
                'description' => 'Can create new overtime record',
                'group' => 'overtimes',
            ],
            [
                'name' => 'Edit Overtime',
                'slug' => 'edit-overtime',
                'description' => 'Can edit overtime record',
                'group' => 'overtimes',
            ],
            [
                'name' => 'Delete Overtime',
                'slug' => 'delete-overtime',
                'description' => 'Can delete overtime record',
                'group' => 'overtimes',
            ],
            [
                'name' => 'Approve Overtime as Supervisor',
                'slug' => 'approve-overtime-supervisor',
                'description' => 'Can approve/reject overtime at supervisor stage',
                'group' => 'overtimes',
            ],
            [
                'name' => 'Approve Overtime as Manager',
                'slug' => 'approve-overtime-manager',
                'description' => 'Can approve/reject overtime at manager stage',
                'group' => 'overtimes',
            ],
            [
                'name' => 'Export Overtimes',
                'slug' => 'export-overtimes',
                'description' => 'Can export overtime data',
                'group' => 'overtimes',
            ],
            [
                'name' => 'Print Overtime Report',
                'slug' => 'print-overtime-report',
                'description' => 'Can print/generate overtime report with signatures',
                'group' => 'overtimes',
            ],

            // Business Trips
            [
                'name' => 'View Business Trips',
                'slug' => 'view-business-trips',
                'description' => 'Can view business trip records',
                'group' => 'business-trips',
            ],
            [
                'name' => 'Create Business Trip',
                'slug' => 'create-business-trip',
                'description' => 'Can create new business trip',
                'group' => 'business-trips',
            ],
            [
                'name' => 'Edit Business Trip',
                'slug' => 'edit-business-trip',
                'description' => 'Can edit business trip',
                'group' => 'business-trips',
            ],
            [
                'name' => 'Delete Business Trip',
                'slug' => 'delete-business-trip',
                'description' => 'Can delete business trip',
                'group' => 'business-trips',
            ],
            [
                'name' => 'Approve Business Trip',
                'slug' => 'approve-business-trip',
                'description' => 'Can approve business trip',
                'group' => 'business-trips',
            ],
            [
                'name' => 'Complete Business Trip',
                'slug' => 'complete-business-trip',
                'description' => 'Can complete business trip',
                'group' => 'business-trips',
            ],
            [
                'name' => 'Print Business Trip',
                'slug' => 'print-business-trip',
                'description' => 'Can print business trip letter',
                'group' => 'business-trips',
            ],
            [
                'name' => 'Export Business Trips',
                'slug' => 'export-business-trips',
                'description' => 'Can export business trip data',
                'group' => 'business-trips',
            ],

            // Cards (E-Money)
            [
                'name' => 'View Cards',
                'slug' => 'view-card',
                'description' => 'Can view e-money cards',
                'group' => 'cards',
            ],
            [
                'name' => 'Create Card',
                'slug' => 'create-card',
                'description' => 'Can create new e-money card',
                'group' => 'cards',
            ],
            [
                'name' => 'Edit Card',
                'slug' => 'edit-card',
                'description' => 'Can edit e-money card',
                'group' => 'cards',
            ],
            [
                'name' => 'Delete Card',
                'slug' => 'delete-card',
                'description' => 'Can delete e-money card',
                'group' => 'cards',
            ],

            // Consumables
            [
                'name' => 'View Consumables',
                'slug' => 'view-consumables',
                'description' => 'Can view consumable items',
                'group' => 'consumables',
            ],
            [
                'name' => 'Create Consumable',
                'slug' => 'create-consumable',
                'description' => 'Can create new consumable item',
                'group' => 'consumables',
            ],
            [
                'name' => 'Edit Consumable',
                'slug' => 'edit-consumable',
                'description' => 'Can edit consumable item',
                'group' => 'consumables',
            ],
            [
                'name' => 'Delete Consumable',
                'slug' => 'delete-consumable',
                'description' => 'Can delete consumable item',
                'group' => 'consumables',
            ],
            [
                'name' => 'View Stock Movements',
                'slug' => 'view-stock-movements',
                'description' => 'Can view stock movements',
                'group' => 'consumables',
            ],
            [
                'name' => 'Create Stock Movement',
                'slug' => 'create-stock-movement',
                'description' => 'Can create stock movement (in/out)',
                'group' => 'consumables',
            ],
            [
                'name' => 'Export Stock Movements',
                'slug' => 'export-stock-movements',
                'description' => 'Can export stock movement data',
                'group' => 'consumables',
            ],

            // Employees
            [
                'name' => 'View Employees',
                'slug' => 'view-employees',
                'description' => 'Can view employee records',
                'group' => 'employees',
            ],
            [
                'name' => 'Create Employee',
                'slug' => 'create-employee',
                'description' => 'Can create new employee',
                'group' => 'employees',
            ],
            [
                'name' => 'Edit Employee',
                'slug' => 'edit-employee',
                'description' => 'Can edit employee record',
                'group' => 'employees',
            ],
            [
                'name' => 'Delete Employee',
                'slug' => 'delete-employee',
                'description' => 'Can delete employee',
                'group' => 'employees',
            ],

            // Break Times (Jam Istirahat)
            [
                'name' => 'View Break Times',
                'slug' => 'view-break-times',
                'description' => 'Can view break time schedules',
                'group' => 'break-times',
            ],
            [
                'name' => 'Create Break Time',
                'slug' => 'create-break-time',
                'description' => 'Can create new break time',
                'group' => 'break-times',
            ],
            [
                'name' => 'Edit Break Time',
                'slug' => 'edit-break-time',
                'description' => 'Can edit break time schedule',
                'group' => 'break-times',
            ],
            [
                'name' => 'Delete Break Time',
                'slug' => 'delete-break-time',
                'description' => 'Can delete break time',
                'group' => 'break-times',
            ],

            // Users
            [
                'name' => 'View Users',
                'slug' => 'view-users',
                'description' => 'Can view user accounts',
                'group' => 'users',
            ],
            [
                'name' => 'Create User',
                'slug' => 'create-user',
                'description' => 'Can create new user account',
                'group' => 'users',
            ],
            [
                'name' => 'Edit User',
                'slug' => 'edit-user',
                'description' => 'Can edit user account',
                'group' => 'users',
            ],
            [
                'name' => 'Delete User',
                'slug' => 'delete-user',
                'description' => 'Can delete user account',
                'group' => 'users',
            ],

            // Roles
            [
                'name' => 'View Roles',
                'slug' => 'view-roles',
                'description' => 'Can view roles',
                'group' => 'roles',
            ],
            [
                'name' => 'Create Role',
                'slug' => 'create-role',
                'description' => 'Can create new role',
                'group' => 'roles',
            ],
            [
                'name' => 'Edit Role',
                'slug' => 'edit-role',
                'description' => 'Can edit role',
                'group' => 'roles',
            ],
            [
                'name' => 'Delete Role',
                'slug' => 'delete-role',
                'description' => 'Can delete role',
                'group' => 'roles',
            ],
            [
                'name' => 'Manage Role Permissions',
                'slug' => 'manage-role-permissions',
                'description' => 'Can assign/revoke permissions to roles',
                'group' => 'roles',
            ],

            // Permissions
            [
                'name' => 'View Permissions',
                'slug' => 'view-permissions',
                'description' => 'Can view permissions',
                'group' => 'permissions',
            ],
            [
                'name' => 'Create Permission',
                'slug' => 'create-permission',
                'description' => 'Can create new permission',
                'group' => 'permissions',
            ],
            [
                'name' => 'Edit Permission',
                'slug' => 'edit-permission',
                'description' => 'Can edit permission',
                'group' => 'permissions',
            ],
            [
                'name' => 'Delete Permission',
                'slug' => 'delete-permission',
                'description' => 'Can delete permission',
                'group' => 'permissions',
            ],

            // Dashboard
            [
                'name' => 'View Dashboard',
                'slug' => 'view-dashboard',
                'description' => 'Can view dashboard analytics',
                'group' => 'dashboard',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Auto-assign all permissions to Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $allPermissions = Permission::all()->pluck('id');
            $superAdminRole->permissions()->sync($allPermissions);
        }

        // Auto-assign supervisor approval permissions to Supervisor role
        $supervisorRole = Role::where('name', 'Supervisor')->first();
        if ($supervisorRole) {
            $supervisorPermissions = Permission::whereIn('slug', [
                'view-overtimes',
                'approve-overtime-supervisor',
            ])->pluck('id');
            $supervisorRole->permissions()->syncWithoutDetaching($supervisorPermissions);
            $this->command->info('Assigned supervisor permissions to Supervisor role');
        }

        // Auto-assign manager approval permissions to Manager role
        $managerRole = Role::where('name', 'Manager')->first();
        if ($managerRole) {
            $managerPermissions = Permission::whereIn('slug', [
                'view-overtimes',
                'approve-overtime-manager',
                'print-overtime-report',
            ])->pluck('id');
            $managerRole->permissions()->syncWithoutDetaching($managerPermissions);
            $this->command->info('Assigned manager permissions to Manager role');
        }

        $this->command->info('Created ' . count($permissions) . ' permissions');
        $this->command->info('Assigned all permissions to Super Admin role');
    }
}
