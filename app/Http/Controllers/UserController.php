<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Only users with permission can manage users
        abort_unless(auth()->user()->hasPermission('view-users'), 403);

        $users = User::with(['employee.section', 'employee.role'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Only users with permission can manage users
        abort_unless(auth()->user()->hasPermission('create-user'), 403);

        // Get active employees that don't have a user account yet
        $employees = Employee::active()
            ->withoutUser()
            ->orderBy('name')
            ->get();

        return view('users.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        // Only users with permission can manage users
        abort_unless(auth()->user()->hasPermission('create-user'), 403);

        $employee = Employee::findOrFail($request->employee_id);

        User::create([
            'employee_id' => $employee->id,
            'name' => $employee->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $employee->role_id,
            'section_id' => $employee->section_id,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dibuat untuk ' . $employee->name);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // Only users with permission can manage users
        abort_unless(auth()->user()->hasPermission('view-users'), 403);

        $user->load(['employee.section', 'employee.role']);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Only users with permission can manage users
        abort_unless(auth()->user()->hasPermission('edit-user'), 403);

        $user->load('employee');

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        // Only users with permission can manage users
        abort_unless(auth()->user()->hasPermission('edit-user'), 403);

        $data = [
            'email' => $request->email,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Only users with permission can manage users
        abort_unless(auth()->user()->hasPermission('delete-user'), 403);

        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }
}
