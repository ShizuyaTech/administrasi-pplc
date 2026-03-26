<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\BusinessTripController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ConsumableController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BreakTimeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SectionController;

// Redirect root to dashboard or login
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Absences
    Route::get('absences', [AbsenceController::class, 'index'])->name('absences.index');
    Route::get('absences/create', [AbsenceController::class, 'create'])->name('absences.create');
    Route::post('absences', [AbsenceController::class, 'store'])->name('absences.store');
    Route::get('absences/{absence}', [AbsenceController::class, 'show'])->name('absences.show')->middleware('section.access:absence');
    Route::get('absences/{absence}/edit', [AbsenceController::class, 'edit'])->name('absences.edit')->middleware('section.access:absence');
    Route::put('absences/{absence}', [AbsenceController::class, 'update'])->name('absences.update')->middleware('section.access:absence');
    Route::delete('absences/{absence}', [AbsenceController::class, 'destroy'])->name('absences.destroy')->middleware('section.access:absence');
    Route::get('absences-export', [AbsenceController::class, 'export'])->name('absences.export');
    
    // Overtimes
    Route::get('overtimes', [OvertimeController::class, 'index'])->name('overtimes.index');
    Route::get('overtimes/create', [OvertimeController::class, 'create'])->name('overtimes.create');
    Route::post('overtimes', [OvertimeController::class, 'store'])->name('overtimes.store');
    
    // Bulk Actions for consolidated date view
    Route::post('overtimes/bulk-action', [OvertimeController::class, 'bulkAction'])->name('overtimes.bulk-action');
    Route::delete('overtimes/bulk-delete', [OvertimeController::class, 'bulkDelete'])->name('overtimes.bulk-delete');
    
    // Overtime 2-Stage Approval System — Supervisor
    Route::middleware('permission:approve-overtime-supervisor')->group(function () {
        Route::get('overtimes/approval/supervisor', [OvertimeController::class, 'supervisorApprovalIndex'])->name('overtimes.approval.supervisor');
        Route::post('overtimes/supervisor-bulk-approve', [OvertimeController::class, 'supervisorBulkApprove'])->name('overtimes.supervisor.bulk-approve');
        Route::post('overtimes/supervisor-bulk-reject', [OvertimeController::class, 'supervisorBulkReject'])->name('overtimes.supervisor.bulk-reject');
        Route::put('overtimes/{overtime}/update-individual', [OvertimeController::class, 'updateIndividual'])->name('overtimes.update-individual');
    });
    
    // Overtime 2-Stage Approval System — Manager
    Route::middleware('permission:approve-overtime-manager')->group(function () {
        Route::get('overtimes/approval/manager', [OvertimeController::class, 'managerApprovalIndex'])->name('overtimes.approval.manager');
        Route::post('overtimes/manager-bulk-approve', [OvertimeController::class, 'managerBulkApprove'])->name('overtimes.manager.bulk-approve');
        Route::post('overtimes/manager-bulk-reject', [OvertimeController::class, 'managerBulkReject'])->name('overtimes.manager.bulk-reject');
    });
    
    // Overtime PDF Report
    Route::middleware('permission:print-overtime-report')->group(function () {
        Route::get('overtimes/pdf', [OvertimeController::class, 'showGeneratePDFForm'])->name('overtimes.pdf');
        Route::get('overtimes/pdf/generate', [OvertimeController::class, 'generatePDF'])->name('overtimes.pdf.generate');
    });
    
    // Profile / Signature Management (only users with upload-signature permission)
    Route::get('profile/signature', [ProfileController::class, 'signature'])->name('profile.signature')->middleware('permission:upload-signature');
    Route::post('profile/signature', [ProfileController::class, 'uploadSignature'])->name('profile.signature.upload')->middleware('permission:upload-signature');
    Route::delete('profile/signature', [ProfileController::class, 'deleteSignature'])->name('profile.signature.delete')->middleware('permission:upload-signature');
    Route::get('profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    
    Route::get('overtimes/batch/{batchId}', [OvertimeController::class, 'batchDetail'])->name('overtimes.batch.detail');
    Route::get('overtimes/batch/{batchId}/edit', [OvertimeController::class, 'batchEdit'])->name('overtimes.batch.edit');
    Route::put('overtimes/batch/{batchId}', [OvertimeController::class, 'batchUpdate'])->name('overtimes.batch.update');
    Route::post('overtimes/batch/{batchId}/approve', [OvertimeController::class, 'batchApprove'])->name('overtimes.batch.approve')->middleware('permission:approve-overtime-supervisor,approve-overtime-manager');
    Route::post('overtimes/batch/{batchId}/reject', [OvertimeController::class, 'batchReject'])->name('overtimes.batch.reject')->middleware('permission:approve-overtime-supervisor,approve-overtime-manager');
    Route::delete('overtimes/batch/{batchId}', [OvertimeController::class, 'batchDelete'])->name('overtimes.batch.delete');
    Route::get('overtimes/{overtime}', [OvertimeController::class, 'show'])->name('overtimes.show')->middleware('section.access:overtime');
    Route::get('overtimes/{overtime}/edit', [OvertimeController::class, 'edit'])->name('overtimes.edit')->middleware('section.access:overtime');
    Route::put('overtimes/{overtime}', [OvertimeController::class, 'update'])->name('overtimes.update')->middleware('section.access:overtime');
    Route::delete('overtimes/{overtime}', [OvertimeController::class, 'destroy'])->name('overtimes.destroy')->middleware('section.access:overtime');
    Route::post('overtimes/{overtime}/approve', [OvertimeController::class, 'approve'])->name('overtimes.approve')->middleware('permission:approve-overtime-supervisor,approve-overtime-manager');
    Route::post('overtimes/{overtime}/reject', [OvertimeController::class, 'reject'])->name('overtimes.reject')->middleware('permission:approve-overtime-supervisor,approve-overtime-manager');
    Route::get('overtimes-export', [OvertimeController::class, 'export'])->name('overtimes.export');
    
    // Business Trips
    Route::get('business-trips', [BusinessTripController::class, 'index'])->name('business-trips.index');
    Route::get('business-trips/create', [BusinessTripController::class, 'create'])->name('business-trips.create');
    Route::post('business-trips', [BusinessTripController::class, 'store'])->name('business-trips.store');
    Route::get('business-trips/{businessTrip}', [BusinessTripController::class, 'show'])->name('business-trips.show')->middleware('section.access:business_trip');
    Route::get('business-trips/{businessTrip}/edit', [BusinessTripController::class, 'edit'])->name('business-trips.edit')->middleware('section.access:business_trip');
    Route::put('business-trips/{businessTrip}', [BusinessTripController::class, 'update'])->name('business-trips.update')->middleware('section.access:business_trip');
    Route::delete('business-trips/{businessTrip}', [BusinessTripController::class, 'destroy'])->name('business-trips.destroy')->middleware('section.access:business_trip');
    Route::post('business-trips/{businessTrip}/approve', [BusinessTripController::class, 'approve'])->name('business-trips.approve');
    Route::post('business-trips/{businessTrip}/complete', [BusinessTripController::class, 'complete'])->name('business-trips.complete');
    Route::get('business-trips/{businessTrip}/print', [BusinessTripController::class, 'print'])->name('business-trips.print');
    Route::get('business-trips-export', [BusinessTripController::class, 'export'])->name('business-trips.export');
    
    // Cards (E-Money Management)
    Route::get('cards', [CardController::class, 'index'])->name('cards.index');
    Route::get('cards/create', [CardController::class, 'create'])->name('cards.create');
    Route::post('cards', [CardController::class, 'store'])->name('cards.store');
    Route::get('cards/active', [CardController::class, 'getActiveCards'])->name('cards.active');
    Route::get('cards/{card}', [CardController::class, 'show'])->name('cards.show');
    Route::get('cards/{card}/edit', [CardController::class, 'edit'])->name('cards.edit');
    Route::put('cards/{card}', [CardController::class, 'update'])->name('cards.update');
    Route::delete('cards/{card}', [CardController::class, 'destroy'])->name('cards.destroy');
    Route::post('cards/{card}/topup', [CardController::class, 'topup'])->name('cards.topup');
    
    // Consumables
    Route::get('consumables', [ConsumableController::class, 'index'])->name('consumables.index');
    Route::get('consumables/master-items', [ConsumableController::class, 'masterItems'])->name('consumables.master-items');
    Route::get('consumables/create', [ConsumableController::class, 'create'])->name('consumables.create');
    Route::post('consumables', [ConsumableController::class, 'store'])->name('consumables.store');
    Route::get('consumables/{consumable}', [ConsumableController::class, 'show'])->name('consumables.show')->middleware('section.access:consumable');
    Route::get('consumables/{consumable}/edit', [ConsumableController::class, 'edit'])->name('consumables.edit')->middleware('section.access:consumable');
    Route::put('consumables/{consumable}', [ConsumableController::class, 'update'])->name('consumables.update')->middleware('section.access:consumable');
    Route::delete('consumables/{consumable}', [ConsumableController::class, 'destroy'])->name('consumables.destroy')->middleware('section.access:consumable');
    
    // Stock Movements
    Route::get('stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
    Route::get('stock-movements/create', [StockMovementController::class, 'create'])->name('stock-movements.create');
    Route::post('stock-movements', [StockMovementController::class, 'store'])->name('stock-movements.store');
    Route::get('stock-movements/{stockMovement}', [StockMovementController::class, 'show'])->name('stock-movements.show');
    Route::get('stock-movements-export', [StockMovementController::class, 'export'])->name('stock-movements.export');
    
    // Employees (Master Data) — static routes MUST come before wildcard routes
    Route::get('employees/search', [EmployeeController::class, 'search'])->name('employees.search')->middleware('permission:view-employees,create-overtime,create-absence,create-business-trip');
    Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index')->middleware('permission:view-employees');
    Route::get('employees/create', [EmployeeController::class, 'create'])->name('employees.create')->middleware('permission:create-employee');
    Route::post('employees', [EmployeeController::class, 'store'])->name('employees.store')->middleware('permission:create-employee');
    Route::get('employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show')->middleware('permission:view-employees');
    Route::get('employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit')->middleware('permission:edit-employee');
    Route::put('employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update')->middleware('permission:edit-employee');
    Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy')->middleware('permission:delete-employee');
    
    // Break Times (Jam Istirahat - Master Data)
    Route::middleware('permission:view-break-times')->group(function () {
        Route::get('break-times', [BreakTimeController::class, 'index'])->name('break-times.index');
    });
    Route::middleware('permission:create-break-time')->group(function () {
        Route::get('break-times/create', [BreakTimeController::class, 'create'])->name('break-times.create');
        Route::post('break-times', [BreakTimeController::class, 'store'])->name('break-times.store');
    });
    Route::middleware('permission:edit-break-time')->group(function () {
        Route::get('break-times/{breakTime}/edit', [BreakTimeController::class, 'edit'])->name('break-times.edit');
        Route::put('break-times/{breakTime}', [BreakTimeController::class, 'update'])->name('break-times.update');
    });
    Route::delete('break-times/{breakTime}', [BreakTimeController::class, 'destroy'])->name('break-times.destroy')
        ->middleware('permission:delete-break-time');
    
    // Users Management (Super Admin Only) — static routes MUST come before wildcard routes
    Route::get('users', [UserController::class, 'index'])->name('users.index')->middleware('permission:view-users');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:create-user');
    Route::post('users', [UserController::class, 'store'])->name('users.store')->middleware('permission:create-user');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('permission:view-users');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:edit-user');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:edit-user');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:delete-user');
    
    // Roles Management (Super Admin Only) — static routes MUST come before wildcard routes
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index')->middleware('permission:view-roles');
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:create-role');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:create-role');
    Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show')->middleware('permission:view-roles');
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:edit-role');
    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:edit-role');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:delete-role');
    Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions')->middleware('permission:manage-role-permissions');
    Route::put('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update')->middleware('permission:manage-role-permissions');
    
    // Permissions Management (Super Admin Only) — static routes MUST come before wildcard routes
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index')->middleware('permission:view-permissions');
    Route::get('permissions/create', [PermissionController::class, 'create'])->name('permissions.create')->middleware('permission:create-permission');
    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store')->middleware('permission:create-permission');
    Route::get('permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show')->middleware('permission:view-permissions');
    Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit')->middleware('permission:edit-permission');
    Route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update')->middleware('permission:edit-permission');
    Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy')->middleware('permission:delete-permission');
    
    // Sections Management (Super Admin Only) — static routes MUST come before wildcard routes
    Route::get('sections', [SectionController::class, 'index'])->name('sections.index')->middleware('permission:view-users');
    Route::get('sections/assignments/manage', [SectionController::class, 'assignments'])->name('sections.assignments')->middleware('permission:view-users');
    Route::get('sections/create', [SectionController::class, 'create'])->name('sections.create')->middleware('permission:create-user');
    Route::post('sections', [SectionController::class, 'store'])->name('sections.store')->middleware('permission:create-user');
    Route::get('sections/assign/{user}', [SectionController::class, 'assignForm'])->name('sections.assign.form')->middleware('permission:view-users');
    Route::post('sections/assign/{user}', [SectionController::class, 'assignSections'])->name('sections.assign')->middleware('permission:create-user');
    Route::get('sections/{section}/edit', [SectionController::class, 'edit'])->name('sections.edit')->middleware('permission:edit-user');
    Route::put('sections/{section}', [SectionController::class, 'update'])->name('sections.update')->middleware('permission:edit-user');
    Route::delete('sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy')->middleware('permission:delete-user');
    Route::delete('sections/assignments/{user}/{section}', [SectionController::class, 'removeAssignment'])->name('sections.assignments.remove')->middleware('permission:edit-user');
});
