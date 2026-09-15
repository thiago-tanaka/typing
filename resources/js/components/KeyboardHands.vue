<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { FINGER_ACCENTS, fingerFor } from '../lib/fingers';

const props = defineProps({
    board: { default: null },
    nextKey: { type: String, default: null },
    shiftSide: { type: String, default: null },
});

// Sizes are in key units (the height of a key) and rows (the distance between two key rows).
// Each finger rests on its home key and joins the palm at `base`, measured from that key:
// x leans toward the middle of the hand and y goes down, so the fingers splay a little.
const FINGERS = [
    { id: 'left-pinky', home: 'a', base: [0.35, 1.8], width: 0.7 },
    { id: 'left-ring', home: 's', base: [0.12, 2.05], width: 0.78 },
    { id: 'left-middle', home: 'd', base: [0, 2.15], width: 0.8 },
    { id: 'left-index', home: 'f', base: [-0.15, 2.05], width: 0.8 },
    { id: 'right-index', home: 'j', base: [0.15, 2.05], width: 0.8 },
    { id: 'right-middle', home: 'k', base: [0, 2.15], width: 0.8 },
    { id: 'right-ring', home: 'l', base: [-0.12, 2.05], width: 0.78 },
    { id: 'right-pinky', home: ';', base: [-0.35, 1.8], width: 0.7 },
];

// The fingertip lands on the lower half of the key, so the letter on it stays readable.
const TIP_DROP = 0.28;
const PALM_TOP = 1.7;
const THUMB_WIDTH = 0.8;

const layout = ref(null);
let observer = null;

// Key positions come from the rendered keyboard, which changes size with the screen.
function measure() {
    const board = props.board;

    if (!board) {
        layout.value = null;

        return;
    }

    const origin = board.getBoundingClientRect();
    const keys = {};

    for (const node of board.querySelectorAll('[data-key-id]')) {
        const rect = node.getBoundingClientRect();
        const left = rect.left - origin.left;

        keys[node.dataset.keyId] = {
            left,
            right: left + rect.width,
            cx: left + rect.width / 2,
            cy: rect.top - origin.top + rect.height / 2,
            height: rect.height,
        };
    }

    layout.value = keys.a && keys.z && keys.space ? { keys, height: origin.height, unit: keys.a.height, row: keys.z.cy - keys.a.cy } : null;
}

watch(
    () => props.board,
    (board) => {
        observer?.disconnect();
        observer = null;

        if (board && typeof ResizeObserver !== 'undefined') {
            observer = new ResizeObserver(measure);
            observer.observe(board);
        }

        measure();
    },
    { immediate: true, flush: 'post' },
);

onBeforeUnmount(() => observer?.disconnect());

// A finger is a capsule anchored where it joins the palm, rotated and stretched until its tip reaches `tip`.
function capsule(base, tip, width, boardHeight) {
    const dx = tip.x - base.x;
    const dy = tip.y - base.y;

    return {
        left: `${base.x - width / 2}px`,
        bottom: `${boardHeight - base.y}px`,
        width: `${width}px`,
        height: `${Math.hypot(dx, dy) + width / 2}px`,
        transform: `rotate(${Math.atan2(dx, -dy)}rad)`,
    };
}

const hands = computed(() => {
    const board = layout.value;

    if (!board) {
        return null;
    }

    const { keys, unit, row } = board;
    const tipOn = (key) => ({ x: key.cx, y: key.cy + TIP_DROP * unit });
    const typing = props.nextKey ? fingerFor(props.nextKey) : null;
    const active = new Set();
    const reach = {};

    if (typing === 'thumb') {
        active.add('left-thumb').add('right-thumb');
    } else if (typing) {
        active.add(typing);

        if (keys[props.nextKey]) {
            reach[typing] = tipOn(keys[props.nextKey]);
        }
    }

    // Shift is held with the pinky of the other hand, near the inner end of the key.
    const shift = props.shiftSide ? keys[`Shift-${props.shiftSide}`] : null;

    if (shift) {
        const pinky = `${props.shiftSide}-pinky`;

        active.add(pinky);
        reach[pinky] = {
            x: props.shiftSide === 'left' ? shift.right - 0.7 * unit : shift.left + 0.7 * unit,
            y: shift.cy + TIP_DROP * unit,
        };
    }

    const fingers = FINGERS.map((finger) => {
        const home = keys[finger.home];
        const width = finger.width * unit;
        const base = { x: home.cx + finger.base[0] * unit, y: home.cy + finger.base[1] * row };

        return {
            id: finger.id,
            hand: finger.id.startsWith('left') ? 0 : 1,
            base,
            width,
            accent: FINGER_ACCENTS[finger.id],
            active: active.has(finger.id),
            style: capsule(base, reach[finger.id] ?? tipOn(home), width, board.height),
        };
    });

    // Thumbs rest on the space bar just inside the index fingers, and lean out toward the palm.
    const space = keys.space;
    const thumbWidth = THUMB_WIDTH * unit;
    const thumbs = [
        { id: 'left-thumb', hand: 0, x: keys.f.cx + 0.9 * unit, lean: -1 },
        { id: 'right-thumb', hand: 1, x: keys.j.cx - 0.9 * unit, lean: 1 },
    ].map((thumb) => {
        const tip = { x: thumb.x, y: space.cy };
        const base = { x: thumb.x + thumb.lean * unit, y: space.cy + 1.2 * row };

        return {
            id: thumb.id,
            hand: thumb.hand,
            base,
            width: thumbWidth,
            accent: FINGER_ACCENTS.thumb,
            active: active.has(thumb.id),
            style: capsule(base, tip, thumbWidth, board.height),
        };
    });

    const palmTop = keys.a.cy + PALM_TOP * row;
    const palms = [0, 1].map((hand) => {
        const joints = [...fingers, ...thumbs].filter((finger) => finger.hand === hand);
        const from = Math.min(...joints.map((finger) => finger.base.x - finger.width / 2));
        const to = Math.max(...joints.map((finger) => finger.base.x + finger.width / 2));

        // The palm runs past the bottom edge, where the keyboard clips it.
        return {
            left: `${from}px`,
            top: `${palmTop}px`,
            width: `${to - from}px`,
            height: `${board.height - palmTop + unit}px`,
            borderRadius: `${0.7 * unit}px ${0.7 * unit}px 0 0`,
        };
    });

    return { palms, fingers: [...fingers, ...thumbs] };
});
</script>

<template>
    <div v-if="hands" class="pointer-events-none absolute inset-0 overflow-hidden rounded-2xl" aria-hidden="true">
        <!-- A single translucent layer, so the places where fingers overlap the palm do not get darker. -->
        <div class="absolute inset-0 opacity-[0.13] dark:opacity-[0.12]">
            <div
                v-for="(palm, index) in hands.palms"
                :key="index"
                class="absolute bg-zinc-900 dark:bg-white"
                :style="palm"
            ></div>
            <div
                v-for="finger in hands.fingers"
                :key="finger.id"
                class="absolute origin-bottom rounded-full bg-zinc-900 transition-[height,transform] duration-100 ease-out motion-reduce:transition-none dark:bg-white"
                :style="finger.style"
            ></div>
        </div>

        <div
            v-for="finger in hands.fingers"
            :key="`${finger.id}-active`"
            class="absolute origin-bottom rounded-full border-2 transition-[height,transform,opacity] duration-100 ease-out [mask-image:linear-gradient(to_top,transparent,black_45%)] motion-reduce:transition-none"
            :class="[finger.accent, finger.active ? 'opacity-100' : 'opacity-0']"
            :style="finger.style"
        ></div>
    </div>
</template>
