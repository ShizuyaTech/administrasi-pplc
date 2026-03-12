<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;

echo "Adding Break Time permissions...\n\n";

$permissions = [
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
];

foreach ($permissions as $permData) {
    $existing = Permission::where('slug', $permData['slug'])->first();
    
    if ($existing) {
        echo "✓ Permission '{$permData['name']}' already exists (skipped)\n";
    } else {
        Permission::create($permData);
        echo "✓ Permission '{$permData['name']}' created successfully\n";
    }
}

echo "\n✅ Done! Break Time permissions have been added.\n";
echo "🔄 Please assign these permissions to roles via the web interface.\n";
