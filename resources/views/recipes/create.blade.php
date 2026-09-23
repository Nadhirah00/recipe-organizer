@extends('layouts.app')

@section('title', 'Add Recipe')

@section('content')

<div class="max-w-4xl mx-auto px-6 py-10">

<!-- ========================================================= -->
<!-- Header -->
<!-- ========================================================= -->

<div class="mb-8">


    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500 mt-6">
        Recipe Collection
    </p>

    <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-gray-800 mt-1">
        Add New Recipe
    </h1>

    <p class="text-gray-500 mt-2 max-w-2xl">
        Add your recipe details below and save it to your collection.
    </p>

</div>

<!-- ========================================================= -->
<!-- Validation Errors -->
<!-- ========================================================= -->

@if ($errors->any())

    <div class="bg-red-50 border border-red-200 rounded-2xl p-5 mb-6">

        <div class="flex items-center gap-2 text-red-700 font-semibold">

            <span>
                ⚠️
            </span>

            <span>
                Please fix the following:
            </span>

        </div>

        <ul class="list-disc list-inside text-sm text-red-600 mt-3 space-y-1">

            @foreach ($errors->all() as $error)

                <li>
                    {{ $error }}
                </li>

            @endforeach

        </ul>

    </div>

@endif

<!-- ========================================================= -->
<!-- Form -->
<!-- ========================================================= -->

<form
    action="{{ url('/recipes') }}"
    method="POST"
    enctype="multipart/form-data"
    class="space-y-6"
>

    @csrf

    <!-- ===================================================== -->
    <!-- Basic Information -->
    <!-- ===================================================== -->

    <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">

        <div class="mb-6">

            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                Step 1
            </p>

            <h2 class="text-2xl font-bold text-gray-800 mt-1">
                Basic Information
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Start with the name, description, and image.
            </p>

        </div>

        <!-- Recipe Name -->

        <div class="mb-6">

            <label
                for="name"
                class="block text-sm font-semibold text-gray-700 mb-2"
            >
                Recipe Name
            </label>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                placeholder="e.g. Chicken Teriyaki"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition"
            >

        </div>

        <!-- Description -->

        <div class="mb-6">

            <label
                for="description"
                class="block text-sm font-semibold text-gray-700 mb-2"
            >
                Description
            </label>

            <textarea
                id="description"
                name="description"
                rows="3"
                placeholder="Briefly describe your recipe..."
                class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition"
            >{{ old('description') }}</textarea>

        </div>

        <!-- Recipe Image -->

        <div>

            <label class="block text-sm font-semibold text-gray-700 mb-2">
                🖼️ Recipe Image
            </label>

            <div class="border-2 border-dashed border-gray-200 rounded-2xl bg-stone-50 p-7 text-center hover:border-orange-300 hover:bg-orange-50/40 transition">

                <div class="w-14 h-14 mx-auto rounded-2xl bg-orange-100 flex items-center justify-center text-2xl">
                    📷
                </div>

                <p class="font-semibold text-gray-700 mt-4">
                    Add a photo of your recipe
                </p>

                <p class="text-sm text-gray-400 mt-1">
                    JPG, JPEG, PNG or WEBP · Maximum 2MB
                </p>


                <label
                    for="image"
                    class="inline-flex items-center justify-center gap-2 bg-orange-500 text-white px-5 py-2.5 rounded-xl font-semibold cursor-pointer hover:bg-orange-600 transition mt-5"
                >
                    📷 Choose Image
                </label>

                <input
                    id="image"
                    type="file"
                    name="image"
                    accept="image/jpeg,image/png,image/webp"
                    class="hidden"
                >

                <p
                    id="file-name"
                    class="text-sm text-gray-400 mt-3"
                >
                    No image selected
                </p>

            </div>

        </div>

    </section>

    <!-- ========================================================= -->
    <!-- Recipe Details -->
    <!-- ========================================================= -->

    <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">

        <div class="mb-6">

            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                Step 2
            </p>

            <h2 class="text-2xl font-bold text-gray-800 mt-1">
                Recipe Details
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Add information users can use when planning meals.
            </p>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <!-- Category -->

            <div>

                <label
                    for="category"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    🏷️ Category
                </label>

                <select
                    id="category"
                    name="category"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                >

                    <option value="">
                        Select Category
                    </option>

                    <option
                        value="Breakfast"
                        {{ old('category') === 'Breakfast' ? 'selected' : '' }}
                    >
                        Breakfast
                    </option>

                    <option
                        value="Lunch"
                        {{ old('category') === 'Lunch' ? 'selected' : '' }}
                    >
                        Lunch
                    </option>

                    <option
                        value="Dinner"
                        {{ old('category') === 'Dinner' ? 'selected' : '' }}
                    >
                        Dinner
                    </option>

                    <option
                        value="Dessert"
                        {{ old('category') === 'Dessert' ? 'selected' : '' }}
                    >
                        Dessert
                    </option>

                    <option
                        value="Snack"
                        {{ old('category') === 'Snack' ? 'selected' : '' }}
                    >
                        Snack
                    </option>

                    <option
                        value="Drinks"
                        {{ old('category') === 'Drinks' ? 'selected' : '' }}
                    >
                        Drinks
                    </option>

                </select>

            </div>

            <!-- Cooking Time -->

            <div>

                <label
                    for="cooking_time"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    ⏱️ Cooking Time
                </label>

                <div class="relative">

                    <input
                        type="number"
                        id="cooking_time"
                        name="cooking_time"
                        value="{{ old('cooking_time') }}"
                        min="1"
                        placeholder="e.g. 30"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 pr-20 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                    >

                    <span class="absolute right-4 top-3.5 text-sm text-gray-400">
                        minutes
                    </span>

                </div>

            </div>

            <!-- Difficulty -->

            <div>

                <label
                    for="difficulty"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    ⭐ Difficulty
                </label>

                <select
                    id="difficulty"
                    name="difficulty"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                >

                    <option value="">
                        Select Difficulty
                    </option>

                    <option
                        value="Easy"
                        {{ old('difficulty') === 'Easy' ? 'selected' : '' }}
                    >
                        Easy
                    </option>

                    <option
                        value="Medium"
                        {{ old('difficulty') === 'Medium' ? 'selected' : '' }}
                    >
                        Medium
                    </option>

                    <option
                        value="Hard"
                        {{ old('difficulty') === 'Hard' ? 'selected' : '' }}
                    >
                        Hard
                    </option>

                </select>

            </div>

            <!-- Servings -->

            <div>

                <label
                    for="servings"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    👥 Servings
                </label>

                <input
                    type="number"
                    id="servings"
                    name="servings"
                    value="{{ old('servings') }}"
                    min="1"
                    placeholder="e.g. 4"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                >

            </div>

        </div>

    </section>

    <!-- ========================================================= -->
    <!-- Ingredients -->
    <!-- ========================================================= -->

    <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">

        <div class="mb-6">

            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                Step 3
            </p>

            <h2 class="text-2xl font-bold text-gray-800 mt-1">
                🥕 Ingredients
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Add one ingredient per line. Include quantities when available.
            </p>

        </div>

        <textarea
            id="ingredients"
            name="ingredients"
            rows="8"
            placeholder="Example:

            3 cups cooked rice
            1 chicken breast
            2 eggs
            1 carrot
            2 cloves garlic"
            class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition"
            >{{ old('ingredients') }}</textarea>

        <div class="flex items-start gap-2 mt-3 text-xs text-gray-400">

            <span>
                💡
            </span>

            <p>
                Quantities such as <strong>2 eggs</strong>, <strong>1/2 teaspoon salt</strong>,
                or <strong>3 cups rice</strong> can also be used by the shopping list.
            </p>

        </div>

    </section>


    <!-- ========================================================= -->
    <!-- Instructions -->
    <!-- ========================================================= -->

    <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">

        <div class="mb-6">

            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                Step 4
            </p>

            <h2 class="text-2xl font-bold text-gray-800 mt-1">
                👨‍🍳 Instructions
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Write the cooking steps in the order they should be followed.
            </p>

        </div>


        <textarea
            id="instructions"
            name="instructions"
            rows="10"
            placeholder="Example:

            1. Prepare the chicken.
            2. Mix the sauce.
            3. Cook the chicken.
            4. Add the sauce.
            5. Simmer until ready.
            6. Serve."
            class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition"
            >{{ old('instructions') }}</textarea>

    </section>


    <!-- ========================================================= -->
    <!-- Tutorial -->
    <!-- ========================================================= -->

    <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">

        <div class="mb-6">

            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                Optional
            </p>

            <h2 class="text-2xl font-bold text-gray-800 mt-1">
                🎥 Tutorial
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Add a YouTube tutorial if you want others to follow a video.
            </p>

        </div>


        <input
            type="url"
            id="tutorial_url"
            name="tutorial_url"
            value="{{ old('tutorial_url') }}"
            placeholder="https://www.youtube.com/..."
            class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
        >

    </section>


    <!-- ========================================================= -->
    <!-- Visibility -->
    <!-- ========================================================= -->

    <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">

        <div class="mb-6">

            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                Sharing
            </p>

            <h2 class="text-2xl font-bold text-gray-800 mt-1">
                Recipe Visibility
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Choose who can discover your recipe.
            </p>

        </div>


        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">


            <!-- Private -->

            <label class="cursor-pointer">

                <input
                    type="radio"
                    name="is_public"
                    value="0"
                    class="peer sr-only"
                    {{ old('is_public', 0) == 0 ? 'checked' : '' }}
                >


                <div class="h-full rounded-2xl border-2 border-gray-200 bg-white p-5 transition peer-checked:border-gray-500 peer-checked:bg-gray-50">

                    <div class="flex items-start gap-3">

                        <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                            🔒
                        </div>

                        <div>

                            <p class="font-semibold text-gray-800">
                                Private
                            </p>

                            <p class="text-sm text-gray-500 mt-1 leading-6">
                                Only you can view this recipe.
                            </p>

                        </div>

                    </div>

                </div>

            </label>


            <!-- Public -->

            <label class="cursor-pointer">

                <input
                    type="radio"
                    name="is_public"
                    value="1"
                    class="peer sr-only"
                    {{ old('is_public') == 1 ? 'checked' : '' }}
                >


                <div class="h-full rounded-2xl border-2 border-gray-200 bg-white p-5 transition peer-checked:border-green-500 peer-checked:bg-green-50">

                    <div class="flex items-start gap-3">

                        <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center shrink-0">
                            🌎
                        </div>

                        <div>

                            <p class="font-semibold text-gray-800">
                                Public
                            </p>

                            <p class="text-sm text-gray-500 mt-1 leading-6">
                                Other users can discover and view this recipe.
                            </p>

                        </div>

                    </div>

                </div>

            </label>

        </div>

    </section>


    <!-- ========================================================= -->
    <!-- Actions -->
    <!-- ========================================================= -->

    <div class="flex flex-col-reverse sm:flex-row gap-3 pt-2">

        <a
            href="{{ url('/recipes') }}"
            class="flex-1 inline-flex items-center justify-center bg-gray-100 text-gray-700 py-3.5 rounded-xl font-semibold hover:bg-gray-200 transition"
        >
            Cancel
        </a>


        <button
            type="submit"
            class="flex-1 inline-flex items-center justify-center gap-2 bg-orange-500 text-white py-3.5 rounded-xl font-semibold shadow-sm hover:bg-orange-600 hover:shadow-md transition"
        >
            🍳 Save Recipe
        </button>

    </div>

    </form>

</div>

<!-- Image filename -->

<script>

    const imageInput =
        document.getElementById('image');

    const fileName =
        document.getElementById('file-name');


    if (imageInput) {

        imageInput.addEventListener(
            'change',
            function () {

                if (this.files.length > 0) {

                    fileName.textContent =
                        this.files[0].name;

                } else {

                    fileName.textContent =
                        'No image selected';

                }

            }
        );

    }

</script>

@endsection
