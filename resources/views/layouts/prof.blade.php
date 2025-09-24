<!doctype html>
<html lang="pt-BR" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Professor')</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script>
    (function() {
      try {
        const saved = localStorage.getItem('theme');
        const prefers = matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        const theme = saved || prefers;
        document.documentElement.classList.toggle('dark', theme === 'dark');
        document.documentElement.dataset.theme = theme;
      } catch (e) {}
    })();
  </script>
</head>
<body class="h-full bg-slate-50 text-slate-800 dark:bg-slate-900 dark:text-slate-100">
  <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200 dark:bg-slate-900/80 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
      <a href="{{ route('prof.basic.dashboard') }}" class="inline-flex items-center gap-2 font-semibold text-slate-900 dark:text-slate-100">
        <span class="inline-grid place-items-center h-8 w-8 rounded-lg bg-indigo-600 text-white">Gf</span>
        <span>GridFlow — Professor</span>
      </a>
      <div class="flex items-center gap-2">
        <x-ui.button variant="subtle" size="sm" href="{{ route('prof.basic.schedule') }}" class="hidden sm:inline-flex">Disponibilidade</x-ui.button>
        <x-ui.button variant="subtle" size="sm" href="{{ route('prof.turmas.index') }}" class="hidden sm:inline-flex">Minhas turmas</x-ui.button>
        <form method="POST" action="{{ route('prof.basic.logout') }}" class="hidden sm:inline">
          @csrf
          <x-ui.button variant="secondary" size="sm">Sair</x-ui.button>
        </form>
        <button type="button" id="themeToggle" class="text-sm rounded-xl bg-slate-100 px-3 py-1.5 font-medium text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
            <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h1M3 12H2m8.042-8.485L11.354 1.646m4.95 1.708l-.707.707M12 21.213V22m-9-9H3m8.042 8.485L11.354 22.354m4.95-1.708l-.707-.707M12 18a6 6 0 100-12 6 6 0 000 12z"></path></svg>
            <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9 9 0 008.354-5.646z"></path></svg>
        </button>
      </div>
    </div>
  </header>

  <main class="max-w-7xl mx-auto p-4 sm:p-6">
    <x-ui.flash />
    @yield('content')
  </main>

  <footer class="mt-10 border-t border-slate-200 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-4 py-6 text-xs text-slate-500">© {{ date('Y') }} GridFlow</div>
  </footer>

  </body>
</html>
