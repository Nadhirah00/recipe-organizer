@extends('layouts.app')

@section('title', $recipe->name)

@section('content')

@php
    $referer = request()->headers->get('referer');

    $backUrl = parse_url($referer ?? '', PHP_URL_HOST) === request()->getHost()
        ? $referer
        : url('/recipes');
@endphp

<div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 md:py-8">

    <!-- Back -->

    <a
        href="{{ $backUrl }}"
        class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-orange-600 transition mb-4"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-4 h-4"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>

        Back
    </a>


    <!-- Recipe Card -->

    <article class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">


        <!-- ===================================================== -->
        <!-- Hero Image -->
        <!-- ===================================================== -->

        <div class="relative h-56 sm:h-64 md:h-80 bg-orange-50 overflow-hidden">

            @if ($recipe->image)

                <img
                    src="{{ asset('storage/' . $recipe->image) }}"
                    alt="{{ $recipe->name }}"
                    class="w-full h-full object-cover"
                >

            @else

                <div class="w-full h-full flex items-center justify-center">

                    <span class="text-7xl">
                        🍳
                    </span>

                </div>

            @endif


            <!-- Visibility Badge -->

            <div class="absolute top-4 left-4 z-10">

                @if ($recipe->is_public)

                    <span class="inline-flex items-center gap-1.5 bg-white/95 backdrop-blur px-3 py-1.5 rounded-full text-xs font-semibold text-green-700 shadow-sm">
                        🌎 Public
                    </span>

                @else

                    <span class="inline-flex items-center gap-1.5 bg-white/95 backdrop-blur px-3 py-1.5 rounded-full text-xs font-semibold text-gray-600 shadow-sm">
                        🔒 Private
                    </span>

                @endif

            </div>


            <!-- ================================================= -->
            <!-- Actions -->
            <!-- ================================================= -->

            <div class="absolute top-4 right-4 z-10 flex items-center gap-1.5">

                @auth

                    @if (
                        $recipe->favorites()
                            ->where('user_id', auth()->id())
                            ->exists()
                    )

                        <form
                            action="{{ url('/recipes/' . $recipe->id . '/favorite') }}"
                            method="POST"
                            data-favorite-toggle
                            data-favorite="1"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                title="Remove from favourites"
                                aria-label="Remove {{ $recipe->name }} from favourites"
                                class="w-9 h-9 inline-flex items-center justify-center bg-white/95 backdrop-blur rounded-full shadow-sm text-sm hover:bg-red-500 hover:text-white transition"
                            >
                                ❤️
                            </button>

                        </form>

                    @else

                        <form
                            action="{{ url('/recipes/' . $recipe->id . '/favorite') }}"
                            method="POST"
                            data-favorite-toggle
                            data-favorite="0"
                        >

                            @csrf

                            <button
                                type="submit"
                                title="Add to favourites"
                                aria-label="Add {{ $recipe->name }} to favourites"
                                class="w-9 h-9 inline-flex items-center justify-center bg-white/95 backdrop-blur rounded-full shadow-sm text-sm hover:bg-red-500 hover:text-white transition"
                            >
                                ♡
                            </button>

                        </form>

                    @endif

                @endauth


                <button
                    type="button"
                    onclick="openRecipeMealModal()"
                    title="Add to meal plan"
                    aria-label="Add {{ $recipe->name }} to meal plan"
                    class="w-9 h-9 inline-flex items-center justify-center bg-white/95 backdrop-blur text-gray-600 rounded-full shadow-sm text-sm hover:bg-orange-500 hover:text-white transition"
                >
                    📅
                </button>


                @auth

                    @if ($recipe->user_id === auth()->id())

                        <a
                            href="{{ url('/recipes/' . $recipe->id . '/edit') }}"
                            title="Edit recipe"
                            aria-label="Edit {{ $recipe->name }}"
                            class="w-9 h-9 inline-flex items-center justify-center bg-white/95 backdrop-blur text-gray-600 rounded-full shadow-sm text-sm hover:bg-orange-500 hover:text-white transition"
                        >
                            ✏️
                        </a>


                        <form
                            action="{{ url('/recipes/' . $recipe->id) }}"
                            method="POST"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                onclick="return confirm('Are you sure you want to delete this recipe?')"
                                title="Delete recipe"
                                aria-label="Delete {{ $recipe->name }}"
                                class="w-9 h-9 inline-flex items-center justify-center bg-white/95 backdrop-blur text-gray-600 rounded-full shadow-sm text-sm hover:bg-red-500 hover:text-white transition"
                            >
                                🗑️
                            </button>

                        </form>

                    @endif

                @endauth

            </div>

        </div>


        <!-- ===================================================== -->
        <!-- Main Content -->
        <!-- ===================================================== -->

        <div class="p-5 sm:p-6 md:p-8">


            <!-- Owner -->

            <p class="text-xs text-gray-500">

                👨‍🍳 By

                @auth

                    @if ($recipe->user_id === auth()->id())

                        <span class="font-semibold text-orange-600">
                            You
                        </span>

                    @else

                        <span class="font-semibold text-gray-700">
                            {{ $recipe->user->name }}
                        </span>

                    @endif

                @else

                    <span class="font-semibold text-gray-700">
                        {{ $recipe->user->name }}
                    </span>

                @endauth

            </p>


            <!-- Title -->

            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight text-gray-800 mt-1">
                {{ $recipe->name }}
            </h1>


            @if ($recipe->description)

                <p class="text-sm sm:text-base text-gray-500 leading-7 mt-2">
                    {{ $recipe->description }}
                </p>

            @endif


            <!-- Recipe Summary -->

            <div class="flex flex-wrap gap-2 mt-4">

                <span class="inline-flex items-center gap-1.5 bg-orange-50 text-orange-700 px-3 py-1.5 rounded-full text-xs font-semibold">
                    🏷️ {{ $recipe->category }}
                </span>

                <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 px-3 py-1.5 rounded-full text-xs font-semibold">
                    ⏱️ {{ $recipe->cooking_time }} min
                </span>

                <span class="inline-flex items-center gap-1.5 bg-purple-50 text-purple-700 px-3 py-1.5 rounded-full text-xs font-semibold">
                    ⭐ {{ $recipe->difficulty }}
                </span>

                <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-3 py-1.5 rounded-full text-xs font-semibold">
                    👥 {{ $recipe->servings }}
                    {{ $recipe->servings == 1 ? 'serving' : 'servings' }}
                </span>

            </div>


            <!-- ================================================= -->
            <!-- Tutorial + Ingredients + Instructions -->
            <!-- ================================================= -->

            <div class="border-t border-gray-100 mt-8 pt-8">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">


                    <!-- Left Column -->

                    <div class="lg:col-span-5">


                        <!-- Tutorial -->

                        @if ($recipe->tutorial_url)

                            <section class="mb-8">

                                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                                    Learn visually
                                </p>

                                <h2 class="text-lg font-bold text-gray-800 mt-1">
                                    Video Tutorial
                                </h2>

                                @if ($embedUrl = $recipe->youtubeEmbedUrl())

                                    <div class="mt-3 aspect-video rounded-2xl overflow-hidden bg-gray-900 shadow-sm">

                                        <iframe
                                            src="{{ $embedUrl }}"
                                            title="{{ $recipe->name }} video tutorial"
                                            class="w-full h-full"
                                            loading="lazy"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                            referrerpolicy="strict-origin-when-cross-origin"
                                            allowfullscreen
                                        ></iframe>

                                    </div>

                                    <a
                                        href="{{ $recipe->tutorial_url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-block text-xs text-gray-500 hover:text-gray-700 underline underline-offset-4 mt-2"
                                    >
                                        Watch on YouTube
                                    </a>

                                @else

                                    <a
                                        href="{{ $recipe->tutorial_url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center gap-2 bg-red-500 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-red-600 transition mt-3"
                                    >
                                        ▶ Watch Tutorial
                                    </a>

                                @endif

                            </section>

                        @endif


                        <!-- Ingredients -->

                        <section>

                            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                                What you need
                            </p>

                            <h2 class="text-lg font-bold text-gray-800 mt-1">
                                Ingredients
                            </h2>

                            <div class="mt-3">

                                @forelse ($recipe->ingredientList() as $ingredient)

                                    <div class="flex items-start gap-3 py-2.5 border-b border-gray-100 last:border-0">

                                        <span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-orange-400 shrink-0"></span>

                                        <span class="text-sm text-gray-700 leading-6">
                                            {{ $ingredient }}
                                        </span>

                                    </div>

                                @empty

                                    <p class="text-sm text-gray-400">
                                        No ingredients listed yet.
                                    </p>

                                @endforelse

                            </div>

                        </section>

                    </div>


                    <!-- Instructions -->

                    <section class="lg:col-span-7">

                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                            How to make it
                        </p>

                        <h2 class="text-lg font-bold text-gray-800 mt-1">
                            Instructions
                        </h2>

                        <div class="mt-3 space-y-3">

                            @forelse ($recipe->instructionSteps() as $step)

                                <div class="flex items-start gap-3">

                                    <span class="mt-0.5 w-6 h-6 shrink-0 rounded-full bg-orange-50 text-orange-600 text-xs font-bold inline-flex items-center justify-center">
                                        {{ $loop->iteration }}
                                    </span>

                                    <span class="text-sm text-gray-700 leading-7">
                                        {{ $step }}
                                    </span>

                                </div>

                            @empty

                                <p class="text-sm text-gray-400">
                                    No instructions yet.
                                </p>

                            @endforelse

                        </div>

                    </section>

                </div>

            </div>

        </div>

    </article>

</div>

<!-- ============================================================= -->

<!-- Add to Meal Plan Modal -->

<!-- ============================================================= -->

<div
    id="recipeMealModal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4"
>

<div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-6">


    <!-- Header -->

    <div class="flex items-start justify-between gap-4 mb-6">

        <div>

            <p class="text-xs font-semibold uppercase tracking-wider text-orange-500">
                Meal Planner
            </p>

            <h2 class="text-2xl font-bold text-gray-800 mt-1">
                Add to Meal Plan
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                {{ $recipe->name }}
            </p>

        </div>


        <button
            type="button"
            onclick="closeRecipeMealModal()"
            class="w-9 h-9 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition text-xl"
            aria-label="Close"
        >
            &times;
        </button>

    </div>


    <!-- Form -->

    <form
        action="{{ url('/meal-planner') }}"
        method="POST"
    >

        @csrf


        <input
            type="hidden"
            name="recipe_id"
            value="{{ $recipe->id }}"
        >


        <!-- Date -->

        <div class="mb-5">

            <label
                for="recipeMealDate"
                class="block text-sm font-semibold text-gray-700 mb-2"
            >
                Date
            </label>

            <input
                type="date"
                name="date"
                id="recipeMealDate"
                value="{{ now()->format('Y-m-d') }}"
                min="{{ now()->format('Y-m-d') }}"
                required
                class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400"
            >

        </div>


        <!-- Meal Type -->

        <div class="mb-6">

            <label
                for="recipeMealType"
                class="block text-sm font-semibold text-gray-700 mb-2"
            >
                Meal Type
            </label>

            <select
                name="meal_type"
                id="recipeMealType"
                required
                class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400"
            >

                <option value="Breakfast">
                    🍳 Breakfast
                </option>

                <option value="Lunch">
                    🍱 Lunch
                </option>

                <option value="Dinner">
                    🍽️ Dinner
                </option>

            </select>


            <p class="text-xs text-gray-400 mt-2">
                We selected a meal type based on the recipe category.
                You can change it.
            </p>

        </div>


        <!-- Buttons -->

        <div class="grid grid-cols-2 gap-3">

            <button
                type="button"
                onclick="closeRecipeMealModal()"
                class="bg-gray-100 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-200 transition"
            >
                Cancel
            </button>


            <button
                type="submit"
                class="bg-orange-500 text-white py-3 rounded-xl font-semibold hover:bg-orange-600 transition"
            >
                Add to Plan
            </button>

        </div>

    </form>

</div>

</div>

<script>

    function openRecipeMealModal()
    {
        const modal =
            document.getElementById(
                'recipeMealModal'
            );


        const category =
            @js($recipe->category);


        const mealType =
            document.getElementById(
                'recipeMealType'
            );


        if (
            category === 'Breakfast' ||
            category === 'Lunch' ||
            category === 'Dinner'
        ) {

            mealType.value = category;

        } else {

            mealType.value = 'Lunch';

        }


        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }


    function closeRecipeMealModal()
    {
        const modal =
            document.getElementById(
                'recipeMealModal'
            );

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

</script>

@endsection
