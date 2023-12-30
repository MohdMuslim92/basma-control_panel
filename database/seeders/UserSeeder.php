<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 30; $i++) {
            User::create([
                'name' => "John Doe{$i}",
                'email' => "john{$i}@example.com",
                'password' => bcrypt('password'), // Hash the password
                'gender' => 'male',
                'state_id' => '1',
                'province_id' => '1',
                'address' => '123 Main Street',
                'phone' => '1234567890',
                'dob' => '1990-01-01',
                'educationLevel' => 'University',
                'specialization' => 'Computer Science',
                'skills' => 'Programming, Communication',
                'alreadyVolunteering' => 'false',
                'organizationName' => null,
                'volunteeringStartDate' => null,
                'volunteeringEndDate' => null,
                'monthlyShare' => 500,
                'meetingDay' => 'Monday',
                'terms' => false,
            ]);
        }
    }
}
