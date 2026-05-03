<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase; 
    public function test_verify_username_redirects_to_password_input_if_valid()
    {
        $user = User::factory()->create([
            'username' => 'ridwan_valid',
            'is_claimed' => true,
            'password' => bcrypt('password123') 
        ]);

        $response = $this->post('/verify-username', [
            'username' => 'ridwan_valid',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('login_username', 'ridwan_valid');
    }

    public function test_admin_can_login_and_redirect_to_dashboard()
    {
        $this->withoutExceptionHandling();

        $admin = User::factory()->create([
            'username' => 'admin_ridwan',
            'password' => bcrypt('secret123'),
            'role' => 'ADMINISTRATOR',
            'is_claimed' => true,
        ]);

        $response = $this->withSession(['login_username' => 'admin_ridwan'])
                         ->post('/verify-password', [
                             'password' => 'secret123',
                         ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect('/dashboard');
        $response->assertSessionMissing('login_username');
    }

    public function test_verify_username_redirects_to_claim_form_if_not_claimed()
    {
        $user = User::factory()->create([
            'username' => 'affiliator_baru',
            'is_claimed' => false, 
        ]);

        $response = $this->post('/verify-username', [
            'username' => 'affiliator_baru',
        ]);

        $response->assertStatus(302);
        
        $response->assertSessionHas('claim_username', 'affiliator_baru');
        
        $response->assertRedirect('/claim'); 
    }
}