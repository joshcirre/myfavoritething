<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-100">
    <div class="flex flex-col justify-center items-center px-4 min-h-screen">
        <div class="mb-8 text-9xl">🤩</div>
        <p class="mb-8 max-w-2xl text-xl text-center text-gray-600">
            Remember your favorite things.
        </p>
        @auth
            <x-primary-button href="{{ url('/dashboard') }}">
                Go to Dashboard
            </x-primary-button>
        @else
            <div class="flex flex-col space-y-4 sm:space-y-0 sm:space-x-4 sm:flex-row">
                <x-primary-button href="{{ route('login') }}">
                    Log in
                </x-primary-button>
                @if (Route::has('register'))
                    <x-secondary-button href="{{ route('register') }}">
                        Create an Account
                    </x-secondary-button>
                @endif
            </div>
        @endauth
    </div>
</body>

</html>
