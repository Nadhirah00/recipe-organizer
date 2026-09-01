@extends('layouts.app')

@section('title', 'Login')

@section('content')

<div class="min-h-[80vh] flex items-center justify-center px-6 py-10">

    <div class="w-full max-w-md">

        <!-- Header -->

        <div class="text-center mb-8">

            <div class="text-5xl mb-4">
                🍳
            </div>

            <h1 class="text-3xl font-bold text-gray-800">
                Welcome Back
            </h1>

            <p class="text-gray-500 mt-2">
                Login to manage your recipes.
            </p>

        </div>


        <!-- Login Card -->

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
                action="{{ url('/login') }}"
                method="POST"
            >

                @csrf


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

                <div class="mb-6">

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
                        placeholder="Enter your password"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >

                    <a
                        href="{{ route('password.request') }}"
                        class="text-sm font-semibold text-orange-500 hover:text-orange-600"
                    >
                        Forgot password?
                    </a>

                </div>

                <!-- Login Button -->

                <button
                    type="submit"
                    class="w-full bg-orange-500 text-white py-3 rounded-lg font-semibold hover:bg-orange-600 transition"
                >
                    Login
                </button>

            </form>


            <!-- Register Link -->

            <div class="text-center mt-6 text-gray-500">

                Don't have an account?

                <a
                    href="{{ url('/register') }}"
                    class="text-orange-500 font-semibold hover:text-orange-600"
                >
                    Create Account
                </a>

            </div>

        </div>

    </div>

</div>

@endsection