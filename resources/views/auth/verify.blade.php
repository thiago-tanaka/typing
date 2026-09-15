@extends('layouts.app')

@section('title', 'Verify your email')

@section('content')
    <x-auth-card title="Verify Your Email Address" description="We sent you a verification link. Open it to start saving your scores.">
        @if (session('resent'))
            <x-alert class="mb-5">A fresh verification link has been sent to your email address.</x-alert>
        @endif

        <p class="text-sm text-zinc-600 dark:text-zinc-300">Did not get the email? Check your spam folder or ask for a new link.</p>

        <form method="POST" action="{{ route('verification.resend') }}" class="mt-5">
            @csrf
            <x-button>Send a new verification link</x-button>
        </form>
    </x-auth-card>
@endsection
