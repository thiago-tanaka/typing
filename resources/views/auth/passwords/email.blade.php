@extends('layouts.app')

@section('title', 'Reset your password')

@section('content')
    <x-auth-card title="Reset your password" description="Enter your email and we will send you a link to choose a new password.">
        @if (session('status'))
            <x-alert class="mb-5">{{ session('status') }}</x-alert>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <x-input name="email" type="email" label="Email" required autocomplete="email" autofocus />

            <x-button>Send reset link</x-button>
        </form>

        <x-slot:footer>
            <a href="{{ route('login') }}" class="font-medium text-orange-600 hover:underline dark:text-orange-400">Back to log in</a>
        </x-slot:footer>
    </x-auth-card>
@endsection
