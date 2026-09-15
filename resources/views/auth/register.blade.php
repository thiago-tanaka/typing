@extends('layouts.app')

@section('title', 'Create an account')

@section('content')
    <x-auth-card title="Create your account" description="Save your scores and see your progress in every lesson.">
        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <x-input name="name" label="Name" required autocomplete="name" autofocus />
            <x-input name="email" type="email" label="Email" required autocomplete="email" />
            <x-input name="password" type="password" label="Password" required autocomplete="new-password" />
            <x-input name="password_confirmation" type="password" label="Confirm password" required autocomplete="new-password" />

            <x-button>Create account</x-button>
        </form>

        <x-slot:footer>
            Already have an account? <a href="{{ route('login') }}" class="font-medium text-orange-600 hover:underline dark:text-orange-400">Log in</a>
        </x-slot:footer>
    </x-auth-card>
@endsection
