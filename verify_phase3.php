<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Webkul\SAAS\Models\Company;
use Webkul\User\Models\User;
use Webkul\Lead\Models\Lead;
use Illuminate\Support\Facades\Session;

echo "Preparing Test Data...\n";

// 1. Create Companies
$companyA = Company::firstOrCreate(['domain' => 'alpha'], ['name' => 'Alpha Corp']);
$companyB = Company::firstOrCreate(['domain' => 'beta'], ['name' => 'Beta Inc']);

echo "Companies Created: Alpha ({$companyA->id}), Beta ({$companyB->id})\n";

// 2. Create Users
// We need to disable the scope temporarily to create users for specific companies if not in session?
// Actually HasCompany trait sets company_id from session.
// We should manually set company_id for seeding.

Session::put('company_id', $companyA->id);
$userA = User::where('email', 'alpha@test.com')->first();
if (!$userA) {
    $userA = new User([
        'name' => 'Alpha User',
        'email' => 'alpha@test.com',
        'password' => bcrypt('password'),
        'status' => 1,
        'role_id' => 1,
    ]);
    $userA->company_id = $companyA->id;
    $userA->saveQuietly(); // Use saveQuietly to avoid trait interference if needed, or just save normally checking session.
}

Session::put('company_id', $companyB->id);
$userB = User::where('email', 'beta@test.com')->first();
if (!$userB) {
    $userB = new User([
        'name' => 'Beta User',
        'email' => 'beta@test.com',
        'password' => bcrypt('password'),
        'status' => 1,
        'role_id' => 1,
    ]);
    $userB->company_id = $companyB->id;
    $userB->saveQuietly();
}

echo "Users Created: Alpha User ({$userA->id}), Beta User ({$userB->id})\n";

// 3. Create Data for Alpha
Session::put('company_id', $companyA->id);
// Note: We need to use 'create' so the trait picks up the session company_id
$leadA = Lead::create([
    'title' => 'Alpha Lead',
    'lead_value' => 1000,
    'status' => 1,
    // Minimum required fields
    'lead_pipeline_id' => 1,
    'lead_pipeline_stage_id' => 1,
]);
echo "Created Lead for Alpha: {$leadA->title} (Company ID: {$leadA->company_id})\n";

// 4. Verify Isolation
echo "\n--- Testing Isolation ---\n";

// Simulate Alpha Request
Session::put('company_id', $companyA->id);
$alphaLeads = Lead::all();
echo "Alpha Context Lead Count: " . $alphaLeads->count() . "\n";
if ($alphaLeads->contains('id', $leadA->id)) {
    echo "✔ Alpha can see Alpha Lead.\n";
} else {
    echo "✘ Alpha CANNOT see Alpha Lead!\n";
}

// Simulate Beta Request
Session::put('company_id', $companyB->id);
$betaLeads = Lead::all();
echo "Beta Context Lead Count: " . $betaLeads->count() . "\n";

if ($betaLeads->contains('id', $leadA->id)) {
    echo "✘ SECURITY FAIL: Beta can see Alpha Lead!\n";
} else {
    echo "✔ SECURITY PASS: Beta cannot see Alpha Lead.\n";
}

// Cleanup
$leadA->delete();
