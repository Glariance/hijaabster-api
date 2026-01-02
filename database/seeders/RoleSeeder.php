<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = config('constants.ADMIN');
        $userId = config('constants.USER');

        // Disable foreign key checks temporarily to allow inserting/updating with specific IDs
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Check if roles table is empty
        $rolesCount = Role::count();
        
        if ($rolesCount == 0) {
            // If roles table is empty, insert roles with correct IDs
            // First insert user role with ID 1
            DB::table('roles')->insert([
                'id' => $userId,
                'name' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Then insert admin role with ID 2
            DB::table('roles')->insert([
                'id' => $adminId,
                'name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Reset auto_increment after manual inserts
            DB::statement('ALTER TABLE roles AUTO_INCREMENT = 3');
        } else {
            // Roles already exist - ensure admin and user roles exist with correct IDs
            // Use DB::table for more reliable existence checks
            
            // Handle user role first (ID 1)
            $userExistsById = DB::table('roles')->where('id', $userId)->exists();
            $userExistsByName = DB::table('roles')->where('name', 'user')->first();
            
            if ($userExistsById) {
                // Role with ID 1 exists - just update name if needed
                DB::table('roles')->where('id', $userId)->update(['name' => 'user']);
            } elseif ($userExistsByName && $userExistsByName->id != $userId) {
                // User role exists but with wrong ID - update all users and change role ID
                DB::table('users')->where('role_id', $userExistsByName->id)->update(['role_id' => $userId]);
                DB::table('roles')->where('id', $userExistsByName->id)->update(['id' => $userId, 'name' => 'user']);
            } else {
                // User role doesn't exist - create it
                // Final check to prevent duplicate key error
                if (!DB::table('roles')->where('id', $userId)->exists()) {
                    DB::table('roles')->insert([
                        'id' => $userId,
                        'name' => 'user',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            
            // Handle admin role (ID 2)
            $adminExistsById = DB::table('roles')->where('id', $adminId)->exists();
            $adminExistsByName = DB::table('roles')->where('name', 'admin')->first();
            
            if ($adminExistsById) {
                // Role with ID 2 exists - just update name if needed
                DB::table('roles')->where('id', $adminId)->update(['name' => 'admin']);
            } elseif ($adminExistsByName && $adminExistsByName->id != $adminId) {
                // Admin role exists but with wrong ID - update all users and change role ID
                DB::table('users')->where('role_id', $adminExistsByName->id)->update(['role_id' => $adminId]);
                DB::table('roles')->where('id', $adminExistsByName->id)->update(['id' => $adminId, 'name' => 'admin']);
            } else {
                // Admin role doesn't exist - create it
                // Final check to prevent duplicate key error
                if (!DB::table('roles')->where('id', $adminId)->exists()) {
                    DB::table('roles')->insert([
                        'id' => $adminId,
                        'name' => 'admin',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Verify roles exist before creating user
        $adminRoleExists = Role::find($adminId);
        if (!$adminRoleExists) {
            throw new \Exception("Admin role with ID {$adminId} does not exist. Please check the roles table.");
        }

        // Create/update admin user safely with correct role_id
        User::updateOrCreate(
            ['email' => 'admin@mail.com'],   // key to find existing
            [
                'name'     => 'Admin',
                'password' => Hash::make('password'),
                'role_id'  => $adminId, // Use constant directly to ensure correct value
            ]
        );
    }
}
