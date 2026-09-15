@props(['name', 'label', 'type' => 'text', 'value' => null])

<div>
    <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $label }}</label>

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
        @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror
        {{ $attributes->class([
            'block w-full rounded-lg border bg-white px-3 py-2 text-zinc-900 shadow-sm outline-none transition focus:ring-2 dark:bg-zinc-950 dark:text-zinc-100',
            'border-zinc-300 focus:border-orange-500 focus:ring-orange-500/30 dark:border-zinc-700' => ! $errors->has($name),
            'border-rose-500 focus:border-rose-500 focus:ring-rose-500/30' => $errors->has($name),
        ]) }}
    >

    @error($name)
        <p id="{{ $name }}-error" class="mt-1.5 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
    @enderror
</div>
