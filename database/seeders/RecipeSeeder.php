<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\User;
use Database\Seeders\Concerns\DownloadsRecipeImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class RecipeSeeder extends Seeder
{
    use DownloadsRecipeImages;

    public function run(): void
    {
        // Change this if you want the recipes to belong to another user.
        $user = User::first();

        if (! $user) {
            $this->command->error('No user found. Please register a user first.');

            return;
        }

        $recipes = [

            [
                'name' => 'Chicken Teriyaki',
                'description' => 'Simple Japanese-style chicken with a sweet and savory teriyaki sauce.',
                'category' => 'Dinner',
                'cooking_time' => 25,
                'difficulty' => 'Easy',
                'servings' => 4,
                'ingredients' => "2 chicken breasts\n3 tablespoons soy sauce\n2 tablespoons honey\n3 cloves garlic\n1 tablespoon ginger\n1 teaspoon sesame seeds\n2 tablespoons cooking oil",
                'instructions' => "1. Cut the chicken into bite-sized pieces.\n2. Mix soy sauce, honey, garlic, and ginger.\n3. Heat oil in a pan and cook the chicken.\n4. Pour the sauce over the chicken.\n5. Simmer until the sauce thickens.\n6. Sprinkle with sesame seeds and serve.",
                'image_source' => 'https://www.themealdb.com/images/media/meals/1525872624.jpg',
                'image_slug' => 'chicken-teriyaki',
                'tutorial_url' => 'https://www.youtube.com/watch?v=QqdcCHQlOe0',
            ],

            [
                'name' => 'Nasi Minyak',
                'description' => 'A fragrant Malaysian rice dish served with flavorful chicken.',
                'category' => 'Lunch',
                'cooking_time' => 40,
                'difficulty' => 'Medium',
                'servings' => 4,
                'ingredients' => "2 cups rice\n1 chicken breast\n1 tablespoon butter\n1 onion\n2 cloves garlic\n1 tablespoon cooking oil\n1 teaspoon salt",
                'instructions' => "1. Wash and prepare the rice.\n2. Fry onion and garlic in oil and butter.\n3. Add the rice and stir well.\n4. Add water and salt.\n5. Cook until the rice is fluffy.\n6. Serve with cooked chicken.",
                'image_source' => 'https://www.themealdb.com/images/media/meals/xw1ruy1763786501.jpg',
                'image_slug' => 'nasi-minyak',
                'tutorial_url' => 'https://www.youtube.com/watch?v=gY6PZzjO53M',
            ],

            [
                'name' => 'Nasi Goreng Kampung',
                'description' => 'A simple Malaysian-style fried rice with egg and vegetables.',
                'category' => 'Lunch',
                'cooking_time' => 20,
                'difficulty' => 'Easy',
                'servings' => 3,
                'ingredients' => "3 cups cooked rice\n2 eggs\n2 cloves garlic\n1 onion\n2 tablespoons cooking oil\n1 tablespoon chili paste\n1 teaspoon salt",
                'instructions' => "1. Heat oil in a wok.\n2. Fry onion and garlic.\n3. Add chili paste and stir.\n4. Add eggs and scramble.\n5. Add cooked rice and mix well.\n6. Season with salt and serve.",
                'image_source' => 'https://www.themealdb.com/images/media/meals/hblwvg1763478203.jpg',
                'image_slug' => 'nasi-goreng-kampung',
                'tutorial_url' => 'https://www.youtube.com/watch?v=9wZAYbPcF-I',
            ],

            [
                'name' => 'Creamy Garlic Pasta',
                'description' => 'Creamy pasta with garlic and herbs for a quick and comforting meal.',
                'category' => 'Dinner',
                'cooking_time' => 25,
                'difficulty' => 'Easy',
                'servings' => 3,
                'ingredients' => "250g pasta\n4 cloves garlic\n1 cup milk\n1 tablespoon butter\n2 tablespoons parmesan cheese\n1 teaspoon black pepper\n1 teaspoon salt",
                'instructions' => "1. Cook the pasta according to the package instructions.\n2. Melt butter in a pan.\n3. Fry the garlic until fragrant.\n4. Add milk and parmesan cheese.\n5. Add cooked pasta and mix well.\n6. Season with salt and black pepper.",
                'image_source' => 'https://www.themealdb.com/images/media/meals/llcbn01574260722.jpg',
                'image_slug' => 'creamy-garlic-pasta',
                'tutorial_url' => 'https://www.youtube.com/watch?v=_T6jkRvhlkk',
            ],

            [
                'name' => 'Chicken Fried Rice',
                'description' => 'Quick fried rice with chicken, egg, and vegetables.',
                'category' => 'Lunch',
                'cooking_time' => 20,
                'difficulty' => 'Easy',
                'servings' => 4,
                'ingredients' => "3 cups cooked rice\n1 chicken breast\n2 eggs\n1 carrot\n2 cloves garlic\n2 tablespoons soy sauce\n2 tablespoons cooking oil",
                'instructions' => "1. Cut the chicken into small pieces.\n2. Heat oil and cook the chicken.\n3. Add garlic and carrot.\n4. Add eggs and scramble.\n5. Add cooked rice and soy sauce.\n6. Stir-fry everything together and serve.",
                'image_source' => 'https://www.themealdb.com/images/media/meals/wuyd2h1765655837.jpg',
                'image_slug' => 'chicken-fried-rice',
                'tutorial_url' => 'https://www.youtube.com/watch?v=mUr-7wjhHuU',
            ],

            [
                'name' => 'Vegetable Stir Fry',
                'description' => 'A quick and colorful vegetable stir fry suitable for a light meal.',
                'category' => 'Dinner',
                'cooking_time' => 15,
                'difficulty' => 'Easy',
                'servings' => 2,
                'ingredients' => "1 carrot\n1 bell pepper\n1 broccoli\n2 cloves garlic\n1 tablespoon soy sauce\n1 tablespoon cooking oil",
                'instructions' => "1. Wash and cut all vegetables.\n2. Heat oil in a pan.\n3. Fry garlic until fragrant.\n4. Add vegetables and stir-fry.\n5. Add soy sauce.\n6. Cook until vegetables are tender but still crisp.",
                'image_source' => 'https://www.themealdb.com/images/media/meals/minfsc1763766806.jpg',
                'image_slug' => 'vegetable-stir-fry',
                'tutorial_url' => 'https://www.youtube.com/watch?v=l8wuWeNy3J4',
            ],

            [
                'name' => 'Banana Pancakes',
                'description' => 'Soft banana pancakes that are simple and quick to prepare.',
                'category' => 'Breakfast',
                'cooking_time' => 15,
                'difficulty' => 'Easy',
                'servings' => 2,
                'ingredients' => "2 bananas\n1 egg\n1 cup flour\n1 cup milk\n1 teaspoon baking powder\n1 tablespoon sugar\n1 tablespoon butter",
                'instructions' => "1. Mash the bananas.\n2. Mix banana, egg, flour, milk, baking powder, and sugar.\n3. Heat butter in a pan.\n4. Pour the batter into small circles.\n5. Cook both sides until golden brown.\n6. Serve warm.",
                'image_source' => 'https://www.themealdb.com/images/media/meals/sywswr1511383814.jpg',
                'image_slug' => 'banana-pancakes',
                'tutorial_url' => 'https://www.youtube.com/watch?v=kSKtb2Sv-_U',
            ],

            [
                'name' => 'Egg Sandwich',
                'description' => 'A simple egg sandwich that is quick to prepare for breakfast.',
                'category' => 'Breakfast',
                'cooking_time' => 10,
                'difficulty' => 'Easy',
                'servings' => 2,
                'ingredients' => "2 eggs\n4 slices bread\n1 tablespoon mayonnaise\n1 tablespoon butter\n1 pinch salt\n1 pinch black pepper",
                'instructions' => "1. Boil or scramble the eggs.\n2. Season with salt and black pepper.\n3. Spread butter and mayonnaise on the bread.\n4. Add the egg.\n5. Close the sandwich and serve.",
                'image_source' => 'https://www.themealdb.com/images/media/meals/sbx7n71587673021.jpg',
                'image_slug' => 'egg-sandwich',
                'tutorial_url' => 'https://www.youtube.com/watch?v=1WDesu7bUDM',
            ],

            [
                'name' => 'Tomato Soup',
                'description' => 'Warm and comforting tomato soup that can be served with bread.',
                'category' => 'Dinner',
                'cooking_time' => 30,
                'difficulty' => 'Easy',
                'servings' => 3,
                'ingredients' => "5 tomatoes\n1 onion\n2 cloves garlic\n2 cups water\n1 tablespoon butter\n1 teaspoon salt\n1 teaspoon black pepper",
                'instructions' => "1. Chop the tomatoes, onion, and garlic.\n2. Melt butter in a pot.\n3. Fry onion and garlic.\n4. Add tomatoes and water.\n5. Simmer for 20 minutes.\n6. Blend until smooth and season before serving.",
                'image_source' => 'https://www.themealdb.com/images/media/meals/stpuws1511191310.jpg',
                'image_slug' => 'tomato-soup',
                'tutorial_url' => 'https://www.youtube.com/watch?v=lBhwjjUiFk4',
            ],

            [
                'name' => 'Chocolate Mug Cake',
                'description' => 'A quick chocolate dessert prepared in a mug.',
                'category' => 'Dessert',
                'cooking_time' => 5,
                'difficulty' => 'Easy',
                'servings' => 1,
                'ingredients' => "4 tablespoons all-purpose flour\n2 tablespoons cocoa powder\n2 tablespoons sugar\n3 tablespoons milk\n1 tablespoon cooking oil\n1/2 teaspoon baking powder\n1 tablespoon chocolate chips",
                'instructions' => "1. Mix flour, cocoa powder, sugar, and baking powder in a mug.\n2. Add milk and cooking oil.\n3. Mix until smooth.\n4. Add chocolate chips.\n5. Microwave for about 1 to 2 minutes.\n6. Let it cool slightly before serving.",
                'image_source' => 'https://www.themealdb.com/images/media/meals/tqtywx1468317395.jpg',
                'image_slug' => 'chocolate-mug-cake',
                'tutorial_url' => 'https://www.youtube.com/watch?v=dsJtgmAhFF4',
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

        $this->command->info('10 recipes inserted successfully.');
    }
}
