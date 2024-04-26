<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Office;

class OfficesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define offices and insert them into the 'offices' table
        $offices = [
            ['name' => 'Membership Affairs'],
            ['name' => 'IT'],
            ['name' => 'Orphans Affairs'],
            ['name' => 'Foreign Affairs'],
            ['name' => 'Executive'],
            ['name' => 'Awareness'],
            ['name' => 'General Secretariat'],
            ['name' => 'Portsudan Sub Office'],
            ['name' => 'Wad Rawah Sub Office'],
            ['name' => 'Financial'],
            ['name' => 'User'],
        ];

        // Insert offices into the database
        foreach ($offices as $office) {
            Office::create($office);
        }

    }
}
