<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Retrieve states' IDs
        $stateIds = State::pluck('id', 'name');

        // Define provinces associated with their respective states
        $provinces = [
            'state1' => [
                ['name' => 'province1', 'state_id' => $stateIds['state1']],
                //['name' => 'province2', 'state_id' => $stateIds['state1']],
            ],
            'state2' => [
                ['name' => 'province2', 'state_id' => $stateIds['state2']],
            ],
            'state3' => [
                ['name' => 'province3', 'state_id' => $stateIds['state3']],
            ],
        ];

        // Insert provinces into the database
        foreach ($provinces as $stateName => $stateProvinces) {
            foreach ($stateProvinces as $province) {
                Province::create($province);
            }
        }
    }}
