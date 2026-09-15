<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="A free touch typing course: type faster and more accurately, one lesson at a time.">

    <title>@hasSection('title')@yield('title') · @endif{{ config('app.name') }}</title>

    {{-- Applies the saved theme (or the system preference) before the page is painted. --}}
    <script>
        (() => {
            let theme = null;
            try { theme = localStorage.getItem('theme'); } catch (error) {}
            const dark = theme ? theme === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400,500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-50 font-sans text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    <a href="#main" class="sr-only rounded-lg bg-orange-500 px-3 py-2 font-medium text-white focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50">Skip to content</a>

    <header class="sticky top-0 z-40 border-b border-zinc-200/80 bg-white/80 backdrop-blur dark:border-zinc-800/80 dark:bg-zinc-950/80">
        <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-4 py-3">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 rounded-lg font-semibold tracking-tight focus-visible:outline-2 focus-visible:outline-orange-500">
                <span class="grid size-8 place-items-center rounded-lg bg-orange-500 text-white shadow-sm" aria-hidden="true">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="2.5" y="6" width="19" height="12" rx="2.5" />
                        <path stroke-linecap="round" d="M6.5 10h.01M9.5 10h.01M12.5 10h.01M15.5 10h.01M18 10h.01M8 14h8" />
                    </svg>
                </span>
                {{ config('app.name') }}
            </a>

            <nav class="flex items-center gap-1.5 text-sm" aria-label="Account">
                <button type="button" data-theme-toggle aria-label="Dark mode" class="grid size-9 place-items-center rounded-lg text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-orange-500 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white">
                    <svg class="size-5 dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 15A9.72 9.72 0 0 1 18 15.75 9.75 9.75 0 0 1 8.25 6c0-1.33.27-2.6.75-3.75A9.75 9.75 0 0 0 12.75 21a9.75 9.75 0 0 0 9-6Z" />
                    </svg>
                    <svg class="hidden size-5 dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.36.39-1.59 1.59M21 12h-2.25m-.39 6.36-1.59-1.59M12 18.75V21m-4.77-4.23-1.59 1.59M5.25 12H3m4.23-4.77L5.64 5.64M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                </button>

                @guest
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 font-medium text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="rounded-lg bg-orange-500 px-3 py-2 font-semibold text-white shadow-sm transition hover:bg-orange-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-500">Sign up</a>
                    @endif
                @else
                    <details class="group relative">
                        <summary class="flex cursor-pointer list-none items-center gap-2 rounded-lg px-2.5 py-2 font-medium text-zinc-700 transition hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800 [&::-webkit-details-marker]:hidden">
                            <span class="grid size-6 place-items-center rounded-full bg-orange-100 text-xs font-semibold text-orange-700 dark:bg-orange-400/15 dark:text-orange-300" aria-hidden="true">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                            <span class="max-w-32 truncate">{{ Auth::user()->name }}</span>
                            <svg class="size-4 text-zinc-400 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </summary>
                        <div class="absolute right-0 mt-2 w-48 overflow-hidden rounded-xl border border-zinc-200 bg-white py-1 shadow-lg dark:border-zinc-800 dark:bg-zinc-900">
                            @if (Auth::user()->is_admin)
                                <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800">Settings</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-left text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800">Log out</button>
                            </form>
                        </div>
                    </details>
                @endguest
            </nav>
        </div>
    </header>

    <main id="main" class="mx-auto max-w-5xl px-4 py-8 sm:py-10">
        @yield('content')
    </main>

    <footer class="mx-auto max-w-5xl px-4 pb-10 text-sm text-zinc-500">
        Practice a little every day. Speed comes from accuracy.
    </footer>
</body>
</html>
