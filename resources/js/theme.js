const STORAGE_KEY = 'theme';

// The initial theme is applied by an inline script in the layout, before the
// page is painted. This only wires the toggle buttons.
export function setupThemeToggle() {
    const root = document.documentElement;

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-pressed', String(root.classList.contains('dark')));

        button.addEventListener('click', () => {
            const dark = root.classList.toggle('dark');

            try {
                localStorage.setItem(STORAGE_KEY, dark ? 'dark' : 'light');
            } catch {
                // Storage can be unavailable (private mode); the toggle still works for this page.
            }

            button.setAttribute('aria-pressed', String(dark));
        });
    });
}
