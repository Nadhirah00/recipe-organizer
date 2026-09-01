<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

use App\Http\Controllers\AiMealPlanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExploreRecipeController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\IngredientSearchController;
use App\Http\Controllers\MealPlanController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\UserPreferenceController;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::get('/register', [AuthController::class,'showRegister'])
    ->name('register');

Route::post('/register', [AuthController::class,'register']);

Route::get('/login', [AuthController::class,'showLogin'])
    ->name('login');
Route::post('/login', [AuthController::class,'login']);
Route::post('/logout', [AuthController::class,'logout'])
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Forgot Password
|--------------------------------------------------------------------------
|
| These routes allow users to request a password reset link and
| create a new password using the reset token.
|
*/

Route::get('/forgot-password', function () {

    return view('auth.forgot-password');

})->middleware('guest')->name('password.request');

Route::post('/forgot-password', function (Request $request) {

    $request->validate([
        'email' => ['required', 'email'],
    ]);


    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::ResetLinkSent

        ? back()->with(
            'success',
            __($status)
        )

        : back()->withErrors([
            'email' => __($status),
        ]);

})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function (
    string $token,
    Request $request
) {

    return view(
        'auth.reset-password',
        [
            'token' => $token,
            'email' => $request->email,
        ]
    );

})->middleware('guest')->name('password.reset');

Route::post('/reset-password', function (
    Request $request
) {

    $request->validate([
        'token' => ['required'],
        'email' => ['required', 'email'],
        'password' => [
            'required',
            'confirmed',
            'min:8'
        ],
    ]);

    $status = Password::reset(

        $request->only(
            'email',
            'password',
            'password_confirmation',
            'token'
        ),

        function (
            $user,
            $password
        ) {

            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();

        }
    );

    return $status === Password::PasswordReset

        ? redirect('/login')->with(
            'success',
            'Your password has been reset successfully. You can now log in.'
        )

        : back()->withErrors([
            'email' => __($status),
        ]);

})->middleware('guest')->name('password.update');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class,'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Recipes
    |--------------------------------------------------------------------------
    */
    Route::get('/recipes', [RecipeController::class,'index']);
    Route::get('/recipes/create', [RecipeController::class,'create']);
    Route::post('/recipes', [RecipeController::class,'store']);
    Route::get('/recipes/{id}', [RecipeController::class,'show']);
    Route::get('/recipes/{id}/edit', [RecipeController::class,'edit']);
    Route::put('/recipes/{id}', [RecipeController::class,'update']);
    Route::delete('/recipes/{id}', [RecipeController::class,'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Favorites
    |--------------------------------------------------------------------------
    */
    Route::post('/recipes/{id}/favorite', [FavoriteController::class,'store']);
    Route::delete('/recipes/{id}/favorite', [FavoriteController::class,'destroy']);
    Route::get('/favorites', [FavoriteController::class,'index']);

    /*
    |--------------------------------------------------------------------------
    | Explore Recipes
    |--------------------------------------------------------------------------
    */
    Route::get('/discover', [RecipeController::class,'discover'])
        ->name('recipes.discover');
    Route::get('/explore', [ExploreRecipeController::class,'index'])
        ->name('recipes.explore');

    /*
    |--------------------------------------------------------------------------
    | Find Recipes By Ingredients
    |--------------------------------------------------------------------------
    */
    Route::get('/find-by-ingredients', [IngredientSearchController::class,'index'])
        ->name('ingredients.search');
    Route::post('/find-by-ingredients', [IngredientSearchController::class,'search'])
        ->name('ingredients.search.results');

    /*
    |--------------------------------------------------------------------------
    | Meal Planner
    |--------------------------------------------------------------------------
    */
    Route::get('/meal-planner', [MealPlanController::class,'index']);
    Route::get('/meal-planner/recipes/search', [MealPlanController::class,'searchRecipes'])
        ->name('meal-planner.recipes.search');
    Route::post('/meal-planner', [MealPlanController::class,'store']);
    Route::delete('/meal-planner/{id}', [MealPlanController::class,'destroy']);
    Route::delete('/meal-planner', [MealPlanController::class,'clear'])
        ->name('meal-planner.clear');

    /*
    |--------------------------------------------------------------------------
    | Meal Preferences
    |--------------------------------------------------------------------------
    */
    Route::get('/preferences', [UserPreferenceController::class,'edit']);
    Route::put('/preferences', [UserPreferenceController::class,'update']);

    /*
    |--------------------------------------------------------------------------
    | AI Meal Planning
    |--------------------------------------------------------------------------
    */
    Route::get('/meal-planner/ai', [AiMealPlanController::class,'generate']);
    Route::get('/meal-planner/ai/setup', [AiMealPlanController::class,'setup'])
        ->name('meal-planner.ai.setup');
    Route::post('/meal-planner/ai/generate', [AiMealPlanController::class,'generate'])
        ->name('meal-planner.ai.generate');
    Route::post('/meal-planner/accept', [AiMealPlanController::class,'accept'])
        ->name('meal-planner.accept');

    /*
    |--------------------------------------------------------------------------
    | Shopping List
    |--------------------------------------------------------------------------
    */
    Route::get('/shopping-list', [ShoppingListController::class,'index']);
    Route::post('/shopping-list/generate', [ShoppingListController::class,'generate']);
    Route::patch('/shopping-list/{id}/toggle', [ShoppingListController::class,'toggle']);
    Route::delete('/shopping-list/{id}', [ShoppingListController::class,'destroy']);
    Route::delete('/shopping-list', [ShoppingListController::class,'clear']);

});
