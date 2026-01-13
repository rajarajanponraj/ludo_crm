<?php

namespace Tests\Feature;

use Tests\TestCase;
use Webkul\User\Models\User;
use Webkul\User\Models\Role;
use Webkul\SAAS\Models\Company;
use Webkul\Contact\Models\Person;
use Illuminate\Support\Facades\Hash;

class GeoFencingTest extends TestCase
{
    protected $company;
    protected $agent;
    protected $role;
    protected $person;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Company & Role
        $this->company = Company::firstOrCreate(
            ['domain' => 'geo-test-company'],
            ['name' => 'Geo Test Company', 'status' => 1]
        );

        $this->role = Role::firstOrCreate(['name' => 'Field Agent'], [
            'name' => 'Field Agent',
            'permission_type' => 'custom',
            'permissions' => []
        ]);

        // 2. Agent
        $this->agent = User::withoutGlobalScopes()->where('email', 'agent@geo.test')->first();
        if (!$this->agent) {
            $this->agent = User::create([
                'email' => 'agent@geo.test',
                'name' => 'Geo Agent',
                'password' => Hash::make('password'),
                'company_id' => $this->company->id,
                'status' => 1,
                'role_id' => $this->role->id,
            ]);
        }
        if ($this->agent->company_id != $this->company->id)
            $this->agent->update(['company_id' => $this->company->id]);

        // 3. Person (Customer)
        $this->person = Person::firstOrCreate(['name' => 'Geo Customer'], [
            'company_id' => $this->company->id,
            // Initially no location
        ]);
        $this->person->update(['latitude' => null, 'longitude' => null]);

        // Clear ongoing visits for this agent
        \Webkul\FieldSales\Models\Visit::where('user_id', $this->agent->id)->delete();
    }

    /** @test */
    public function first_check_in_sets_customer_location()
    {
        $response = $this->actingAs($this->agent, 'sanctum')
            ->postJson(route('field-sales.api.visits.check-in'), [
                'person_id' => $this->person->id,
                'latitude' => 12.9715987,
                'longitude' => 77.5945627
            ]);

        $response->assertStatus(201);

        $this->person->refresh();
        $this->assertNotNull($this->person->latitude);
        $this->assertEquals(12.9715987, $this->person->latitude);
    }

    /** @test */
    public function check_in_fails_if_too_far()
    {
        // Set Customer Location
        $this->person->update([
            'latitude' => 12.9715987,
            'longitude' => 77.5945627
        ]);

        // Agent is 1km away
        // 12.98 ~ 1.1km away from 12.97
        $response = $this->actingAs($this->agent, 'sanctum')
            ->postJson(route('field-sales.api.visits.check-in'), [
                'person_id' => $this->person->id,
                'latitude' => 12.9815987,
                'longitude' => 77.5945627
            ]);

        $response->assertStatus(400)
            ->assertJsonFragment(['message' => 'You are too far from the customer location.']);
    }

    /** @test */
    public function check_in_succeeds_if_nearby()
    {
        // Set Customer Location
        $this->person->update([
            'latitude' => 12.9715987,
            'longitude' => 77.5945627
        ]);

        // Agent is very close (same point)
        $response = $this->actingAs($this->agent, 'sanctum')
            ->postJson(route('field-sales.api.visits.check-in'), [
                'person_id' => $this->person->id,
                'latitude' => 12.9716000,
                'longitude' => 77.5945600
            ]);

        $response->assertStatus(201);
    }
}
