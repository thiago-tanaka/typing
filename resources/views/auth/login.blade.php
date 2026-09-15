@extends('layouts.app')

@section('title', 'Log in')

@section('content')
    <x-auth-card title="Welcome back" description="Log in to save your scores and track your progress.">
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <x-input name="email" type="email" label="Email" required autocomplete="email" autofocus />
            <x-input name="password" type="password" label="Password" required autocomplete="current-password" />

            <div class="flex items-center justify-between gap-4 text-sm">
                <label class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300">
                    <input type="checkbox" name="remember" class="size-4 accent-orange-500" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="font-medium text-orange-600 hover:underline dark:text-orange-400">Forgot your password?</a>
                @endif
            </div>

            <x-button>Log in</x-button>
        </form>

        @if (Route::has('register'))
            <x-slot:footer>
                New here? <a href="{{ route('register') }}" class="font-medium text-orange-600 hover:underline dark:text-orange-400">Create an account</a>
            </x-slot:footer>
        @endif
    </x-auth-card>
@endsection
