<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define roles and insert them into the 'roles' table
        $roles = [
            ['name' => 'main_admin'],
            ['name' => 'membership'],
            ['name' => 'it'],
            ['name' => 'orphans'],
            ['name' => 'Foreign_affairs'],
            ['name' => 'executive'],
            ['name' => 'awareness'],
            ['name' => 'general_secretariat'],
            ['name' => 'sub_offices'],
            ['name' => 'financial'],
            ['name' => 'user'],
        ];

        // Insert roles into the database
        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
