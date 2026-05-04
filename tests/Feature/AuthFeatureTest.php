<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
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
    public function test_verify_username_redirects_to_access_request_if_not_found()
    {
        $response = $this->post('/verify-username', [
            'username' => 'user_tidak_dikenal',
        ]);

        $response->assertStatus(302);
        
        $response->assertSessionHas('login_username', 'user_tidak_dikenal');
        
        $response->assertRedirect('/request-access'); 
    }

    public function test_guest_can_submit_access_request_form()
    {
        $response = $this->post('/request-access', [
            'tiktok_username' => 'ridwan_tiktok',
            'phone_number' => '081234567890',
            'email' => 'ridwan@example.com',
        ]);

        $response->assertStatus(302); 
        $this->assertDatabaseHas('system_access_requests', [
            'tiktok_username' => 'ridwan_tiktok',
            'email' => 'ridwan@example.com',
            'status' => 'PENDING',
        ]);
    }

    public function test_user_cannot_login_with_incorrect_password()
    {
        $user = User::factory()->create([
            'username' => 'ridwan_valid',
            'password' => bcrypt('password_yang_benar'),
            'is_claimed' => true,
            'role' => 'AFFILIATOR' 
        ]);

        $response = $this->withSession(['login_username' => 'ridwan_valid'])
                         ->post('/verify-password', [
                             'password' => 'password_yang_salah',
                         ]);
        $response->assertStatus(302);
        $this->assertGuest();
        
        $response->assertSessionHasErrors(); 
        
        
        $response->assertSessionHas('login_username', 'ridwan_valid');
    }
    public function test_user_can_request_password_reset_link()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email'    => 'ridwan_affiliate@example.com',
            'is_claimed' => true,
        ]);

        $response = $this->withSession(['login_username' => 'ridwan_lupa_sandi'])
            ->post('/forgot-password', [
                'email'    => 'ridwan_affiliate@example.com',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
        $response->assertSessionHas('status');

        Notification::assertSentTo(
            [$user], ResetPasswordNotification::class
        );
    }
    public function test_system_rejects_unregistered_email_for_password_reset()
    {
        $response = $this->withSession(['login_username' => 'user_tidak_dikenal'])
            ->post('/forgot-password', [
                'email'    => 'email_tidak_dikenal@example.com',
                'username' => 'user_tidak_dikenal', 
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email'); 
    }
    public function test_user_can_reset_password_with_valid_token()
    {
        $user = User::factory()->create([
            'username' => 'ridwan_reset_sandi',
            'email' => 'ridwan_affiliate@example.com',
            'password' => bcrypt('password_lama_123'),
            'role' => 'AFFILIATOR',
            'is_claimed' => true,
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->withSession(['login_username' => 'ridwan_reset_sandi'])
                         ->post('/reset-password', [
                             'token' => $token,
                             'email' => 'ridwan_affiliate@example.com',
                             'username' => 'ridwan_reset_sandi',
                             'password' => 'PasswordBaruEleanor123!',
                             'password_confirmation' => 'PasswordBaruEleanor123!',
                         ]);

        $response->assertSessionHasNoErrors();
        $response->assertStatus(302); 
        $this->assertTrue(Hash::check('PasswordBaruEleanor123!', $user->fresh()->password));
    }
}