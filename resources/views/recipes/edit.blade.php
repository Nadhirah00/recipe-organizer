@extends('layouts.app')

@section('title', 'Edit Recipe')

@section('content')

@php
    $field = 'w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800
              placeholder-gray-400 bg-gray-50 focus:bg-white focus:outline-none
              focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition';

    $fieldInvalid = 'border-red-300 bg-red-50/40 focus:ring-red-400 focus:border-red-400';

    $label = 'block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1.5';

    $card = 'bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6';

    $sectionTitle = 'text-lg font-bold text-gray-800';

    $sectionHint = 'text-sm text-gray-500 mt-1 mb-4';
@endphp

<div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 md:py-8">

    <!-- Header -->

    <div class="mb-6">

        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
            Recipe Collection
        </p>

        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight text-gray-800 mt-1">
            Edit Recipe
        </h1>

        <p class="text-sm text-gray-500 mt-2">
            Update
            <a
                href="{{ url('/recipes/' . $recipe->id) }}"
                class="font-semibold text-orange-600 hover:text-orange-700 transition"
            >{{ $recipe->name }}</a>
            — your changes are saved when you submit the form.
        </p>

    </div>


    <!-- Validation Errors -->

    @if ($errors->any())

        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6">

            <div class="flex items-center gap-2 text-sm font-semibold text-red-700">

                <span>⚠️</span>

                <span>Please fix the following:</span>

            </div>

            <ul class="list-disc list-inside text-sm text-red-600 mt-2 space-y-1">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <!-- Form -->

    <form
        action="{{ url('/recipes/' . $recipe->id) }}"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-6"
    >

        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Basics -->

            <section class="{{ $card }}">

                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                    Step 1
                </p>

                <h2 class="{{ $sectionTitle }} mt-1">
                    Basics
                </h2>

                <p class="{{ $sectionHint }}">
                    The name and summary shown on your recipe card.
                </p>

                <div class="mb-4">

                    <label for="name" class="{{ $label }}">Recipe name</label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $recipe->name) }}"
                        placeholder="e.g. Chicken Teriyaki"
                        @error('name') aria-invalid="true" @enderror
                        class="{{ $field }} {{ $errors->has('name') ? $fieldInvalid : '' }}"
                    >

                    @error('name')
                        <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                    @enderror

                </div>

                <div>

                    <label for="description" class="{{ $label }}">Description</label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Briefly describe your recipe..."
                        @error('description') aria-invalid="true" @enderror
                        class="{{ $field }} {{ $errors->has('description') ? $fieldInvalid : '' }}"
                    >{{ old('description', $recipe->description) }}</textarea>

                    @error('description')
                        <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                    @enderror

                </div>

            </section>


            <!-- Details -->

            <section class="{{ $card }}">

                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                    Step 2
                </p>

                <h2 class="{{ $sectionTitle }} mt-1">
                    Details
                </h2>

                <p class="{{ $sectionHint }}">
                    Used for filtering, sorting and meal planning.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>

                        <label for="category" class="{{ $label }}">🏷️ Category</label>

                        <select
                            id="category"
                            name="category"
                            @error('category') aria-invalid="true" @enderror
                            class="{{ $field }} {{ $errors->has('category') ? $fieldInvalid : '' }}"
                        >

                            <option value="Breakfast" {{ old('category', $recipe->category) === 'Breakfast' ? 'selected' : '' }}>
                                Breakfast
                            </option>

                            <option value="Lunch" {{ old('category', $recipe->category) === 'Lunch' ? 'selected' : '' }}>
                                Lunch
                            </option>

                            <option value="Dinner" {{ old('category', $recipe->category) === 'Dinner' ? 'selected' : '' }}>
                                Dinner
                            </option>

                            <option value="Dessert" {{ old('category', $recipe->category) === 'Dessert' ? 'selected' : '' }}>
                                Dessert
                            </option>

                            <option value="Snack" {{ old('category', $recipe->category) === 'Snack' ? 'selected' : '' }}>
                                Snack
                            </option>

                            <option value="Drinks" {{ old('category', $recipe->category) === 'Drinks' ? 'selected' : '' }}>
                                Drinks
                            </option>

                        </select>

                        @error('category')
                            <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror

                    </div>

                    <div>

                        <label for="cooking_time" class="{{ $label }}">⏱️ Cooking time</label>

                        <div class="relative">

                            <input
                                type="number"
                                id="cooking_time"
                                name="cooking_time"
                                value="{{ old('cooking_time', $recipe->cooking_time) }}"
                                min="1"
                                placeholder="30"
                                @error('cooking_time') aria-invalid="true" @enderror
                                class="{{ $field }} pr-20 {{ $errors->has('cooking_time') ? $fieldInvalid : '' }}"
                            >

                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">
                                minutes
                            </span>

                        </div>

                        @error('cooking_time')
                            <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror

                    </div>

                    <div>

                        <label for="difficulty" class="{{ $label }}">⭐ Difficulty</label>

                        <select
                            id="difficulty"
                            name="difficulty"
                            @error('difficulty') aria-invalid="true" @enderror
                            class="{{ $field }} {{ $errors->has('difficulty') ? $fieldInvalid : '' }}"
                        >

                            <option value="Easy" {{ old('difficulty', $recipe->difficulty) === 'Easy' ? 'selected' : '' }}>
                                Easy
                            </option>

                            <option value="Medium" {{ old('difficulty', $recipe->difficulty) === 'Medium' ? 'selected' : '' }}>
                                Medium
                            </option>

                            <option value="Hard" {{ old('difficulty', $recipe->difficulty) === 'Hard' ? 'selected' : '' }}>
                                Hard
                            </option>

                        </select>

                        @error('difficulty')
                            <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror

                    </div>

                    <div>

                        <label for="servings" class="{{ $label }}">👥 Servings</label>

                        <input
                            type="number"
                            id="servings"
                            name="servings"
                            value="{{ old('servings', $recipe->servings) }}"
                            min="1"
                            placeholder="4"
                            @error('servings') aria-invalid="true" @enderror
                            class="{{ $field }} {{ $errors->has('servings') ? $fieldInvalid : '' }}"
                        >

                        @error('servings')
                            <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror

                    </div>

                </div>

            </section>

        </div>


        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Ingredients -->

            <section class="{{ $card }}">

                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                    Step 3
                </p>

                <h2 class="{{ $sectionTitle }} mt-1">
                    Ingredients
                </h2>

                <p class="{{ $sectionHint }}">
                    One per line, with quantities where possible.
                </p>

                <textarea
                    id="ingredients"
                    name="ingredients"
                    rows="11"
                    spellcheck="false"
                    placeholder="2 eggs&#10;1/2 teaspoon salt&#10;3 cups rice"
                    @error('ingredients') aria-invalid="true" @enderror
                    class="{{ $field }} font-mono leading-6 {{ $errors->has('ingredients') ? $fieldInvalid : '' }}"
                >{{ old('ingredients', $recipe->ingredients) }}</textarea>

                @error('ingredients')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror

                <p class="text-xs text-gray-400 mt-2">
                    💡 These are rendered as a list on the recipe page.
                </p>

            </section>


            <!-- Instructions -->

            <section class="{{ $card }}">

                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                    Step 4
                </p>

                <h2 class="{{ $sectionTitle }} mt-1">
                    Instructions
                </h2>

                <p class="{{ $sectionHint }}">
                    One step per line, in the order they should be followed.
                </p>

                <textarea
                    id="instructions"
                    name="instructions"
                    rows="11"
                    spellcheck="false"
                    placeholder="1. Beat the eggs.&#10;2. Heat the pan.&#10;3. Cook until done."
                    @error('instructions') aria-invalid="true" @enderror
                    class="{{ $field }} font-mono leading-6 {{ $errors->has('instructions') ? $fieldInvalid : '' }}"
                >{{ old('instructions', $recipe->instructions) }}</textarea>

                @error('instructions')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror

                <p class="text-xs text-gray-400 mt-2">
                    💡 Leading numbers are stripped and re-added automatically.
                </p>

            </section>

        </div>


        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Image -->

            <section class="{{ $card }}">

                <h2 class="{{ $sectionTitle }}">
                    Photo
                </h2>

                <p class="{{ $sectionHint }}">
                    Uploading a new image replaces the current one.
                </p>

                <div class="relative h-40 rounded-xl overflow-hidden bg-orange-50 border border-gray-100">

                    <img
                        id="image-preview"
                        @if ($recipe->image) src="{{ asset('storage/' . $recipe->image) }}" @endif
                        alt="{{ $recipe->name }}"
                        class="w-full h-full object-cover {{ $recipe->image ? '' : 'hidden' }}"
                    >

                    <div
                        id="image-placeholder"
                        class="{{ $recipe->image ? 'hidden ' : '' }}w-full h-full flex flex-col items-center justify-center"
                    >
                        <span class="text-4xl">🍳</span>
                        <p class="text-xs text-gray-400 mt-1">No image yet</p>
                    </div>

                </div>

                <label
                    for="image"
                    class="mt-3 flex items-center justify-between gap-3 border border-dashed border-gray-300 rounded-xl px-4 py-3 bg-stone-50 hover:border-orange-300 hover:bg-orange-50/40 cursor-pointer transition"
                >
                    <span class="flex items-center gap-2 text-sm text-gray-600 min-w-0">
                        <span>📷</span>
                        <span id="file-name" class="truncate">Choose a new image</span>
                    </span>

                    <span class="shrink-0 text-xs font-semibold text-orange-600">
                        Browse
                    </span>

                    <input
                        id="image"
                        type="file"
                        name="image"
                        accept="image/jpeg,image/png,image/webp"
                        class="sr-only"
                    >

                </label>

                @error('image')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror

            </section>


            <!-- Tutorial + Visibility -->

            <section class="{{ $card }}">

                <h2 class="{{ $sectionTitle }}">
                    Sharing
                </h2>

                <p class="{{ $sectionHint }}">
                    Link a tutorial and control who can see this recipe.
                </p>

                <label for="tutorial_url" class="{{ $label }}">Video tutorial</label>

                <input
                    type="url"
                    id="tutorial_url"
                    name="tutorial_url"
                    value="{{ old('tutorial_url', $recipe->tutorial_url) }}"
                    placeholder="https://www.youtube.com/..."
                    @error('tutorial_url') aria-invalid="true" @enderror
                    class="{{ $field }} {{ $errors->has('tutorial_url') ? $fieldInvalid : '' }}"
                >

                @error('tutorial_url')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror

                @if ($recipe->youtubeEmbedUrl())

                    <div class="mt-2 aspect-video rounded-xl overflow-hidden bg-gray-900">
                        <iframe
                            src="{{ $recipe->youtubeEmbedUrl() }}"
                            title="{{ $recipe->name }} video tutorial"
                            class="w-full h-full"
                            loading="lazy"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen
                        ></iframe>
                    </div>

                @endif


                <p class="{{ $label }} mt-5">Visibility</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                    <label class="cursor-pointer">

                        <input
                            type="radio"
                            name="is_public"
                            value="0"
                            class="peer sr-only"
                            {{ old('is_public', $recipe->is_public) == 0 ? 'checked' : '' }}
                        >

                        <div class="h-full rounded-xl border-2 border-gray-200 bg-white p-3 transition peer-checked:border-gray-500 peer-checked:bg-gray-50">
                            <p class="text-sm font-semibold text-gray-800">🔒 Private</p>
                            <p class="text-xs text-gray-500 mt-0.5">Only you can see it</p>
                        </div>

                    </label>

                    <label class="cursor-pointer">

                        <input
                            type="radio"
                            name="is_public"
                            value="1"
                            class="peer sr-only"
                            {{ old('is_public', $recipe->is_public) == 1 ? 'checked' : '' }}
                        >

                        <div class="h-full rounded-xl border-2 border-gray-200 bg-white p-3 transition peer-checked:border-green-500 peer-checked:bg-green-50">
                            <p class="text-sm font-semibold text-gray-800">🌎 Public</p>
                            <p class="text-xs text-gray-500 mt-0.5">Anyone can discover it</p>
                        </div>

                    </label>

                </div>

                @error('is_public')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror

            </section>

        </div>


        <!-- Actions -->

        <div class="sticky bottom-4 z-20">

            <div class="bg-white/95 backdrop-blur border border-gray-100 rounded-2xl shadow-lg px-4 py-3 flex flex-col-reverse sm:flex-row gap-3">

                <a
                    href="{{ url('/recipes/' . $recipe->id) }}"
                    class="flex-1 inline-flex items-center justify-center bg-gray-100 text-gray-700 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-200 transition"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2 bg-orange-500 text-white py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:bg-orange-600 hover:shadow-md transition"
                >
                    Save changes
                </button>

            </div>

        </div>

    </form>

</div>

<script>

    const imageInput = document.getElementById('image');
    const fileName = document.getElementById('file-name');
    const preview = document.getElementById('image-preview');
    const placeholder = document.getElementById('image-placeholder');

    if (imageInput) {

        imageInput.addEventListener('change', function () {

            const file = this.files[0];

            if (!file) {

                fileName.textContent = 'Choose a new image';

                return;

            }

            fileName.textContent = file.name;

            if (preview) {

                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');

            }

            if (placeholder) {

                placeholder.classList.add('hidden');

            }

        });

    }

</script>

@endsection
