<script setup>
import { computed, ref } from 'vue';
import KeyboardHands from './KeyboardHands.vue';
import { FINGER_ZONES, fingerFor } from '../lib/fingers';

const props = defineProps({
    nextKey: { type: String, default: null },
    shift: { type: Boolean, default: false },
    wrongKey: { type: String, default: null },
    hands: { type: Boolean, default: true },
});

const board = ref(null);

const letter = (value, width = 1) => ({ id: value, char: value, label: value.toUpperCase(), width });
const modifier = (label, width, side = null) => ({ id: `${label}-${side ?? width}`, char: null, label, width, side });

// Every row is 15 key units wide, so wide keys line up like on a real keyboard.
const rows = [
    [modifier('Tab', 1.5), ...[...'qwertyuiop[]'].map((key) => letter(key)), letter('\\', 1.5)],
    [modifier('Caps', 1.75), ...[..."asdfghjkl;'"].map((key) => letter(key)), modifier('Enter', 2.25)],
    [modifier('Shift', 2.25, 'left'), ...[...'zxcvbnm,./'].map((key) => letter(key)), modifier('Shift', 2.75, 'right')],
];

const space = { id: 'space', char: ' ', label: '', width: 6.25 };

const legend = [
    { label: 'Pinky', zone: FINGER_ZONES['left-pinky'] },
    { label: 'Ring', zone: FINGER_ZONES['left-ring'] },
    { label: 'Middle', zone: FINGER_ZONES['left-middle'] },
    { label: 'Left index', zone: FINGER_ZONES['left-index'] },
    { label: 'Right index', zone: FINGER_ZONES['right-index'] },
    { label: 'Thumbs', zone: FINGER_ZONES.thumb },
];

// Shift is pressed with the pinky of the other hand.
const shiftSide = computed(() => {
    if (!props.shift || !props.nextKey) {
        return null;
    }

    return fingerFor(props.nextKey)?.startsWith('left') ? 'right' : 'left';
});

function keyWidth(key) {
    return { width: `calc(var(--u) * ${key.width} + ${(key.width - 1) * 0.375}rem)` };
}

function keyClass(key) {
    if (key.char !== null && key.char === props.wrongKey) {
        return 'border-rose-600 bg-rose-500 text-white';
    }

    if ((key.char !== null && key.char === props.nextKey) || (key.side && key.side === shiftSide.value)) {
        return 'border-orange-600 bg-orange-500 text-white shadow-md shadow-orange-500/40';
    }

    if (key.char === null) {
        return 'border-zinc-300 bg-zinc-200/70 text-zinc-400 dark:border-zinc-700 dark:bg-zinc-800/70 dark:text-zinc-500';
    }

    return `${FINGER_ZONES[fingerFor(key.char)] ?? FINGER_ZONES.thumb} text-zinc-700 dark:text-zinc-300`;
}
</script>

<template>
    <div class="hidden md:block" aria-hidden="true">
        <div class="relative mx-auto w-fit">
            <div
                ref="board"
                class="select-none rounded-2xl border border-zinc-200 bg-zinc-100 p-2.5 on-screen-keyboard dark:border-zinc-800 dark:bg-zinc-900"
            >
                <div v-for="(row, index) in rows" :key="index" class="mb-1.5 flex gap-1.5">
                    <div
                        v-for="key in row"
                        :key="key.id"
                        :data-key-id="key.id"
                        class="relative grid h-(--u) place-items-center rounded-lg border text-xs font-medium transition-colors duration-75 lg:text-sm"
                        :class="keyClass(key)"
                        :style="keyWidth(key)"
                    >
                        {{ key.label }}
                        <span
                            v-if="key.char === 'f' || key.char === 'j'"
                            class="absolute bottom-1.5 h-0.5 w-2.5 rounded-full bg-current opacity-60"
                        ></span>
                    </div>
                </div>
                <div class="flex justify-center">
                    <div
                        :data-key-id="space.id"
                        class="h-(--u) rounded-lg border transition-colors duration-75"
                        :class="keyClass(space)"
                        :style="keyWidth(space)"
                    ></div>
                </div>
            </div>

            <KeyboardHands v-if="hands" :board="board" :next-key="nextKey" :shift-side="shiftSide" />
        </div>

        <ul class="mt-3 flex flex-wrap justify-center gap-x-4 gap-y-2 text-xs text-zinc-500 [@media(max-height:820px)]:hidden dark:text-zinc-400">
            <li v-for="item in legend" :key="item.label" class="flex items-center gap-1.5">
                <span class="size-3 rounded border" :class="item.zone"></span>
                {{ item.label }}
            </li>
        </ul>
    </div>
</template>
