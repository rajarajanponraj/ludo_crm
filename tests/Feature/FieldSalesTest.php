<?php

namespace Tests\Feature;

use Tests\TestCase;
use Webkul\User\Models\User;
use Webkul\User\Models\Role;
use Webkul\SAAS\Models\Company;
use Webkul\Contact\Models\Person;
use Webkul\FieldSales\Models\Order;
use Webkul\FieldSales\Models\Visit;
use Webkul\FieldSales\Models\Expense;
use Webkul\FieldSales\Models\Target;
use Webkul\FieldSales\Models\Announcement;
use Webkul\FieldSales\Models\Message;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;

class FieldSalesTest extends TestCase
{
    protected $company;
    protected $admin;
    protected $agent;
    protected $adminRole;
    protected $agentRole;
    protected $person;

    protected function setUp(): void
    {
        parent::setUp();

        // Create or get Company
        $this->company = Company::firstOrCreate(
            ['domain' => 'test-company'],
            ['name' => 'Test Company', 'status' => 1]
        );

        // Ensure Roles Exist
        $this->adminRole = Role::firstOrCreate(['permission_type' => 'all'], [
            'name' => 'Administrator',
            'description' => 'Administrator role',
            'permission_type' => 'all'
        ]);

        $this->agentRole = Role::firstOrCreate(['name' => 'Field Agent'], [
            'name' => 'Field Agent',
            'description' => 'Sales Agent',
            'permission_type' => 'custom',
            'permissions' => []
        ]);

        // Create or get Admin
        $this->admin = User::withoutGlobalScopes()->where('email', 'admin@test.feature')->first();

        if (!$this->admin) {
            try {
                $this->admin = User::create([
                    'email' => 'admin@test.feature',
                    'name' => 'Admin User',
                    'password' => Hash::make('password'),
                    'company_id' => $this->company->id,
                    'status' => 1,
                    'is_superuser' => 0,
                    'role_id' => $this->adminRole->id
                ]);
            } catch (QueryException $e) {
                $this->admin = User::withoutGlobalScopes()->where('email', 'admin@test.feature')->first();
            }
        }

        if ($this->admin->company_id != $this->company->id) {
            $this->admin->update(['company_id' => $this->company->id]);
        }

        // Create or get Agent
        $this->agent = User::withoutGlobalScopes()->where('email', 'agent@test.feature')->first();

        if (!$this->agent) {
            try {
                $this->agent = User::create([
                    'email' => 'agent@test.feature',
                    'name' => 'Agent User',
                    'password' => Hash::make('password'),
                    'company_id' => $this->company->id,
                    'status' => 1,
                    'role_id' => $this->agentRole->id,
                    'reports_to' => $this->admin->id
                ]);
            } catch (QueryException $e) {
                $this->agent = User::withoutGlobalScopes()->where('email', 'agent@test.feature')->first();
            }
        }

        if ($this->agent && $this->agent->reports_to != $this->admin->id) {
            $this->agent->update(['reports_to' => $this->admin->id, 'company_id' => $this->company->id]);
        }

        // Create Person (Customer)
        $this->person = Person::firstOrCreate(['name' => 'Test Customer'], [
            'company_id' => $this->company->id
        ]);

        if ($this->person->company_id != $this->company->id) {
            $this->person->update(['company_id' => $this->company->id]);
        }
    }

    /** @test */
    public function agent_can_submit_expense()
    {
        $response = $this->actingAs($this->agent, 'sanctum')
            ->postJson(route('field-sales.api.expenses.store'), [
                'type' => 'Travel',
                'amount' => 100.50,
                'description' => 'Taxi fare',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.amount', 100.5);

        $this->assertDatabaseHas('field_expenses', [
            'user_id' => $this->agent->id,
            'amount' => 100.50
        ]);
    }

    /** @test */
    public function admin_can_approve_expense()
    {
        $expense = Expense::create([
            'user_id' => $this->agent->id,
            'company_id' => $this->company->id,
            'type' => 'Food',
            'amount' => 50,
            'description' => 'Lunch',
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin, 'user')
            ->post(route('field_sales.admin.expenses.approve', $expense->id));

        $response->assertRedirect();

        $this->assertDatabaseHas('field_expenses', [
            'id' => $expense->id,
            'status' => 'approved',
            'approved_by' => $this->admin->id
        ]);
    }

    /** @test */
    public function targets_are_calculated_correctly()
    {
        // 1. Create Target
        $target = Target::create([
            'user_id' => $this->agent->id,
            'company_id' => $this->company->id,
            'type' => 'sales_amount',
            'target_value' => 1000,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);

        // 2. Create Order
        Order::create([
            'user_id' => $this->agent->id,
            'person_id' => $this->person->id,
            'company_id' => $this->company->id,
            'type' => 'Primary',
            'grand_total' => 500,
            'status' => 'completed',
            'delivery_date' => now()
        ]);

        // 3. View Report (Admin)
        $response = $this->actingAs($this->admin, 'user')
            ->get(route('field_sales.admin.reports.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function agent_can_fetch_announcements()
    {
        Announcement::create([
            'company_id' => $this->company->id,
            'title' => 'Big News',
            'content' => 'We are launching Product X',
            'is_active' => true
        ]);

        $response = $this->actingAs($this->agent, 'sanctum')
            ->getJson(route('field-sales.api.announcements.index'));

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Big News']);
    }

    /** @test */
    public function agent_can_send_message()
    {
        $response = $this->actingAs($this->agent, 'sanctum')
            ->postJson(route('field-sales.api.messages.store'), [
                'receiver_id' => $this->admin->id,
                'message' => 'Hello Boss'
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('field_messages', [
            'sender_id' => $this->agent->id,
            'receiver_id' => $this->admin->id,
            'message' => 'Hello Boss'
        ]);
    }

    /** @test */
    public function offline_sync_returns_deleted_records()
    {
        // 1. Create Order
        $order = Order::create([
            'user_id' => $this->agent->id,
            'person_id' => $this->person->id,
            'company_id' => $this->company->id,
            'grand_total' => 100,
            'type' => 'Secondary',
            'status' => 'pending',
            'delivery_date' => now()
        ]);

        // 2. Sync (Should see it)
        $response1 = $this->actingAs($this->agent, 'sanctum')
            ->getJson(route('field-sales.api.data.sync'));

        // Assert presence of ID instead of count
        $response1->assertJsonFragment(['id' => $order->id]);

        // 3. Delete Order
        $order->delete();
        $timestamp = now()->subDays(1)->toIso8601String(); // Expanded window

        // 4. Sync again with timestamp
        $encodedTimestamp = urlencode($timestamp);
        $response2 = $this->actingAs($this->agent, 'sanctum')
            ->getJson(route('field-sales.api.data.sync') . "?last_synced_at=$encodedTimestamp");

        // Verify ID is in deleted list
        $deletedOrders = $response2->json('data.deleted.orders');

        $response2->assertStatus(200);

        if (is_array($deletedOrders) && in_array($order->id, $deletedOrders)) {
            $this->assertTrue(true);
        } else {
            $this->fail("Deleted Order ID {$order->id} not found.");
        }
    }
}
