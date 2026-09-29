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
     *
     * Maisara is created first so she stays the owner of the seeded recipes,
     * which both recipe seeders assign to User::first().
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Maisara',
            'email' => 'S71297@ocean.umt.edu.my',
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            NineMoreRecipesSeeder::class,
            RecipeSeeder::class,
        ]);

        $this->call(ExistingRecipeVisibilitySeeder::class);
        $this->call(AdditionalUsersRecipesSeeder::class);
    }
}
