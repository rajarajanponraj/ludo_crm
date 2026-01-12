<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Webkul\Lead\Models\Pipeline;
use Webkul\Lead\Models\Stage;

echo "Pipelines:\n";
foreach (Pipeline::all() as $pipeline) {
    echo "ID: {$pipeline->id}, Name: {$pipeline->name}\n";
}

echo "\nStages:\n";
foreach (Stage::all() as $stage) {
    echo "ID: {$stage->id}, Code: {$stage->code}, Name: {$stage->name}, Pipeline ID: {$stage->lead_pipeline_id}\n";
}
