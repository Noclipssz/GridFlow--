<!doctype html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        (function () {
            try {
                const saved = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = saved || (prefersDark ? 'dark' : 'light');
                document.documentElement.classList.toggle('dark', theme === 'dark');
            } catch (e) {}
        })();
    </script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="flex min-h-screen flex-col">
        <header class="border-b border-slate-200 bg-white/90 backdrop-blur dark:border-slate-800 dark:bg-slate-900/80">
            <div class="page-container flex items-center justify-between gap-3 py-3">
                <a href="/admin" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-white">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-base text-white">GF</span>
                    <span>GridFlow — Admin</span>
                </a>
                <nav class="flex items-center gap-2 text-sm">
                    <a href="/admin" class="hidden sm:inline-flex btn btn-ghost">Painel</a>
                    <button type="button" id="themeToggle" class="btn btn-ghost">Tema</button>
                </nav>
            </div>
        </header>

        <main class="page-container w-full flex-1 py-8">
            <x-ui.flash />
            @yield('content')
        </main>

        <footer class="border-t border-slate-200 py-6 dark:border-slate-800">
            <div class="page-container text-xs text-slate-500 dark:text-slate-400">© {{ date('Y') }} GridFlow</div>
        </footer>
    </div>

    <script>
        (function () {
            const btn = document.getElementById('themeToggle');
            if (!btn) return;
            btn.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                try {
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                } catch (e) {}
            });
        })();
    </script>
</body>
</html>
