<?php

namespace Tests\Feature\Affiliator;

use App\Models\TaskReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $affiliator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->affiliator = User::factory()->create(['role' => 'AFFILIATOR']);
    }

    public function test_affiliator_can_submit_task_with_valid_tiktok_link()
    {
        $task = TaskReport::factory()->create([
            'user_id'     => $this->affiliator->id,
            'task_status' => 'PROCESSING',
        ]);

        $response = $this->actingAs($this->affiliator)
                         ->post(route('affiliator.task.submit', $task->id), [
                             'tiktok_video_link' => 'https://www.tiktok.com/@username/video/1234567890123456789',
                             'product'           => [1],
                         ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('task_reports', [
            'id'          => $task->id,
            'task_status' => 'COMPLETED',
            'video_id'    => '1234567890123456789',
        ]);
    }

    public function test_task_submission_fails_if_link_is_invalid()
    {
        $task = TaskReport::factory()->create([
            'user_id'     => $this->affiliator->id,
            'task_status' => 'PROCESSING',
        ]);

        $response = $this->actingAs($this->affiliator)
                         ->post(route('affiliator.task.submit', $task->id), [
                             'tiktok_video_link' => 'bukan-url-valid',
                             'product'           => [1],
                         ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['tiktok_video_link']);
    }
}