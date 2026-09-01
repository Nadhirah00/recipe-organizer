<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller
{
    public function edit()
    {
        $preference = UserPreference::firstOrCreate([
            'user_id' => Auth::id(),
        ]);

        return view('preferences.edit', compact('preference'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'preferred_difficulty' => 'nullable|in:Easy,Medium,Hard',
            'max_cooking_time' => 'nullable|integer|min:1',
            'preferred_categories' => 'nullable|array',
            'preferred_meal_types' => 'nullable|array',
            'additional_preferences' => 'nullable|string|max:1000',
        ]);

        $validated['preferred_categories'] =
            !empty($validated['preferred_categories'])
                ? implode(', ', $validated['preferred_categories'])
                : null;

        $validated['preferred_meal_types'] =
            !empty($validated['preferred_meal_types'])
                ? implode(', ', $validated['preferred_meal_types'])
                : null;

        UserPreference::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'preferred_difficulty' => $validated['preferred_difficulty'] ?? null,
                'max_cooking_time' => $validated['max_cooking_time'] ?? null,
                'preferred_categories' => $validated['preferred_categories'],
                'preferred_meal_types' => $validated['preferred_meal_types'],
                'additional_preferences' => $validated['additional_preferences'] ?? null,
            ]
        );

        return redirect('/preferences')
            ->with('success', 'Your preferences have been saved!');
    }
}