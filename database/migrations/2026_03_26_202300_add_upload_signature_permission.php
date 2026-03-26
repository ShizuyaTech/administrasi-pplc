<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add upload-signature permission
        $permission = \App\Models\Permission::create([
            'name'        => 'Upload E-Signature',
            'slug'        => 'upload-signature',
            'description' => 'Can upload personal e-signature (Supervisor/Manager)',
            'group'       => 'profile',
        ]);

        // Assign to Super Admin (all permissions)
        $superAdmin = \App\Models\Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->permissions()->syncWithoutDetaching([$permission->id]);
        }

        // Assign to Supervisor role
        $supervisor = \App\Models\Role::where('name', 'Supervisor')->first();
        if ($supervisor) {
            $supervisor->permissions()->syncWithoutDetaching([$permission->id]);
        }

        // Assign to Manager role
        $manager = \App\Models\Role::where('name', 'Manager')->first();
        if ($manager) {
            $manager->permissions()->syncWithoutDetaching([$permission->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permission = \App\Models\Permission::where('slug', 'upload-signature')->first();
        if ($permission) {
            $permission->roles()->detach();
            $permission->delete();
        }
    }
};
