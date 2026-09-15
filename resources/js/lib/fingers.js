// Touch typing finger for each key of a QWERTY keyboard. Letters, space,
// comma and period sit in the same place on US, Brazilian (ABNT2) and
// Japanese (JIS) layouts, which is what the default lessons use.
const KEYS_BY_FINGER = {
    'left-pinky': '`1qaz',
    'left-ring': '2wsx',
    'left-middle': '3edc',
    'left-index': '45rtfgvb',
    'right-index': '67yuhjnm',
    'right-middle': '8ik,',
    'right-ring': '9ol.',
    'right-pinky': "0-=p[]\\;'/",
    thumb: ' ',
};

const FINGER_BY_KEY = Object.fromEntries(
    Object.entries(KEYS_BY_FINGER).flatMap(([finger, keys]) => [...keys].map((key) => [key, finger])),
);

// Characters typed with Shift, mapped to the key that produces them.
const UNSHIFTED = {
    '~': '`',
    '!': '1',
    '@': '2',
    '#': '3',
    $: '4',
    '%': '5',
    '^': '6',
    '&': '7',
    '*': '8',
    '(': '9',
    ')': '0',
    _: '-',
    '+': '=',
    '{': '[',
    '}': ']',
    '|': '\\',
    ':': ';',
    '"': "'",
    '<': ',',
    '>': '.',
    '?': '/',
};

export const FINGER_LABELS = {
    'left-pinky': 'left pinky',
    'left-ring': 'left ring finger',
    'left-middle': 'left middle finger',
    'left-index': 'left index finger',
    'right-index': 'right index finger',
    'right-middle': 'right middle finger',
    'right-ring': 'right ring finger',
    'right-pinky': 'right pinky',
    thumb: 'thumb',
};

const PINKY = 'border-rose-200 bg-rose-50 dark:border-rose-400/25 dark:bg-rose-400/10';
const RING = 'border-amber-200 bg-amber-50 dark:border-amber-400/25 dark:bg-amber-400/10';
const MIDDLE = 'border-emerald-200 bg-emerald-50 dark:border-emerald-400/25 dark:bg-emerald-400/10';

export const FINGER_ZONES = {
    'left-pinky': PINKY,
    'left-ring': RING,
    'left-middle': MIDDLE,
    'left-index': 'border-sky-200 bg-sky-50 dark:border-sky-400/25 dark:bg-sky-400/10',
    'right-index': 'border-indigo-200 bg-indigo-50 dark:border-indigo-400/25 dark:bg-indigo-400/10',
    'right-middle': MIDDLE,
    'right-ring': RING,
    'right-pinky': PINKY,
    thumb: 'border-zinc-300 bg-white dark:border-zinc-700 dark:bg-zinc-800',
};

// Stronger versions of the zone colors, for the finger that has to press the next key.
const PINKY_ACCENT = 'border-rose-500 bg-rose-400/40 dark:border-rose-400 dark:bg-rose-400/30';
const RING_ACCENT = 'border-amber-500 bg-amber-400/40 dark:border-amber-400 dark:bg-amber-400/30';
const MIDDLE_ACCENT = 'border-emerald-500 bg-emerald-400/40 dark:border-emerald-400 dark:bg-emerald-400/30';

export const FINGER_ACCENTS = {
    'left-pinky': PINKY_ACCENT,
    'left-ring': RING_ACCENT,
    'left-middle': MIDDLE_ACCENT,
    'left-index': 'border-sky-500 bg-sky-400/40 dark:border-sky-400 dark:bg-sky-400/30',
    'right-index': 'border-indigo-500 bg-indigo-400/40 dark:border-indigo-400 dark:bg-indigo-400/30',
    'right-middle': MIDDLE_ACCENT,
    'right-ring': RING_ACCENT,
    'right-pinky': PINKY_ACCENT,
    thumb: 'border-zinc-500 bg-zinc-400/40 dark:border-zinc-300 dark:bg-zinc-300/30',
};

export function keyFor(character) {
    if (!character) {
        return null;
    }

    if (character >= 'A' && character <= 'Z') {
        return { key: character.toLowerCase(), shift: true };
    }

    if (UNSHIFTED[character]) {
        return { key: UNSHIFTED[character], shift: true };
    }

    return { key: character.toLowerCase(), shift: false };
}

export function fingerFor(character) {
    const info = keyFor(character);

    return info ? (FINGER_BY_KEY[info.key] ?? null) : null;
}
