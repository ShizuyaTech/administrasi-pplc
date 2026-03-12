<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Consumable;
use App\Models\Section;

class ConsumableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = Section::all();
        
        $items = [
            ['name' => 'Kertas A4', 'unit' => 'Rim', 'current_stock' => 50, 'minimum_stock' => 20],
            ['name' => 'Sarung Tangan Kain', 'unit' => 'Pasang', 'current_stock' => 100, 'minimum_stock' => 50],
            ['name' => 'Lakban Coklat', 'unit' => 'Roll', 'current_stock' => 30, 'minimum_stock' => 15],
            ['name' => 'Majun Putih', 'unit' => 'Kg', 'current_stock' => 25, 'minimum_stock' => 10],
            ['name' => 'Spidol Permanent', 'unit' => 'Pcs', 'current_stock' => 40, 'minimum_stock' => 20],
            ['name' => 'Masker 3M', 'unit' => 'Box', 'current_stock' => 15, 'minimum_stock' => 10],
            ['name' => 'Sarung Tangan Karet', 'unit' => 'Pasang', 'current_stock' => 8, 'minimum_stock' => 15], // Low stock
            ['name' => 'Lakban Hitam', 'unit' => 'Roll', 'current_stock' => 12, 'minimum_stock' => 10],
            ['name' => 'Plastic Wrapping', 'unit' => 'Roll', 'current_stock' => 5, 'minimum_stock' => 8], // Low stock
            ['name' => 'Label Stiker', 'unit' => 'Lembar', 'current_stock' => 200, 'minimum_stock' => 100],
        ];
        
        foreach ($sections as $section) {
            // Give each section some random items
            $sectionItems = fake()->randomElements($items, rand(5, 8));
            
            foreach ($sectionItems as $item) {
                Consumable::create([
                    'section_id' => $section->id,
                    'name' => $item['name'],
                    'unit' => $item['unit'],
                    'current_stock' => $item['current_stock'],
                    'minimum_stock' => $item['minimum_stock'],
                ]);
            }
        }
    }
}
