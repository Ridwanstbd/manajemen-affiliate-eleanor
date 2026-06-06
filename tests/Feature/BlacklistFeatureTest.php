<?php

namespace Tests\Feature\Admin;

use App\Models\Blacklist;
use App\Models\User;
use App\Services\Admin\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlacklistFeatureTest extends TestCase
{
    use RefreshDatabase;

    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = app(UserService::class);
    }


    private function makeAdmin(): User
    {
        return User::factory()->create([
            'role'       => 'ADMINISTRATOR',
            'is_claimed' => true,
            'password'   => bcrypt('password'),
        ]);
    }

    private function makeAffiliator(string $status = 'ACTIVE'): User
    {
        return User::factory()->create([
            'role'           => 'AFFILIATOR',
            'is_claimed'     => true,
            'account_status' => $status,
        ]);
    }

    private function blacklistUser(User $user, string $reason = 'Pelanggaran kebijakan'): Blacklist
    {
        return Blacklist::create([
            'user_id'          => $user->id,
            'blacklist_date'   => now(),
            'violation_reason' => $reason,
        ]);
    }

    public function test_add_to_blacklist_changes_user_status_to_banned()
    {
        $affiliator = $this->makeAffiliator();

        $this->userService->addToBlacklist([
            'user_id'          => $affiliator->id,
            'violation_reason' => 'Melanggar ketentuan layanan',
        ]);

        $this->assertDatabaseHas('users', [
            'id'             => $affiliator->id,
            'account_status' => 'BANNED',
        ]);
    }

    public function test_add_to_blacklist_creates_blacklist_record()
    {
        $affiliator = $this->makeAffiliator();
        $reason     = 'Spam produk tanpa izin';

        $this->userService->addToBlacklist([
            'user_id'          => $affiliator->id,
            'violation_reason' => $reason,
        ]);

        $this->assertDatabaseHas('blacklists', [
            'user_id'          => $affiliator->id,
            'violation_reason' => $reason,
        ]);
    }

    public function test_add_to_blacklist_returns_blacklist_instance()
    {
        $affiliator = $this->makeAffiliator();

        $result = $this->userService->addToBlacklist([
            'user_id'          => $affiliator->id,
            'violation_reason' => 'Test return value',
        ]);

        $this->assertInstanceOf(Blacklist::class, $result);
        $this->assertEquals($affiliator->id, $result->user_id);
    }

    public function test_add_to_blacklist_throws_exception_for_nonexistent_user()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->userService->addToBlacklist([
            'user_id'          => 99999,
            'violation_reason' => 'User tidak ada',
        ]);
    }

    public function test_add_to_blacklist_rolls_back_on_failure()
    {
        $affiliator = $this->makeAffiliator();

        try {
            $this->userService->addToBlacklist([
                'user_id'          => $affiliator->id,
                'violation_reason' => null, 
            ]);
        } catch (\Throwable $e) {
        }

        $this->assertDatabaseHas('users', [
            'id'             => $affiliator->id,
            'account_status' => 'ACTIVE',
        ]);

        $this->assertDatabaseMissing('blacklists', [
            'user_id' => $affiliator->id,
        ]);
    }

    public function test_restore_blacklist_changes_user_status_to_active()
    {
        $affiliator = $this->makeAffiliator('BANNED');
        $this->blacklistUser($affiliator);

        $this->userService->restoreBlacklist((string) $affiliator->id);

        $this->assertDatabaseHas('users', [
            'id'             => $affiliator->id,
            'account_status' => 'ACTIVE',
        ]);
    }

    public function test_restore_blacklist_deletes_all_blacklist_records()
    {
        $affiliator = $this->makeAffiliator('BANNED');
        $this->blacklistUser($affiliator, 'Pelanggaran pertama');
        $this->blacklistUser($affiliator, 'Pelanggaran kedua');

        $this->assertEquals(2, Blacklist::where('user_id', $affiliator->id)->count());

        $this->userService->restoreBlacklist((string) $affiliator->id);

        $this->assertDatabaseMissing('blacklists', [
            'user_id' => $affiliator->id,
        ]);
    }

    public function test_restore_blacklist_returns_updated_user_instance()
    {
        $affiliator = $this->makeAffiliator('BANNED');
        $this->blacklistUser($affiliator);

        $result = $this->userService->restoreBlacklist((string) $affiliator->id);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('ACTIVE', $result->account_status);
    }

    public function test_restore_blacklist_throws_exception_for_nonexistent_user()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->userService->restoreBlacklist('99999');
    }

    public function test_restore_blacklist_works_even_without_blacklist_record()
    {
        $affiliator = $this->makeAffiliator('BANNED');

        $result = $this->userService->restoreBlacklist((string) $affiliator->id);

        $this->assertEquals('ACTIVE', $result->account_status);
    }

    public function test_admin_can_blacklist_affiliator_via_http()
    {
        $admin      = $this->makeAdmin();
        $affiliator = $this->makeAffiliator();

        $response = $this->actingAs($admin)
            ->post('/dashboard/users/store-blacklist', [
                'user_id'          => $affiliator->id,
                'violation_reason' => 'Melanggar aturan platform',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id'             => $affiliator->id,
            'account_status' => 'BANNED',
        ]);
        $this->assertDatabaseHas('blacklists', [
            'user_id' => $affiliator->id,
        ]);
    }

    public function test_admin_can_restore_affiliator_from_blacklist_via_http()
    {
        $admin      = $this->makeAdmin();
        $affiliator = $this->makeAffiliator('BANNED');
        $this->blacklistUser($affiliator);

        $response = $this->actingAs($admin)
            ->post('/dashboard/users/restore-blacklist', [
                'id' => $affiliator->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id'             => $affiliator->id,
            'account_status' => 'ACTIVE',
        ]);
        $this->assertDatabaseMissing('blacklists', [
            'user_id' => $affiliator->id,
        ]);
    }

    public function test_affiliator_cannot_blacklist_other_user()
    {
        $affiliator       = $this->makeAffiliator();
        $targetAffiliator = $this->makeAffiliator();

        $response = $this->actingAs($affiliator)
            ->post('/dashboard/users/store-blacklist', [
                'user_id'          => $targetAffiliator->id,
                'violation_reason' => 'Coba-coba',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('users', [
            'id'             => $targetAffiliator->id,
            'account_status' => 'ACTIVE',
        ]);
    }

    public function test_store_blacklist_requires_user_id()
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)
            ->post('/dashboard/users/store-blacklist', [
                'violation_reason' => 'Ada alasan tapi tidak ada user_id',
            ]);

        $response->assertSessionHasErrors('user_id');
    }

    public function test_store_blacklist_requires_violation_reason()
    {
        $admin      = $this->makeAdmin();
        $affiliator = $this->makeAffiliator();

        $response = $this->actingAs($admin)
            ->post('/dashboard/users/store-blacklist', [
                'user_id' => $affiliator->id,
            ]);

        $response->assertSessionHasErrors('violation_reason');
    }

    public function test_store_blacklist_requires_valid_user_id()
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)
            ->post('/dashboard/users/store-blacklist', [
                'user_id'          => 99999,
                'violation_reason' => 'User palsu',
            ]);

        $response->assertSessionHasErrors('user_id');
    }
    public function test_restore_blacklist_requires_valid_id()
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)
            ->post('/dashboard/users/restore-blacklist', [
                'id' => 99999,
            ]);

        $response->assertSessionHasErrors('id');
    }
    public function test_full_cycle_blacklist_then_restore()
    {
        $admin      = $this->makeAdmin();
        $affiliator = $this->makeAffiliator();

        $this->userService->addToBlacklist([
            'user_id'          => $affiliator->id,
            'violation_reason' => 'Pelanggaran pertama',
        ]);

        $this->assertEquals('BANNED', $affiliator->fresh()->account_status);
        $this->assertEquals(1, Blacklist::where('user_id', $affiliator->id)->count());

        $this->userService->restoreBlacklist((string) $affiliator->id);

        $this->assertEquals('ACTIVE', $affiliator->fresh()->account_status);
        $this->assertEquals(0, Blacklist::where('user_id', $affiliator->id)->count());
    }

    public function test_restore_removes_all_blacklist_entries_from_multiple_violations()
    {
        $affiliator = $this->makeAffiliator('BANNED');

        foreach (['Pelanggaran 1', 'Pelanggaran 2', 'Pelanggaran 3'] as $reason) {
            $this->blacklistUser($affiliator, $reason);
        }

        $this->userService->restoreBlacklist((string) $affiliator->id);

        $this->assertEquals(0, Blacklist::where('user_id', $affiliator->id)->count());
        $this->assertEquals('ACTIVE', $affiliator->fresh()->account_status);
    }
}