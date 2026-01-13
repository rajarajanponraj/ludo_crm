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
use Illuminate\Support\Facades\DB;

class StockManagementTest extends TestCase
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
            ['domain' => 'stock-test-company'],
            ['name' => 'Stock Test Company', 'status' => 1]
        );

        $this->role = Role::firstOrCreate(['name' => 'Field Agent'], [
            'name' => 'Field Agent',
            'permission_type' => 'custom',
            'permissions' => []
        ]);

        // 2. Agent
        $this->agent = User::withoutGlobalScopes()->where('email', 'agent@stock.test')->first();
        if (!$this->agent) {
            $this->agent = User::create([
                'email' => 'agent@stock.test',
                'name' => 'Stock Agent',
                'password' => Hash::make('password'),
                'company_id' => $this->company->id,
                'status' => 1,
                'role_id' => $this->role->id,
            ]);
        }
        if ($this->agent->company_id != $this->company->id)
            $this->agent->update(['company_id' => $this->company->id]);

        // 3. Person
        $this->person = Person::firstOrCreate(['name' => 'Stock Customer'], [
            'company_id' => $this->company->id
        ]);

        // 4. Product & Inventory
        $this->product = Product::firstOrCreate(['sku' => 'TEST-STOCK-1'], [
            'name' => 'Test Product',
            'quantity' => 20, // Cache
            'price' => 10,
            'company_id' => $this->company->id
        ]);

        // Ensure Warehouse Exists
        $warehouse = Warehouse::firstOrCreate(['name' => 'Main Warehouse'], [
            'contact_name' => 'Warehouse Mgr',
            'contact_email' => 'warehouse@test.com',
            'contact_numbers' => '1234567890',
            'contact_address' => '123 Warehouse St',
            'company_id' => $this->company->id // Assuming shared or specific
        ]);

        ProductInventory::where('product_id', $this->product->id)->delete();

        $this->inventory = ProductInventory::create([
            'product_id' => $this->product->id,
            'warehouse_id' => $warehouse->id,
            'in_stock' => 10,
            'allocated' => 0
        ]);
    }

    /** @test */
    public function order_decrements_stock()
    {
        $response = $this->actingAs($this->agent, 'sanctum')
            ->postJson(route('field-sales.api.orders.store'), [
                'person_id' => $this->person->id,
                'type' => 'primary',
                'delivery_date' => now()->addDay()->format('Y-m-d'),
                'items' => [
                    [
                        'product_id' => $this->product->id,
                        'qty' => 5,
                        'price' => 10
                    ]
                ]
            ]);

        $response->assertStatus(201);

        // Verify Inventory Decremented
        $this->inventory->refresh();
        $this->assertEquals(5, $this->inventory->in_stock);

        // Verify Product Cache Decremented
        $this->product->refresh();
        $this->assertEquals(15, $this->product->quantity);
    }

    /** @test */
    public function order_fails_if_insufficient_stock()
    {
        $response = $this->actingAs($this->agent, 'sanctum')
            ->postJson(route('field-sales.api.orders.store'), [
                'person_id' => $this->person->id,
                'type' => 'primary',
                'delivery_date' => now()->addDay()->format('Y-m-d'),
                'items' => [
                    [
                        'product_id' => $this->product->id,
                        'qty' => 15, // > 10
                        'price' => 10
                    ]
                ]
            ]);

        $response->assertStatus(400)
            ->assertJsonFragment(['message' => 'Failed to create order.']);
    }
}
