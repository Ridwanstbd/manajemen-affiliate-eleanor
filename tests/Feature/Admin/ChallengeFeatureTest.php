<?php

namespace Tests\Feature\Admin;

use App\Models\Challenge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;


class ChallengeFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $affiliator;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::factory()->create([
            'role'       => 'ADMINISTRATOR',
            'is_claimed' => true,
        ]);
        $this->affiliator = User::factory()->create([
            'role'           => 'AFFILIATOR',
            'is_claimed'     => true,
            'account_status' => 'ACTIVE',
        ]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title'            => 'Challenge Keren',
            'rules'            => 'Buat konten semenarik mungkin.',
            'start_date'       => now()->toDateString(),
            'end_date'         => now()->addDays(30)->toDateString(),
            'commission_bonus' => 5.0,
            'is_active'        => 1,
        ], $overrides);
    }


    public function test_admin_can_view_challenge_index()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin-dashboard.challenge.index'));

        $response->assertStatus(200);
    }

    public function test_affiliator_cannot_view_challenge_index()
    {
        $response = $this->actingAs($this->affiliator)
                         ->get(route('admin-dashboard.challenge.index'));

        $response->assertStatus(403);
    }

    public function test_admin_can_create_challenge()
    {
        $response = $this->actingAs($this->admin)
                         ->post(route('admin-dashboard.challenge.create'), $this->validPayload());

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('challenges', ['title' => 'Challenge Keren']);
    }

    public function test_create_challenge_requires_title()
    {
        $response = $this->actingAs($this->admin)
                         ->postJson(route('admin-dashboard.challenge.create'), $this->validPayload(['title' => '']));

        $response->assertStatus(422)->assertJsonValidationErrors(['title']);
    }

    public function test_create_challenge_requires_valid_end_date_after_start()
    {
        $response = $this->actingAs($this->admin)
                         ->postJson(route('admin-dashboard.challenge.create'), $this->validPayload([
                             'start_date' => now()->toDateString(),
                             'end_date'   => now()->subDays(1)->toDateString(), // sebelum start
                         ]));

        $response->assertStatus(422)->assertJsonValidationErrors(['end_date']);
    }

    public function test_admin_can_create_challenge_with_banner_image()
    {
        $file = UploadedFile::fake()->create('banner.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->admin)
                         ->post(route('admin-dashboard.challenge.create'), $this->validPayload([
                             'banner_image' => $file,
                         ]));

        $response->assertRedirect();
        $challenge = Challenge::latest()->first();
        Storage::disk('public')->assertExists($challenge->banner_image_path);
    }

    public function test_affiliator_cannot_create_challenge()
    {
        $response = $this->actingAs($this->affiliator)
                         ->post(route('admin-dashboard.challenge.create'), $this->validPayload());

        $response->assertStatus(403);
    }

    public function test_admin_can_update_challenge()
    {
        $challenge = Challenge::create($this->validPayload());

        $response = $this->actingAs($this->admin)
                         ->put(route('admin-dashboard.challenge.update'), $this->validPayload([
                             'id'    => $challenge->id,
                             'title' => 'Judul Challenge Baru',
                         ]));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('challenges', [
            'id'    => $challenge->id,
            'title' => 'Judul Challenge Baru',
        ]);
    }

    public function test_affiliator_cannot_update_challenge()
    {
        $challenge = Challenge::create($this->validPayload());

        $response = $this->actingAs($this->affiliator)
                         ->put(route('admin-dashboard.challenge.update'), $this->validPayload([
                             'id' => $challenge->id,
                         ]));

        $response->assertStatus(403);
    }


    public function test_admin_can_delete_challenge()
    {
        $challenge = Challenge::create($this->validPayload());

        $response = $this->actingAs($this->admin)
                         ->delete(route('admin-dashboard.challenge.destroy'), ['id' => $challenge->id]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('challenges', ['id' => $challenge->id]);
    }

    public function test_affiliator_cannot_delete_challenge()
    {
        $challenge = Challenge::create($this->validPayload());

        $response = $this->actingAs($this->affiliator)
                         ->delete(route('admin-dashboard.challenge.destroy'), ['id' => $challenge->id]);

        $response->assertStatus(403);
    }
}



class SettingFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $affiliator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin      = User::factory()->create(['role' => 'ADMINISTRATOR', 'is_claimed' => true]);
        $this->affiliator = User::factory()->create(['role' => 'AFFILIATOR', 'is_claimed' => true, 'account_status' => 'ACTIVE']);
    }

    public function test_admin_can_update_task_deadline_setting()
    {
        $response = $this->actingAs($this->admin)
                         ->put(route('admin-dashboard.settings.update-task-deadline'), [
                             'task_deadline_days' => 14,
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('settings', [
            'key'   => 'task_deadline_days',
            'value' => '14',
        ]);
    }

    public function test_affiliator_cannot_access_settings()
    {
        $response = $this->actingAs($this->affiliator)
                         ->put(route('admin-dashboard.settings.update-task-deadline'), [
                             'task_deadline_days' => 14,
                         ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_setting_multiple_times_without_duplicating()
    {
        $this->actingAs($this->admin)
             ->put(route('admin-dashboard.settings.update-task-deadline'), ['task_deadline_days' => 7]);

        $this->actingAs($this->admin)
             ->put(route('admin-dashboard.settings.update-task-deadline'), ['task_deadline_days' => 14]);

        $this->assertDatabaseCount('settings', 1);
        $this->assertDatabaseHas('settings', ['key' => 'task_deadline_days', 'value' => '14']);
    }
}