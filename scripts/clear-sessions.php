<?php

/**
 * Clear all sessions to force fresh login
 * Usage: php clear-sessions.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Clear All Sessions ===\n\n";

try {
    $deleted = \Illuminate\Support\Facades\DB::table('sessions')->delete();
    echo "✅ Deleted {$deleted} sessions from database\n";
    echo "   All users need to login again.\n\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   Sessions table might not exist or database not configured.\n\n";
}

echo "=== Clear File-based Cache ===\n\n";

// Clear storage/framework/sessions if using file driver
$sessionPath = storage_path('framework/sessions');
if (is_dir($sessionPath)) {
    $files = glob($sessionPath . '/*');
    $count = 0;
    foreach ($files as $file) {
        if (is_file($file) && unlink($file)) {
            $count++;
        }
    }
    echo "✅ Deleted {$count} session files from storage\n\n";
} else {
    echo "⚠️  Session storage directory not found\n\n";
}

echo "Done!\n";
