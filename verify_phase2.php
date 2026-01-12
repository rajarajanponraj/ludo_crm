<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Schema;
use Webkul\SAAS\Models\Company;

echo "Checking Schema...\n";
if (Schema::hasTable('companies')) {
    echo "✔ 'companies' table exists.\n";
} else {
    echo "✘ 'companies' table MISSING.\n";
}

$tables = ['users', 'leads', 'persons', 'products', 'activities'];
foreach ($tables as $table) {
    if (Schema::hasColumn($table, 'company_id')) {
        echo "✔ '$table' has 'company_id'.\n";
    } else {
        echo "✘ '$table' missing 'company_id'.\n";
    }
}

echo "\nChecking Models...\n";
try {
    $company = Company::create(['name' => 'Test Company', 'domain' => 'test']);
    echo "✔ Created Company: {$company->name} (ID: {$company->id})\n";
} catch (\Exception $e) {
    echo "✘ Creating Company Failed: " . $e->getMessage() . "\n";
}
