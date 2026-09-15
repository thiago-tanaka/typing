<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import OnScreenKeyboard from './OnScreenKeyboard.vue';
import { FINGER_LABELS, fingerFor, keyFor } from '../lib/fingers';
import { LEVEL_STYLES, levelFor } from '../lib/levels';

const props = defineProps({
    unit: { type: Number, required: true },
    lesson: { type: Number, required: true },
    lines: { type: Array, required: true },
    saveUrl: { type: String, default: null },
    canSave: { type: Boolean, default: false },
    nextUrl: { type: String, default: null },
    loginUrl: { type: String, required: true },
    registerUrl: { type: String, default: null },
    best: { type: Object, default: null },
    levels: { type: Array, required: true },
});

const lines = props.lines.map((text) => String(text ?? ''));
const totalChars = lines.reduce((sum, text) => sum + text.length, 0);

const line = ref(0);
const position = ref(0);
const correct = ref(0);
const attempts = ref(0);
const startedAt = ref(null);
const finishedAt = ref(null);
const now = ref(0);
const errorFlash = ref(false);
const wrongKey = ref(null);
const saveState = ref('idle');
const saved = ref(false);
const bestScore = ref(props.best);
const previousBest = ref(props.best);
const imeWarning = ref(false);
const root = ref(null);
const showHands = ref(readHandsPreference());
const hintVisible = ref(false);

// How long a pause has to be before the keyboard shows the next key.
const HINT_DELAY = 800;

let ticker = null;
let flashTimer = null;
let imeTimer = null;
let hintTimer = null;

const kbd =
    'rounded-md border border-zinc-300 bg-zinc-100 px-1.5 py-0.5 font-mono text-xs text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200';
const primaryButton =
    'inline-flex items-center gap-2 rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-500';
const secondaryButton =
    'inline-flex items-center gap-2 rounded-lg border border-zinc-300 bg-white px-4 py-2 text-sm font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700';

const smallButton =
    'inline-flex items-center gap-2 rounded-lg border border-zinc-300 bg-white px-3 py-1.5 text-sm font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700';

const started = computed(() => startedAt.value !== null);
const finished = computed(() => finishedAt.value !== null);
const nextChar = computed(() => (finished.value ? null : (lines[line.value]?.[position.value] ?? null)));
const nextKey = computed(() => keyFor(nextChar.value));
const nextLabel = computed(() => (nextChar.value === ' ' ? 'Space' : nextChar.value));
const fingerLabel = computed(() => FINGER_LABELS[fingerFor(nextChar.value)] ?? null);

const typedChars = computed(() =>
    finished.value ? totalChars : lines.slice(0, line.value).reduce((sum, text) => sum + text.length, 0) + position.value,
);
const progress = computed(() => (totalChars === 0 ? 100 : Math.round((typedChars.value / totalChars) * 100)));
const elapsedMs = computed(() => (started.value ? (finishedAt.value ?? now.value) - startedAt.value : 0));

// Characters typed correctly per minute, the unit the scores have always used.
const speed = computed(() =>
    started.value ? Math.round(correct.value / (Math.max(elapsedMs.value, 1000) / 60000)) : 0,
);
const accuracy = computed(() => (attempts.value === 0 ? 100 : Math.round((correct.value / attempts.value) * 100)));
const level = computed(() => LEVEL_STYLES[levelFor(props.levels, speed.value, accuracy.value)]);

const elapsedLabel = computed(() => {
    const seconds = Math.floor(elapsedMs.value / 1000);

    return `${Math.floor(seconds / 60)}:${String(seconds % 60).padStart(2, '0')}`;
});

const stats = computed(() => [
    { label: 'Speed', value: speed.value, unit: 'CPM' },
    { label: 'Accuracy', value: accuracy.value, unit: '%' },
    { label: 'Time', value: elapsedLabel.value, unit: '' },
    bestScore.value
        ? { label: 'Your best', value: bestScore.value.velocidade, unit: `CPM · ${bestScore.value.precisao}%` }
        : { label: 'Your best', value: '—', unit: '' },
]);

const improved = computed(
    () =>
        !previousBest.value ||
        speed.value !== previousBest.value.velocidade ||
        accuracy.value !== previousBest.value.precisao,
);

const saveMessage = computed(() => {
    if (!props.canSave && saveState.value !== 'invalid') {
        return '';
    }

    switch (saveState.value) {
        case 'saving':
            return 'Saving your score…';
        case 'error':
            return 'Your score could not be saved. Check your connection and try again.';
        case 'expired':
            return 'Your session has expired, so this score was not saved.';
        case 'relogin':
            return 'Your session has expired.';
        case 'unverified':
            return 'Verify your email address to save your scores.';
        case 'invalid':
            return 'This result could not be saved.';
        case 'saved':
            if (!saved.value) {
                return bestScore.value
                    ? `Not a new record. Your best here is ${bestScore.value.velocidade} CPM · ${bestScore.value.precisao}%.`
                    : 'Not a new record.';
            }

            return improved.value ? 'New personal best! Your score was saved.' : 'You matched your best score.';
        default:
            return '';
    }
});

const showLoginPrompt = computed(
    () => saveState.value === 'relogin' || (!props.canSave && ['saving', 'pending'].includes(saveState.value)),
);

function charClass(lineIndex, charIndex) {
    const done = finished.value || lineIndex < line.value || (lineIndex === line.value && charIndex < position.value);

    if (done) {
        return 'text-zinc-400 dark:text-zinc-600';
    }

    if (lineIndex === line.value && charIndex === position.value) {
        return errorFlash.value
            ? 'rounded-sm border-b-2 border-rose-500 bg-rose-500/20 text-rose-600 dark:text-rose-400'
            : 'rounded-sm border-b-2 border-orange-500 bg-orange-500/15 text-zinc-900 motion-safe:animate-caret dark:text-white';
    }

    return 'text-zinc-800 dark:text-zinc-200';
}

function startTicker() {
    now.value = performance.now();
    ticker = setInterval(() => {
        now.value = performance.now();
    }, 200);
}

function stopTicker() {
    clearInterval(ticker);
    ticker = null;
}

// Moves to the next line when the current one is done, and finishes the lesson after the last line.
function settle() {
    while (position.value >= (lines[line.value]?.length ?? 0)) {
        if (line.value >= lines.length - 1) {
            finish();

            return;
        }

        line.value += 1;
        position.value = 0;
    }
}

function finish() {
    finishedAt.value = started.value ? performance.now() : 0;
    stopTicker();
    previousBest.value = bestScore.value;

    if (props.saveUrl && started.value) {
        save();
    }
}

// Keeps the lesson card above the text in sync with the best score just saved.
function updateLessonCard(best) {
    const card = document.querySelector(`[data-lesson-card="${props.lesson}"]`);

    if (!card || !best) {
        return;
    }

    const nivel = levelFor(props.levels, best.velocidade, best.precisao);
    const style = LEVEL_STYLES[nivel];
    const text = card.querySelector('[data-score-text]');
    const label = card.querySelector('[data-score-label]');

    card.dataset.level = nivel;
    card.querySelector('[data-score]')?.removeAttribute('hidden');
    card.querySelector('[data-score-empty]')?.setAttribute('hidden', '');
    card.querySelector('[data-score-dot]')?.setAttribute('class', `size-2 shrink-0 rounded-full ${style.dot}`);

    if (text) {
        text.textContent = `${best.velocidade} · ${best.precisao}%`;
    }

    if (label) {
        label.textContent = style.label;
    }
}

async function save() {
    saveState.value = 'saving';

    try {
        const response = await fetch(props.saveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
            },
            body: JSON.stringify({ licao_velocidade: speed.value, licao_precisao: accuracy.value }),
        });

        const problems = { 401: 'expired', 403: 'unverified', 419: 'expired', 422: 'invalid' };

        if (problems[response.status]) {
            saveState.value = problems[response.status];

            return;
        }

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();

        // The server keeps a guest's result and saves it once they log in or sign up.
        if (data.pending) {
            saveState.value = props.canSave ? 'relogin' : 'pending';

            return;
        }

        saved.value = data.saved;
        bestScore.value = data.best;
        updateLessonCard(data.best);
        saveState.value = 'saved';
    } catch {
        saveState.value = 'error';
    }
}

// On short screens, bring the text and the keyboard into view when the lesson starts.
function keepLessonInView() {
    const section = root.value;

    if (section && section.getBoundingClientRect().bottom > window.innerHeight) {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        section.scrollIntoView({ block: 'start', behavior: reduceMotion ? 'auto' : 'smooth' });
    }
}

// The keyboard shows the next key only when the typist pauses or misses, so typing
// at a steady pace keeps the eyes on the text instead of on the keyboard.
function showHint() {
    clearTimeout(hintTimer);
    hintVisible.value = true;
}

function scheduleHint() {
    clearTimeout(hintTimer);
    hintVisible.value = false;

    if (!finished.value) {
        hintTimer = setTimeout(showHint, HINT_DELAY);
    }
}

function flashError(key) {
    showHint();
    errorFlash.value = true;
    wrongKey.value = keyFor(key)?.key ?? null;
    clearTimeout(flashTimer);
    flashTimer = setTimeout(() => {
        errorFlash.value = false;
        wrongKey.value = null;
    }, 180);
}

function warnAboutIme() {
    imeWarning.value = true;
    clearTimeout(imeTimer);
    imeTimer = setTimeout(() => {
        imeWarning.value = false;
    }, 5000);
}

function restart() {
    stopTicker();
    clearTimeout(flashTimer);
    line.value = 0;
    position.value = 0;
    correct.value = 0;
    attempts.value = 0;
    startedAt.value = null;
    finishedAt.value = null;
    errorFlash.value = false;
    wrongKey.value = null;
    saveState.value = 'idle';
    saved.value = false;
    settle();
    showHint();
}

function restartFromButton(event) {
    event.currentTarget.blur();
    restart();
}

const HANDS_PREFERENCE = 'typing:hands';

function readHandsPreference() {
    try {
        return window.localStorage.getItem(HANDS_PREFERENCE) !== 'off';
    } catch {
        return true;
    }
}

function toggleHands(event) {
    event.currentTarget.blur();
    showHands.value = !showHands.value;

    try {
        window.localStorage.setItem(HANDS_PREFERENCE, showHands.value ? 'on' : 'off');
    } catch {
        // Storage can be blocked (private windows); the toggle still works on this page.
    }
}

function onKeydown(event) {
    if (event.defaultPrevented || event.ctrlKey || event.metaKey || event.altKey) {
        return;
    }

    const target = event.target;

    if (target instanceof HTMLElement && (target.isContentEditable || ['INPUT', 'TEXTAREA', 'SELECT'].includes(target.tagName))) {
        return;
    }

    // With a Japanese input method on, the browser sends composition keys instead of characters.
    if (event.isComposing || event.key === 'Process') {
        warnAboutIme();

        return;
    }

    if (event.key === 'Escape') {
        event.preventDefault();
        restart();

        return;
    }

    if (finished.value) {
        if (event.key === 'Enter' && props.nextUrl) {
            event.preventDefault();
            window.location.assign(props.nextUrl);
        }

        return;
    }

    // Ignore Shift, Tab, dead keys and other keys that do not type a character.
    if (event.key.length !== 1) {
        return;
    }

    imeWarning.value = false;

    event.preventDefault();
    attempts.value += 1;

    if (event.key !== nextChar.value) {
        flashError(event.key);

        return;
    }

    if (!started.value) {
        startedAt.value = performance.now();
        startTicker();
        keepLessonInView();
    }

    correct.value += 1;
    position.value += 1;
    settle();
    scheduleHint();
}

onMounted(() => {
    window.addEventListener('keydown', onKeydown);
    settle();
    showHint();
});

onBeforeUnmount(() => {
    window.removeEventListener('keydown', onKeydown);
    stopTicker();
    clearTimeout(flashTimer);
    clearTimeout(imeTimer);
    clearTimeout(hintTimer);
});
</script>

<template>
    <section ref="root" class="scroll-mt-20 space-y-4" :aria-label="`Unit ${unit}, lesson ${lesson}`">
        <p
            class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900 sm:hidden dark:border-amber-400/30 dark:bg-amber-400/10 dark:text-amber-200"
        >
            This course is made for a physical keyboard. Open it on a computer for the full experience.
        </p>

        <div
            class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
        >
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 px-5 py-2.5 sm:px-8">
                <dl class="flex flex-wrap items-baseline gap-x-6 gap-y-1">
                    <div v-for="stat in stats" :key="stat.label" class="flex items-baseline gap-1.5">
                        <dt class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ stat.label }}
                        </dt>
                        <dd class="text-xl font-semibold tabular-nums">
                            {{ stat.value }}
                            <span v-if="stat.unit" class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{
                                stat.unit
                            }}</span>
                        </dd>
                    </div>
                </dl>
                <div class="ml-auto flex items-center gap-2">
                    <button
                        type="button"
                        :class="[smallButton, 'max-md:hidden']"
                        :aria-pressed="showHands"
                        @click="toggleHands"
                    >
                        Hands
                        <span
                            class="rounded-md px-1.5 py-0.5 text-xs font-semibold"
                            :class="
                                showHands
                                    ? 'bg-orange-100 text-orange-700 dark:bg-orange-400/15 dark:text-orange-300'
                                    : 'bg-zinc-100 text-zinc-500 dark:bg-zinc-700 dark:text-zinc-400'
                            "
                            >{{ showHands ? 'On' : 'Off' }}</span
                        >
                    </button>
                    <button type="button" :class="smallButton" @click="restartFromButton">
                        Restart <kbd :class="kbd">Esc</kbd>
                    </button>
                </div>
            </div>

            <div
                class="h-1 bg-zinc-100 dark:bg-zinc-800"
                role="progressbar"
                aria-label="Lesson progress"
                aria-valuemin="0"
                aria-valuemax="100"
                :aria-valuenow="progress"
            >
                <div class="h-full bg-orange-500 transition-[width] duration-150" :style="{ width: `${progress}%` }"></div>
            </div>

            <div class="relative">
                <div
                    class="px-5 py-5 font-mono text-lg leading-relaxed tracking-wide sm:px-8 sm:text-2xl"
                    :class="{ 'motion-safe:animate-shake': errorFlash }"
                >
                    <p v-for="(text, lineIndex) in lines" :key="lineIndex" class="whitespace-pre-wrap break-words">
                        <span
                            v-for="(character, charIndex) in text"
                            :key="charIndex"
                            :class="charClass(lineIndex, charIndex)"
                            >{{ character }}</span
                        >
                    </p>
                </div>

                <div
                    v-if="finished"
                    class="absolute inset-0 grid place-items-center bg-white/90 px-4 backdrop-blur-sm dark:bg-zinc-900/90"
                    role="status"
                    aria-live="polite"
                >
                    <div class="text-center">
                        <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">
                            Lesson complete
                        </p>
                        <div class="mt-1 flex flex-wrap items-center justify-center gap-x-3 gap-y-1">
                            <p class="text-4xl font-semibold tabular-nums">
                                {{ speed }}<span class="text-base font-medium text-zinc-500 dark:text-zinc-400"> CPM</span>
                                <span class="px-1 text-zinc-300 dark:text-zinc-600">·</span>
                                {{ accuracy }}<span class="text-base font-medium text-zinc-500 dark:text-zinc-400">%</span>
                            </p>
                            <span
                                class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium ring-1 ring-inset"
                                :class="level.badge"
                                >{{ level.label }}</span
                            >
                        </div>
                        <p v-if="saveMessage" class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">{{ saveMessage }}</p>
                        <p v-if="showLoginPrompt" class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
                            <a :href="loginUrl" class="font-medium text-orange-600 hover:underline dark:text-orange-400">Log in</a>
                            <template v-if="registerUrl">
                                or
                                <a :href="registerUrl" class="font-medium text-orange-600 hover:underline dark:text-orange-400"
                                    >create an account</a
                                >
                            </template>
                            to save this result.
                        </p>
                        <div class="mt-4 flex flex-wrap justify-center gap-2">
                            <button type="button" :class="secondaryButton" @click="restartFromButton">
                                Try again <kbd :class="kbd">Esc</kbd>
                            </button>
                            <button v-if="saveState === 'error'" type="button" :class="secondaryButton" @click="save">
                                Retry saving
                            </button>
                            <a v-if="nextUrl" :href="nextUrl" :class="primaryButton">
                                Next lesson <kbd class="rounded-md bg-white/20 px-1.5 py-0.5 font-mono text-xs">Enter</kbd>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p
            v-if="imeWarning"
            role="alert"
            class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-400/30 dark:bg-amber-400/10 dark:text-amber-200"
        >
            Your keyboard is in Japanese input mode. Switch to direct input (for example with the 半角/全角 key) to type
            the lesson.
        </p>

        <p class="text-sm text-zinc-600 dark:text-zinc-400">
            <template v-if="finished">
                Press <kbd :class="kbd">Esc</kbd> to try again<template v-if="nextUrl">
                    or <kbd :class="kbd">Enter</kbd> for the next lesson</template
                >.
            </template>
            <template v-else-if="!started">
                Start typing to begin: the timer starts with your first correct key. Keep your eyes on the text; the
                keyboard lights up only when you pause.
            </template>
            <span v-else :class="{ invisible: !hintVisible }">
                Next key <kbd :class="kbd">{{ nextLabel }}</kbd>
                <template v-if="fingerLabel"> with your {{ fingerLabel }}</template>
                <template v-if="nextKey?.shift"> + Shift</template>
            </span>
        </p>

        <OnScreenKeyboard
            :next-key="hintVisible ? (nextKey?.key ?? null) : null"
            :shift="hintVisible && (nextKey?.shift ?? false)"
            :wrong-key="wrongKey"
            :hands="showHands"
        />
    </section>
</template>
