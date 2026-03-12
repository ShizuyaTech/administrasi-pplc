<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            ['name' => 'Material Control', 'code' => 'MC', 'description' => 'Seksi Material Control'],
            ['name' => 'PPC + Toolroom', 'code' => 'PPC', 'description' => 'Seksi PPC dan Toolroom'],
            ['name' => 'Logistik', 'code' => 'LOG', 'description' => 'Seksi Logistik'],
            ['name' => 'Delivery', 'code' => 'DLV', 'description' => 'Seksi Delivery'],
        ];

        foreach ($sections as $section) {
            DB::table('sections')->insert([
                'name' => $section['name'],
                'code' => $section['code'],
                'description' => $section['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
