<!doctype html>
<html lang="pt-BR" class="h-full antialiased">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Dashboard — Professor</title>

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

@php
  /** =======================
   *  REGRAS DE NEGÓCIO (inalteradas)
   *  ======================= */
  // normaliza arrays vindos do banco (podem ser json ou array)
  $toArr = fn($v) => is_array($v) ? $v : (json_decode((string)($v ?? '[]'), true) ?? []);

  $manha = $toArr($prof->horario_manha);
  $tarde = $toArr($prof->horario_tarde);
  $noite = $toArr($prof->horario_noite);

  $labels = ['manha' => 'Manhã', 'tarde' => 'Tarde', 'noite' => 'Noite'];
  $periods = ['manha' => $manha, 'tarde' => $tarde, 'noite' => $noite];

  // computa disponíveis (1) e aulas (2) por período
  $stats = [];
  foreach ($periods as $key => $arr) {
    $disp = 0; $aulas = 0;
    foreach ($arr as $dia) if (is_array($dia)) foreach ($dia as $v) {
      $v = (int) $v;
      if ($v === 1) $disp++;
      elseif ($v === 2) $aulas++;
    }
    $stats[$key] = [$disp, $aulas];
  }

  // CPF e Aulas/semana (da matéria)
  $cpfFmt = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $prof->cpf ?? '');
  $aulasSemanaMateria = optional($prof->materia)->quant_aulas ?? '—';
@endphp

<main class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-12">
  <!-- HEADER -->
  <div class="mb-8 flex flex-wrap items-center justify-between gap-y-4 gap-x-6">
    <div>
      <h1 class="text-2xl font-bold tracking-tight">
        Bem-vindo, <span class="capitalize">{{ $prof->nome }}</span>
      </h1>
      <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
        Matéria: <span class="font-medium text-slate-700 dark:text-slate-300">{{ optional($prof->materia)->nome ?? 'Não definida' }}</span>
      </p>
    </div>
    <div class="flex gap-2 items-center">
      <a href="{{ route('prof.turmas.index') }}"
         class="rounded-lg h-9 px-3 inline-flex items-center justify-center text-sm font-medium
                bg-white/60 text-slate-700 hover:bg-white
                dark:bg-white/10 dark:text-slate-200 dark:hover:bg-white/20
                ring-1 ring-slate-200/80 dark:ring-white/10
                focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-colors">
        Minhas turmas
      </a>
      <a href="{{ route('prof.basic.schedule') }}"
         class="rounded-lg h-9 px-4 inline-flex items-center justify-center text-sm font-semibold
                bg-indigo-600 text-white hover:bg-indigo-500
                focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-50 dark:focus:ring-offset-slate-950 focus:ring-indigo-500 transition-colors">
        Editar disponibilidade
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

  <!-- STATS -->
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
    <div class="rounded-xl bg-white/60 dark:bg-white/10 ring-1 ring-slate-200/80 dark:ring-white/10 backdrop-blur-sm p-5">
      <div class="text-sm text-slate-500 dark:text-slate-400">CPF</div>
      <div class="mt-2 text-xl font-bold text-slate-900 dark:text-white">{{ $cpfFmt }}</div>
    </div>
    <div class="rounded-xl bg-white/60 dark:bg-white/10 ring-1 ring-slate-200/80 dark:ring-white/10 backdrop-blur-sm p-5">
      <div class="text-sm text-slate-500 dark:text-slate-400">Aulas/semana (matéria)</div>
      <div class="mt-2 text-xl font-bold text-slate-900 dark:text-white">{{ $aulasSemanaMateria }}</div>
    </div>
    <div class="rounded-xl bg-white/60 dark:bg-white/10 ring-1 ring-slate-200/80 dark:ring-white/10 backdrop-blur-sm p-5">
      <div class="text-sm text-slate-500 dark:text-slate-400">Turmas ativas</div>
      <div class="mt-2 text-xl font-bold text-slate-900 dark:text-white">{{ $turmasAtivas }}</div>
    </div>
  </div>

  <!-- DISPONIBILIDADE -->
  <div class="mt-10">
    <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-200">Resumo de disponibilidade</h2>
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      @foreach ($stats as $k => $v)
        <a href="{{ route('prof.basic.schedule', ['periodo' => $k]) }}"
           class="group rounded-xl bg-white/60 dark:bg-white/10 ring-1 ring-slate-200/80 dark:ring-white/10 backdrop-blur-sm p-5
                  hover:bg-white/80 dark:hover:bg-white/20 hover:ring-indigo-400 dark:hover:ring-indigo-500
                  focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-50 dark:focus:ring-offset-slate-950 focus:ring-indigo-500
                  transition">
          <div class="flex items-center justify-between">
            <span class="text-base font-semibold text-slate-800 dark:text-slate-200">{{ $labels[$k] }}</span>
            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">
              {{ $v[0] + $v[1] }} horários
            </span>
          </div>
          <div class="mt-4 space-y-2">
            <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
              <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
              <span>Disponíveis:</span>
              <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $v[0] }}</span>
            </div>
            <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
              <span class="h-2 w-2 rounded-full bg-sky-500"></span>
              <span>Aulas:</span>
              <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $v[1] }}</span>
            </div>
          </div>
        </a>
      @endforeach
    </div>
  </div>

  <footer class="mt-12 text-center text-xs text-slate-500">
    © {{ date('Y') }} — GridFlow. Todos os direitos reservados.
  </footer>
</main>
</body>
</html>