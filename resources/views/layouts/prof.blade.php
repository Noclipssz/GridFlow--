<!doctype html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Professor')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        (() => {
            const storageKey = 'theme';
            const doc = document.documentElement;
            try {
                const stored = localStorage.getItem(storageKey);
                if (stored === 'dark' || stored === 'light') {
                    doc.classList.toggle('dark', stored === 'dark');
                    doc.dataset.theme = stored;
                    return;
                }
            } catch (e) {}

            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            doc.classList.toggle('dark', prefersDark);
            doc.dataset.theme = prefersDark ? 'dark' : 'light';
        })();
    </script>
</head>
@php
    $layoutMode = trim((string) $__env->yieldContent('layout_mode')) ?: 'app';
    $isAuth = $layoutMode === 'auth';
    $mainClass = $isAuth
        ? trim((string) $__env->yieldContent('auth_main_class')) ?: 'w-full max-w-md mx-auto py-12'
        : 'page-container w-full flex-1 py-8';
@endphp
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="{{ $isAuth ? 'flex min-h-screen flex-col items-center justify-center px-4' : 'flex min-h-screen flex-col' }}">
        @unless($isAuth)
            <header class="border-b border-slate-200 bg-white/90 backdrop-blur dark:border-slate-800 dark:bg-slate-900/80">
                <div class="page-container flex items-center justify-between gap-3 py-3">
                    <a href="{{ route('prof.basic.dashboard') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-white">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-base text-white">GF</span>
                        <span>GridFlow — Professor</span>
                    </a>
                    <nav class="flex items-center gap-2 text-sm">
                        <a href="{{ route('prof.basic.schedule') }}" class="hidden sm:inline-flex btn btn-ghost">Disponibilidade</a>
                        <a href="{{ route('prof.turmas.index') }}" class="hidden sm:inline-flex btn btn-ghost">Minhas turmas</a>
                        <form method="POST" action="{{ route('prof.basic.logout') }}" class="hidden sm:inline">
                            @csrf
                            <button type="submit" class="btn btn-ghost">Sair</button>
                        </form>
                        <div class="theme-toggle" role="group" aria-label="Selecionar tema">
                            <button type="button" class="theme-option" data-theme-value="light">Claro</button>
                            <button type="button" class="theme-option" data-theme-value="dark">Escuro</button>
                        </div>
                    </nav>
                </div>
            </header>
        @endunless

        <main class="{{ $mainClass }}">
            @unless($isAuth)
                <x-ui.flash />
            @endunless
            @yield('content')
        </main>

        @unless($isAuth)
            <footer class="border-t border-slate-200 py-6 dark:border-slate-800">
                <div class="page-container text-xs text-slate-500 dark:text-slate-400">© {{ date('Y') }} GridFlow</div>
            </footer>
        @endunless
    </div>
</body>
</html>
