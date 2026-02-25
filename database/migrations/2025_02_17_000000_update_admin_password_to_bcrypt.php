<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Sets admin user(s) password to bcrypt('admin123') so admin login works.
     */
    public function up(): void
    {
        $hashedPassword = Hash::make('admin123');

        // Update by admin role_id (config value 2)
        DB::table('users')
            ->where('role_id', config('constants.ADMIN', 2))
            ->update(['password' => $hashedPassword]);

        // Also update common admin email if present
        DB::table('users')
            ->where('email', 'admin@mail.com')
            ->update(['password' => $hashedPassword]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot safely reverse - would need to know previous password
    }
};
