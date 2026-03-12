<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BusinessTrip;
use App\Models\Section;
use App\Models\User;

class BusinessTripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = Section::all();
        $users = User::all();
        $approvers = User::whereIn('role_id', [1, 2, 3])->get();
        
        $names = ['Agus Santoso', 'Bambang Wijaya', 'Candra Kusuma', 'Dedi Supardi', 'Eko Prasetyo'];
        $destinations = ['Jakarta', 'Surabaya', 'Bandung', 'Semarang', 'Yogyakarta', 'Bali'];
        $purposes = [
            'Training teknis operasional',
            'Meeting dengan supplier',
            'Koordinasi dengan customer',
            'Audit sistem manajemen',
            'Troubleshooting equipment',
            'Installation mesin baru',
        ];
        $transports = ['Mobil Dinas', 'Mobil Pribadi', 'Motor', 'Pesawat', 'Kereta', 'Travel'];
        
        $counter = 1;
        
        // Create business trips for the last 60 days
        for ($i = 0; $i < 20; $i++) {
            $section = $sections->random();
            $creator = $users->where('section_id', $section->id)->first() ?? $users->first();
            $approver = $approvers->where('section_id', $section->id)->first() ?? $approvers->first();
            
            $departureDate = now()->addDays(rand(-30, 30));
            $returnDate = $departureDate->copy()->addDays(rand(1, 5));
            
            $status = ['draft', 'approved', 'approved', 'completed', 'completed'][rand(0, 4)];
            
            BusinessTrip::create([
                'letter_number' => 'SPD/' . date('Y') . '/' . str_pad($counter++, 4, '0', STR_PAD_LEFT),
                'section_id' => $section->id,
                'employee_name' => $names[rand(0, count($names) - 1)],
                'destination' => $destinations[rand(0, count($destinations) - 1)],
                'departure_date' => $departureDate,
                'return_date' => $returnDate,
                'purpose' => $purposes[rand(0, count($purposes) - 1)],
                'transport' => $transports[rand(0, count($transports) - 1)],
                'estimated_cost' => rand(500, 5000) * 1000,
                'status' => $status,
                'attachment' => null,
                'approved_by' => in_array($status, ['approved', 'completed']) ? $approver->id : null,
                'approved_at' => in_array($status, ['approved', 'completed']) ? now()->subDays(rand(1, 10)) : null,
                'created_by' => $creator->id,
            ]);
        }
    }
}
