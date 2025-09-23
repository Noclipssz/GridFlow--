import './bootstrap';

(function () {
    const storageKey = 'theme';
    const doc = document.documentElement;
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');

    const apply = (value, persist = true) => {
        const theme = value === 'dark' ? 'dark' : 'light';
        doc.classList.toggle('dark', theme === 'dark');
        doc.dataset.theme = theme;
        if (persist) {
            try {
                localStorage.setItem(storageKey, theme);
            } catch (e) {}
        }
        doc.dispatchEvent(new CustomEvent('themechange', { detail: theme }));
        return theme;
    };

    let stored;
    try {
        stored = localStorage.getItem(storageKey);
    } catch (e) {
        stored = null;
    }

    const initial = stored === 'light' || stored === 'dark'
        ? stored
        : (prefersDark.matches ? 'dark' : 'light');

    apply(initial, stored === 'light' || stored === 'dark');

    prefersDark.addEventListener('change', (event) => {
        let manual;
        try {
            manual = localStorage.getItem(storageKey);
        } catch (e) {
            manual = null;
        }
        if (manual !== 'light' && manual !== 'dark') {
            apply(event.matches ? 'dark' : 'light', false);
        }
    });

    const setTheme = (theme) => apply(theme);
    const toggleTheme = () => apply(doc.classList.contains('dark') ? 'light' : 'dark');

    window.appTheme = {
        set: setTheme,
        toggle: toggleTheme,
        current: () => (doc.classList.contains('dark') ? 'dark' : 'light'),
        onChange(callback) {
            if (typeof callback === 'function') {
                doc.addEventListener('themechange', (event) => callback(event.detail));
                callback(this.current());
            }
        },
    };

    window.addEventListener('DOMContentLoaded', () => {
        const button = document.getElementById('themeToggle');
        if (button) {
            const clone = button.cloneNode(true);
            button.replaceWith(clone);
            clone.addEventListener('click', () => toggleTheme());
        }

        const themeButtons = document.querySelectorAll('[data-theme-value]');
        const syncState = (theme) => {
            themeButtons.forEach((btn) => {
                const isActive = btn.dataset.themeValue === theme;
                btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });
        };

        if (themeButtons.length) {
            themeButtons.forEach((btn) => {
                btn.addEventListener('click', () => setTheme(btn.dataset.themeValue));
            });
            window.appTheme.onChange(syncState);
        }
    });
})();
