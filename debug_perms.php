<?php

use Webkul\User\Models\Admin;
use Webkul\User\Models\Role;

// Assuming standard krayin/webkul models, though namespaces might vary.
// Checking the UserSeeder used `DB::table` so models might be in Webkul\User...
// Let's rely on auth()

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \Illuminate\Support\Facades\DB::table('users')->where('id', 1)->first();
$role = \Illuminate\Support\Facades\DB::table('roles')->where('id', $user->role_id)->first();

echo "User ID: " . $user->id . "\n";
echo "User Name: " . $user->name . "\n";
echo "Role ID: " . $user->role_id . "\n";
echo "Role Name: " . $role->name . "\n";
echo "Permission Type: " . $role->permission_type . "\n";
echo "Permissions: " . $role->permissions . "\n";
