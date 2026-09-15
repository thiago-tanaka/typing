@extends('layouts.app')

@section('title', 'Choose a new password')

@section('content')
    <x-auth-card title="Choose a new password">
        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <x-input name="email" type="email" label="Email" :value="$email ?? ''" required autocomplete="email" />
            <x-input name="password" type="password" label="New password" required autocomplete="new-password" autofocus />
            <x-input name="password_confirmation" type="password" label="Confirm new password" required autocomplete="new-password" />

            <x-button>Reset password</x-button>
        </form>
    </x-auth-card>
@endsection
