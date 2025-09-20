<!doctype html>
<html lang="pt-BR" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Professor')</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
        <a href="{{ route('prof.basic.schedule') }}" class="hidden sm:inline-flex text-sm px-3 py-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">Disponibilidade</a>
        <a href="{{ route('prof.turmas.index') }}" class="hidden sm:inline-flex text-sm px-3 py-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">Minhas turmas</a>
        <form method="POST" action="{{ route('prof.basic.logout') }}" class="hidden sm:inline">
          @csrf
          <button class="text-sm rounded-xl bg-slate-800 px-3 py-1.5 text-white font-medium hover:bg-slate-900">Sair</button>
        </form>
        <button type="button" id="themeToggle" class="text-sm rounded-xl bg-slate-100 px-3 py-1.5 font-medium text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">Tema</button>
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

  <script>
    (function(){
      const btn = document.getElementById('themeToggle');
      if (!btn) return;
      btn.addEventListener('click', () => {
        const isDark = document.documentElement.classList.toggle('dark');
        const theme = isDark ? 'dark' : 'light';
        try { localStorage.setItem('theme', theme); } catch(e) {}
      });
    })();
  </script>
</body>
</html>
