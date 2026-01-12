<?php

namespace Webkul\Installer\Database\Seeders\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @param  array  $parameters
     * @return void
     */
    public function run($parameters = [])
    {
        DB::table('users')->delete();
        DB::table('companies')->delete();

        $companyId = DB::table('companies')->insertGetId([
            'id' => 1,
            'name' => 'Main Company',
            'domain' => 'app', // Default placeholder
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Example Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            // 'api_token'       => Str::random(80),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status' => 1,
            'role_id' => 1,
            'view_permission' => 'global',
            'company_id' => $companyId,
            'is_superuser' => 1,
        ]);
    }
}
