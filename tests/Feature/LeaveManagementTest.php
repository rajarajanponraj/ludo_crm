<?php

namespace Tests\Feature;

use Tests\TestCase;
use Webkul\User\Models\User;
use Webkul\User\Models\Role;
use Webkul\SAAS\Models\Company;
use Webkul\FieldSales\Models\Leave;
use Illuminate\Support\Facades\Hash;

class LeaveManagementTest extends TestCase
{
    protected $company;
    protected $admin;
    protected $agent;
    protected $adminRole;
    protected $agentRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Company & Roles similar to FieldSalesTest
        $this->company = Company::firstOrCreate(
            ['domain' => 'leave-test-company'],
            ['name' => 'Leave Test Company', 'status' => 1]
        );

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

        $this->admin = User::withoutGlobalScopes()->where('email', 'admin@leave.test')->first();
        if (!$this->admin) {
            $this->admin = User::create([
                'email' => 'admin@leave.test',
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'company_id' => $this->company->id,
                'status' => 1,
                'role_id' => $this->adminRole->id
            ]);
        }
        if ($this->admin->company_id != $this->company->id)
            $this->admin->update(['company_id' => $this->company->id]);

        $this->agent = User::withoutGlobalScopes()->where('email', 'agent@leave.test')->first();
        if (!$this->agent) {
            $this->agent = User::create([
                'email' => 'agent@leave.test',
                'name' => 'Agent User',
                'password' => Hash::make('password'),
                'company_id' => $this->company->id,
                'status' => 1,
                'role_id' => $this->agentRole->id,
                'reports_to' => $this->admin->id
            ]);
        }
        if ($this->agent->company_id != $this->company->id)
            $this->agent->update(['company_id' => $this->company->id]);
    }

    /** @test */
    public function agent_can_apply_for_leave()
    {
        $response = $this->actingAs($this->agent, 'sanctum')
            ->postJson(route('field-sales.api.leaves.store'), [
                'start_date' => now()->addDays(1)->format('Y-m-d'),
                'end_date' => now()->addDays(2)->format('Y-m-d'),
                'type' => 'casual',
                'reason' => 'Family Function'
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('field_leaves', [
            'user_id' => $this->agent->id,
            'reason' => 'Family Function',
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function agent_can_list_leaves()
    {
        Leave::create([
            'company_id' => $this->company->id,
            'user_id' => $this->agent->id,
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(6),
            'type' => 'sick',
            'status' => 'approved'
        ]);

        $response = $this->actingAs($this->agent, 'sanctum')
            ->getJson(route('field-sales.api.leaves.index'));

        $response->assertStatus(200)
            ->assertJsonFragment(['type' => 'sick']);
    }

    /** @test */
    public function admin_can_approve_leave()
    {
        $leave = Leave::create([
            'company_id' => $this->company->id,
            'user_id' => $this->agent->id,
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(11),
            'type' => 'privilege',
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin, 'user')
            ->put(route('field_sales.admin.leaves.update', $leave->id), [
                'status' => 'approved'
            ]);

        $response->assertRedirect(); // Back

        $this->assertDatabaseHas('field_leaves', [
            'id' => $leave->id,
            'status' => 'approved',
            'approved_by' => $this->admin->id
        ]);
    }
}
