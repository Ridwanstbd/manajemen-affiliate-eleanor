<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->unique()->numerify('###################'),
            'name' => $this->faker->words(3, true),
            'category' => $this->faker->randomElement(['Fashion', 'Elektronik', 'Kecantikan', 'Makanan']),
            'seller_sku' => 'SKU-' . strtoupper($this->faker->bothify('???-###')),
            'product_detail' => $this->faker->paragraph(),
            'brand' => $this->faker->company(),
            'price' => $this->faker->randomFloat(2, 10000, 500000),
            'stock' => $this->faker->numberBetween(10, 1000),
            'parcel_weight' => $this->faker->randomFloat(2, 0.1, 5),
            'mandatory_video_count' => 3,
            'is_visible' => true,
        ];
    }
}