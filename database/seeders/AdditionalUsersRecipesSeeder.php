<?php

namespace Database\Seeders;

use App\Models\Favorite;
use App\Models\MealPlan;
use App\Models\Recipe;
use App\Models\ShoppingList;
use App\Models\User;
use App\Models\UserPreference;
use App\Support\IngredientParser;
use Carbon\Carbon;
use Database\Seeders\Concerns\DownloadsRecipeImages;
use Illuminate\Database\Seeder;

class AdditionalUsersRecipesSeeder extends Seeder
{
    use DownloadsRecipeImages;

    /**
     * The accounts created by this seeder. The key is used to attach
     * recipes, favourites, meal plans and preferences to each account.
     *
     * @var array<string, array{name: string, email: string}>
     */
    private const ACCOUNTS = [
        'aisyah' => [
            'name' => 'Nur Aisyah Rahman',
            'email' => 'aisyah.rahman@example.com',
        ],
        'weijian' => [
            'name' => 'Lim Wei Jian',
            'email' => 'weijian.lim@example.com',
        ],
    ];

    /**
     * Fully detailed recipes, five per account and half of them public.
     *
     * @var list<array{
     *     owner: string,
     *     name: string,
     *     description: string,
     *     category: string,
     *     cooking_time: int,
     *     difficulty: string,
     *     servings: int,
     *     ingredients: string,
     *     instructions: string,
     *     is_public: bool,
     *     image_source: string,
     *     image_slug: string,
     *     tutorial_url: string
     * }>
     */
    private const RECIPES = [
        [
            'owner' => 'aisyah',
            'name' => 'Ayam Goreng Berempah',
            'description' => 'Golden fried chicken marinated in a traditional Malay spice paste and braised in coconut milk until the sauce turns rich and dark.',
            'category' => 'Dinner',
            'cooking_time' => 60,
            'difficulty' => 'Hard',
            'servings' => 4,
            'ingredients' => "2 whole chickens, cut into 8 pieces\n3 tablespoons plain flour\n2 teaspoons turmeric powder\n1 tablespoon ginger powder\n1 tablespoon garlic powder\n2 teaspoons ground lemongrass\n1 teaspoon ground cinnamon\n1 teaspoon ground cloves\n2 tablespoons coconut milk\n2 tablespoons cooking oil\n1 tablespoon sugar\n1 teaspoon salt",
            'instructions' => "1. Clean the chicken and pat it dry.\n2. Mix the flour with the turmeric, ginger, garlic, lemongrass, cinnamon and cloves.\n3. Coat the chicken with the spice flour and marinate for at least 30 minutes.\n4. Heat the oil in a wok and fry the chicken until golden brown.\n5. Pour in the coconut milk and add the sugar and salt.\n6. Simmer over low heat until the sauce thickens and the chicken is tender.\n7. Increase the heat to crisp the skin, then serve with rice.",
            'is_public' => true,
            'image_source' => 'https://www.themealdb.com/images/media/meals/el64dy1763483009.jpg',
            'image_slug' => 'ayam-goreng-berempah',
            'tutorial_url' => 'https://www.youtube.com/watch?v=fF4GpyiZL8Q',
        ],
        [
            'owner' => 'aisyah',
            'name' => 'Laksa Serut',
            'description' => 'A rich coconut laksa with prawns and tofu lifted by lime, fish sauce and plenty of fresh herbs.',
            'category' => 'Lunch',
            'cooking_time' => 45,
            'difficulty' => 'Medium',
            'servings' => 4,
            'ingredients' => "300g dried rice noodles\n800ml coconut milk\n1 litre chicken stock\n1 tablespoon belacan\n2 tablespoons red curry paste\n4 prawns\n200g firm tofu, sliced\n1 cucumber, cut into strips\n2 tablespoons lime juice\n2 tablespoons fish sauce\n1 teaspoon sugar\nBean sprouts and lime leaves for serving",
            'instructions' => "1. Soak the rice noodles in warm water until softened, then drain.\n2. Fry the belacan and curry paste in a pot until aromatic.\n3. Pour in the coconut milk and chicken stock and bring to a gentle simmer.\n4. Add the tofu and prawns and cook for 5 minutes.\n5. Season with the fish sauce, sugar and lime juice.\n6. Divide the noodles between bowls and ladle the broth over them.\n7. Top with cucumber, bean sprouts and lime leaves.",
            'is_public' => true,
            'image_source' => 'https://www.themealdb.com/images/media/meals/rvypwy1503069308.jpg',
            'image_slug' => 'laksa-serut',
            'tutorial_url' => 'https://www.youtube.com/watch?v=OcarztU8cYo',
        ],
        [
            'owner' => 'aisyah',
            'name' => 'Rendang Daging',
            'description' => 'The celebrated dry curry of West Sumatra, slow braised in coconut milk until the beef is meltingly tender and dark.',
            'category' => 'Dinner',
            'cooking_time' => 150,
            'difficulty' => 'Hard',
            'servings' => 6,
            'ingredients' => "1kg beef, cut into cubes\n4 tablespoons grated coconut\n1 stalk lemongrass, bruised\n2 kaffir lime leaves\n1 tablespoon galangal\n1 tablespoon tamarind paste\n1 teaspoon turmeric powder\n2 tablespoons chilli paste\n4 tablespoons coconut milk\n1 tablespoon cooking oil\n1 teaspoon salt",
            'instructions' => "1. Heat the oil in a heavy pot and fry the chilli paste until fragrant.\n2. Add the beef and brown it on all sides.\n3. Stir in the coconut milk and add enough water to cover the meat.\n4. Add the lemongrass, lime leaves, galangal, turmeric and salt.\n5. Simmer gently for two hours, stirring now and then.\n6. Add the grated coconut and the tamarind paste.\n7. Cook uncovered until the liquid reduces to a thick, dark paste.",
            'is_public' => true,
            'image_source' => 'https://www.themealdb.com/images/media/meals/bc8v651619789840.jpg',
            'image_slug' => 'rendang-daging',
            'tutorial_url' => 'https://www.youtube.com/watch?v=Ot-dmfBaZrA',
        ],
        [
            'owner' => 'aisyah',
            'name' => 'Kuih Ketayap',
            'description' => 'Crisp sugar-pancake fritters filled with grated coconut, a classic Malay kuih for tea time.',
            'category' => 'Dessert',
            'cooking_time' => 30,
            'difficulty' => 'Easy',
            'servings' => 12,
            'ingredients' => "2 cups wheat flour\n1 teaspoon baking powder\n1/2 teaspoon salt\n3 eggs\n3 tablespoons castor sugar\n1 tablespoon margarine\n1 tablespoon condensed milk\n1 cup grated coconut\n12 pieces palm sugar (gula melaka)\nCooking oil for frying",
            'instructions' => "1. Mix the flour, baking powder and salt in a bowl.\n2. Beat the eggs, sugar, margarine and condensed milk until smooth.\n3. Fold the wet mixture into the dry ingredients to form a smooth dough.\n4. Divide the dough into 12 balls and flatten each into a disc.\n5. Fill the centre of each disc with grated coconut and a piece of palm sugar, then seal.\n6. Fry in moderately hot oil until golden brown.\n7. Drain, cool slightly and serve warm.",
            'is_public' => false,
            'image_source' => 'https://www.themealdb.com/images/media/meals/oaqz9f1766593912.jpg',
            'image_slug' => 'kuih-ketayap',
            'tutorial_url' => 'https://www.youtube.com/watch?v=gK1b5gymAUY',
        ],
        [
            'owner' => 'aisyah',
            'name' => 'Bubur Ayam Berempah',
            'description' => 'A comforting chicken congee flavoured with ginger, garlic and dried shrimp, finished with fried shallots.',
            'category' => 'Breakfast',
            'cooking_time' => 40,
            'difficulty' => 'Easy',
            'servings' => 4,
            'ingredients' => "150g rice\n1.2 litres chicken stock\n1 whole chicken\n2 tablespoons dried shrimp, soaked\n1 thumb ginger\n2 cloves garlic\n1 teaspoon white pepper\n1 teaspoon salt\nSpring onion and fried shallots for serving",
            'instructions' => "1. Rinse the rice until the water runs clear.\n2. Bring the chicken stock to a boil with the ginger and garlic.\n3. Add the rice and simmer, stirring occasionally to stop it sticking.\n4. Add the chicken pieces and the soaked dried shrimp.\n5. Season with salt and white pepper.\n6. Simmer until the rice breaks down into a thick, creamy porridge.\n7. Top with spring onion and fried shallots.",
            'is_public' => false,
            'image_source' => 'https://www.themealdb.com/images/media/meals/1529446352.jpg',
            'image_slug' => 'bubur-ayam-berempah',
            'tutorial_url' => 'https://www.youtube.com/watch?v=kqEfk801E94',
        ],
        [
            'owner' => 'weijian',
            'name' => 'Char Kway Teow',
            'description' => 'Flat rice noodles wok-charred with smoky depth, prawns, Chinese sausage and bean sprouts.',
            'category' => 'Lunch',
            'cooking_time' => 35,
            'difficulty' => 'Medium',
            'servings' => 3,
            'ingredients' => "400g flat rice noodles\n250g prawns\n200g Chinese sausage, sliced\n2 eggs\n2 tablespoons dark soy sauce\n1 tablespoon light soy sauce\n1 teaspoon sugar\n2 cloves garlic\n2 tablespoons cooking oil\nBean sprouts and Chinese chives",
            'instructions' => "1. Soak the flat noodles in warm water for 20 minutes, then drain well.\n2. Heat a wok until smoking hot and add the oil.\n3. Fry the Chinese sausage until the fat renders, then add the garlic.\n4. Add the noodles and press them into the wok without stirring for a minute so they char.\n5. Add the prawns and toss until they turn pink.\n6. Push everything aside, crack in the eggs and scramble them through.\n7. Season with both soy sauces and the sugar, then add the bean sprouts and chives.",
            'is_public' => true,
            'image_source' => 'https://www.themealdb.com/images/media/meals/1529445434.jpg',
            'image_slug' => 'char-kway-teow',
            'tutorial_url' => 'https://www.youtube.com/watch?v=wzaTcpoFEaY',
        ],
        [
            'owner' => 'weijian',
            'name' => 'Hokkien Mee',
            'description' => 'Noodles simmered in a fragrant prawn and pork broth until they drink up every drop.',
            'category' => 'Dinner',
            'cooking_time' => 50,
            'difficulty' => 'Medium',
            'servings' => 4,
            'ingredients' => "400g yellow noodles\n250g prawns\n200g pork belly\n2 eggs\n4 shallots\n3 cloves garlic\n3 tablespoons cooking oil\n1 tablespoon oyster sauce\n1 tablespoon dark soy sauce\n1 teaspoon sugar\n1 litre chicken stock\nChinese chives and sambal belacan for serving",
            'instructions' => "1. Boil the pork and prawns separately, then slice the pork and keep the shells.\n2. Fry the sliced shallots in oil until golden and set aside.\n3. Fry the garlic, then add the pork shells and chicken stock and simmer for 15 minutes.\n4. Strain the broth and season it with the sauces and sugar.\n5. Boil the noodles separately and drain them thoroughly.\n6. Return the noodles to the pot, pour in the broth and let them absorb it for 2 minutes.\n7. Top with prawns, sliced pork, fried shallots, chives and sambal.",
            'is_public' => true,
            'image_source' => 'https://www.themealdb.com/images/media/meals/f3cxnc1765656994.jpg',
            'image_slug' => 'hokkien-mee',
            'tutorial_url' => 'https://www.youtube.com/watch?v=DiFUyPAGoug',
        ],
        [
            'owner' => 'weijian',
            'name' => 'Mee Goreng Mamak',
            'description' => 'Penang-style fried noodles with beef, curry powder and a fiery chilli kick, best eaten with lime.',
            'category' => 'Lunch',
            'cooking_time' => 25,
            'difficulty' => 'Easy',
            'servings' => 2,
            'ingredients' => "300g yellow noodles\n200g beef slices\n2 eggs\n1 onion, sliced\n2 tablespoons cooking oil\n1 tablespoon curry powder\n1 tablespoon chilli paste\n1 tablespoon soy sauce\n1 teaspoon sugar\nLime and sambal for serving",
            'instructions' => "1. Boil the noodles for 3 minutes, then drain and toss with a little oil.\n2. Heat the oil in a wok and stir-fry the beef until browned, then set it aside.\n3. Fry the onion and the curry powder until aromatic.\n4. Add the chilli paste, the beef and the noodles back to the wok.\n5. Season with soy sauce and sugar and toss over high heat.\n6. Push the noodles aside, crack in the eggs and scramble them through.\n7. Serve with lime and sambal.",
            'is_public' => false,
            'image_source' => 'https://www.themealdb.com/images/media/meals/xquakq1619787532.jpg',
            'image_slug' => 'mee-goreng-mamak',
            'tutorial_url' => 'https://www.youtube.com/watch?v=pOJEmDBgtSk',
        ],
        [
            'owner' => 'weijian',
            'name' => 'Steamboat Szechuan',
            'description' => 'A numbing, spicy broth for a cook-it-yourself dinner with sliced beef, fish balls and greens.',
            'category' => 'Dinner',
            'cooking_time' => 30,
            'difficulty' => 'Easy',
            'servings' => 4,
            'ingredients' => "1 litre clear chicken stock\n2 tablespoons Sichuan peppercorns\n4 dried chillies\n4 cloves garlic\n1 tablespoon fermented bean paste\n300g sliced beef\n200g fish balls\n1 bunch bok choy\n1 pack tofu skin\n1 bunch coriander",
            'instructions' => "1. Bring the chicken stock to a boil with the Sichuan peppercorns and dried chillies.\n2. Add the garlic, fermented bean paste and coriander stems and simmer for 10 minutes.\n3. Strain the broth into a serving pot and keep it over a low flame.\n4. Arrange the beef, fish balls, tofu skin and bok choy on a platter.\n5. Let everyone cook the ingredients in the simmering broth.\n6. Drizzle with sesame oil and dip the cooked food in chilli oil or sambal.",
            'is_public' => false,
            'image_source' => 'https://www.themealdb.com/images/media/meals/uuuspp1511297945.jpg',
            'image_slug' => 'steamboat-szechuan',
            'tutorial_url' => 'https://www.youtube.com/watch?v=bXKWu4GojNI',
        ],
        [
            'owner' => 'weijian',
            'name' => 'Kuih Cincin',
            'description' => 'Buttery ring cookies with a vanilla glaze, crisp at the edges and soft in the middle.',
            'category' => 'Snack',
            'cooking_time' => 20,
            'difficulty' => 'Easy',
            'servings' => 24,
            'ingredients' => "250g butter, softened\n150g castor sugar\n1 egg\n300g plain flour\n2 tablespoons cornflour\n1/2 teaspoon salt\n1/2 teaspoon vanilla essence\nGlaze and hundreds-and-thousands to decorate",
            'instructions' => "1. Cream the butter and castor sugar until pale and fluffy.\n2. Beat in the egg and the vanilla essence.\n3. Sift in the flour, cornflour and salt and mix into a soft dough.\n4. Chill the dough for 15 minutes.\n5. Roll it out to 5mm thickness and cut out 5cm rounds.\n6. Cut a 2cm hole from the centre of each round to form a ring.\n7. Bake at 180 degrees for 12 minutes, then glaze and decorate while still warm.",
            'is_public' => false,
            'image_source' => 'https://www.themealdb.com/images/media/meals/7mxnzz1593350801.jpg',
            'image_slug' => 'kuih-cincin',
            'tutorial_url' => 'https://www.youtube.com/watch?v=VAR10T9mfhU',
        ],
    ];

    /**
     * Favourites per account, mixing their own recipes with public ones.
     *
     * @var array<string, list<string>>
     */
    private const FAVORITES = [
        'aisyah' => [
            'Ayam Goreng Berempah',
            'Laksa Serut',
            'Rendang Daging',
            'Chicken Teriyaki',
            'Nasi Goreng Kampung',
        ],
        'weijian' => [
            'Char Kway Teow',
            'Hokkien Mee',
            'Mee Goreng Mamak',
            'Creamy Mushroom Pasta',
            'Chicken Fried Rice',
        ],
    ];

    /**
     * Meal plans for the current week, keyed by account.
     *
     * @var array<string, list<array{recipe: string, day: int, meal_type: string}>>
     */
    private const MEAL_PLANS = [
        'aisyah' => [
            ['recipe' => 'Bubur Ayam Berempah', 'day' => 0, 'meal_type' => 'Breakfast'],
            ['recipe' => 'Laksa Serut', 'day' => 1, 'meal_type' => 'Lunch'],
            ['recipe' => 'Ayam Goreng Berempah', 'day' => 2, 'meal_type' => 'Dinner'],
            ['recipe' => 'Nasi Goreng Kampung', 'day' => 3, 'meal_type' => 'Lunch'],
            ['recipe' => 'Rendang Daging', 'day' => 4, 'meal_type' => 'Dinner'],
        ],
        'weijian' => [
            ['recipe' => 'Omelette', 'day' => 0, 'meal_type' => 'Breakfast'],
            ['recipe' => 'Hokkien Mee', 'day' => 1, 'meal_type' => 'Dinner'],
            ['recipe' => 'Char Kway Teow', 'day' => 2, 'meal_type' => 'Lunch'],
            ['recipe' => 'Chicken Fried Rice', 'day' => 3, 'meal_type' => 'Dinner'],
            ['recipe' => 'Mee Goreng Mamak', 'day' => 4, 'meal_type' => 'Lunch'],
        ],
    ];

    /**
     * Cooking preferences per account.
     *
     * @var array<string, array<string, mixed>>
     */
    private const PREFERENCES = [
        'aisyah' => [
            'preferred_difficulty' => 'Medium',
            'max_cooking_time' => 60,
            'preferred_categories' => 'Lunch, Dinner',
            'preferred_meal_types' => 'Breakfast, Dinner',
            'additional_preferences' => 'Low oil, no coconut milk.',
        ],
        'weijian' => [
            'preferred_difficulty' => 'Easy',
            'max_cooking_time' => 45,
            'preferred_categories' => 'Lunch, Snack',
            'preferred_meal_types' => 'Lunch, Dinner',
            'additional_preferences' => 'Quick weekday meals that keep well for lunch.',
        ],
    ];

    public function run(): void
    {
        $users = $this->createAccounts();

        $recipesByName = $this->createRecipes($users);

        $this->createFavorites($users, $recipesByName);
        $this->createMealPlans($users, $recipesByName);
        $this->createPreferences($users);
    }

    /**
     * @return array<string, User>
     */
    private function createAccounts(): array
    {
        $users = [];

        foreach (self::ACCOUNTS as $key => $account) {
            $user = User::where('email', $account['email'])->first();

            if (! $user) {
                $user = User::factory()->create($account);
            }

            $users[$key] = $user;
        }

        $this->command?->info('2 additional accounts ready.');

        return $users;
    }

    /**
     * Insert the ten fully detailed recipes, downloading a real photo for each.
     *
     * @param  array<string, User>  $users
     * @return array<string, Recipe>
     */
    private function createRecipes(array $users): array
    {
        $recipesByName = [];
        $missingImages = [];

        foreach (self::RECIPES as $recipe) {
            $owner = $users[$recipe['owner']];

            $existing = Recipe::where('user_id', $owner->id)
                ->where('name', $recipe['name'])
                ->first();

            $image = $existing?->image
                ?? $this->storeRecipeImage(
                    $recipe['image_source'],
                    $recipe['image_slug'],
                    $recipe['name'],
                );

            if ($image === null) {
                $missingImages[] = $recipe['name'];
            }

            $attributes = [
                'user_id' => $owner->id,
                'name' => $recipe['name'],
                'description' => $recipe['description'],
                'category' => $recipe['category'],
                'cooking_time' => $recipe['cooking_time'],
                'difficulty' => $recipe['difficulty'],
                'servings' => $recipe['servings'],
                'ingredients' => $recipe['ingredients'],
                'instructions' => $recipe['instructions'],
                'image' => $image,
                'tutorial_url' => $recipe['tutorial_url'],
                'is_public' => $recipe['is_public'],
            ];

            $recipesByName[$recipe['name']] = $existing
                ? tap($existing)->update($attributes)
                : Recipe::create($attributes);
        }

        $this->command?->info(sprintf(
            '%d recipes inserted, %d public and %d private.',
            count(self::RECIPES),
            count(array_filter(self::RECIPES, fn (array $recipe): bool => $recipe['is_public'])),
            count(array_filter(self::RECIPES, fn (array $recipe): bool => ! $recipe['is_public'])),
        ));

        if ($missingImages !== []) {
            $this->command?->warn(
                'Could not download an image for: '.implode(', ', $missingImages)
            );
        }

        return $recipesByName;
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, Recipe>  $recipesByName
     */
    private function createFavorites(array $users, array $recipesByName): void
    {
        foreach (self::FAVORITES as $key => $names) {
            foreach ($names as $name) {
                $recipe = $recipesByName[$name] ?? Recipe::where('name', $name)->first();

                if (! $recipe) {
                    $this->command?->warn("Skipping favourite for missing recipe: {$name}");

                    continue;
                }

                Favorite::firstOrCreate([
                    'user_id' => $users[$key]->id,
                    'recipe_id' => $recipe->id,
                ]);
            }
        }
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, Recipe>  $recipesByName
     */
    private function createMealPlans(array $users, array $recipesByName): void
    {
        $startOfWeek = now()->startOfWeek();

        foreach (self::MEAL_PLANS as $key => $plans) {
            foreach ($plans as $plan) {
                $recipe = $recipesByName[$plan['recipe']] ?? Recipe::where('name', $plan['recipe'])->first();

                if (! $recipe) {
                    $this->command?->warn("Skipping meal plan for missing recipe: {$plan['recipe']}");

                    continue;
                }

                MealPlan::firstOrCreate(
                    [
                        'user_id' => $users[$key]->id,
                        'date' => $startOfWeek->copy()->addDays($plan['day'])->toDateString(),
                        'meal_type' => $plan['meal_type'],
                    ],
                    ['recipe_id' => $recipe->id],
                );
            }

            $this->buildShoppingList($users[$key], $startOfWeek);
        }

        $this->command?->info('Meal plans and shopping lists inserted.');
    }

    /**
     * Mirror MealPlanController::syncShoppingList() so the seeded list stays
     * consistent with the seeded meals and re-seeding does not duplicate rows.
     */
    private function buildShoppingList(User $user, Carbon $startOfWeek): void
    {
        $endOfWeek = $startOfWeek->copy()->addDays(6);

        ShoppingList::where('user_id', $user->id)
            ->whereDate('week_start', $startOfWeek->toDateString())
            ->delete();

        $mealPlans = MealPlan::with('recipe')
            ->where('user_id', $user->id)
            ->whereBetween('date', [
                $startOfWeek->toDateString(),
                $endOfWeek->toDateString(),
            ])
            ->get();

        $savedIngredients = [];

        foreach ($mealPlans as $mealPlan) {
            if (! $mealPlan->recipe) {
                continue;
            }

            $recipeId = $mealPlan->recipe->id;

            $ingredients = preg_split(
                '/\r\n|\r|\n/',
                $mealPlan->recipe->ingredients
            );

            foreach ($ingredients as $ingredient) {
                $ingredient = trim($ingredient);

                if ($ingredient === '') {
                    continue;
                }

                $parsed = IngredientParser::parse($ingredient);

                $ingredientName = trim($parsed['ingredient']);

                if ($ingredientName === '') {
                    continue;
                }

                $key = $recipeId.'|'.strtolower($ingredientName);

                if (isset($savedIngredients[$key])) {
                    continue;
                }

                $savedIngredients[$key] = true;

                ShoppingList::create([
                    'user_id' => $user->id,
                    'week_start' => $startOfWeek->toDateString(),
                    'recipe_id' => $recipeId,
                    'ingredient' => ucwords($ingredientName),
                    'quantity' => $parsed['quantity'],
                    'is_checked' => false,
                ]);
            }
        }

        $checkedIds = ShoppingList::where('user_id', $user->id)
            ->whereDate('week_start', $startOfWeek->toDateString())
            ->inRandomOrder()
            ->limit(3)
            ->pluck('id');

        ShoppingList::whereIn('id', $checkedIds)->update(['is_checked' => true]);
    }

    /**
     * @param  array<string, User>  $users
     */
    private function createPreferences(array $users): void
    {
        foreach (self::PREFERENCES as $key => $preferences) {
            UserPreference::updateOrCreate(
                ['user_id' => $users[$key]->id],
                $preferences,
            );
        }

        $this->command?->info('Preferences inserted.');
    }
}
