<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>@yield('title', 'Recipe Organizer')</title>

@vite(['resources/css/app.css', 'resources/js/app.js'])


</head>

<body class="min-h-screen bg-stone-50 text-gray-800">

<!-- Navigation -->

<nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-xl border-b border-gray-200 shadow-sm">

    <div class="max-w-7xl mx-auto px-6">

        <div class="flex flex-wrap items-center justify-between gap-5 py-4">

            <!-- Logo -->

            <a
                href="{{ url('/dashboard') }}"
                class="group flex items-center gap-3"

                >

                <span

                    class="w-10 h-10 rounded-2xl bg-orange-50 flex items-center justify-center text-xl group-hover:scale-105 transition"
                >
                    🍳
                </span>

                <span class="text-xl font-bold tracking-tight text-gray-800">
                    Recipe<span class="text-orange-500">Organizer</span>
                </span>

            </a>

            @auth

                <!-- Navigation -->

                <div class="flex flex-wrap items-center justify-center gap-x-2 gap-y-2 text-sm">
                    @php

                        $navItems = [

                            [
                                'url' => url('/dashboard'),
                                'icon' => '🏠',
                                'label' => 'Dashboard',
                                'pattern' => 'dashboard',
                            ],

                            [
                                'url' => route('recipes.explore'),
                                'icon' => '🌎',
                                'label' => 'Explore Recipes',
                                'pattern' => 'explore',
                            ],

                            [
                                'url' => url('/recipes'),
                                'icon' => '🍽️',
                                'label' => 'My Recipes',
                                'pattern' => 'recipes',
                            ],

                            [
                                'url' => url('/favorites'),
                                'icon' => '❤️',
                                'label' => 'Favorites',
                                'pattern' => 'favorites',
                            ],

                            [
                                'url' => url('/meal-planner'),
                                'icon' => '📅',
                                'label' => 'Meal Planner',
                                'pattern' => 'meal-planner',
                            ],

                            [
                                'url' => url('/shopping-list?week=' . now()->startOfWeek()->format('Y-m-d')),
                                'icon' => '🛒',
                                'label' => 'Shopping List',
                                'pattern' => 'shopping-list',
                            ],

                            [
                                'url' => url('/preferences'),
                                'icon' => '⚙️',
                                'label' => 'Preferences',
                                'pattern' => 'preferences',
                            ],

                        ];

                    @endphp


                    @foreach ($navItems as $item)

                        @php

                            $isActive = request()->is(
                                $item['pattern'],
                                $item['pattern'] . '/*'
                            );

                        @endphp


                        <a
                            href="{{ $item['url'] }}"
                            class="
                                relative
                                flex
                                items-center
                                gap-1.5
                                px-3
                                py-2
                                rounded-lg
                                transition
                                {{ $isActive
                                    ? 'bg-orange-50 text-orange-600 font-semibold'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-orange-500'
                                }}
                            "
                        >

                            <span>
                                {{ $item['icon'] }}
                            </span>

                            <span>
                                {{ $item['label'] }}
                            </span>


                            @if ($isActive)

                                <span
                                    class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1.5 h-1.5 rounded-full bg-orange-500"
                                ></span>

                            @endif

                        </a>

                    @endforeach


                    <!-- Add Recipe -->

                    <a
                        href="{{ url('/recipes/create') }}"
                        class="inline-flex items-center gap-1.5 bg-orange-500 text-white px-4 py-2 rounded-lg font-semibold shadow-sm hover:bg-orange-600 transition"
                    >
                        <span>+</span>
                        <span>Add Recipe</span>
                    </a>


                    <!-- User -->

                    <div class="hidden lg:flex items-center gap-2 pl-4 ml-1 border-l border-gray-200">

                        <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
                            👤
                        </div>

                        <span class="text-gray-700 font-medium">
                            {{ auth()->user()->name }}
                        </span>

                    </div>


                    <!-- Logout -->

                    <form
                        action="{{ route('logout') }}"
                        method="POST"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="text-gray-500 hover:text-red-500 font-medium transition"
                        >
                            Logout
                        </button>

                    </form>

                </div>

            @else

                <!-- Guest Navigation -->

                <div class="flex items-center gap-3">

                    <a
                        href="{{ route('login') }}"
                        class="text-gray-600 hover:text-orange-500 font-medium transition"
                    >
                        Login
                    </a>

                    <a
                        href="{{ route('register') }}"
                        class="bg-orange-500 text-white px-4 py-2 rounded-lg font-semibold shadow-sm hover:bg-orange-600 transition"
                    >
                        Register
                    </a>

                </div>

            @endauth

        </div>

    </div>

</nav>


<!-- Main -->

<main>

    <!-- Global Notifications -->

    <div class="max-w-7xl mx-auto px-6 pt-6">

        @if (session('success'))

            <div class="flex items-start gap-3 bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-4 mb-6 shadow-sm">

                <span class="text-lg leading-none mt-0.5">
                    ✓
                </span>

                <p class="font-medium">
                    {{ session('success') }}
                </p>

            </div>

        @endif


        @if (session('error'))

            <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4 mb-6 shadow-sm">

                <span class="text-lg leading-none mt-0.5">
                    ⚠️
                </span>

                <p class="font-medium">
                    {{ session('error') }}
                </p>

            </div>

        @endif

    </div>


    <!-- Page Content -->

    @yield('content')

</main>


</body>
</html>
