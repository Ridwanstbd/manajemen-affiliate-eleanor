<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\SampleRequest;
use App\Models\SampleRequestDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SampleRequestSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $users = User::all();
        $products = Product::all();

        if ($users->count() === 0 || $products->count() === 0) {
            $this->command->info('Pastikan Anda sudah menjalankan UserSeeder/DatabaseSeeder dan ProductSeeder terlebih dahulu.');
            return;
        }

        // Buat 3 contoh Sample Request
        for ($i = 0; $i < 3; $i++) {
            $user = $users->random();
            
            $sampleRequest = SampleRequest::create([
                'user_id' => $user->id,
                'status' => $faker->randomElement(['PENDING', 'APPROVED', 'SHIPPED', 'COMPLETED', 'REJECTED']),
                'address' => $faker->address(),
                'tracking_number' => 'RESI' . $faker->unique()->numerify('##########'),
                'courier' => $faker->randomElement(['JNT', 'JNE', 'SiCepat', 'AnterAja']),
                'shipping_cost' => $faker->randomFloat(2, 10000, 50000),
            ]);
            $randomProducts = $products->random(rand(1, 3));

            foreach ($randomProducts as $product) {
                SampleRequestDetail::create([
                    'sample_request_id' => $sampleRequest->id,
                    'product_id' => $product->id,
                    'quantity' => $faker->numberBetween(1, 2),
                ]);
            }
        }
    }
}