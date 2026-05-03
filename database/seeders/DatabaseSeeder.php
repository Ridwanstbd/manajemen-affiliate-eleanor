<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'role' => 'ADMINISTRATOR',
            'password' => bcrypt('password'),
            'account_status' => 'ACTIVE',
            'is_claimed' => true,
        ]);

        User::factory()->create([
            'username' => 'testaffiliate',
            'email' => 'testaffiliate@example.com',
            'password' => bcrypt('password'),
            'account_status' => 'ACTIVE',
            'is_claimed' => true,
        ]);

        // $this->call([

        // ]);
    }
}
