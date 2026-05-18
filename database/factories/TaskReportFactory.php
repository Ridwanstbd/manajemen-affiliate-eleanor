<?php

namespace Database\Factories;

use App\Models\TaskReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskReportFactory extends Factory
{
    protected $model = TaskReport::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'task_status' => 'PROCESSING',
            'due_date' => now()->addDays(7),
            'tiktok_video_link' => null,
            'video_id' => null,
        ];
    }

    public function completed()
    {
        return $this->state(fn (array $attributes) => [
            'task_status' => 'COMPLETED',
            'tiktok_video_link' => 'https://www.tiktok.com/@' . $this->faker->userName() . '/video/' . $this->faker->numerify('###################'),
            'video_id' => $this->faker->numerify('###################'),
        ]);
    }
}