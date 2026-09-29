<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\User;
use Database\Seeders\Concerns\DownloadsRecipeImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class NineMoreRecipesSeeder extends Seeder
{
    use DownloadsRecipeImages;

    public function run(): void
    {
        $user = User::first();

        if (! $user) {
            $this->command->error(
                'No user found. Please register a user first.'
            );

            return;
        }

        $recipes = [

            [
                'name' => 'Omelette',
                'description' => 'A simple fluffy omelette with vegetables and cheese.',
                'category' => 'Breakfast',
                'cooking_time' => 10,
                'difficulty' => 'Easy',
                'servings' => 2,
                'ingredients' => '3 eggs
1/4 onion
1/4 bell pepper
2 tablespoons cheese
1 tablespoon butter
1 pinch salt
1 pinch black pepper',
                'instructions' => '1. Beat the eggs in a bowl.
2. Add salt and black pepper.
3. Melt butter in a pan.
4. Add onion and bell pepper.
5. Pour in the eggs.
6. Add cheese and cook until set.
7. Fold and serve.',
                'image_source' => 'https://www.themealdb.com/images/media/meals/yvpuuy1511797244.jpg',
                'image_slug' => 'omelette',
                'tutorial_url' => 'https://www.youtube.com/watch?v=qXPhVYpQLPA',
            ],

            [
                'name' => 'Chicken Rice Bowl',
                'description' => 'A balanced rice bowl with grilled chicken and vegetables.',
                'category' => 'Lunch',
                'cooking_time' => 30,
                'difficulty' => 'Easy',
                'servings' => 2,
                'ingredients' => '2 cups cooked rice
1 chicken breast
1 carrot
1 cucumber
1 tablespoon soy sauce
1 tablespoon cooking oil
1 teaspoon sesame seeds',
                'instructions' => '1. Season and cook the chicken until done.
2. Slice the carrot and cucumber.
3. Prepare the cooked rice in bowls.
4. Slice the chicken.
5. Arrange chicken and vegetables over the rice.
6. Drizzle with soy sauce.
7. Sprinkle with sesame seeds.',
                'image_source' => 'https://www.themealdb.com/images/media/meals/wai9bw1619788844.jpg',
                'image_slug' => 'chicken-rice-bowl',
                'tutorial_url' => 'https://www.youtube.com/watch?v=IUIs4C86Ilo',
            ],

            [
                'name' => 'Beef Fried Noodles',
                'description' => 'Stir-fried noodles with beef and fresh vegetables.',
                'category' => 'Lunch',
                'cooking_time' => 25,
                'difficulty' => 'Medium',
                'servings' => 3,
                'ingredients' => '250g noodles
200g beef slices
1 carrot
1/2 bell pepper
2 cloves garlic
2 tablespoons soy sauce
1 tablespoon oyster sauce
1 tablespoon cooking oil',
                'instructions' => '1. Cook the noodles according to the package instructions.
2. Heat oil in a wok.
3. Fry garlic and beef.
4. Add carrot and bell pepper.
5. Add cooked noodles.
6. Add soy sauce and oyster sauce.
7. Stir-fry until well combined.',
                'image_source' => 'https://www.themealdb.com/images/media/meals/1529444830.jpg',
                'image_slug' => 'beef-fried-noodles',
                'tutorial_url' => 'https://www.youtube.com/watch?v=ZT9LSsNXXe0',
            ],

            [
                'name' => 'Tuna Sandwich',
                'description' => 'A quick and simple tuna sandwich for breakfast or a light meal.',
                'category' => 'Breakfast',
                'cooking_time' => 10,
                'difficulty' => 'Easy',
                'servings' => 2,
                'ingredients' => '1 can tuna
4 slices bread
2 tablespoons mayonnaise
1/4 onion
1 lettuce leaf
1 pinch black pepper',
                'instructions' => '1. Drain the tuna.
2. Mix tuna with mayonnaise and chopped onion.
3. Season with black pepper.
4. Place lettuce on the bread.
5. Add tuna mixture.
6. Cover with another slice of bread and serve.',
                'image_source' => 'https://www.themealdb.com/images/media/meals/yypwwq1511304979.jpg',
                'image_slug' => 'tuna-sandwich',
                'tutorial_url' => 'https://www.youtube.com/watch?v=3_UAxkx0u6U',
            ],

            [
                'name' => 'Chicken Tomato Pasta',
                'description' => 'Pasta with chicken in a light tomato sauce.',
                'category' => 'Dinner',
                'cooking_time' => 30,
                'difficulty' => 'Medium',
                'servings' => 4,
                'ingredients' => '250g pasta
1 chicken breast
2 tomatoes
2 cloves garlic
1 onion
1 tablespoon cooking oil
1 teaspoon salt
1/2 teaspoon black pepper',
                'instructions' => '1. Cook the pasta until tender.
2. Cut the chicken into small pieces.
3. Heat oil and cook the chicken.
4. Add onion and garlic.
5. Add chopped tomatoes.
6. Season with salt and black pepper.
7. Add the cooked pasta and mix well.',
                'image_source' => 'https://www.themealdb.com/images/media/meals/ustsqw1468250014.jpg',
                'image_slug' => 'chicken-tomato-pasta',
                'tutorial_url' => 'https://www.youtube.com/watch?v=1IszT_guI08',
            ],

            [
                'name' => 'Vegetable Soup',
                'description' => 'A light and comforting soup made with mixed vegetables.',
                'category' => 'Dinner',
                'cooking_time' => 30,
                'difficulty' => 'Easy',
                'servings' => 4,
                'ingredients' => '2 carrots
1 potato
1 onion
1 tomato
2 cloves garlic
3 cups water
1 teaspoon salt
1/2 teaspoon black pepper',
                'instructions' => '1. Chop all vegetables.
2. Sauté onion and garlic briefly.
3. Add water and vegetables.
4. Bring to a boil.
5. Simmer until vegetables are tender.
6. Season with salt and black pepper.
7. Serve warm.',
                'image_source' => 'https://www.themealdb.com/images/media/meals/1brbso1763585098.jpg',
                'image_slug' => 'vegetable-soup',
                'tutorial_url' => 'https://www.youtube.com/watch?v=X3BxTRAhgTc',
            ],

            [
                'name' => 'French Toast with Banana',
                'description' => 'Golden French toast served with fresh banana slices.',
                'category' => 'Breakfast',
                'cooking_time' => 15,
                'difficulty' => 'Easy',
                'servings' => 2,
                'ingredients' => '4 slices bread
2 eggs
1/2 cup milk
1 banana
1 tablespoon sugar
1/2 teaspoon cinnamon
1 tablespoon butter',
                'instructions' => '1. Beat eggs, milk, sugar, and cinnamon.
2. Dip the bread into the mixture.
3. Melt butter in a pan.
4. Cook the bread on both sides until golden.
5. Slice the banana.
6. Serve the French toast with banana.',
                'image_source' => 'https://www.themealdb.com/images/media/meals/927vvp1779732018.jpg',
                'image_slug' => 'french-toast-with-banana',
                'tutorial_url' => 'https://www.youtube.com/watch?v=ympLYopSKow',
            ],

            [
                'name' => 'Creamy Mushroom Pasta',
                'description' => 'Creamy pasta with mushrooms and garlic.',
                'category' => 'Dinner',
                'cooking_time' => 25,
                'difficulty' => 'Easy',
                'servings' => 3,
                'ingredients' => '250g pasta
150g mushrooms
2 cloves garlic
1 cup milk
1 tablespoon butter
2 tablespoons parmesan cheese
1/2 teaspoon salt
1/2 teaspoon black pepper',
                'instructions' => '1. Cook the pasta.
2. Slice the mushrooms.
3. Melt butter in a pan.
4. Fry garlic and mushrooms.
5. Add milk and parmesan cheese.
6. Season with salt and pepper.
7. Add pasta and mix until creamy.',
                'image_source' => 'https://www.themealdb.com/images/media/meals/ssyqwr1511451678.jpg',
                'image_slug' => 'creamy-mushroom-pasta',
                'tutorial_url' => 'https://www.youtube.com/watch?v=GNN7_ZSJ5YE',
            ],

            [
                'name' => 'Fruit Yogurt Parfait',
                'description' => 'A refreshing layered yogurt dessert with fruit and granola.',
                'category' => 'Breakfast',
                'cooking_time' => 10,
                'difficulty' => 'Easy',
                'servings' => 2,
                'ingredients' => '1 cup yogurt
1 banana
1/2 cup strawberries
1/4 cup granola
1 tablespoon honey',
                'instructions' => '1. Slice the fruit.
2. Add yogurt to a glass.
3. Add a layer of fruit.
4. Add granola.
5. Repeat the layers.
6. Drizzle with honey and serve.',
                'image_source' => 'https://www.themealdb.com/images/media/meals/oe8rg51699014028.jpg',
                'image_slug' => 'fruit-yogurt-parfait',
                'tutorial_url' => 'https://www.youtube.com/watch?v=ybWHc4Vi-xU',
            ],

        ];

        foreach ($recipes as $recipe) {
            $image = $this->storeRecipeImage(
                $recipe['image_source'],
                $recipe['image_slug'],
                $recipe['name'],
            );

            Recipe::create([
                'user_id' => $user->id,
                ...Arr::except($recipe, ['image_source', 'image_slug']),
                'image' => $image,
            ]);
        }

        $this->command->info(
            '9 additional recipes inserted successfully.'
        );
    }
}
