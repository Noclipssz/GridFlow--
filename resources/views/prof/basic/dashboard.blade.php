<!doctype html>
<html lang="pt-BR" class="h-full antialiased" x-data="{ dark: true }" x-bind:class="dark ? 'dark' : ''">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard — Professor</title>

  <!-- Inter + Tailwind + Alpine -->
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    :root { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif; }
    /* scrollbar sutil no dark */
    ::-webkit-scrollbar{height:10px;width:10px}::-webkit-scrollbar-thumb{background:#0f172a20;border-radius:6px}
    .dark ::-webkit-scrollbar-thumb{background:#94a3b81a}
  </style>
</head>
<body class="h-full bg-slate-100 text-slate-900 dark:bg-[#0b0f19] dark:text-slate-100 selection:bg-indigo-200/60 dark:selection:bg-indigo-400/30">

@php
  // ---------- Normalização & métricas ----------
  $toArr = fn($v) => is_array($v) ? $v : (json_decode((string)($v ?? '[]'), true) ?? []);
  $manha = $toArr($prof->horario_manha);
  $tarde = $toArr($prof->horario_tarde);
  $noite = $toArr($prof->horario_noite);

  $dias      = ['seg','ter','qua','qui','sex','sáb','dom'];
  $periodos  = ['Manhã' => $manha, 'Tarde' => $tarde, 'Noite' => $noite];

  $countBy = function(array $matriz, int $what) {
    $c = 0;
    foreach ($matriz as $dia) if (is_array($dia)) foreach ($dia as $v) if ((int)$v === $what) $c++;
    return $c;
  };

  // Hoje
  $wd = (int) now()->dayOfWeekIso; // 1..7
  $idxHoje = max(0, $wd - 1);
  $dispHoje = 0; $aulasHoje = 0;
  foreach ($periodos as $arr) {
    $diaArr = (isset($arr[$idxHoje]) && is_array($arr[$idxHoje])) ? $arr[$idxHoje] : [];
    foreach ($diaArr as $v) {
      $v = (int)$v;
      if ($v === 1) $dispHoje++;
      if ($v === 2) $aulasHoje++;
    }
  }

  $dispSemana  = $countBy($manha,1) + $countBy($tarde,1) + $countBy($noite,1);
  $aulasSemana = $countBy($manha,2) + $countBy($tarde,2) + $countBy($noite,2);

  // Turmas ativas — sempre seguro
  $turmasAtivas = 0;
  if (isset($turmas)) {
    $turmasAtivas = is_countable($turmas) ? count($turmas) : collect($turmas)->count();
  } elseif (method_exists($prof, 'turmas')) {
    if (method_exists($prof, 'relationLoaded') && $prof->relationLoaded('turmas') && $prof->turmas !== null) {
      $turmasAtivas = $prof->turmas->count();
    } else {
      $turmasAtivas = (int) optional($prof->turmas())->count();
    }
  }

  // Próximas aulas (controller pode passar $proximasAulas)
  $proximas = isset($proximasAulas) && is_iterable($proximasAulas) ? $proximasAulas : [];
@endphp

  <!-- Topbar minimal -->
  <header class="sticky top-0 z-30 backdrop-blur bg-white/70 dark:bg-[#0b0f19]/70 border-b border-slate-200/70 dark:border-slate-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
      <a href="{{ route('prof.basic.dashboard') }}" class="flex items-center gap-2">
        <span class="inline-grid place-items-center h-8 w-8 rounded-xl bg-indigo-600 text-white text-sm font-bold">Gf</span>
        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">GridFlow — Professor</span>
      </a>
      <nav class="flex items-center gap-2">
        <a href="{{ route('prof.basic.schedule') }}" class="rounded-xl h-9 px-3 text-xs font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">Disponibilidade</a>
        <a href="{{ route('prof.turmas.index') }}" class="rounded-xl h-9 px-3 text-xs font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">Minhas turmas</a>
        <form method="POST" action="{{ route('prof.basic.logout') }}">
          @csrf
          <button class="rounded-xl h-9 px-3 text-xs font-semibold bg-rose-600 text-white hover:bg-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-500/40">Sair</button>
        </form>
        <button type="button" class="rounded-xl h-9 w-9 grid place-items-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30" @click="dark = !dark" :aria-label="dark ? 'Tema claro' : 'Tema escuro'">
          <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 18a6 6 0 1 1 0-12 6 6 0 0 1 0 12Zm0-16h0M12 22h0M2 12h0M22 12h0M4.22 4.22h0M19.78 4.22h0M4.22 19.78h0M19.78 19.78h0"/></svg>
          <svg x-show="dark" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="currentColor"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 1 0 9.8 9.8Z"/></svg>
        </button>
      </nav>
    </div>
  </header>

  <main class="relative z-10">
    <!-- fundo decorativo sutil -->
    <div aria-hidden="true" class="pointer-events-none fixed inset-0 overflow-hidden -z-10">
      <div class="absolute -top-24 -right-24 h-80 w-80 rounded-full blur-3xl opacity-30 bg-indigo-400/40 dark:bg-indigo-500/25"></div>
      <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-80 w-[42rem] bg-gradient-to-t from-indigo-300/20 to-transparent dark:from-indigo-500/10 blur-2xl"></div>
    </div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
      <!-- Saudações -->
      <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
          <h1 class="text-3xl font-semibold tracking-tight">
            Olá, <span class="capitalize text-indigo-600 dark:text-indigo-400">{{ $prof->nome }}</span>
          </h1>
          <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            Hoje é {{ now()->translatedFormat('l, d \\d\\e F') }} — bom trabalho! ✨
          </p>
        </div>
        <div class="flex items-center gap-2">
          <a href="{{ route('prof.basic.schedule') }}" class="rounded-xl h-10 px-4 inline-flex items-center justify-center text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/40">Editar disponibilidade</a>
          <a href="{{ route('prof.turmas.index') }}" class="rounded-xl h-10 px-4 inline-flex items-center justify-center text-sm font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">Minhas turmas</a>
        </div>
      </div>

      <!-- KPIs -->
      <section class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white/80 dark:bg-slate-900/60 ring-1 ring-slate-200/60 dark:ring-white/10 backdrop-blur p-5 shadow-sm">
          <div class="text-[11px] uppercase tracking-wide text-slate-500">Disponíveis hoje</div>
          <div class="mt-2 text-2xl font-semibold">{{ $dispHoje }}</div>
          <div class="mt-1 text-xs text-slate-500">Slots livres por período</div>
        </div>
        <div class="rounded-2xl bg-white/80 dark:bg-slate-900/60 ring-1 ring-slate-200/60 dark:ring-white/10 backdrop-blur p-5 shadow-sm">
          <div class="text-[11px] uppercase tracking-wide text-slate-500">Aulas hoje</div>
          <div class="mt-2 text-2xl font-semibold">{{ $aulasHoje }}</div>
          <div class="mt-1 text-xs text-slate-500">Inclui todas as turmas</div>
        </div>
        <div class="rounded-2xl bg-white/80 dark:bg-slate-900/60 ring-1 ring-slate-200/60 dark:ring-white/10 backdrop-blur p-5 shadow-sm">
          <div class="text-[11px] uppercase tracking-wide text-slate-500">Aulas/semana (planejado)</div>
          <div class="mt-2 text-2xl font-semibold">{{ $aulasSemana }}</div>
          <div class="mt-1 text-xs text-slate-500">A partir da matéria</div>
        </div>
        <div class="rounded-2xl bg-white/80 dark:bg-slate-900/60 ring-1 ring-slate-200/60 dark:ring-white/10 backdrop-blur p-5 shadow-sm">
          <div class="text-[11px] uppercase tracking-wide text-slate-500">Turmas ativas</div>
          <div class="mt-2 text-2xl font-semibold">{{ $turmasAtivas }}</div>
          <div class="mt-1 text-xs text-slate-500">No semestre atual</div>
        </div>
      </section>

      <!-- GRID PRINCIPAL -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Próximas aulas -->
        <section class="lg:col-span-2 rounded-2xl bg-white/80 dark:bg-slate-900/60 ring-1 ring-slate-200/60 dark:ring-white/10 backdrop-blur p-6 shadow-sm">
          <header class="mb-4 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200">Próximas aulas</h2>
            <a href="{{ route('prof.turmas.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-500">ver todas</a>
          </header>

          @if(empty($proximas))
            <div class="rounded-xl border border-dashed border-slate-300 dark:border-slate-700 p-8 text-center">
              <p class="text-sm text-slate-500">Sem aulas agendadas. Que tal
                <a class="text-indigo-600 hover:underline" href="{{ route('prof.turmas.index') }}">criar/atribuir uma turma</a>?
              </p>
            </div>
          @else
            <ul class="divide-y divide-slate-200/70 dark:divide-slate-800/80">
              @foreach($proximas as $aula)
                <li class="py-4 flex items-center gap-4">
                  <div class="w-16 shrink-0 text-center">
                    <div class="text-xs uppercase text-slate-500">{{ \Carbon\Carbon::parse($aula['data'])->translatedFormat('D') }}</div>
                    <div class="text-base font-semibold">{{ \Carbon\Carbon::parse($aula['data'])->format('d') }}</div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium truncate">{{ $aula['turma'] ?? 'Turma' }}</div>
                    <div class="text-xs text-slate-500">
                      {{ \Carbon\Carbon::parse($aula['data'])->format('H:i') }} •
                      {{ $aula['duracao'] ?? 50 }}min
                      @if(!empty($aula['sala'])) • Sala {{ $aula['sala'] }} @endif
                    </div>
                  </div>
                  <a href="{{ route('prof.turmas.index') }}" class="rounded-lg px-3 py-1.5 text-xs font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">detalhes</a>
                </li>
              @endforeach
            </ul>
          @endif
        </section>

        <!-- Heatmap + Ações -->
        <aside class="space-y-6">
          <!-- Heatmap -->
          <section class="rounded-2xl bg-white/80 dark:bg-slate-900/60 ring-1 ring-slate-200/60 dark:ring-white/10 backdrop-blur p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-4">Disponibilidade (semana)</h2>
            <div class="grid grid-cols-8 gap-2 text-[11px] text-slate-500">
              <div></div>
              @foreach($dias as $d) <div class="text-center uppercase">{{ $d }}</div> @endforeach

              @foreach($periodos as $label => $matriz)
                <div class="text-right pr-2 font-medium text-slate-600 dark:text-slate-300">{{ $label }}</div>
                @for($i=0;$i<7;$i++)
                  @php
                    $hasDisp = isset($matriz[$i]) && in_array(1, array_map('intval',$matriz[$i] ?? []), true);
                    $hasAula = isset($matriz[$i]) && in_array(2, array_map('intval',$matriz[$i] ?? []), true);
                  @endphp
                  <div class="h-7 rounded-md ring-1 ring-slate-200/70 dark:ring-slate-800/70
                              @if($hasAula) bg-indigo-600/30 @elseif($hasDisp) bg-emerald-500/25 @else bg-slate-100 dark:bg-slate-800 @endif">
                    <span class="sr-only">{{ $label }} {{ $dias[$i] }} — {{ $hasAula ? 'Aula' : ($hasDisp ? 'Disponível' : 'Vazio') }}</span>
                  </div>
                @endfor
              @endforeach
            </div>
            <div class="mt-3 flex items-center gap-3 text-[11px] text-slate-500">
              <span class="inline-block h-3 w-3 rounded bg-emerald-500/40 ring-1 ring-emerald-700/20"></span> disponível
              <span class="inline-block h-3 w-3 rounded bg-indigo-600/40 ring-1 ring-indigo-700/20"></span> aula
              <span class="inline-block h-3 w-3 rounded bg-slate-200 dark:bg-slate-800 ring-1 ring-slate-300/40 dark:ring-slate-700/40"></span> vazio
            </div>
          </section>

          <!-- Ações rápidas -->
          <section class="rounded-2xl bg-white/80 dark:bg-slate-900/60 ring-1 ring-slate-200/60 dark:ring-white/10 backdrop-blur p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-4">Ações rápidas</h2>
            <div class="grid grid-cols-1 gap-3">
              <a href="{{ route('prof.basic.schedule') }}" class="rounded-xl w-full text-left ring-1 ring-slate-200 dark:ring-slate-800 bg-slate-50 px-4 py-3 text-sm font-medium hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/40">Ver / editar disponibilidade</a>
              <a href="{{ route('prof.turmas.index') }}" class="rounded-xl w-full text-left ring-1 ring-slate-200 dark:ring-slate-800 bg-slate-50 px-4 py-3 text-sm font-medium hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/40">Minhas turmas</a>
              <form method="POST" action="{{ route('prof.basic.logout') }}" class="pt-1">
                @csrf
                <button type="submit" class="w-full rounded-xl h-11 bg-rose-600 hover:bg-rose-500 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-rose-500/40">Sair</button>
              </form>
            </div>
          </section>
        </aside>
      </div>

      <footer class="mt-10 text-center text-xs text-slate-500">
        © {{ date('Y') }} — Sistema Escolar
      </footer>
    </div>
  </main>
</body>
</html>
