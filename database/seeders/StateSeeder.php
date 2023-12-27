<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define states and insert them into the 'states' table
        $states = [
            ['name' => 'state1'],
            ['name' => 'state2'],
            ['name' => 'state3'],
        ];

        // Insert roles into the database
        foreach ($states as $state) {
            State::create($state);
        }
    }
}
