<?php

namespace Tests\Feature\Commands;

use App\Models\Blacklist;
use App\Models\Setting;
use App\Models\SampleRequest;
use App\Models\TaskReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateOverdueTaskStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_updates_processing_tasks_past_due_date_to_overdue()
    {
        $user = User::factory()->create(['role' => 'AFFILIATOR']);

        TaskReport::factory()->create([
            'user_id'     => $user->id,
            'task_status' => 'PROCESSING',
            'due_date'    => now()->subDays(2),
        ]);

        $this->artisan('task:update-overdue')->assertExitCode(0);

        $this->assertDatabaseHas('task_reports', [
            'user_id'     => $user->id,
            'task_status' => 'OVERDUE',
        ]);
    }

    public function test_command_does_not_update_tasks_with_future_due_date()
    {
        $user = User::factory()->create(['role' => 'AFFILIATOR']);

        TaskReport::factory()->create([
            'user_id'     => $user->id,
            'task_status' => 'PROCESSING',
            'due_date'    => now()->addDays(3),
        ]);

        $this->artisan('task:update-overdue')->assertExitCode(0);

        $this->assertDatabaseHas('task_reports', [
            'user_id'     => $user->id,
            'task_status' => 'PROCESSING',
        ]);
    }

    public function test_command_does_not_update_already_completed_tasks()
    {
        $user = User::factory()->create(['role' => 'AFFILIATOR']);

        TaskReport::factory()->create([
            'user_id'     => $user->id,
            'task_status' => 'COMPLETED',
            'due_date'    => now()->subDays(5),
        ]);

        $this->artisan('task:update-overdue')->assertExitCode(0);

        $this->assertDatabaseHas('task_reports', [
            'user_id'     => $user->id,
            'task_status' => 'COMPLETED',
        ]);
    }

    public function test_command_does_not_update_tasks_without_due_date()
    {
        $user = User::factory()->create(['role' => 'AFFILIATOR']);

        TaskReport::factory()->create([
            'user_id'     => $user->id,
            'task_status' => 'PROCESSING',
            'due_date'    => null,
        ]);

        $this->artisan('task:update-overdue')->assertExitCode(0);

        $this->assertDatabaseHas('task_reports', [
            'user_id'     => $user->id,
            'task_status' => 'PROCESSING',
        ]);
    }

    public function test_command_outputs_count_of_updated_tasks()
    {
        $user = User::factory()->create(['role' => 'AFFILIATOR']);

        TaskReport::factory()->create([
            'user_id'     => $user->id,
            'task_status' => 'PROCESSING',
            'due_date'    => now()->subDays(1),
        ]);

        $this->artisan('task:update-overdue')
             ->expectsOutputToContain('1 tugas diubah menjadi OVERDUE')
             ->assertExitCode(0);
    }

    public function test_command_outputs_zero_when_no_tasks_overdue()
    {
        $this->artisan('task:update-overdue')
             ->expectsOutputToContain('0 tugas diubah menjadi OVERDUE')
             ->assertExitCode(0);
    }

    public function test_command_updates_multiple_overdue_tasks_in_bulk()
    {
        $user = User::factory()->create(['role' => 'AFFILIATOR']);

        for ($i = 0; $i < 3; $i++) {
            TaskReport::factory()->create([
                'user_id'     => $user->id,
                'task_status' => 'PROCESSING',
                'due_date'    => now()->subDays($i + 1),
            ]);
        }

        $this->artisan('task:update-overdue')->assertExitCode(0);

        $this->assertEquals(3, TaskReport::where('task_status', 'OVERDUE')->count());
    }
}


class AutoBlacklistOverdueCreatorsTest extends TestCase
{
    use RefreshDatabase;

    private function makeAffiliator(): User
    {
        return User::factory()->create([
            'role'           => 'AFFILIATOR',
            'is_claimed'     => true,
            'account_status' => 'ACTIVE',
        ]);
    }

    private function makeDeliveredRequest(User $user, int $deliveredDaysAgo = 20): SampleRequest
    {
        return SampleRequest::factory()->create([
            'user_id'      => $user->id,
            'status'       => 'DELIVERED',
            'delivered_at' => now()->subDays($deliveredDaysAgo),
        ]);
    }

    public function test_command_blacklists_affiliator_with_overdue_delivered_request()
    {
        Setting::create(['key' => 'task_deadline_days', 'value' => '14']);

        $user = $this->makeAffiliator();
        $this->makeDeliveredRequest($user, 20); // 20 hari lalu, deadline 14 hari

        $this->artisan('creator:auto-blacklist')->assertExitCode(0);

        $this->assertDatabaseHas('blacklists', ['user_id' => $user->id]);
        $this->assertDatabaseHas('users', [
            'id'             => $user->id,
            'account_status' => 'BLACKLISTED',
        ]);
    }

    public function test_command_does_not_blacklist_if_within_deadline()
    {
        Setting::create(['key' => 'task_deadline_days', 'value' => '14']);

        $user = $this->makeAffiliator();
        $this->makeDeliveredRequest($user, 5); // hanya 5 hari lalu, masih dalam batas

        $this->artisan('creator:auto-blacklist')->assertExitCode(0);

        $this->assertDatabaseMissing('blacklists', ['user_id' => $user->id]);
        $this->assertDatabaseHas('users', [
            'id'             => $user->id,
            'account_status' => 'ACTIVE',
        ]);
    }

    public function test_command_does_not_blacklist_if_task_completed()
    {
        Setting::create(['key' => 'task_deadline_days', 'value' => '14']);

        $user    = $this->makeAffiliator();
        $request = $this->makeDeliveredRequest($user, 20);

        // Buat task report yang sudah selesai (tidak ter-blacklist)
        $taskReport = TaskReport::factory()->create([
            'user_id'     => $user->id,
            'task_status' => 'COMPLETED',
        ]);
        $taskReport->sampleRequests()->attach($request->id);

        $this->artisan('creator:auto-blacklist')->assertExitCode(0);

        $this->assertDatabaseMissing('blacklists', ['user_id' => $user->id]);
    }

    public function test_command_does_not_duplicate_blacklist_entry()
    {
        Setting::create(['key' => 'task_deadline_days', 'value' => '14']);

        $user = $this->makeAffiliator();
        $this->makeDeliveredRequest($user, 20);

        $this->artisan('creator:auto-blacklist')->assertExitCode(0);
        $this->artisan('creator:auto-blacklist')->assertExitCode(0); // jalankan dua kali

        $this->assertEquals(1, Blacklist::where('user_id', $user->id)->count());
    }

    public function test_command_uses_setting_value_for_deadline()
    {
        Setting::create(['key' => 'task_deadline_days', 'value' => '7']);

        $user = $this->makeAffiliator();
        $this->makeDeliveredRequest($user, 10); // 10 hari lalu, deadline 7 hari → overdue

        $this->artisan('creator:auto-blacklist')->assertExitCode(0);

        $this->assertDatabaseHas('blacklists', ['user_id' => $user->id]);
    }

    public function test_command_uses_default_14_days_when_setting_missing()
    {
        // Tidak ada Setting di DB → gunakan default 14 hari
        $user = $this->makeAffiliator();
        $this->makeDeliveredRequest($user, 20); // lewat 14 hari default

        $this->artisan('creator:auto-blacklist')->assertExitCode(0);

        $this->assertDatabaseHas('blacklists', ['user_id' => $user->id]);
    }

    public function test_command_does_not_blacklist_when_no_overdue_requests()
    {
        $this->artisan('creator:auto-blacklist')->assertExitCode(0);

        $this->assertDatabaseCount('blacklists', 0);
    }

    public function test_command_outputs_info_message()
    {
        $this->artisan('creator:auto-blacklist')
             ->expectsOutputToContain('Proses auto-blacklist selesai dijalankan')
             ->assertExitCode(0);
    }
}