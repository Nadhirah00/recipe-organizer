<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\UserPreference;
use App\Models\MealPlan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AiMealPlanController extends Controller
{
    public function generate(Request $request)
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Selected week
        |--------------------------------------------------------------------------
        */

        $startOfWeek = $request->filled('week')
            ? Carbon::parse($request->week)->startOfWeek()
            : now()->startOfWeek();

        $endOfWeek = $startOfWeek->copy()->addDays(6);


        /*
        |--------------------------------------------------------------------------
        | Get saved preferences
        |--------------------------------------------------------------------------
        */

        $savedPreferences = UserPreference::where(
            'user_id',
            $user->id
        )->first();


        /*
        |--------------------------------------------------------------------------
        | Decide which preferences to use
        |
        | use_custom = 1
        | → use ONLY the temporary preferences from this week
        |
        | use_custom = 0
        | → use the user's saved preferences
        |--------------------------------------------------------------------------
        */

        $useCustom = $request->boolean('use_custom');

        if ($useCustom) {

            $preferenceData = [
                'difficulty' => $request->input('difficulty'),
                'max_cooking_time' => $request->input('max_cooking_time'),
                'categories' => $request->input('categories'),
                'meal_types' => $request->input('meal_types'),
                'additional_preferences' => $request->input('additional_preferences'),
            ];

        } else {

            $preferenceData = [
                'difficulty' => $savedPreferences?->preferred_difficulty,
                'max_cooking_time' => $savedPreferences?->max_cooking_time,
                'categories' => $savedPreferences?->preferred_categories,
                'meal_types' => $savedPreferences?->preferred_meal_types,
                'additional_preferences' => $savedPreferences?->additional_preferences,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Get user's recipes
        |--------------------------------------------------------------------------
        */

        $recipes = Recipe::where('user_id', $user->id)
            ->latest()
            ->get();

        if ($recipes->isEmpty()) {

            return redirect('/preferences')
                ->with(
                    'error',
                    'Please add some recipes before using AI meal planning.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Prepare recipe data for Gemini
        |--------------------------------------------------------------------------
        */

        $recipeData = $recipes
            ->map(function ($recipe) {

                return [
                    'id' => $recipe->id,
                    'name' => $recipe->name,
                    'category' => $recipe->category,
                    'cooking_time' => $recipe->cooking_time,
                    'difficulty' => $recipe->difficulty,
                ];

            })
            ->values()
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | Normalize meal types
        |--------------------------------------------------------------------------
        */

        $requestedMealTypes = $preferenceData['meal_types']
            ?? ['Breakfast', 'Lunch', 'Dinner'];

        if (is_string($requestedMealTypes)) {

            $requestedMealTypes = array_map(
                'trim',
                explode(',', $requestedMealTypes)
            );
        }

        $requestedMealTypes = array_values(
            array_filter($requestedMealTypes)
        );

        if (empty($requestedMealTypes)) {

            $requestedMealTypes = [
                'Breakfast',
                'Lunch',
                'Dinner',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Week information
        |--------------------------------------------------------------------------
        */

        $weekStart = $startOfWeek->format('Y-m-d');
        $weekEnd = $endOfWeek->format('Y-m-d');


        /*
        |--------------------------------------------------------------------------
        | Gemini prompt
        |--------------------------------------------------------------------------
        */

        $prompt = "
    You are a weekly meal planning assistant.

    Create a practical and varied 7-day meal plan for the specific week:

    {$weekStart} to {$weekEnd}

    IMPORTANT:
    Use ONLY the recipes provided in the available recipes list.

    USER PREFERENCES FOR THIS WEEK:
    " . json_encode(
        $preferenceData,
        JSON_PRETTY_PRINT
    ) . "

    AVAILABLE RECIPES:
    " . json_encode(
        $recipeData,
        JSON_PRETTY_PRINT
    ) . "

    IMPORTANT RULES:

    1. Only use recipes from the available recipes list.
    2. Never invent a recipe.
    3. Never invent a recipe_id.
    4. Every recipe_id must exactly match an available recipe.
    5. Plan ONLY the requested meal types:
    " . implode(', ', $requestedMealTypes) . "
    6. Create all seven days:
    Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday.
    7. Every requested meal type should have a suitable recipe whenever possible.
    8. Do NOT use the same recipe twice on the same day.
    9. Do NOT use the same recipe more than 2 times during the week when enough alternatives exist.
    10. Avoid using the same recipe on consecutive meals when possible.
    11. Prioritize variety across the whole week.
    12. Prefer recipes whose category matches the meal type.
    13. For Breakfast, prefer Breakfast recipes.
    14. For Lunch, prefer Lunch recipes.
    15. For Dinner, prefer Dinner recipes.
    16. Do not normally use Dessert or Drinks as Breakfast, Lunch, or Dinner when better alternatives exist.
    17. Respect the requested difficulty where possible.
    18. Respect the maximum cooking time where possible.
    19. Respect the requested categories where possible.
    20. If there are not enough recipes in the preferred category, use another suitable recipe from the available list.
    21. Do not return 'No suggestion' when a suitable available recipe exists.
    22. Every generated meal must contain both recipe_id and recipe_name.
    23. Return all seven days even if some days need a fallback recipe.
    24. Return ONLY valid JSON.
    25. Do not include markdown code fences.

    Use this exact JSON structure:

    {
        \"Monday\": {
            \"Breakfast\": {
                \"recipe_id\": 1,
                \"recipe_name\": \"Recipe Name\"
            },
            \"Lunch\": {
                \"recipe_id\": 2,
                \"recipe_name\": \"Recipe Name\"
            },
            \"Dinner\": {
                \"recipe_id\": 3,
                \"recipe_name\": \"Recipe Name\"
            }
        }
    }
    ";


        /*
        |--------------------------------------------------------------------------
        | Send request to Gemini
        |--------------------------------------------------------------------------
        */

        $response = Http::timeout(60)->post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash-lite:generateContent?key='
            . config('services.gemini.api_key'),
            [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt,
                            ],
                        ],
                    ],
                ],
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Check Gemini response
        |--------------------------------------------------------------------------
        */

        if ($response->failed()) {

            return redirect(
                '/meal-planner?week=' .
                $startOfWeek->format('Y-m-d')
            )->with(
                'error',
                'Unable to generate AI suggestions. Please try again.'
            );
        }


        $result = $response->json();


        $text = $result['candidates'][0]['content']['parts'][0]['text']
            ?? null;


        if (!$text) {

            return redirect(
                '/meal-planner?week=' .
                $startOfWeek->format('Y-m-d')
            )->with(
                'error',
                'AI did not return any suggestions.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Clean Gemini response
        |--------------------------------------------------------------------------
        */

        $text = trim($text);

        $text = preg_replace(
            '/^```json\s*/i',
            '',
            $text
        );

        $text = preg_replace(
            '/\s*```$/i',
            '',
            $text
        );

        $text = trim($text);


        /*
        |--------------------------------------------------------------------------
        | Convert JSON into PHP array
        |--------------------------------------------------------------------------
        */

        $suggestions = json_decode(
            $text,
            true
        );


        if (!is_array($suggestions)) {

            return redirect(
                '/meal-planner?week=' .
                $startOfWeek->format('Y-m-d')
            )->with(
                'error',
                'AI returned an invalid meal plan. Please try again.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Validate and improve AI suggestions
        |--------------------------------------------------------------------------
        */

        $suggestions = $this->validateAndImproveSuggestions(
            $suggestions,
            $recipes,
            $preferenceData
        );


        /*
        |--------------------------------------------------------------------------
        | Calculate meal coverage
        |--------------------------------------------------------------------------
        */

        $days = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday',
        ];


        $totalSlots =
            count($days) *
            count($requestedMealTypes);


        $filledSlots = 0;


        foreach ($days as $day) {

            foreach ($requestedMealTypes as $mealType) {

                if (
                    isset($suggestions[$day][$mealType]) &&
                    !empty($suggestions[$day][$mealType]['recipe_id'])
                ) {
                    $filledSlots++;
                }
            }
        }


        $missingSlots =
            $totalSlots - $filledSlots;


        $coveragePercentage =
            $totalSlots > 0
                ? round(
                    ($filledSlots / $totalSlots) * 100
                )
                : 0;


        /*
        |--------------------------------------------------------------------------
        | Return AI suggestions page
        |--------------------------------------------------------------------------
        */

        return view(
            'meal-plans.ai-suggestions',
            compact(
                'suggestions',
                'recipes',
                'startOfWeek',
                'endOfWeek',
                'filledSlots',
                'totalSlots',
                'missingSlots',
                'coveragePercentage'
            )
        );
    }

    public function accept(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'weekly_plan' => 'required|json',
            'week' => 'required|date',
        ]);

        $weeklyPlan = json_decode($request->weekly_plan, true);

        if (!is_array($weeklyPlan) || empty($weeklyPlan)) {
            return back()->with(
                'error',
                'The AI meal plan could not be accepted because no meals were submitted.'
            );
        }

        $startOfWeek = Carbon::parse($request->week)
            ->startOfWeek();

        $endOfWeek = $startOfWeek
            ->copy()
            ->addDays(6);

        $dayOffsets = [
            'Monday' => 0,
            'Tuesday' => 1,
            'Wednesday' => 2,
            'Thursday' => 3,
            'Friday' => 4,
            'Saturday' => 5,
            'Sunday' => 6,
        ];

        $mealsToSave = [];

        /*
        |--------------------------------------------------------------------------
        | Validate everything BEFORE deleting the existing week
        |--------------------------------------------------------------------------
        */

        foreach ($weeklyPlan as $meal) {

            if (
                empty($meal['recipe_id']) ||
                empty($meal['day']) ||
                empty($meal['meal_type'])
            ) {
                continue;
            }

            if (!isset($dayOffsets[$meal['day']])) {
                continue;
            }

            if (!in_array(
                $meal['meal_type'],
                ['Breakfast', 'Lunch', 'Dinner']
            )) {
                continue;
            }

            $recipe = Recipe::where('id', $meal['recipe_id'])
                ->where('user_id', $user->id)
                ->first();

            if (!$recipe) {
                continue;
            }

            $date = $startOfWeek
                ->copy()
                ->addDays($dayOffsets[$meal['day']]);

            $mealsToSave[] = [
                'user_id' => $user->id,
                'recipe_id' => $recipe->id,
                'date' => $date->toDateString(),
                'meal_type' => $meal['meal_type'],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Don't delete the existing week if there is nothing valid to save
        |--------------------------------------------------------------------------
        */

        if (empty($mealsToSave)) {
            return back()->with(
                'error',
                'The AI meal plan contains no valid meals to save. Please generate it again.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Replace only the selected week
        |--------------------------------------------------------------------------
        */

        \App\Models\MealPlan::where('user_id', $user->id)
            ->whereBetween('date', [
                $startOfWeek->toDateString(),
                $endOfWeek->toDateString(),
            ])
            ->delete();

        foreach ($mealsToSave as $meal) {
            \App\Models\MealPlan::create($meal);
        }

        return redirect(
            '/meal-planner?week=' . $startOfWeek->format('Y-m-d')
        )->with(
            'success',
            'Your AI weekly meal plan has been accepted and the previous plan for this week has been replaced.'
        );
    }

    private function validateAndImproveSuggestions(
        array $suggestions,
        $recipes,
        array $preferences
    ): array {
        $recipeMap = $recipes->keyBy('id');

        /*
        |--------------------------------------------------------------------------
        | Normalize meal types
        |--------------------------------------------------------------------------
        */

        $rawMealTypes = $preferences['meal_types'] ?? null;

        if (is_array($rawMealTypes)) {
            $mealTypes = array_map('trim', $rawMealTypes);
        } elseif (is_string($rawMealTypes) && trim($rawMealTypes) !== '') {
            $mealTypes = array_map('trim', explode(',', $rawMealTypes));
        } else {
            $mealTypes = ['Breakfast', 'Lunch', 'Dinner'];
        }

        $mealTypes = array_values(
            array_filter($mealTypes)
        );


        /*
        |--------------------------------------------------------------------------
        | If nothing is selected, use all meal types
        |--------------------------------------------------------------------------
        */

        if (empty($mealTypes)) {
            $mealTypes = ['Breakfast', 'Lunch', 'Dinner'];
        }


        /*
        |--------------------------------------------------------------------------
        | Seven days
        |--------------------------------------------------------------------------
        */

        $days = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday',
        ];


        /*
        |--------------------------------------------------------------------------
        | Track recipe usage
        |--------------------------------------------------------------------------
        */

        $usedCounts = [];

        $result = [];


        /*
        |--------------------------------------------------------------------------
        | Process EVERY day and EVERY selected meal type
        |--------------------------------------------------------------------------
        */

        foreach ($days as $day) {

            $result[$day] = [];

            $usedToday = [];


            foreach ($mealTypes as $mealType) {

                $recipeId = null;


                /*
                |--------------------------------------------------------------------------
                | 1. Try AI's recommendation
                |--------------------------------------------------------------------------
                */

                $aiMeal = $suggestions[$day][$mealType] ?? null;

                if (
                    is_array($aiMeal) &&
                    !empty($aiMeal['recipe_id'])
                ) {

                    $candidateId = (int) $aiMeal['recipe_id'];

                    if (
                        $recipeMap->has($candidateId) &&
                        !in_array($candidateId, $usedToday) &&
                        ($usedCounts[$candidateId] ?? 0) < 2
                    ) {
                        $recipeId = $candidateId;
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | 2. Prefer correct category
                |--------------------------------------------------------------------------
                */

                if (!$recipeId) {

                    $candidate = $recipes
                        ->filter(function ($recipe) use (
                            $mealType,
                            $usedToday,
                            $usedCounts
                        ) {

                            if (in_array($recipe->id, $usedToday)) {
                                return false;
                            }

                            if (($usedCounts[$recipe->id] ?? 0) >= 2) {
                                return false;
                            }

                            return strtolower($recipe->category) ===
                                strtolower($mealType);

                        })
                        ->sortBy(function ($recipe) use ($usedCounts) {
                            return $usedCounts[$recipe->id] ?? 0;
                        })
                        ->first();

                    if ($candidate) {
                        $recipeId = $candidate->id;
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | 3. If no exact category match, use another suitable recipe
                |--------------------------------------------------------------------------
                */

                if (!$recipeId) {

                    $candidate = $recipes
                        ->filter(function ($recipe) use (
                            $usedToday,
                            $usedCounts
                        ) {

                            if (in_array($recipe->id, $usedToday)) {
                                return false;
                            }

                            if (($usedCounts[$recipe->id] ?? 0) >= 2) {
                                return false;
                            }

                            if (in_array(
                                strtolower($recipe->category),
                                ['dessert', 'drinks']
                            )) {
                                return false;
                            }

                            return true;

                        })
                        ->sortBy(function ($recipe) use ($usedCounts) {
                            return $usedCounts[$recipe->id] ?? 0;
                        })
                        ->first();

                    if ($candidate) {
                        $recipeId = $candidate->id;
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | 4. Last fallback
                |--------------------------------------------------------------------------
                */

                if (!$recipeId) {

                    $candidate = $recipes
                        ->filter(function ($recipe) use ($usedToday) {
                            return !in_array(
                                $recipe->id,
                                $usedToday
                            );
                        })
                        ->sortBy(function ($recipe) use ($usedCounts) {
                            return $usedCounts[$recipe->id] ?? 0;
                        })
                        ->first();

                    if ($candidate) {
                        $recipeId = $candidate->id;
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | 5. Save suggestion
                |--------------------------------------------------------------------------
                */

                if ($recipeId && $recipeMap->has($recipeId)) {

                    $recipe = $recipeMap->get($recipeId);

                    $usedCounts[$recipeId] =
                        ($usedCounts[$recipeId] ?? 0) + 1;

                    $usedToday[] = $recipeId;

                    $result[$day][$mealType] = [
                        'recipe_id' => $recipe->id,
                        'recipe_name' => $recipe->name,
                    ];
                }
            }
        }

        return $result;
    }

    public function setup(Request $request)
    {
        $user = Auth::user();

        $startOfWeek = $request->filled('week')
            ? Carbon::parse($request->week)->startOfWeek()
            : now()->startOfWeek();

        $endOfWeek = $startOfWeek->copy()->addDays(6);

        $preferences = UserPreference::where(
            'user_id',
            $user->id
        )->first();

        return view(
            'meal-plans.ai-setup',
            compact(
                'preferences',
                'startOfWeek',
                'endOfWeek'
            )
        );
    }
}