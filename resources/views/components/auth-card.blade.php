@props(['title', 'description' => null])

<div class="mx-auto w-full max-w-md py-4 sm:py-8">
    <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8 dark:border-zinc-800 dark:bg-zinc-900">
        <h1 class="text-xl font-semibold tracking-tight">{{ $title }}</h1>

        @if ($description)
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
        @endif

        <div class="mt-6">
            {{ $slot }}
        </div>
    </div>

    @isset($footer)
        <p class="mt-4 text-center text-sm text-zinc-500 dark:text-zinc-400">{{ $footer }}</p>
    @endisset
</div>
