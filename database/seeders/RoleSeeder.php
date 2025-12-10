<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles safely (no duplicates)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole  = Role::firstOrCreate(['name' => 'user']);

        // Create/update admin user safely
        User::updateOrCreate(
            ['email' => 'admin@mail.com'],   // key to find existing
            [
                'name'     => 'Admin',
                'password' => Hash::make('password'),
                'role_id'  => $adminRole->id,
            ]
        );
    }
}
