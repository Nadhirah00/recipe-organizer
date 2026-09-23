<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    @yield('title', 'Recipe Organizer')
</title>

@vite([
    'resources/css/app.css',
    'resources/js/app.js'
])

</head>

<body class="min-h-screen bg-app-bg text-gray-800 antialiased">

<!-- ========================================================= -->
<!-- Header -->
<!-- ========================================================= -->

<header class="sticky top-0 z-40 bg-gray-50/95 backdrop-blur-xl border-b border-gray-200/80">

    <div class="h-16 flex items-center justify-between px-4 lg:px-6">

        <!-- ================================================= -->
        <!-- Hamburger / Logo -->
        <!-- ================================================= -->

        <div class="flex items-center gap-3">

            <button
                id="sidebar-toggle"
                type="button"
                class="lg:hidden inline-flex items-center justify-center w-10 h-10 rounded-xl text-gray-600 hover:bg-gray-100 hover:text-orange-500 transition"
                aria-label="Open sidebar"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <a href="{{ url('/dashboard') }}" class="group flex items-center gap-3">

                <span class="w-10 h-10 rounded-2xl bg-orange-50 border border-orange-100 flex items-center justify-center text-xl group-hover:scale-105 group-hover:shadow-sm transition">
                    🍳
                </span>

                <span class="text-xl font-bold tracking-tight text-gray-800">
                    Recipe<span class="text-orange-500">Organizer</span>
                </span>

            </a>

        </div>

        <!-- ================================================= -->
        <!-- Account -->
        <!-- ================================================= -->

        @auth

            <div class="flex items-center gap-2">

                <div class="hidden sm:flex items-center gap-2">

                    <div class="w-9 h-9 rounded-xl bg-orange-50 border border-orange-100 flex items-center justify-center">
                        👤
                    </div>

                    <span class="text-gray-700 font-medium">
                        {{ auth()->user()->name }}
                    </span>

                </div>

                <form action="{{ route('logout') }}" method="POST" class="ml-1">

                    @csrf

                    <button
                        type="submit"
                        class="inline-flex items-center px-3 py-2 rounded-xl text-gray-500 font-medium hover:bg-red-50 hover:text-red-500 transition"
                    >
                        Logout
                    </button>

                </form>

            </div>

        @else

            <div class="flex items-center gap-2">

                <a
                    href="{{ route('login') }}"
                    class="inline-flex items-center px-4 py-2.5 rounded-xl text-gray-600 font-semibold hover:bg-gray-50 hover:text-orange-500 transition"
                >
                    Login
                </a>

                <a
                    href="{{ route('register') }}"
                    class="inline-flex items-center bg-orange-500 text-white px-4 py-2.5 rounded-xl font-semibold shadow-sm hover:bg-orange-600 hover:shadow-md transition"
                >
                    Register
                </a>

            </div>

        @endauth

    </div>

</header>

<!-- ========================================================= -->
<!-- Sidebar Backdrop -->
<!-- ========================================================= -->

<div id="sidebar-backdrop" class="fixed inset-0 z-40 bg-gray-900/40 backdrop-blur-sm hidden lg:hidden"></div>

<div class="flex">

<!-- ========================================================= -->
<!-- Desktop Sidebar -->
<!-- ========================================================= -->

@auth

<aside class="hidden lg:flex lg:sticky lg:top-16 lg:h-[calc(100vh-4rem)] w-64 bg-gray-50 border-r border-gray-200/80 flex-col shrink-0">

    <nav class="flex-1 overflow-y-auto px-3 py-4">

        @php

            $navItems = [

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
                    'url' => url(
                        '/shopping-list?week=' .
                        now()
                            ->startOfWeek()
                            ->format('Y-m-d')
                    ),
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

        <div class="space-y-1">

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
                        gap-3
                        px-4
                        py-2.5
                        rounded-xl
                        font-medium
                        transition
                        {{ $isActive
                            ? 'bg-orange-50 text-orange-600 font-semibold'
                            : 'text-gray-600 hover:bg-gray-100 hover:text-orange-500'
                        }}
                    "
                >

                    <span class="text-lg w-5 text-center shrink-0">
                        {{ $item['icon'] }}
                    </span>

                    <span>
                        {{ $item['label'] }}
                    </span>

                    @if ($isActive)

                        <span
                            class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-7 rounded-full bg-orange-500"
                        ></span>

                    @endif

                </a>

            @endforeach

        </div>

        <!-- ================================================= -->
        <!-- Add Recipe -->
        <!-- ================================================= -->

        <a
            href="{{ url('/recipes/create') }}"
            class="mt-3 flex items-center gap-3 bg-orange-500 text-white px-4 py-2.5 rounded-xl font-semibold shadow-sm hover:bg-orange-600 hover:shadow-md transition"
        >

            <span class="text-lg leading-none w-5 text-center shrink-0">
                +
            </span>

            <span>
                Add Recipe
            </span>

        </a>

    </nav>

</aside>

@endauth

    <!-- ========================================================= -->
    <!-- Main -->
    <!-- ========================================================= -->

    <main class="flex-1 min-w-0 min-h-screen">

        <!-- ========================================================= -->
        <!-- Global Notifications -->
        <!-- ========================================================= -->

        <div class="max-w-7xl mx-auto px-6 pt-6">

            @if (session('success'))

                <div
                    class="flex items-start gap-3 bg-green-50 border border-green-200 text-green-700 rounded-2xl px-5 py-4 mb-6 shadow-sm"
                >

                    <span class="w-7 h-7 rounded-lg bg-green-100 flex items-center justify-center text-sm shrink-0">
                        ✓
                    </span>

                    <p class="font-medium leading-6">
                        {{ session('success') }}
                    </p>

                </div>

            @endif


            @if (session('error'))

                <div
                    class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-4 mb-6 shadow-sm"
                >

                    <span class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center text-sm shrink-0">
                        ⚠️
                    </span>

                    <p class="font-medium leading-6">
                        {{ session('error') }}
                    </p>

                </div>

            @endif

        </div>


        <!-- ========================================================= -->
        <!-- Page Content -->
        <!-- ========================================================= -->

        @yield('content')

    </main>

</div>

<!-- ========================================================= -->
<!-- Mobile Sidebar -->
<!-- ========================================================= -->

@auth

<aside
    id="sidebar"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-50 border-r border-gray-200/80 flex flex-col -translate-x-full lg:hidden transition-transform duration-200"
>

    <!-- ================================================= -->
    <!-- Logo -->
    <!-- ================================================= -->

    <div class="flex items-center justify-between px-5 py-5 border-b border-gray-200/80">

        <a href="{{ url('/dashboard') }}" class="group flex items-center gap-3">

            <span class="w-10 h-10 rounded-2xl bg-orange-50 border border-orange-100 flex items-center justify-center text-xl group-hover:scale-105 group-hover:shadow-sm transition">
                🍳
            </span>

            <span class="text-xl font-bold tracking-tight text-gray-800">
                Recipe<span class="text-orange-500">Organizer</span>
            </span>

        </a>

        <button
            id="sidebar-close"
            type="button"
            class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-500 hover:bg-gray-100 hover:text-orange-500 transition"
            aria-label="Close sidebar"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

    </div>

    <!-- ================================================= -->
    <!-- Navigation -->
    <!-- ================================================= -->

    <nav class="flex-1 overflow-y-auto px-3 py-4">

        @php

            $navItems = [

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
                    'url' => url(
                        '/shopping-list?week=' .
                        now()
                            ->startOfWeek()
                            ->format('Y-m-d')
                    ),
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

        <div class="space-y-1">

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
                        gap-3
                        px-4
                        py-2.5
                        rounded-xl
                        font-medium
                        transition
                        {{ $isActive
                            ? 'bg-orange-50 text-orange-600 font-semibold'
                            : 'text-gray-600 hover:bg-gray-100 hover:text-orange-500'
                        }}
                    "
                >

                    <span class="text-lg w-5 text-center shrink-0">
                        {{ $item['icon'] }}
                    </span>

                    <span>
                        {{ $item['label'] }}
                    </span>

                    @if ($isActive)

                        <span
                            class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-7 rounded-full bg-orange-500"
                        ></span>

                    @endif

                </a>

            @endforeach

        </div>

        <!-- ================================================= -->
        <!-- Add Recipe -->
        <!-- ================================================= -->

        <a
            href="{{ url('/recipes/create') }}"
            class="mt-3 flex items-center gap-3 bg-orange-500 text-white px-4 py-2.5 rounded-xl font-semibold shadow-sm hover:bg-orange-600 hover:shadow-md transition"
        >

            <span class="text-lg leading-none w-5 text-center shrink-0">
                +
            </span>

            <span>
                Add Recipe
            </span>

        </a>

    </nav>

</aside>

@endauth

</body>

</html>