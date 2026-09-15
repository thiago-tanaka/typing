@extends('layouts.app')

@section('title', "Unit {$unidade} · Lesson {$licao}")

@php
    $levelStyles = [
        'excellent' => ['label' => 'Excellent', 'dot' => 'bg-violet-500'],
        'good' => ['label' => 'Good', 'dot' => 'bg-emerald-500'],
        'average' => ['label' => 'Average', 'dot' => 'bg-amber-500'],
        'practice' => ['label' => 'Keep practicing', 'dot' => 'bg-rose-500'],
    ];

    $currentUnit = $units->first(fn ($unit) => (int) $unit->name === $unidade);
    $lessons = $currentUnit ? $currentUnit->lessons->sortBy(fn ($item) => (int) $item->name) : collect();
@endphp

@section('content')
    <div class="space-y-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-orange-600 dark:text-orange-400">Unit {{ $unidade }} · Lesson {{ $licao }}</p>
                <h1 class="text-xl font-semibold tracking-tight sm:text-2xl">Practice touch typing</h1>
            </div>

            <nav aria-label="Units" class="inline-flex gap-1 self-start rounded-xl bg-zinc-200/70 p-1 sm:self-auto dark:bg-zinc-800/70">
                @foreach ($units as $unit)
                    @php $active = (int) $unit->name === $unidade; @endphp
                    <a href="{{ url('/'.$unit->name.'/'.($unit->lessons->min(fn ($item) => (int) $item->name) ?? 1)) }}"
                       @class([
                           'rounded-lg px-3 py-1.5 text-sm font-medium transition focus-visible:outline-2 focus-visible:outline-orange-500',
                           'bg-white text-zinc-900 shadow-sm dark:bg-zinc-700 dark:text-white' => $active,
                           'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' => ! $active,
                       ])
                       @if ($active) aria-current="page" @endif>Unit {{ $unit->name }}</a>
                @endforeach
            </nav>
        </div>

        <nav aria-label="Lessons in unit {{ $unidade }}">
            <ol class="grid grid-cols-5 gap-1.5 sm:gap-2">
                @foreach ($lessons as $item)
                    @php
                        $number = (int) $item->name;
                        $score = $pontuacoes[$number] ?? null;
                        $active = $number === $licao;
                    @endphp
                    <li>
                        <a href="{{ url("/{$unidade}/{$number}") }}"
                           @class([
                               'flex min-w-0 flex-col items-center rounded-xl border px-1 py-1.5 text-center transition focus-visible:outline-2 focus-visible:outline-orange-500 sm:px-2 sm:py-2',
                               'border-orange-500 bg-orange-50 ring-1 ring-orange-500 dark:bg-orange-400/10' => $active,
                               'border-zinc-200 bg-white hover:border-zinc-300 hover:shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700' => ! $active,
                           ])
                           @if ($active) aria-current="page" @endif
                           @if ($score) data-level="{{ $score['nivel'] }}" @endif
                           data-lesson-card="{{ $number }}">
                            <span class="text-sm font-semibold leading-tight"><span class="sr-only font-normal text-zinc-500 sm:not-sr-only dark:text-zinc-400">Lesson </span>{{ $number }}</span>
                            <span data-score class="mt-0.5 flex items-center gap-1.5 text-xs tabular-nums text-zinc-600 dark:text-zinc-300" @unless ($score) hidden @endunless>
                                <span data-score-dot class="size-2 shrink-0 rounded-full {{ $score ? $levelStyles[$score['nivel']]['dot'] : '' }}" aria-hidden="true"></span>
                                <span data-score-text class="hidden sm:inline">{{ $score ? $score['velocidade'].' · '.$score['precisao'].'%' : '' }}</span>
                                <span data-score-label class="sr-only">{{ $score ? $levelStyles[$score['nivel']]['label'] : '' }}</span>
                            </span>
                            <span data-score-empty class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-600" aria-hidden="true" @if ($score) hidden @endif>—</span>
                        </a>
                    </li>
                @endforeach
            </ol>
        </nav>

        <div data-vue="typing-lesson" data-props="{{ json_encode($lessonProps, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}">
            <div class="min-h-[16.5rem] rounded-2xl border border-zinc-200 bg-white px-5 py-5 font-mono text-lg leading-relaxed tracking-wide text-zinc-800 shadow-sm sm:px-8 sm:text-2xl dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-200">
                @foreach ($lessonProps['lines'] as $line)
                    <p class="whitespace-pre-wrap break-words">{{ $line }}</p>
                @endforeach
            </div>
            <noscript>
                <p class="mt-3 text-sm text-rose-600 dark:text-rose-400">Turn on JavaScript to take the lesson.</p>
            </noscript>
        </div>

        <section aria-labelledby="levels-title" class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-wrap items-baseline justify-between gap-2">
                <h2 id="levels-title" class="font-semibold">Score levels</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Speed is in characters per minute (CPM). A level needs both speed and accuracy.</p>
            </div>
            <ul class="mt-4 grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($niveis as $nivel)
                    <li class="flex items-center gap-2.5">
                        <span class="size-2.5 shrink-0 rounded-full {{ $levelStyles[$nivel['nivel']]['dot'] }}" aria-hidden="true"></span>
                        <span><span class="font-medium">{{ $levelStyles[$nivel['nivel']]['label'] }}</span> <span class="text-zinc-500 dark:text-zinc-400">{{ $nivel['velocidade'] }}+ CPM · {{ $nivel['precisao'] }}%+</span></span>
                    </li>
                @endforeach
                <li class="flex items-center gap-2.5">
                    <span class="size-2.5 shrink-0 rounded-full {{ $levelStyles['practice']['dot'] }}" aria-hidden="true"></span>
                    <span><span class="font-medium">{{ $levelStyles['practice']['label'] }}</span> <span class="text-zinc-500 dark:text-zinc-400">below that</span></span>
                </li>
            </ul>
            @guest
                <p class="mt-4 text-sm text-zinc-600 dark:text-zinc-300">
                    <a href="{{ route('login') }}" class="font-medium text-orange-600 hover:underline dark:text-orange-400">Log in</a> to save your scores and track your progress.
                </p>
            @endguest
        </section>
    </div>
@endsection
