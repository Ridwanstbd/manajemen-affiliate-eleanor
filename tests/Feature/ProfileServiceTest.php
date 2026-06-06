<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\Affiliator\ProfileService;
use Tests\TestCase;
class ProfileServiceTest extends TestCase
{
    private ProfileService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProfileService();
    }
    private function makeUser(array $attributes = []): User
    {
        $user = new User();
        $user->forceFill(array_merge([
            'username'       => 'test_user',
            'email'          => 'test@example.com',
            'phone_number'   => '081234567890',
            'account_status' => 'ACTIVE',
            'role'           => 'AFFILIATOR',
        ], $attributes));

        return $user;
    }

    public function test_active_status_returns_label_akun_aktif()
    {
        $user = $this->makeUser(['account_status' => 'ACTIVE']);
        $this->actingAs($user);

        $result = $this->service->getProfileData();

        $this->assertSame('Akun Aktif', $result['status']);
    }

    public function test_pending_status_returns_label_menunggu_persetujuan()
    {
        $user = $this->makeUser(['account_status' => 'PENDING']);
        $this->actingAs($user);

        $result = $this->service->getProfileData();

        $this->assertSame('Menunggu Persetujuan', $result['status']);
    }

    public function test_banned_status_returns_label_akun_diblokir()
    {
        $user = $this->makeUser(['account_status' => 'BANNED']);
        $this->actingAs($user);

        $result = $this->service->getProfileData();

        $this->assertSame('Akun Diblokir', $result['status']);
    }

    public function test_unknown_status_returns_ucfirst_lowercase()
    {
        $user = $this->makeUser(['account_status' => 'SUSPENDED']);
        $this->actingAs($user);

        $result = $this->service->getProfileData();

        $this->assertSame('Suspended', $result['status']);
    }

    public function test_lowercase_unknown_status_is_formatted_correctly()
    {
        $user = $this->makeUser(['account_status' => 'inactive']);
        $this->actingAs($user);

        $result = $this->service->getProfileData();

        $this->assertSame('Inactive', $result['status']);
    }

    public function test_username_without_at_sign_gets_prepended()
    {
        $user = $this->makeUser(['username' => 'ridwan_affiliate']);
        $this->actingAs($user);

        $result = $this->service->getProfileData();

        $this->assertSame('@ridwan_affiliate', $result['username']);
    }

    public function test_username_with_at_sign_is_not_duplicated()
    {
        $user = $this->makeUser(['username' => '@ridwan_affiliate']);
        $this->actingAs($user);

        $result = $this->service->getProfileData();

        $this->assertSame('@ridwan_affiliate', $result['username']);
        $this->assertStringStartsNotWith('@@', $result['username']);
    }

    public function test_email_is_returned_when_set()
    {
        $user = $this->makeUser(['email' => 'ridwan@tiktok.com']);
        $this->actingAs($user);

        $result = $this->service->getProfileData();

        $this->assertSame('ridwan@tiktok.com', $result['email']);
    }

    public function test_null_email_returns_fallback_text()
    {
        $user = $this->makeUser(['email' => null]);
        $this->actingAs($user);

        $result = $this->service->getProfileData();

        $this->assertSame('Belum mengatur email', $result['email']);
    }

    public function test_phone_is_returned_when_set()
    {
        $user = $this->makeUser(['phone_number' => '081234567890']);
        $this->actingAs($user);

        $result = $this->service->getProfileData();

        $this->assertSame('081234567890', $result['phone']);
    }

    public function test_null_phone_returns_fallback_text()
    {
        $user = $this->makeUser(['phone_number' => null]);
        $this->actingAs($user);

        $result = $this->service->getProfileData();

        $this->assertSame('Belum mengatur nomor HP', $result['phone']);
    }

    public function test_return_value_has_correct_array_keys()
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $result = $this->service->getProfileData();

        $this->assertArrayHasKey('username', $result);
        $this->assertArrayHasKey('email',    $result);
        $this->assertArrayHasKey('phone',    $result);
        $this->assertArrayHasKey('status',   $result);
        $this->assertCount(4, $result);
    }
    public function test_all_values_are_strings()
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $result = $this->service->getProfileData();

        foreach ($result as $key => $value) {
            $this->assertIsString($value, "Key '{$key}' seharusnya bertipe string");
        }
    }

    public function test_all_nullable_fields_null_returns_all_fallbacks()
    {
        $user = $this->makeUser([
            'email'          => null,
            'phone_number'   => null,
            'account_status' => 'UNKNOWN_STATUS',
            'username'       => 'bareusername',
        ]);
        $this->actingAs($user);

        $result = $this->service->getProfileData();

        $this->assertSame('@bareusername',      $result['username']);
        $this->assertSame('Belum mengatur email',     $result['email']);
        $this->assertSame('Belum mengatur nomor HP',  $result['phone']);
        $this->assertSame('Unknown_status',           $result['status']);
    }
}