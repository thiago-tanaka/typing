@extends('layouts.app')

@section('title', 'Confirm your password')

@section('content')
    <x-auth-card title="Confirm your password" description="Please confirm your password before continuing.">
        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
            @csrf

            <x-input name="password" type="password" label="Password" required autocomplete="current-password" autofocus />

            <x-button>Confirm password</x-button>
        </form>

        @if (Route::has('password.request'))
            <x-slot:footer>
                <a href="{{ route('password.request') }}" class="font-medium text-orange-600 hover:underline dark:text-orange-400">Forgot your password?</a>
            </x-slot:footer>
        @endif
    </x-auth-card>
@endsection
