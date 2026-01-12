<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "--- Menu Debug ---\n";

// 1. Check Config
$menuConfig = config('menu.admin');
$found = false;
foreach ($menuConfig as $item) {
    if ($item['key'] === 'settings.companies') {
        echo "Found 'settings.companies' in config.\n";
        print_r($item);
        $found = true;
        break;
    }
}
if (!$found) {
    echo "ERROR: 'settings.companies' NOT found in config('menu.admin').\n";
    // echo "Dumping config keys:\n";
    // foreach ($menuConfig as $item) echo $item['key'] . "\n";
}

// 2. Check User Permission
$user = \Webkul\User\Models\User::find(1); // Assuming ID 1
echo "\n--- User Debug (ID: {$user->id}) ---\n";
echo "is_superuser: " . ($user->is_superuser ? 'YES' : 'NO') . "\n";
echo "Has Permission 'settings.companies': " . ($user->hasPermission('settings.companies') ? 'YES' : 'NO') . "\n";

// 3. Check Menu Builder (Simulate)
echo "\n--- Menu Builder Debug ---\n";
// We need to act as the user
auth()->guard('user')->setUser($user);

$tree = \Webkul\Core\Tree::create();
foreach ($menuConfig as $item) {
    $tree->add($item, 'menu');
}

$start = microtime(true);
$menuItems = $tree->items;
// Inspect the structure to find settings -> companies
$settings = null;
foreach ($menuItems as $item) {
    if ($item['key'] == 'settings') {
        $settings = $item;
        break;
    }
}

if ($settings) {
    echo "Found 'settings' parent.\n";
    $hasCompanies = false;
    foreach ($settings['children'] as $child) {
        if ($child['key'] == 'settings.companies') {
            echo "Found 'settings.companies' in Tree.\n";
            $hasCompanies = true;
            break;
        }
    }
    if (!$hasCompanies) {
        echo "ERROR: 'settings.companies' NOT found in 'settings' children in Tree.\n";
        echo "Children keys: " . implode(', ', array_column($settings['children'], 'key')) . "\n";
    }
} else {
    echo "ERROR: 'settings' parent NOT found in Tree.\n";
}
