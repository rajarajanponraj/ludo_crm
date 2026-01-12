<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Webkul\SAAS\Models\Company;
use Webkul\User\Models\User;
use Webkul\Lead\Models\Lead;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$errors = [];
$successes = [];

function pass($msg)
{
    global $successes;
    $successes[] = $msg;
    echo "[\033[32mPASS\033[0m] $msg\n";
}
function fail($msg)
{
    global $errors;
    $errors[] = $msg;
    echo "[\033[31mFAIL\033[0m] $msg\n";
}

echo "\n--- Krayin CRM Multi-tenancy System Check ---\n\n";

// 1. INFRASTRUCTURE & SCHEMA
echo ">> Checking Infrastructure...\n";
if (class_exists(\Webkul\SAAS\Providers\ModuleServiceProvider::class))
    pass("SAAS Module Provider exists.");
else
    fail("SAAS Module Provider NOT found.");

if (Schema::hasTable('companies'))
    pass("Database: 'companies' table exists.");
else
    fail("Database: 'companies' table MISSING.");

$requiredTables = ['users', 'leads', 'persons', 'organizations', 'products', 'quotes', 'activities'];
$missingCols = [];
foreach ($requiredTables as $table) {
    if (!Schema::hasColumn($table, 'company_id')) {
        $missingCols[] = $table;
    }
}
if (empty($missingCols))
    pass("Database: 'company_id' column present on all core tables.");
else
    fail("Database: 'company_id' missing from: " . implode(', ', $missingCols));

if (Schema::hasColumn('users', 'is_superuser'))
    pass("Database: 'is_superuser' column exists on users table.");
else
    fail("Database: 'is_superuser' column MISSING on users table.");

// 2. LOGIC & ISOLATION
echo "\n>> Checking Data Isolation Logic...\n";
try {
    // Cleanup
    Company::whereIn('domain', ['check1', 'check2'])->delete();

    // Create Tenants
    $c1 = Company::create(['name' => 'Check 1', 'domain' => 'check1', 'status' => 1]);
    $c2 = Company::create(['name' => 'Check 2', 'domain' => 'check2', 'status' => 1]);

    // Create Data
    session()->put('company_id', $c1->id);
    $lead1 = Lead::create(['title' => 'Lead C1', 'lead_value' => 100, 'status' => 1, 'lead_pipeline_id' => 1, 'lead_pipeline_stage_id' => 1]);

    // Test Visibility C1
    $count1 = Lead::count();
    if ($count1 === 1)
        pass("Isolation: Company 1 sees its own data.");
    else
        fail("Isolation: Company 1 failed to see data (Count: $count1).");

    // Test Visibility C2
    session()->put('company_id', $c2->id);
    $count2 = Lead::count();
    if ($count2 === 0)
        pass("Isolation: Company 2 CANNOT see Company 1 data.");
    else
        fail("Isolation: Company 2 LEAKED data (Count: $count2)!");

    // Cleanup
    $lead1->delete();
    $c1->delete();
    $c2->delete();
    User::whereIn('email', ['heading@check1.com', 'heading@check2.com', 'admin@check1.com', 'admin@check2.com'])->delete(); // Cleanup potential side effects
} catch (\Exception $e) {
    fail("Isolation Test Exception: " . $e->getMessage());
}

// 3. AUTOMATION & SUPER ADMIN
echo "\n>> Checking Super Admin Features...\n";
try {
    // Test Auto-Admin Creation Triggered by Company Creation
    // (Simulating controller logic manually since we are in script)
    $domain = 'auto-test';
    Company::where('domain', $domain)->delete();
    User::where('email', "admin@$domain.com")->delete();

    // We can't easily test the Controller 'store' method directly without a full request mock.
    // Instead, we verify the user model capability.

    $user = new User();
    $user->is_superuser = 1;
    $user->role = new \Webkul\User\Models\Role(['permission_type' => 'custom']); // restrictive role

    if ($user->hasPermission('some.random.permission')) {
        pass("Superuser Logic: 'is_superuser' grants global permission bypass.");
    } else {
        fail("Superuser Logic: Permission bypass FAILED.");
    }

} catch (\Exception $e) {
    fail("Super Admin Test Exception: " . $e->getMessage());
}

echo "\n--- SUMMARY ---\n";
if (empty($errors)) {
    echo "\033[32mALL CHECKS PASSED. SYSTEM IS STABLE.\033[0m\n";
    exit(0);
} else {
    echo "\033[31mSYSTEM UNSTABLE. " . count($errors) . " CHECKS FAILED.\033[0m\n";
    exit(1);
}
