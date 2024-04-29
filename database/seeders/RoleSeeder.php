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
            ['name' => 'User'],
            ['name' => 'Membership'],
            ['name' => 'IT'],
            ['name' => 'Orphans Affairs'],
            ['name' => 'Foreign Affairs'],
            ['name' => 'Executive'],
            ['name' => 'Awareness'],
            ['name' => 'General Secretariat'],
            ['name' => 'Portsudan Sub Office'],
            ['name' => 'Wad Rawah Sub Office'],
            ['name' => 'Financial'],
            ['name' => 'Main Admin'],
        ];

        // Insert roles into the database
        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
