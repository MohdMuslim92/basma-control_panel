<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
                'terms' => true,
            ]);
        }
        
        
        /*
        // Fetch all users from the old_users table
        $oldUsers = DB::table('old_users')->get();

        // Loop through each user and insert into the new users table
        foreach ($oldUsers as $oldUser) {
            DB::table('users')->insert([
                'name' => $oldUser->name,
                'email' => $oldUser->email,
                'email_verified_at' => $oldUser->email_verified_at ? date('Y-m-d H:i:s', strtotime($oldUser->email_verified_at)) : null,
                'password' => Hash::make($oldUser->password),
                'gender' => $oldUser->gender,
                'state_id' => 1,
                'province_id' => 1,
                'address' => $oldUser->address,
                'phone' => $oldUser->phone,
                'dob' => $oldUser->birthdate,
                'educationLevel' => $oldUser->education,
                'specialization' => $oldUser->field,
                'skills' => $oldUser->skills,
                'alreadyVolunteering' => 'No',
                'organizationName' => null,
                'volunteeringStartDate' => null,
                'volunteeringEndDate' => null,
                'monthlyShare' => $oldUser->share,
                'meetingDay' => 'Friday',
                'role_id' => 1,
                'terms' => true,
                'remember_token' => null,
                'current_team_id' => null,
                'profile_photo_path' => null,
                'user_status_id' => 1,
                'admin_mail' => null,
                'last_pay' => null,
                'last_seen_at' => null,
                'gone_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        */
    }
}
