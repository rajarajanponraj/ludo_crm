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

// Ensure we have a user
$user = User::first();
if (!$user) {
    echo "No admin user found. Please run migrations/seeds first.\n";
    exit;
}

// 1. Create Products
echo "Creating Products...\n";
$product1 = Product::firstOrCreate(
    ['sku' => 'SRV-001'],
    [
        'name' => 'Premium Consultancy',
        'price' => 5000,
        'description' => 'High-end business consultancy integration',
        'quantity' => 100,
    ]
);

$product2 = Product::firstOrCreate(
    ['sku' => 'SRV-002'],
    [
        'name' => 'Basic Support Plan',
        'price' => 1200,
        'description' => 'Monthly support and maintenance',
        'quantity' => 100,
    ]
);

// 2. Create Persons
echo "Creating Persons...\n";
// Note: Person doesn't have simple unique constraint on name, but we can check name + user_id or similar.
// For simplicity, we just check name here to avoid gross duplication in this script context.
$person1 = Person::firstOrCreate(
    ['name' => 'Alice Smith'],
    [
        'emails' => [['value' => 'alice@example.com', 'label' => 'work']],
        'contact_numbers' => [['value' => '1234567890', 'label' => 'mobile']],
        'user_id' => $user->id,
    ]
);

$person2 = Person::firstOrCreate(
    ['name' => 'Bob Jones'],
    [
        'emails' => [['value' => 'bob@example.com', 'label' => 'work']],
        'contact_numbers' => [['value' => '0987654321', 'label' => 'mobile']],
        'user_id' => $user->id,
    ]
);

// 3. Create Leads
echo "Creating Leads...\n";
$lead1 = Lead::create([
    'title' => 'Consultancy Inquiry',
    'description' => 'Interested in premium plan',
    'lead_value' => 5000,
    'status' => 1,
    'user_id' => $user->id,
    'person_id' => $person1->id,
    'lead_pipeline_id' => 1,
    'lead_pipeline_stage_id' => 1, // New
    'lead_type_id' => 1, // Assuming default exists
    'lead_source_id' => 1, // Assuming default exists
    'created_at' => now(),
]);

// Attach product to lead
Webkul\Lead\Models\Product::create([
    'lead_id' => $lead1->id,
    'product_id' => $product1->id,
    'quantity' => 1,
    'price' => 5000,
    'amount' => 5000,
]);


$lead2 = Lead::create([
    'title' => 'Support Request',
    'description' => 'Needs monthly support',
    'lead_value' => 1200,
    'status' => 1,
    'user_id' => $user->id,
    'person_id' => $person2->id,
    'lead_pipeline_id' => 1,
    'lead_pipeline_stage_id' => 1,
    'lead_type_id' => 1,
    'lead_source_id' => 1,
    'created_at' => now(),
]);

Webkul\Lead\Models\Product::create([
    'lead_id' => $lead2->id,
    'product_id' => $product2->id,
    'quantity' => 1,
    'price' => 1200,
    'amount' => 1200,
]);

echo "Dashboard data seeded successfully!\n";
