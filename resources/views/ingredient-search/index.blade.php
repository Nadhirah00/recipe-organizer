@extends('layouts.app')

@section('title', 'Find Recipes by Ingredients')

@section('content')

<div class="max-w-5xl mx-auto px-6 py-10">

    <!-- Header -->

    <div class="mb-8">

        <h1 class="text-4xl font-bold text-gray-800">
            🔎 Find Recipes From My Ingredients
        </h1>

        <p class="text-gray-500 mt-2">
            Enter the ingredients you already have and discover matching recipes.
        </p>

    </div>


    <!-- Search Card -->

    <form
        action="{{ route('ingredients.search.results') }}"
        method="POST"
        class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6"
    >

        @csrf


        <label
            for="ingredients"
            class="block text-lg font-semibold text-gray-800 mb-2"
        >
            What ingredients do you have?
        </label>

        <p class="text-sm text-gray-500 mb-4">
            Enter ingredients separated by commas or put one ingredient on each line.
        </p>


        <textarea
            id="ingredients"
            name="ingredients"
            rows="6"
            placeholder="Chicken
Egg
Rice
Garlic"
            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
        >{{ old('ingredients') }}</textarea>


        @if (session('error'))

            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mt-4">
                {{ session('error') }}
            </div>

        @endif


        @error('ingredients')

            <div class="text-red-500 text-sm mt-2">
                {{ $message }}
            </div>

        @enderror


        <div class="flex flex-col sm:flex-row gap-3 mt-5">

            <button
                type="submit"
                class="flex-1 bg-orange-500 text-white py-3 rounded-xl font-semibold hover:bg-orange-600 transition"
            >
                🔎 Find Matching Recipes
            </button>

            <a
                href="{{ url('/recipes') }}"
                class="flex-1 text-center border border-gray-300 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-50 transition"
            >
                View All Recipes
            </a>

        </div>

    </form>


    <!-- How it works -->

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">

        <div class="bg-orange-50 rounded-xl p-5">

            <div class="text-3xl mb-3">
                📝
            </div>

            <h3 class="font-bold text-gray-800">
                1. Enter Ingredients
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Tell us what you already have at home.
            </p>

        </div>


        <div class="bg-blue-50 rounded-xl p-5">

            <div class="text-3xl mb-3">
                🔍
            </div>

            <h3 class="font-bold text-gray-800">
                2. Find Matches
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Your ingredients are compared with your recipes.
            </p>

        </div>


        <div class="bg-green-50 rounded-xl p-5">

            <div class="text-3xl mb-3">
                🍳
            </div>

            <h3 class="font-bold text-gray-800">
                3. Choose a Recipe
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                See how much of each recipe you can make.
            </p>

        </div>

    </div>

</div>

@endsection
