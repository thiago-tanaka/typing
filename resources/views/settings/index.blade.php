@extends('layouts.app')

@section('title', 'Settings')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Lesson texts</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Edit the four lines of each lesson. Changes go live as soon as you save.</p>
        </div>

        <div data-vue="lesson-editor" data-props="{{ json_encode(['unitsUrl' => route('units.index'), 'updateUrl' => url('/lesson')], JSON_UNESCAPED_SLASHES) }}">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Loading lessons…</p>
        </div>
    </div>
@endsection
