<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_unless(auth()->user()->hasPermission('view-roles'), 403);

        $roles = Role::withCount(['users', 'permissions'])
            ->orderBy('name')
            ->paginate(15);

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(auth()->user()->hasPermission('create-role'), 403);

        return view('roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        abort_unless(auth()->user()->hasPermission('create-role'), 403);

        Role::create($request->validated());

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        abort_unless(auth()->user()->hasPermission('view-roles'), 403);

        $role->load(['users', 'permissions']);

        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        abort_unless(auth()->user()->hasPermission('edit-role'), 403);

        return view('roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        abort_unless(auth()->user()->hasPermission('edit-role'), 403);

        $role->update($request->validated());

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        abort_unless(auth()->user()->hasPermission('delete-role'), 403);

        // Prevent deleting if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Tidak dapat menghapus role yang masih memiliki user');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil dihapus');
    }

    /**
     * Show the form for managing role permissions.
     */
    public function permissions(Role $role)
    {
        abort_unless(auth()->user()->hasPermission('manage-role-permissions'), 403);

        $role->load('permissions');
        
        // Group permissions by their group
        $permissionGroups = Permission::all()->groupBy('group');

        return view('roles.permissions', compact('role', 'permissionGroups'));
    }

    /**
     * Update role permissions.
     */
    public function updatePermissions(Request $request, Role $role)
    {
        abort_unless(auth()->user()->hasPermission('manage-role-permissions'), 403);

        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('roles.permissions', $role)
            ->with('success', 'Permissions role berhasil diperbarui');
    }
}
