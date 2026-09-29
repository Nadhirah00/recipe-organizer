<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

<meta charset="utf-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1"
>

<title>
    Recipe Organizer
</title>

@vite([
    'resources/css/app.css',
    'resources/js/app.js'
])

</head>

<body class="min-h-screen bg-app-bg text-gray-800 antialiased flex flex-col">

<div class="w-full max-w-5xl mx-auto px-6 py-6 flex items-center justify-between">

    <a href="/" class="flex items-center gap-3">

        <span class="w-10 h-10 rounded-2xl bg-orange-50 border border-orange-100 flex items-center justify-center text-xl">
            🍳
        </span>

        <span class="text-xl font-bold tracking-tight text-gray-800">
            Recipe<span class="text-orange-500">Organizer</span>
        </span>

    </a>

    <nav class="flex items-center gap-3">

        <a
            href="{{ route('login') }}"
            class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold text-gray-700 hover:text-orange-500 transition"
        >
            Log in
        </a>

        <a
            href="{{ route('register') }}"
            class="inline-flex items-center justify-center gap-2 bg-orange-500 text-white px-4 py-2 rounded-xl text-sm font-semibold shadow-sm hover:bg-orange-600 hover:shadow-md transition"
        >
            Create account
        </a>

    </nav>

</div>

<main class="flex-1 w-full max-w-5xl mx-auto px-6 flex flex-col items-center justify-center text-center py-12 md:py-16">

    <span class="orange-badge">
        Your recipes, in one place
    </span>

    <h1 class="mt-5 text-4xl md:text-6xl font-bold tracking-tight text-gray-800 max-w-3xl">
        Save, explore and plan with
        <span class="text-orange-500">Recipe Organizer</span>
    </h1>

    <p class="mt-5 text-lg text-gray-600 max-w-xl leading-7">
        Keep your favorite recipes close, discover new ones, and turn them into a weekly meal plan and shopping list.
    </p>

    <div class="mt-8 flex flex-col sm:flex-row gap-3">

        <a href="{{ route('register') }}" class="primary-button">
            Get started
        </a>

        <a href="{{ route('login') }}" class="outline-button">
            I already have an account
        </a>

    </div>

</main>

<section class="w-full max-w-5xl mx-auto px-6 pb-16">

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 text-left">

            <span class="text-3xl">🍽️</span>

            <h2 class="text-lg font-bold text-gray-800 mt-3">
                My Recipes
            </h2>

            <p class="text-sm text-gray-500 mt-1 leading-6">
                Keep every recipe you love in one collection, with photos and step-by-step instructions.
            </p>

        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 text-left">

            <span class="text-3xl">🌎</span>

            <h2 class="text-lg font-bold text-gray-800 mt-3">
                Explore &amp; Discover
            </h2>

            <p class="text-sm text-gray-500 mt-1 leading-6">
                Browse recipes from other cooks or find ideas from the ingredients already in your kitchen.
            </p>

        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 text-left">

            <span class="text-3xl">🗓️</span>

            <h2 class="text-lg font-bold text-gray-800 mt-3">
                Plan &amp; Shop
            </h2>

            <p class="text-sm text-gray-500 mt-1 leading-6">
                Arrange meals for the week and turn them into an automatic shopping list.
            </p>

        </div>

    </div>

</section>

</body>

</html>