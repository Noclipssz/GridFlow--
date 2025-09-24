<!doctype html>
<html lang="pt-BR" class="h-full antialiased">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Turma: {{ $turma->nome }} — Professor</title>

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

<main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
  <!-- HEADER -->
  <div class="mb-8 flex flex-wrap items-center justify-between gap-y-4 gap-x-6">
    <div>
      <h1 class="text-2xl font-bold tracking-tight">Turma: {{ $turma->nome }}</h1>
      <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
        Visualize o horário completo da turma, com suas aulas e as dos outros professores.
      </p>
    </div>
    <div class="flex gap-2 items-center">
      <a href="{{ route('prof.turmas.index') }}"
         class="rounded-lg h-9 px-3 inline-flex items-center justify-center text-sm font-medium
                bg-white/60 text-slate-700 hover:bg-white
                dark:bg-white/10 dark:text-slate-200 dark:hover:bg-white/20
                ring-1 ring-slate-200/80 dark:ring-white/10
                focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-colors">
        Ver todas as turmas
      </a>
      <a href="{{ route('prof.basic.dashboard') }}"
         class="rounded-lg h-9 px-3 inline-flex items-center justify-center text-sm font-medium
                bg-white/60 text-slate-700 hover:bg-white
                dark:bg-white/10 dark:text-slate-200 dark:hover:bg-white/20
                ring-1 ring-slate-200/80 dark:ring-white/10
                focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-colors">
        Dashboard
      </a>
    </div>
  </div>

  <div class="rounded-xl bg-white/60 dark:bg-white/10 ring-1 ring-slate-200/80 dark:ring-white/10 backdrop-blur-sm p-5">
    <!-- TABS DE PERÍODO -->
    <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
      <div>
        @php
          $p = request('periodo', $periodosAtivos[0] ?? 'manha');
          $labels = ['manha' => 'Manhã', 'tarde' => 'Tarde', 'noite' => 'Noite'];
        @endphp
        @if (count($periodosAtivos) > 1)
        <div class="flex items-center gap-2 rounded-lg bg-slate-100 dark:bg-slate-800 p-1">
          @foreach ($periodosAtivos as $periodoItem)
            <a href="{{ route('prof.turmas.show', [$turma->id, 'periodo' => $periodoItem]) }}"
               class="text-sm font-semibold px-3 py-1 rounded-md transition-colors {{ $p === $periodoItem ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600 hover:text-slate-800 dark:text-slate-300 dark:hover:text-slate-100' }}">
              {{ $labels[$periodoItem] }}
            </a>
          @endforeach
        </div>
        @endif
      </div>
      <div class="flex flex-wrap gap-x-4 gap-y-2 text-xs text-slate-600 dark:text-slate-400">
        <div class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-indigo-400 dark:bg-indigo-500"></span>Sua aula</div>
        <div class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>Outro professor</div>
      </div>
    </div>

    <!-- TABELA DE HORÁRIOS -->
    <div class="overflow-x-auto -mx-2">
      <table class="w-full border-separate border-spacing-1.5">
        <thead>
          <tr>
            <th class="w-20"></th>
            @foreach ($days as $d)
              <th class="text-center text-sm font-semibold text-slate-800 dark:text-slate-200 pb-2">{{ $d }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @for ($a = 0; $a < 5; $a++)
            <tr>
              <td class="whitespace-nowrap pr-2 text-sm font-semibold text-slate-600 dark:text-slate-400 text-right">{{ $a + 1 }}ª aula</td>
              @for ($d = 0; $d < 5; $d++)
                @php $cell = $grid[$a][$d] ?? null; @endphp
                <td>
                  <div class="w-full rounded-lg border text-xs h-20 p-2 transition-all duration-150 flex flex-col justify-center text-center
                    @if(is_array($cell))
                      @php
                        $pid = (int) ($cell['professor_id'] ?? 0);
                        $isMe = $pid === $prof->id;
                      @endphp
                      {{ $isMe 
                        ? 'bg-indigo-50 text-indigo-800 border-indigo-200 dark:bg-indigo-500/10 dark:text-indigo-200 dark:border-indigo-500/20' 
                        : 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-800/70 dark:text-slate-300 dark:border-slate-700/80' }}
                    @else
                      'bg-slate-50/50 border-slate-200/60 dark:bg-slate-800/40 dark:border-slate-700/50'
                    @endif
                  ">
                    @if (is_array($cell))
                      @php
                        $pid = (int) ($cell['professor_id'] ?? 0);
                        $mid = (int) ($cell['materia_id'] ?? 0);
                        $p = $profMap[$pid] ?? null;
                        $m = $matMap[$mid] ?? null;
                      @endphp
                      <span class="font-bold text-sm leading-tight">{{ $m?->nome ?? '-' }}</span>
                      <span class="mt-1 text-xs leading-tight truncate">{{ $p?->nome ?? '-' }}</span>
                    @else
                      <span class="text-slate-400 dark:text-slate-600">Vago</span>
                    @endif
                  </div>
                </td>
              @endfor
            </tr>
          @endfor
        </tbody>
      </table>
    </div>
  </div>

  <footer class="mt-12 text-center text-xs text-slate-500">
    © {{ date('Y') }} — GridFlow. Todos os direitos reservados.
  </footer>
</main>
</body>
</html>