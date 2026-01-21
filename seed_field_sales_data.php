<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Webkul\Contact\Models\Person;
use Webkul\User\Models\User;
use Webkul\FieldSales\Models\Order;
use Webkul\FieldSales\Models\Collection;
use Webkul\FieldSales\Models\Target;
use Webkul\User\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

echo "Starting Field Sales Data Seeding...\n";

// 1. Create/Get Field Sales Role
$role = Role::firstOrCreate(
    ['name' => 'Field Sales Agent'],
    ['description' => 'Field Sales Agent Role', 'permission_type' => 'custom']
);

// 2. Create Agents
$agents = [];
$agentNames = ['Stock Agent', 'Notify Agent', 'Alice Walker', 'Bob Builder', 'Charlie Chap'];

echo "Creating Agents...\n";
foreach ($agentNames as $name) {
    $email = strtolower(str_replace(' ', '.', $name)) . '@example.com';
    $agent = User::firstOrCreate(
        ['email' => $email],
        [
            'name' => $name,
            'password' => Hash::make('admin123'),
            'status' => 1,
            'role_id' => $role->id,
        ]
    );
    $agents[] = $agent;
}

// 3. Create Persons (Customers)
echo "Creating Customers...\n";
$customers = [];
for ($i = 0; $i < 10; $i++) {
    $customers[] = Person::firstOrCreate(
        ['name' => 'Customer ' . ($i + 1)],
        [
            'emails' => [['value' => "customer{$i}@test.com", 'label' => 'work']],
            'contact_numbers' => [['value' => '987654321' . $i, 'label' => 'mobile']],
            'user_id' => $agents[0]->id, // Assign to first agent initially
        ]
    );
}

// 4. Create Targets (For this month)
echo "Creating Targets...\n";
$startDate = Carbon::now()->startOfMonth();
$endDate = Carbon::now()->endOfMonth();

foreach ($agents as $agent) {
    // Sales Target
    Target::updateOrCreate(
        [
            'user_id' => $agent->id,
            'type' => 'sales_amount',
            'start_date' => $startDate,
            'end_date' => $endDate,
        ],
        [
            'target_value' => rand(50000, 100000),
            'company_id' => 1,
        ]
    );

    // Visit Target
    Target::updateOrCreate(
        [
            'user_id' => $agent->id,
            'type' => 'visit_count',
            'start_date' => $startDate,
            'end_date' => $endDate,
        ],
        [
            'target_value' => rand(20, 50),
            'company_id' => 1,
        ]
    );
}

// 5. Create Orders
echo "Creating Orders...\n";
$statuses = ['pending', 'approved', 'rejected', 'completed'];

// Create some for today (for the cards)
foreach ($agents as $agent) {
    // Today's orders
    if (rand(0, 1)) {
        Order::create([
            'user_id' => $agent->id,
            'person_id' => $customers[array_rand($customers)]->id,
            'grand_total' => rand(1000, 5000),
            'status' => $statuses[rand(0, 3)],
            'created_at' => Carbon::now(), // Today
            'delivery_date' => Carbon::now()->addDays(rand(1, 5)),
            'company_id' => 1,
        ]);
    }

    // Past orders (this month)
    for ($k = 0; $k < rand(3, 8); $k++) {
        Order::create([
            'user_id' => $agent->id,
            'person_id' => $customers[array_rand($customers)]->id,
            'grand_total' => rand(500, 8000),
            'status' => $statuses[rand(0, 3)],
            'created_at' => Carbon::now()->subDays(rand(1, 25)),
            'delivery_date' => Carbon::now()->addDays(rand(1, 5)),
            'company_id' => 1,
        ]);
    }
}

// Ensure some pending dispatches specifically
Order::create([
    'user_id' => $agents[0]->id,
    'person_id' => $customers[0]->id,
    'grand_total' => 2500,
    'status' => 'pending',
    'created_at' => Carbon::now()->subHours(2),
    'company_id' => 1,
]);

Order::create([
    'user_id' => $agents[1]->id,
    'person_id' => $customers[1]->id,
    'grand_total' => 3500,
    'status' => 'approved', // Approved but not dispatched/completed counts as pending dispatch usually? 
    // Check controller logic: status pending OR approved
    'created_at' => Carbon::now()->subHours(5),
    'company_id' => 1,
]);


// 6. Create Collections
echo "Creating Collections...\n";
foreach ($agents as $agent) {
    // Today's collections
    if (rand(0, 1)) {
        Collection::create([
            'user_id' => $agent->id,
            'person_id' => $customers[array_rand($customers)]->id,
            'amount' => rand(500, 2000),
            'payment_mode' => 'cash',
            'collected_at' => Carbon::now(),
            'company_id' => 1,
        ]);
    }

    // Past collections
    for ($m = 0; $m < rand(1, 5); $m++) {
        Collection::create([
            'user_id' => $agent->id,
            'person_id' => $customers[array_rand($customers)]->id,
            'amount' => rand(500, 5000),
            'payment_mode' => 'cash',
            'collected_at' => Carbon::now()->subDays(rand(1, 20)),
            'company_id' => 1,
        ]);
    }
}

echo "Field Sales Data seeding completed!\n";
