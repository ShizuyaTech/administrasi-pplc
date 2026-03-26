<?php

/**
 * One-time script to sync User data with Employee data
 * 
 * Run this script to ensure all users have matching data with their employee records
 * Usage: php sync-user-employee-data.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Sync User-Employee Data Script ===\n\n";

$users = \App\Models\User::whereNotNull('employee_id')->with('employee')->get();

if ($users->isEmpty()) {
    echo "No users with employee_id found.\n";
    exit(0);
}

echo "Found {$users->count()} users with employee accounts.\n\n";

$syncedCount = 0;
$skippedCount = 0;

foreach ($users as $user) {
    if (!$user->employee) {
        echo "⚠️  User #{$user->id} ({$user->email}) - Employee not found\n";
        $skippedCount++;
        continue;
    }
    
    $employee = $user->employee;
    $changes = [];
    
    // Check name
    if ($user->name !== $employee->name) {
        $changes['name'] = [
            'from' => $user->name,
            'to' => $employee->name
        ];
    }
    
    // Check section_id
    if ($user->section_id !== $employee->section_id) {
        $changes['section_id'] = [
            'from' => $user->section_id,
            'to' => $employee->section_id
        ];
    }
    
    // Check shift (map employee enum to user integer)
    $shiftMapping = [
        'Shift A' => 1,
        'Shift B' => 2,
        'Non Shift' => null,
    ];
    $userShift = $shiftMapping[$employee->shift] ?? null;
    if ($user->shift !== $userShift) {
        $changes['shift'] = [
            'from' => $user->shift,
            'to' => $userShift,
            'employee_shift' => $employee->shift
        ];
    }
    
    if (empty($changes)) {
        $skippedCount++;
        continue;
    }
    
    // Display changes
    echo "🔄 User #{$user->id} ({$user->email}) - Employee: {$employee->name} (NRP: {$employee->nrp})\n";
    foreach ($changes as $field => $change) {
        $fromDisplay = $change['from'] ?? 'NULL';
        $toDisplay = $change['to'] ?? 'NULL';
        
        if ($field === 'section_id') {
            $fromSection = $change['from'] ? \App\Models\Section::find($change['from'])->name ?? 'Unknown' : 'NULL';
            $toSection = $change['to'] ? \App\Models\Section::find($change['to'])->name ?? 'Unknown' : 'NULL';
            echo "   - {$field}: {$fromSection} → {$toSection}\n";
        } elseif ($field === 'shift') {
            $fromShift = $fromDisplay === 'NULL' ? 'NULL' : ($fromDisplay == 1 ? 'Shift A (1)' : 'Shift B (2)');
            $employeeShift = $change['employee_shift'] ?? '';
            $toShift = $toDisplay === 'NULL' ? "NULL ({$employeeShift})" : ($toDisplay == 1 ? "Shift A (1)" : "Shift B (2)");
            echo "   - {$field}: {$fromShift} → {$toShift}\n";
        } else {
            echo "   - {$field}: {$fromDisplay} → {$toDisplay}\n";
        }
    }
    
    // Update user
    $updateData = [];
    foreach ($changes as $field => $change) {
        $updateData[$field] = $change['to'];
    }
    
    try {
        $user->update($updateData);
        echo "   ✅ Synced successfully\n\n";
        $syncedCount++;
    } catch (\Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n\n";
    }
}

echo "\n=== Summary ===\n";
echo "Total users: {$users->count()}\n";
echo "Synced: {$syncedCount}\n";
echo "Skipped (no changes): {$skippedCount}\n";
echo "\nDone!\n";
