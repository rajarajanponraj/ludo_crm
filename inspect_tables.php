<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Schema;

$columns = Schema::getColumnListing('lead_products');
print_r($columns);

$columns2 = Schema::getColumnListing('products');
print_r($columns2);
