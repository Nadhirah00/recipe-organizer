@extends('layouts.app')

@section('title', 'Create Account')

@section('content')

<div class="min-h-[80vh] flex items-center justify-center px-6 py-10">

    <div class="w-full max-w-md">

        <!-- Header -->

        <div class="text-center mb-8">

            <div class="text-5xl mb-4">
                🍳
            </div>

            <h1 class="text-3xl font-bold text-gray-800">
                Create Your Account
            </h1>

            <p class="text-gray-500 mt-2">
                Start organizing your favorite recipes.
            </p>

        </div>


        <!-- Registration Card -->

        <div class="bg-white rounded-2xl shadow-md p-8">

            @if ($errors->any())

                <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-6">

                    <ul class="list-disc list-inside space-y-1 text-sm">

                        @foreach ($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <form
                action="{{ url('/register') }}"
                method="POST"
            >

                @csrf


                <!-- Name -->

                <div class="mb-5">

                    <label
                        for="name"
                        class="block text-gray-700 font-semibold mb-2"
                    >
                        Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Enter your name"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >

                </div>


                <!-- Email -->

                <div class="mb-5">

                    <label
                        for="email"
                        class="block text-gray-700 font-semibold mb-2"
                    >
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="you@example.com"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >

                </div>


                <!-- Password -->

                <div class="mb-5">

                    <label
                        for="password"
                        class="block text-gray-700 font-semibold mb-2"
                    >
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="At least 8 characters"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >

                </div>


                <!-- Confirm Password -->

                <div class="mb-6">

                    <label
                        for="password_confirmation"
                        class="block text-gray-700 font-semibold mb-2"
                    >
                        Confirm Password
                    </label>

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Enter your password again"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >

                </div>


                <!-- Register Button -->

                <button
                    type="submit"
                    class="w-full bg-orange-500 text-white py-3 rounded-lg font-semibold hover:bg-orange-600 transition"
                >
                    Create Account
                </button>

            </form>


            <!-- Login Link -->

            <div class="text-center mt-6 text-gray-500">

                Already have an account?

                <a
                    href="{{ url('/login') }}"
                    class="text-orange-500 font-semibold hover:text-orange-600"
                >
                    Login
                </a>

            </div>

        </div>

    </div>

</div>

@endsection