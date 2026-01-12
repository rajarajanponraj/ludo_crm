<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Webkul\Lead\Models\Lead;

$leads = Lead::all();
echo "Total Leads: " . $leads->count() . "\n";
foreach ($leads as $lead) {
    echo "Lead ID: {$lead->id}, Title: {$lead->title}, Value: {$lead->lead_value}, Pipeline ID: {$lead->lead_pipeline_id}, Stage ID: {$lead->lead_pipeline_stage_id}, Created At: {$lead->created_at}\n";
}
