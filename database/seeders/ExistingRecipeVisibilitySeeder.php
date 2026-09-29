<?php

namespace Database\Seeders;

use App\Models\Recipe;
use Illuminate\Database\Seeder;

class ExistingRecipeVisibilitySeeder extends Seeder
{
    /**
     * The seeded recipes that stay private. Everything else is published.
     *
     * @var list<string>
     */
    private const PRIVATE_RECIPES = [
        'Vegetable Stir Fry',
        'Banana Pancakes',
        'Egg Sandwich',
        'Tomato Soup',
        'Chocolate Mug Cake',
    ];

    public function run(): void
    {
        if (Recipe::query()->doesntExist()) {
            $this->command?->warn('No recipes found, skipping visibility pass.');

            return;
        }

        $privateIds = Recipe::whereIn('name', self::PRIVATE_RECIPES)->pluck('id');

        if ($privateIds->count() !== count(self::PRIVATE_RECIPES)) {
            $this->command?->warn(
                'Some private recipe names were not found: '
                .implode(', ', array_diff(self::PRIVATE_RECIPES, Recipe::whereIn('id', $privateIds)->pluck('name')->all()))
            );
        }

        Recipe::whereNotIn('id', $privateIds)->update(['is_public' => true]);
        Recipe::whereIn('id', $privateIds)->update(['is_public' => false]);

        $this->command?->info(sprintf(
            'Recipe visibility set: %d public, %d private.',
            Recipe::where('is_public', true)->count(),
            Recipe::where('is_public', false)->count(),
        ));
    }
}
