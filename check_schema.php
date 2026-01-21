<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "--- CHECKING USERS TABLE SCHEMA ---\n";

try {
    $columns = DB::select("DESCRIBE users");
    foreach ($columns as $col) {
        if ($col->Field === 'id') {
            echo "Column: {$col->Field}\n";
            echo "Type: {$col->Type}\n";
            echo "Null: {$col->Null}\n";
            echo "Key: {$col->Key}\n";
            echo "Extra: {$col->Extra}\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n--- CHECKING COMPANIES TABLE SCHEMA ---\n";
try {
    $columns = DB::select("DESCRIBE companies");
    foreach ($columns as $col) {
        if ($col->Field === 'id') {
            echo "Column: {$col->Field}\n";
            echo "Type: {$col->Type}\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "--- END ---\n";
