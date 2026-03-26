<?php

/**
 * Script untuk assign section ke Manager
 * Jalankan: php add-manager-sections.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Section;

echo "=== ASSIGN SECTIONS KE MANAGER ===\n\n";

// Ambil manager (sesuaikan dengan email manager Anda)
$managerEmail = readline("Masukkan email Manager: ");

$manager = User::where('email', $managerEmail)->first();

if (!$manager) {
    echo "❌ User dengan email {$managerEmail} tidak ditemukan\n";
    exit;
}

if (!$manager->role || $manager->role->name !== 'Manager') {
    echo "⚠️  User ini bukan Manager (Role: {$manager->role->name})\n";
    $confirm = readline("Tetap lanjutkan assign sections? (y/n): ");
    if (strtolower($confirm) !== 'y') {
        exit;
    }
}

echo "\n📋 Sections yang tersedia:\n";
$sections = Section::all();

foreach ($sections as $index => $section) {
    $isAssigned = $manager->sections->contains($section->id) ? ' ✅ (sudah ter-assign)' : '';
    echo "   " . ($index + 1) . ". {$section->name}{$isAssigned}\n";
}

echo "\nPilih sections untuk di-assign (pisahkan dengan koma, contoh: 1,2,3)\n";
echo "Atau ketik 'all' untuk assign semua sections: ";

$input = readline();

if (strtolower(trim($input)) === 'all') {
    $selectedSections = $sections->pluck('id')->toArray();
} else {
    $selectedIndexes = array_map('trim', explode(',', $input));
    $selectedSections = [];
    
    foreach ($selectedIndexes as $index) {
        if (is_numeric($index) && isset($sections[$index - 1])) {
            $selectedSections[] = $sections[$index - 1]->id;
        }
    }
}

if (empty($selectedSections)) {
    echo "❌ Tidak ada section yang dipilih\n";
    exit;
}

// Sync sections (akan replace yang lama)
$manager->sections()->sync($selectedSections);

echo "\n✅ Berhasil assign " . count($selectedSections) . " section ke {$manager->name}\n";
echo "\nSections yang ter-assign:\n";

$manager->load('sections');
foreach ($manager->sections as $section) {
    echo "   ✓ {$section->name}\n";
}

echo "\n💡 Sekarang manager bisa:\n";
echo "   - Melihat data overtime di halaman Index dari section yang ter-assign\n";
echo "   - Approve overtime yang sudah di-approve Supervisor di halaman Manager Approval\n";
