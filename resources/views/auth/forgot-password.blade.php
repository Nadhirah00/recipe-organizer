@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')

<div class="min-h-[80vh] flex items-center justify-center px-6 py-10">

<div class="w-full max-w-md">

    <!-- Header -->

    <div class="text-center mb-8">

        <div class="mx-auto w-16 h-16 rounded-2xl bg-orange-50 flex items-center justify-center text-3xl">
            🔐
        </div>

        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500 mt-5">
            Account Recovery
        </p>

        <h1 class="text-3xl font-bold text-gray-800 mt-1">
            Forgot Password?
        </h1>

        <p class="text-gray-500 mt-2">
            Enter your email and we'll send you a link to create a new password.
        </p>

    </div>


    <!-- Form -->

    <form
        action="{{ route('password.email') }}"
        method="POST"
        class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8"
    >

        @csrf


        <!-- Email -->

        <div>

            <label
                for="email"
                class="block text-sm font-semibold text-gray-700 mb-2"
            >
                Email Address
            </label>

            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="you@example.com"
                required
                autofocus
                class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
            >

            @error('email')

                <p class="text-sm text-red-500 mt-2">
                    {{ $message }}
                </p>

            @enderror

        </div>


        <!-- Submit -->

        <button
            type="submit"
            class="w-full inline-flex items-center justify-center gap-2 bg-orange-500 text-white py-3.5 rounded-xl font-semibold hover:bg-orange-600 transition mt-6"
        >
            📧 Send Reset Link
        </button>


        <!-- Back -->

        <a
            href="{{ route('login') }}"
            class="w-full inline-flex items-center justify-center text-gray-500 font-semibold text-sm hover:text-orange-500 transition mt-4"
        >
            ← Back to Login
        </a>

    </form>

</div>

</div>

@endsection
