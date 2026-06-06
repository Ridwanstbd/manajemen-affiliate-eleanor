<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiddlewareAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create([
            'role'       => 'ADMINISTRATOR',
            'is_claimed' => true,
            'password'   => bcrypt('password'),
        ]);
    }

    private function makeAffiliator(): User
    {
        return User::factory()->create([
            'role'           => 'AFFILIATOR',
            'is_claimed'     => true,
            'account_status' => 'ACTIVE',
            'password'       => bcrypt('password'),
        ]);
    }


    public function test_guest_cannot_access_admin_dashboard()
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_affiliator_area()
    {
        $response = $this->get('/affiliator');

        $response->assertRedirect('/login');
    }


    public function test_affiliator_cannot_access_admin_dashboard()
    {
        $affiliator = $this->makeAffiliator();

        $response = $this->actingAs($affiliator)->get('/dashboard');

        $response->assertStatus(403);
    }

    public function test_affiliator_cannot_access_admin_user_management()
    {
        $affiliator = $this->makeAffiliator();

        $response = $this->actingAs($affiliator)->get('/dashboard/users');

        $response->assertStatus(403);
    }

    public function test_affiliator_cannot_access_admin_analytics()
    {
        $affiliator = $this->makeAffiliator();

        $response = $this->actingAs($affiliator)->get('/dashboard/analytics');

        $response->assertStatus(403);
    }

    public function test_affiliator_cannot_access_admin_import()
    {
        $affiliator = $this->makeAffiliator();

        $response = $this->actingAs($affiliator)->get('/dashboard/import-data');

        $response->assertStatus(403);
    }

    public function test_affiliator_cannot_access_admin_products()
    {
        $affiliator = $this->makeAffiliator();

        $response = $this->actingAs($affiliator)->get('/dashboard/product');

        $response->assertStatus(403);
    }

    public function test_affiliator_cannot_access_admin_request_samples()
    {
        $affiliator = $this->makeAffiliator();

        $response = $this->actingAs($affiliator)->get('/dashboard/request-samples');

        $response->assertStatus(403);
    }

    public function test_affiliator_cannot_access_admin_challenge()
    {
        $affiliator = $this->makeAffiliator();

        $response = $this->actingAs($affiliator)->get('/dashboard/challenge');

        $response->assertStatus(403);
    }

    public function test_affiliator_cannot_access_admin_task_monitoring()
    {
        $affiliator = $this->makeAffiliator();

        $response = $this->actingAs($affiliator)->get('/dashboard/task-monitoring');

        $response->assertStatus(403);
    }

    public function test_affiliator_cannot_post_to_admin_approve_access()
    {
        $affiliator = $this->makeAffiliator();

        $response = $this->actingAs($affiliator)->post('/dashboard/users/approve-access', [
            'id' => 1,
        ]);

        $response->assertStatus(403);
    }

    public function test_affiliator_cannot_post_to_admin_blacklist()
    {
        $affiliator = $this->makeAffiliator();

        $response = $this->actingAs($affiliator)->post('/dashboard/users/store-blacklist', [
            'user_id'          => 1,
            'violation_reason' => 'Test',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_affiliator_dashboard()
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/affiliator');

        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_affiliator_catalog()
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/affiliator/catalog');

        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_affiliator_cart()
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/affiliator/cart');

        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_affiliator_sample_request()
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/affiliator/sample-request');

        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_affiliator_task()
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/affiliator/task');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard()
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_affiliator_can_access_affiliator_dashboard()
    {
        $affiliator = $this->makeAffiliator();

        $response = $this->actingAs($affiliator)->get('/affiliator');

        $response->assertStatus(200);
    }

    public function test_banned_affiliator_cannot_access_catalog()
    {
        $banned = User::factory()->create([
            'role'           => 'AFFILIATOR',
            'is_claimed'     => true,
            'account_status' => 'BANNED',
        ]);

        \App\Models\Blacklist::create([
            'user_id'          => $banned->id,
            'blacklist_date'   => now(),
            'violation_reason' => 'Pelanggaran tes',
        ]);

        $response = $this->actingAs($banned)->get('/affiliator/catalog');

        $response->assertRedirect('/affiliator');
        $response->assertSessionHas('error');
    }

    public function test_active_affiliator_can_access_catalog()
    {
        $affiliator = $this->makeAffiliator();

        $response = $this->actingAs($affiliator)->get('/affiliator/catalog');

        $response->assertStatus(200);
    }
}