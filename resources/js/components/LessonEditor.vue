<script setup>
import { computed, onMounted, reactive, ref } from 'vue';

const props = defineProps({
    unitsUrl: { type: String, required: true },
    updateUrl: { type: String, required: true },
});

const MAX_LENGTH = 255;
const FIELDS = ['text1', 'text2', 'text3', 'text4'];

const units = ref([]);
const activeUnitId = ref(null);
const loading = ref(true);
const loadFailed = ref(false);
const status = reactive({});

const activeUnit = computed(() => units.value.find((unit) => unit.id === activeUnitId.value) ?? null);

const statusText = {
    saving: 'Saving…',
    saved: 'Saved',
    error: 'Could not save. Try again.',
};

const statusClass = {
    saving: 'text-zinc-500 dark:text-zinc-400',
    saved: 'text-emerald-600 dark:text-emerald-400',
    error: 'text-rose-600 dark:text-rose-400',
};

const byName = (a, b) => Number(a.name) - Number(b.name);

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

async function load() {
    loading.value = true;
    loadFailed.value = false;

    try {
        const response = await fetch(props.unitsUrl, { headers: { Accept: 'application/json' } });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();
        units.value = data.units.map((unit) => ({ ...unit, lessons: [...unit.lessons].sort(byName) })).sort(byName);
        activeUnitId.value = units.value[0]?.id ?? null;
    } catch {
        loadFailed.value = true;
    } finally {
        loading.value = false;
    }
}

async function save(lesson) {
    status[lesson.id] = 'saving';

    try {
        const response = await fetch(`${props.updateUrl}/${lesson.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: JSON.stringify(Object.fromEntries(FIELDS.map((field) => [field, lesson[field]]))),
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        status[lesson.id] = 'saved';
        setTimeout(() => {
            if (status[lesson.id] === 'saved') {
                status[lesson.id] = null;
            }
        }, 2500);
    } catch {
        status[lesson.id] = 'error';
    }
}

onMounted(load);
</script>

<template>
    <div>
        <p v-if="loading" class="text-sm text-zinc-500 dark:text-zinc-400">Loading lessons…</p>

        <div
            v-else-if="loadFailed"
            class="rounded-xl border border-rose-300 bg-rose-50 p-4 text-sm text-rose-800 dark:border-rose-400/30 dark:bg-rose-400/10 dark:text-rose-200"
        >
            The lessons could not be loaded.
            <button type="button" class="font-semibold underline" @click="load">Try again</button>
        </div>

        <template v-else>
            <div role="tablist" aria-label="Units" class="inline-flex gap-1 rounded-xl bg-zinc-200/70 p-1 dark:bg-zinc-800/70">
                <button
                    v-for="unit in units"
                    :key="unit.id"
                    type="button"
                    role="tab"
                    :aria-selected="unit.id === activeUnitId"
                    class="rounded-lg px-4 py-1.5 text-sm font-medium transition focus-visible:outline-2 focus-visible:outline-orange-500"
                    :class="
                        unit.id === activeUnitId
                            ? 'bg-white text-zinc-900 shadow-sm dark:bg-zinc-700 dark:text-white'
                            : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white'
                    "
                    @click="activeUnitId = unit.id"
                >
                    Unit {{ unit.name }}
                </button>
            </div>

            <div v-if="activeUnit" role="tabpanel" class="mt-6 grid gap-4 lg:grid-cols-2">
                <form
                    v-for="lesson in activeUnit.lessons"
                    :key="lesson.id"
                    class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
                    @submit.prevent="save(lesson)"
                >
                    <h2 class="font-semibold">Lesson {{ lesson.name }}</h2>

                    <div class="mt-4 space-y-3">
                        <label v-for="(field, index) in FIELDS" :key="field" class="block">
                            <span class="flex justify-between text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                <span>Line {{ index + 1 }}</span>
                                <span class="tabular-nums">{{ (lesson[field] ?? '').length }}/{{ MAX_LENGTH }}</span>
                            </span>
                            <input
                                v-model="lesson[field]"
                                type="text"
                                required
                                :maxlength="MAX_LENGTH"
                                class="mt-1 block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 font-mono text-sm text-zinc-900 outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                            />
                        </label>
                    </div>

                    <div class="mt-4 flex items-center justify-end gap-3">
                        <span class="text-sm" aria-live="polite" :class="statusClass[status[lesson.id]]">
                            {{ statusText[status[lesson.id]] ?? '' }}
                        </span>
                        <button
                            type="submit"
                            :disabled="status[lesson.id] === 'saving'"
                            class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-500 disabled:opacity-60"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </template>
    </div>
</template>
