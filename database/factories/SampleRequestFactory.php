<?php

namespace Database\Factories;

use App\Models\SampleRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SampleRequestFactory extends Factory
{
    protected $model = SampleRequest::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), 
            'status' => 'PENDING',
            'address' => $this->faker->address(),
            'tracking_number' => null,
            'courier' => null,
            'shipping_cost' => null,
            'reject_reason' => null,
            'delivered_at' => null,
        ];
    }

    public function approved()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'APPROVED',
            'courier' => 'JNE',
            'tracking_number' => 'RESI' . $this->faker->numerify('##########'),
        ]);
    }
}