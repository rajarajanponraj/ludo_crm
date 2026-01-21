<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Webkul\User\Models\User;

class SeedSuperAdmin extends Seeder
{
    public function run()
    {
        $email = 'super@crm.com';
        $password = 'admin123';

        // Bypass any global scopes (like CompanyScope) that might act up
        $user = User::withoutGlobalScopes()->where('email', $email)->first();

        if (!$user) {
            User::create([
                'name' => 'Super Admin',
                'email' => $email,
                'password' => Hash::make($password),
                'status' => 1,
                'role_id' => 1, // Assuming Role ID 1 is Admin
                'is_superuser' => 1,
                'company_id' => null,
            ]);
            $this->command->info("Super Admin created: $email / $password");
        } else {
            $user->update(['is_superuser' => 1]);
            $this->command->info("Existing user promoted to Super Admin: $email");
        }
    }
}
