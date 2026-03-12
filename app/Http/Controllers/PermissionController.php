<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_unless(auth()->user()->hasPermission('view-permissions'), 403);

        $permissionGroups = Permission::with('roles')
            ->get()
            ->groupBy('group');

        return view('permissions.index', compact('permissionGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(auth()->user()->hasPermission('create-permission'), 403);

        return view('permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionRequest $request)
    {
        abort_unless(auth()->user()->hasPermission('create-permission'), 403);

        Permission::create($request->validated());

        return redirect()->route('permissions.index')
            ->with('success', 'Permission berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        abort_unless(auth()->user()->hasPermission('view-permissions'), 403);

        $permission->load('roles');

        return view('permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        abort_unless(auth()->user()->hasPermission('edit-permission'), 403);

        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        abort_unless(auth()->user()->hasPermission('edit-permission'), 403);

        $permission->update($request->validated());

        return redirect()->route('permissions.index')
            ->with('success', 'Permission berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        abort_unless(auth()->user()->hasPermission('delete-permission'), 403);

        // Detach all roles before deleting
        $permission->roles()->detach();
        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', 'Permission berhasil dihapus');
    }
}
