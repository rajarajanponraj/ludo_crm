<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Webkul\Contact\Models\Person;
use Webkul\Lead\Models\Lead;
use Webkul\Product\Models\Product;
use Webkul\User\Models\User;
use Webkul\Lead\Models\Pipeline;
use Webkul\Lead\Models\Stage;
use Webkul\Lead\Models\Source;
use Webkul\Lead\Models\Type;
use Carbon\Carbon;

// Ensure we have a user
$user = User::first();
if (!$user) {
    echo "No admin user found. Please run migrations/seeds first.\n";
    exit;
}

echo "Seeding Dashboard Data...\n";

// 1. Fetch Necessary IDs
$wonStage = Stage::where('code', 'won')->first();
$lostStage = Stage::where('code', 'lost')->first();
$newStage = Stage::where('code', 'new')->first(); // Assuming 'new' exists or pick first non-won/lost

// Fallback if 'new' doesn't exist, pick the first one that isn't won/lost
if (!$newStage) {
    $newStage = Stage::whereNotIn('code', ['won', 'lost'])->first();
}

if (!$wonStage || !$lostStage) {
    echo "Error: Could not find 'won' or 'lost' stages. Please check your DB configuration.\n";
    exit;
}

$sources = Source::all();
$types = Type::all();

if ($sources->isEmpty() || $types->isEmpty()) {
    echo "Error: No lead sources or types found. Please seed basic data first.\n";
    exit;
}

// 2. Create Products
echo "Creating Products...\n";
$products = [];
$productData = [
    ['sku' => 'SRV-001', 'name' => 'Premium Consultancy', 'price' => 5000],
    ['sku' => 'SRV-002', 'name' => 'Basic Support Plan', 'price' => 1200],
    ['sku' => 'SW-001', 'name' => 'CRM License', 'price' => 250],
    ['sku' => 'HW-001', 'name' => 'IoT Device', 'price' => 1500],
    ['sku' => 'MKT-001', 'name' => 'Marketing Bundle', 'price' => 3000],
];

foreach ($productData as $p) {
    $products[] = Product::firstOrCreate(
        ['sku' => $p['sku']],
        [
            'name' => $p['name'],
            'price' => $p['price'],
            'description' => 'Test Product',
            'quantity' => 100,
        ]
    );
}

// 3. Create Persons
echo "Creating Persons...\n";
$persons = [];
$personNames = ['Alice Smith', 'Bob Jones', 'Charlie Brown', 'David Miller', 'Eve Wilson', 'Frank Thomas'];

foreach ($personNames as $index => $name) {
    $persons[] = Person::firstOrCreate(
        ['name' => $name],
        [
            'emails' => [['value' => strtolower(str_replace(' ', '.', $name)) . '@example.com', 'label' => 'work']],
            'contact_numbers' => [['value' => '100000000' . $index, 'label' => 'mobile']],
            'user_id' => $user->id,
        ]
    );
}

// 4. Create Leads (Won, Lost, Open)
echo "Creating Leads...\n";

// Helper to create a lead
function createTestLead($user, $person, $product, $stage, $source, $type, $isWon = false, $isLost = false, $dateOffset = 0)
{
    $createdAt = Carbon::now()->subDays($dateOffset);
    $closedAt = ($isWon || $isLost) ? $createdAt->copy()->addHours(rand(1, 48)) : null;

    // Assign pipeline stage
    $pipelineId = 1; // Default pipeline

    $lead = Lead::create([
        'title' => ($isWon ? 'Won' : ($isLost ? 'Lost' : 'Open')) . ' Deal - ' . $product->name,
        'description' => 'Auto-generated test lead',
        'lead_value' => $product->price,
        'status' => 1,
        'user_id' => $user->id,
        'person_id' => $person->id,
        'lead_pipeline_id' => $pipelineId,
        'lead_pipeline_stage_id' => $stage->id,
        'lead_type_id' => $type->id,
        'lead_source_id' => $source->id,
        'created_at' => $createdAt,
        'closed_at' => $closedAt,
        'expected_close_date' => Carbon::now()->addDays(10),
    ]);

    // Attach product
    Webkul\Lead\Models\Product::create([
        'lead_id' => $lead->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => $product->price,
        'amount' => $product->price,
    ]);
}

// Create 10 Won Leads (Spread over last 30 days)
for ($i = 0; $i < 10; $i++) {
    createTestLead(
        $user,
        $persons[array_rand($persons)],
        $products[array_rand($products)],
        $wonStage,
        $sources->random(),
        $types->random(),
        true,
        false,
        rand(0, 30)
    );
}

// Create 5 Lost Leads
for ($i = 0; $i < 5; $i++) {
    createTestLead(
        $user,
        $persons[array_rand($persons)],
        $products[array_rand($products)],
        $lostStage,
        $sources->random(),
        $types->random(),
        false,
        true,
        rand(0, 30)
    );
}

// Create 5 Open Leads
for ($i = 0; $i < 5; $i++) {
    createTestLead(
        $user,
        $persons[array_rand($persons)],
        $products[array_rand($products)],
        $newStage,
        $sources->random(),
        $types->random(),
        false,
        false,
        rand(0, 10)
    );
}

echo "Dashboard data created successfully! Refresh your dashboard.\n";
