<?php

namespace Database\Seeders;

use App\Models\Flower;
use App\Models\FlowerLike;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@flowerdex.io',
            'password' => Hash::make('1234'),
        ]);

        $flower = Flower::factory()->create();

        FlowerLike::factory()->create([
            'user_id' => $user->id,
            'flower_id' => $flower->id,
        ]);
    }
}
