<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "Checking attendances table...\n";
if (Schema::hasTable('attendances')) {
    echo "Table 'attendances' EXISTS.\n";
    print_r(DB::select('describe attendances'));
} else {
    echo "Table 'attendances' DOES NOT EXIST.\n";
}
