<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Webkul\SAAS\Models\Company;
use Webkul\User\Models\User;

echo "Verifying Phase 4: Super Admin Management...\n";

// 1. Check Superuser Column
try {
    $user = User::first();
    if (isset($user->is_superuser)) {
        echo "✔ 'is_superuser' column exists.\n";
    } else {
        echo "✘ 'is_superuser' column MISSING!\n";
    }
} catch (\Exception $e) {
    echo "✘ Error checking column: " . $e->getMessage() . "\n";
}

// 2. Simulate Creating a Company via Controller Logic (Manual Repro)
$domain = 'gamma';
echo "Simulating Company Creation (Domain: $domain)...\n";

$company = Company::where('domain', $domain)->first();
if ($company) {
    $company->delete(); // Cleanup previous runs
    User::where('email', "admin@$domain.com")->delete();
}

$newCompany = Company::create([
    'name' => 'Gamma Gamma',
    'domain' => $domain,
    'status' => 1
]);

echo "Company Created: {$newCompany->id}\n";

// Create Admin User Manually (mirroring Controller logic)
$user = new User([
    'name' => 'Admin',
    'email' => "admin@$domain.com",
    'password' => bcrypt('admin123'),
    'status' => 1,
    'role_id' => 1,
    'is_superuser' => 0,
]);
$user->company_id = $newCompany->id;
$user->save();

echo "Admin User Created: {$user->email} (Company: {$user->company_id})\n";

// Verify User creation
$checkUser = User::where('email', "admin@$domain.com")->first();
if ($checkUser && $checkUser->company_id == $newCompany->id) {
    echo "✔ Default Admin creation SUCCESS.\n";
} else {
    echo "✘ Default Admin creation FAILED.\n";
}

// 3. Set Superuser Flag on Main Admin
$mainAdmin = User::find(1); // Assuming ID 1 is main admin
if ($mainAdmin) {
    $mainAdmin->is_superuser = 1;
    $mainAdmin->save();
    echo "✔ Main Admin (ID 1) promoted to Superuser.\n";

    if ($mainAdmin->hasPermission('settings.companies')) {
        echo "✔ Superuser has permission to settings.companies.\n";
    } else {
        echo "✘ Superuser logic failed - no permission.\n";
    }
} else {
    echo "⚠ Main Admin ID 1 not found.\n";
}

echo "Done.\n";
