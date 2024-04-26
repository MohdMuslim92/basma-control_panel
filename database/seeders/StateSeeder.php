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
            ['name' => 'البحر الأحمر'],
            ['name' => 'الجزيرة'],
            ['name' => 'الخرطوم'],
            ['name' => 'الشمالية'],
            ['name' => 'القضارف'],
            ['name' => 'النيل الأبيض'],
            ['name' => 'النيل الأزرق'],
            ['name' => 'جنوب دارفور'],
            ['name' => 'جنوب كردفان'],
            ['name' => 'سنار'],
            ['name' => 'شرق دارفور'],
            ['name' => 'شمال دارفور'],
            ['name' => 'شمال كردفان'],
            ['name' => 'غرب دارفور'],
            ['name' => 'كسلا'],
            ['name' => 'نهر النيل'],
            ['name' => 'وسط دارفور'],
            ['name' => 'غرب كردفان'],
        ];

        // Insert roles into the database
        foreach ($states as $state) {
            State::create($state);
        }
    }
}
