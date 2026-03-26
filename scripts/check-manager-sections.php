<?php

/**
 * Script untuk cek section yang ter-assign ke Manager
 * Jalankan: php check-manager-sections.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== CEK MANAGER SECTIONS ===\n\n";

// Ambil semua user dengan role Manager
$managers = User::whereHas('role', function($q) {
    $q->where('name', 'Manager');
})->with(['sections', 'role'])->get();

if ($managers->isEmpty()) {
    echo "❌ Tidak ada user dengan role Manager\n";
    exit;
}

foreach ($managers as $manager) {
    echo "👤 Manager: {$manager->name} (ID: {$manager->id})\n";
    echo "   Email: {$manager->email}\n";
    
    if ($manager->sections->isEmpty()) {
        echo "   ⚠️  TIDAK TER-ASSIGN KE SECTION MANAPUN\n";
        echo "   💡 Solusi: Assign section ke manager ini menggunakan script add-manager-sections.php\n";
    } else {
        echo "   ✅ Ter-assign ke " . $manager->sections->count() . " section:\n";
        foreach ($manager->sections as $section) {
            echo "      - {$section->name}\n";
        }
    }
    echo "\n";
}

echo "\n=== CEK OVERTIME DATA ===\n\n";

use App\Models\Overtime;

$pendingCount = Overtime::where('status', 'pending')->count();
$supervisorApprovedCount = Overtime::where('status', 'supervisor_approved')->count();
$fullyApprovedCount = Overtime::where('status', 'fully_approved')->count();

echo "📊 Total Overtime berdasarkan status:\n";
echo "   - Pending (menunggu Supervisor): {$pendingCount}\n";
echo "   - Supervisor Approved (menunggu Manager): {$supervisorApprovedCount}\n";
echo "   - Fully Approved (sudah final): {$fullyApprovedCount}\n";
