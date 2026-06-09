<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['Minor Service', 'minor_service'],
            ['Major Service', 'major_service'],
            ['Oil & Filter Change', 'minor_service'],
            ['Tyre Replacement', 'tyres'],
            ['Wheel Alignment', 'tyres'],
            ['Wheel Balancing', 'tyres'],
            ['Brake Pad Replacement', 'brakes'],
            ['Brake Inspection', 'brakes'],
            ['Engine Diagnostics', 'repair'],
            ['Clutch Repair', 'repair'],
            ['Battery Replacement', 'other'],
            ['Air-Con Regas', 'other'],
        ];

        foreach ($services as [$name, $category]) {
            Service::firstOrCreate(['name' => $name], ['category' => $category]);
        }
    }
}
