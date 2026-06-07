<?php

namespace Tests\Unit\Affiliator;

use App\Models\TaskReport;
use App\Models\User;
use App\Services\Affiliator\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    private TaskService $service;
    private User $affiliator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service    = new TaskService();
        $this->affiliator = User::factory()->create(['role' => 'AFFILIATOR']);
        $this->actingAs($this->affiliator);
    }

    // ─── getTaskData ──────────────────────────────────────────────────────────

    public function test_get_task_data_returns_paginator()
    {
        TaskReport::factory()->create(['user_id' => $this->affiliator->id, 'task_status' => 'PROCESSING']);

        $result = $this->service->getTaskData(new Request());

        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
    }

    public function test_get_task_data_returns_only_processing_and_overdue()
    {
        TaskReport::factory()->create(['user_id' => $this->affiliator->id, 'task_status' => 'PROCESSING']);
        TaskReport::factory()->create(['user_id' => $this->affiliator->id, 'task_status' => 'OVERDUE']);
        TaskReport::factory()->create(['user_id' => $this->affiliator->id, 'task_status' => 'COMPLETED']);

        $result = $this->service->getTaskData(new Request());

        $this->assertEquals(2, $result->total());
    }

    public function test_get_task_data_excludes_other_users_tasks()
    {
        $otherUser = User::factory()->create(['role' => 'AFFILIATOR']);
        TaskReport::factory()->create(['user_id' => $otherUser->id, 'task_status' => 'PROCESSING']);

        $result = $this->service->getTaskData(new Request());

        $this->assertEquals(0, $result->total());
    }

    // ─── getCompletedTaskData ─────────────────────────────────────────────────

    public function test_get_completed_task_data_returns_only_completed()
    {
        TaskReport::factory()->create(['user_id' => $this->affiliator->id, 'task_status' => 'COMPLETED']);
        TaskReport::factory()->create(['user_id' => $this->affiliator->id, 'task_status' => 'PROCESSING']);

        $result = $this->service->getCompletedTaskData(new Request());

        $this->assertEquals(1, $result->total());
        $this->assertEquals('COMPLETED', $result->items()[0]->task_status);
    }

    public function test_get_completed_task_data_excludes_other_users()
    {
        $otherUser = User::factory()->create(['role' => 'AFFILIATOR']);
        TaskReport::factory()->create(['user_id' => $otherUser->id, 'task_status' => 'COMPLETED']);

        $result = $this->service->getCompletedTaskData(new Request());

        $this->assertEquals(0, $result->total());
    }

    // ─── getTabData routing ───────────────────────────────────────────────────

    public function test_get_tab_data_routes_completed_tab()
    {
        TaskReport::factory()->create(['user_id' => $this->affiliator->id, 'task_status' => 'COMPLETED']);
        TaskReport::factory()->create(['user_id' => $this->affiliator->id, 'task_status' => 'PROCESSING']);

        $result = $this->service->getTabData('completed', new Request());

        $this->assertEquals(1, $result->total());
    }

    public function test_get_tab_data_routes_process_overdue_tab()
    {
        TaskReport::factory()->create(['user_id' => $this->affiliator->id, 'task_status' => 'OVERDUE']);

        $result = $this->service->getTabData('process-overdue', new Request());

        $this->assertEquals(1, $result->total());
    }

    public function test_get_tab_data_defaults_to_task_data_for_unknown_tab()
    {
        TaskReport::factory()->create(['user_id' => $this->affiliator->id, 'task_status' => 'PROCESSING']);

        $result = $this->service->getTabData('tidak-dikenal', new Request());

        $this->assertEquals(1, $result->total());
    }

    // ─── getTaskDetail ────────────────────────────────────────────────────────

    public function test_get_task_detail_returns_task_belonging_to_user()
    {
        $task = TaskReport::factory()->create([
            'user_id'     => $this->affiliator->id,
            'task_status' => 'PROCESSING',
        ]);

        $result = $this->service->getTaskDetail($this->affiliator, $task->id);

        $this->assertEquals($task->id, $result->id);
    }

    public function test_get_task_detail_throws_for_nonexistent_task()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->service->getTaskDetail($this->affiliator, 99999);
    }

    public function test_get_task_detail_throws_for_task_belonging_to_another_user()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $otherUser = User::factory()->create(['role' => 'AFFILIATOR']);
        $task      = TaskReport::factory()->create([
            'user_id'     => $otherUser->id,
            'task_status' => 'PROCESSING',
        ]);

        $this->service->getTaskDetail($this->affiliator, $task->id);
    }

    // ─── pagination ───────────────────────────────────────────────────────────

    public function test_get_task_data_paginates_10_per_page()
    {
        for ($i = 0; $i < 15; $i++) {
            TaskReport::factory()->create([
                'user_id'     => $this->affiliator->id,
                'task_status' => 'PROCESSING',
            ]);
        }

        $result = $this->service->getTaskData(new Request());

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(15, $result->total());
    }
}