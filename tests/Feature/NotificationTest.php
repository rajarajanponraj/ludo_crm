<?php

namespace Tests\Feature;

use Tests\TestCase;
use Webkul\User\Models\User;
use Webkul\User\Models\Role;
use Webkul\SAAS\Models\Company;
use Webkul\Contact\Models\Person;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductInventory;
use Webkul\Warehouse\Models\Warehouse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class NotificationTest extends TestCase
{
    protected $company;
    protected $agent;
    protected $role;
    protected $person;
    protected $product;
    protected $inventory;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Company & Role
        $this->company = Company::firstOrCreate(
            ['domain' => 'notify-test-company'],
            ['name' => 'Notify Test Company', 'status' => 1]
        );

        $this->role = Role::firstOrCreate(['name' => 'Field Agent'], [
            'name' => 'Field Agent',
            'permission_type' => 'custom',
            'permissions' => []
        ]);

        // 2. Agent
        $this->agent = User::withoutGlobalScopes()->where('email', 'agent@notify.test')->first();
        if (!$this->agent) {
            $this->agent = User::create([
                'email' => 'agent@notify.test',
                'name' => 'Notify Agent',
                'password' => Hash::make('password'),
                'company_id' => $this->company->id,
                'status' => 1,
                'role_id' => $this->role->id,
            ]);
        }
        if ($this->agent->company_id != $this->company->id)
            $this->agent->update(['company_id' => $this->company->id]);

        // 3. Person
        $this->person = Person::firstOrCreate(['name' => 'Notify Customer'], [
            'company_id' => $this->company->id
        ]);

        // 4. Product & Inventory
        $this->product = Product::firstOrCreate(['sku' => 'TEST-NOTIFY-1'], [
            'name' => 'Notify Product',
            'quantity' => 100,
            'price' => 10,
            'company_id' => $this->company->id
        ]);

        $warehouse = Warehouse::firstOrCreate(['name' => 'Main Warehouse'], [
            'contact_name' => 'Warehouse Mgr',
            'contact_email' => 'warehouse@test.com',
            'contact_numbers' => '1234567890',
            'contact_address' => '123 Warehouse St',
            'company_id' => $this->company->id
        ]);

        ProductInventory::where('product_id', $this->product->id)->delete();
        ProductInventory::create([
            'product_id' => $this->product->id,
            'warehouse_id' => $warehouse->id,
            'in_stock' => 100,
            'allocated' => 0
        ]);
    }

    /** @test */
    public function order_creation_triggers_notification_event()
    {
        Event::fake();

        $response = $this->actingAs($this->agent, 'sanctum')
            ->postJson(route('field-sales.api.orders.store'), [
                'person_id' => $this->person->id,
                'type' => 'primary',
                'delivery_date' => now()->addDay()->format('Y-m-d'),
                'items' => [
                    [
                        'product_id' => $this->product->id,
                        'qty' => 1,
                        'price' => 10
                    ]
                ]
            ]);

        $response->assertStatus(201);

        Event::assertDispatched('field_sales.order.created');
    }
}
