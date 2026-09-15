// Visual style of each score level. The thresholds come from the server
// (App\Models\Digitacao::niveis()), so they are defined in one place only.
export const LEVEL_STYLES = {
    excellent: {
        label: 'Excellent',
        dot: 'bg-violet-500',
        badge: 'bg-violet-100 text-violet-700 ring-violet-600/20 dark:bg-violet-400/15 dark:text-violet-300 dark:ring-violet-400/30',
    },
    good: {
        label: 'Good',
        dot: 'bg-emerald-500',
        badge: 'bg-emerald-100 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-400/15 dark:text-emerald-300 dark:ring-emerald-400/30',
    },
    average: {
        label: 'Average',
        dot: 'bg-amber-500',
        badge: 'bg-amber-100 text-amber-800 ring-amber-600/20 dark:bg-amber-400/15 dark:text-amber-300 dark:ring-amber-400/30',
    },
    practice: {
        label: 'Keep practicing',
        dot: 'bg-rose-500',
        badge: 'bg-rose-100 text-rose-700 ring-rose-600/20 dark:bg-rose-400/15 dark:text-rose-300 dark:ring-rose-400/30',
    },
};

export function levelFor(levels, speed, accuracy) {
    const level = levels.find((item) => speed >= item.velocidade && accuracy >= item.precisao);

    return level ? level.nivel : 'practice';
}
