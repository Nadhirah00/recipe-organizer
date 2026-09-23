@php

    $segments = request()->segments();

    $crumbLabels = collect([

        'recipes' => 'My Recipes',

        'explore' => 'Explore Recipes',

        'discover' => 'Discover',

        'favorites' => 'Favorites',

        'meal-planner' => 'Meal Planner',

        'shopping-list' => 'Shopping List',

        'preferences' => 'Preferences',

        'find-by-ingredients' => 'Find by Ingredients',

        'login' => 'Login',

        'register' => 'Register',

    ]);

    $crumbs = [];

    if ($segments === [] || $segments === ['dashboard']) {

        $crumbs = [

            ['label' => 'Home', 'url' => null],

        ];

    } else {

        $crumbs[] = ['label' => 'Home', 'url' => url('/dashboard')];

        $first = $segments[0];

        $rest = array_slice($segments, 1);

        $sectionLabel = $crumbLabels->get($first);

        if ($sectionLabel === null) {

            $crumbs[] = [

                'label' => \Illuminate\Support\Str::headline(str_replace('-', ' ', $first)),

                'url' => null,

            ];

        } elseif ($rest === []) {

            $crumbs[] = ['label' => $sectionLabel, 'url' => null];

        } else {

            $crumbs[] = ['label' => $sectionLabel, 'url' => url('/' . $first)];

            if ($first === 'recipes') {

                $sub = $rest[0];

                if ($sub === 'create') {

                    $crumbs[] = ['label' => 'Create Recipe', 'url' => null];

                } elseif (is_numeric($sub)) {

                    $recipe = \App\Models\Recipe::find($sub);

                    $name = $recipe?->name ?? 'Recipe #' . $sub;

                    if (($rest[1] ?? null) === 'edit') {

                        $crumbs[] = ['label' => $name, 'url' => url('/recipes/' . $sub)];

                        $crumbs[] = ['label' => 'Edit Recipe', 'url' => null];

                    } else {

                        $crumbs[] = ['label' => $name, 'url' => null];

                    }

                }

            } elseif ($first === 'meal-planner') {

                if (($rest[0] ?? null) === 'ai') {

                    $crumbs[] = [

                        'label' => ($rest[1] ?? null) === 'setup' ? 'AI Setup' : 'AI Suggestions',

                        'url' => null,

                    ];

                }

            }

        }

    }

@endphp

@if (count($crumbs) > 1)

    <nav aria-label="Breadcrumb" class="max-w-7xl mx-auto px-6 pt-6">

        <ol class="flex items-center flex-wrap gap-1.5 text-sm">

            @foreach ($crumbs as $crumb)

                <li class="flex items-center gap-1.5">

                    @if (!$loop->last && $crumb['url'])

                        <a
                            href="{{ $crumb['url'] }}"
                            class="text-gray-500 font-medium hover:text-orange-500 transition"
                        >
                            {{ $crumb['label'] }}
                        </a>

                    @else

                        <span class="{{ $loop->last ? 'text-gray-800 font-semibold' : 'text-gray-500 font-medium' }}">
                            {{ $crumb['label'] }}
                        </span>

                    @endif

                    @if (!$loop->last)

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>

                    @endif

                </li>

            @endforeach

        </ol>

    </nav>

@endif