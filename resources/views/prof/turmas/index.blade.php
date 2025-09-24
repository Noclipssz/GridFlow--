<!doctype html>
<html lang="pt-BR" class="h-full antialiased">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Minhas Turmas — Professor</title>

  <!-- Inter + Tailwind -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    :root { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif; }
    /* scrollbar sutil */
    ::-webkit-scrollbar{height:10px;width:10px}::-webkit-scrollbar-thumb{background:#0f172a20;border-radius:6px}
    @media (prefers-color-scheme: dark) {.dark ::-webkit-scrollbar-thumb{background:#94a3b81a}}
  </style>
</head>

<body class="h-full bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 selection:bg-indigo-200/60 dark:selection:bg-indigo-400/30">

<!-- fundo decorativo sutil -->
<div aria-hidden="true" class="pointer-events-none fixed inset-0 overflow-hidden -z-10">
  <div class="absolute -top-24 -right-24 h-80 w-80 rounded-full blur-3xl opacity-30 bg-indigo-400/40 dark:bg-indigo-500/25"></div>
  <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-80 w-[42rem] bg-gradient-to-t from-indigo-300/20 to-transparent dark:from-indigo-500/10 blur-2xl"></div>
</div>

<main class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-12">
  <!-- HEADER -->
  <div class="mb-8 flex flex-wrap items-center justify-between gap-y-4 gap-x-6">
    <div>
      <h1 class="text-2xl font-bold tracking-tight">Minhas Turmas</h1>
      <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
        Veja as turmas em que você tem aulas atribuídas.
      </p>
    </div>
    <div class="flex gap-2 items-center">
      <a href="{{ route('prof.basic.dashboard') }}"
         class="rounded-lg h-9 px-3 inline-flex items-center justify-center text-sm font-medium
                bg-white/60 text-slate-700 hover:bg-white
                dark:bg-white/10 dark:text-slate-200 dark:hover:bg-white/20
                ring-1 ring-slate-200/80 dark:ring-white/10
                focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-colors">
        Voltar ao Dashboard
      </a>
      <form method="POST" action="{{ route('prof.basic.logout') }}" class="ml-2">
        @csrf
        <button type="submit" title="Sair"
                class="rounded-lg h-9 w-9 inline-flex items-center justify-center text-sm font-medium
                       text-slate-600 hover:bg-white/80 hover:text-slate-800
                       dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-slate-200
                       focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M16.75 2.5a.75.75 0 0 0-1.5 0v10a.75.75 0 0 0 1.5 0v-10Zm3.25 5a.75.75 0 0 0 0 1.5h-3.5a.75.75 0 0 0 0-1.5h3.5ZM10.25 2.5a.75.75 0 0 0-1.5 0v10a.75.75 0 0 0 1.5 0v-10ZM6.5 7.5a.75.75 0 0 0 0 1.5h3.5a.75.75 0 0 0 0-1.5h-3.5Z M4 2.75A2.75 2.75 0 0 0 1.25 5.5v12A2.75 2.75 0 0 0 4 20.25h16a2.75 2.75 0 0 0 2.75-2.75v-12A2.75 2.75 0 0 0 20 2.75H4Zm0 1.5h16a1.25 1.25 0 0 1 1.25 1.25v12a1.25 1.25 0 0 1-1.25 1.25H4a1.25 1.25 0 0 1-1.25-1.25v-12A1.25 1.25 0 0 1 4 4.25Z"/></svg>
        </button>
      </form>
    </div>
  </div>

  @if ($errors->any())
    <div class="mb-4 rounded-lg bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-700 dark:text-rose-300 px-4 py-3 text-sm">
      @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
    </div>
  @endif

  @if (empty($mine))
    <div class="rounded-xl bg-white/60 dark:bg-white/10 ring-1 ring-slate-200/80 dark:ring-white/10 backdrop-blur-sm p-12 text-center">
      <div class="mx-auto w-fit bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 rounded-full p-3">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8"><path d="M4.25 3.75a.75.75 0 0 0-1.5 0v16.5a.75.75 0 0 0 1.5 0V3.75ZM21.25 3.75a.75.75 0 0 0-1.5 0v16.5a.75.75 0 0 0 1.5 0V3.75ZM16.75 3.75a.75.75 0 0 0-1.5 0v16.5a.75.75 0 0 0 1.5 0V3.75ZM8.75 3.75a.75.75 0 0 0-1.5 0v16.5a.75.75 0 0 0 1.5 0V3.75ZM12.75 3.75a.75.75 0 0 0-1.5 0v16.5a.75.75 0 0 0 1.5 0V3.75Z"/></svg>
      </div>
      <h3 class="mt-4 text-lg font-semibold text-slate-800 dark:text-slate-200">Nenhuma turma encontrada</h3>
      <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Você ainda não possui aulas atribuídas em nenhuma turma.</p>
    </div>
  @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      @foreach ($mine as $item)
        @php $t = $item['turma']; $aulas = $item['aulas']; @endphp
        <a href="{{ route('prof.turmas.show', $t->id) }}" 
           class="group rounded-xl bg-white/60 dark:bg-white/10 ring-1 ring-slate-200/80 dark:ring-white/10 backdrop-blur-sm p-6
                  hover:bg-white/80 dark:hover:bg-white/20 hover:ring-indigo-400 dark:hover:ring-indigo-500
                  focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-50 dark:focus:ring-offset-slate-950 focus:ring-indigo-500
                  transition">
          <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
              <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Turma #{{ $t->id }}</p>
              <h3 class="mt-1 text-lg font-semibold text-slate-800 dark:text-slate-200">{{ $t->nome }}</h3>
            </div>
            <div class="text-xs rounded-full px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-medium">
              {{ $t->periodo }}
            </div>
          </div>
          <div class="mt-4 border-t border-slate-200/80 dark:border-white/10 pt-4 text-sm text-slate-600 dark:text-slate-400">
            Você tem <span class="font-bold text-slate-800 dark:text-slate-200">{{ $aulas }}</span> {{ $aulas == 1 ? 'aula' : 'aulas' }} nesta turma.
          </div>
        </a>
      @endforeach
    </div>
  @endif

  <footer class="mt-12 text-center text-xs text-slate-500">
    © {{ date('Y') }} — GridFlow. Todos os direitos reservados.
  </footer>
</main>
</body>
</html>