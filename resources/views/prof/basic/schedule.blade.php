<!doctype html>
<html lang="pt-BR" class="h-full antialiased">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Editar Disponibilidade — Professor</title>

  <!-- Inter + Tailwind -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="//unpkg.com/alpinejs" defer></script>

  <style>
    :root { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif; }
    [x-cloak] { display: none !important; }
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

<main class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-12">
  <!-- HEADER -->
  <div class="mb-8 flex flex-wrap items-center justify-between gap-y-4 gap-x-6">
    <div>
      <h1 class="text-2xl font-bold tracking-tight">Editar Disponibilidade</h1>
      <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
        Selecione os horários em que você está disponível para dar aulas.
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

  @if (session('ok'))
    <div class="mb-4 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-300 px-4 py-3 text-sm">
      {{ session('ok') }}
    </div>
  @endif
  @if ($errors->any())
    <div class="mb-4 rounded-lg bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-700 dark:text-rose-300 px-4 py-3 text-sm">
      @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
    </div>
  @endif

  <div x-data="schedule({ rows: {{ $rows }}, cols: {{ $cols }}, initial: @json($grid) })" x-cloak>
    <div class="rounded-xl bg-white/60 dark:bg-white/10 ring-1 ring-slate-200/80 dark:ring-white/10 backdrop-blur-sm p-5">
      <form method="POST" action="{{ route('prof.basic.schedule.save', ['periodo' => $periodo ?? 'manha']) }}" @submit.prevent="submit" x-ref="form" class="space-y-6">
        @csrf
        <input type="hidden" name="grid" x-model="payload">
        <input type="hidden" name="periodo" value="{{ $periodo ?? 'manha' }}">

        <!-- TABS DE PERÍODO -->
        <div class="flex flex-wrap items-center justify-between gap-4">
          <div>
            @php $p = $periodo ?? 'manha'; @endphp
            <div class="flex items-center gap-2 rounded-lg bg-slate-100 dark:bg-slate-800 p-1">
              <a href="{{ route('prof.basic.schedule', ['periodo' => 'manha']) }}" class="text-sm font-semibold px-3 py-1 rounded-md transition-colors {{ $p==='manha' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600 hover:text-slate-800 dark:text-slate-300 dark:hover:text-slate-100' }}">Manhã</a>
              <a href="{{ route('prof.basic.schedule', ['periodo' => 'tarde']) }}" class="text-sm font-semibold px-3 py-1 rounded-md transition-colors {{ $p==='tarde' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600 hover:text-slate-800 dark:text-slate-300 dark:hover:text-slate-100' }}">Tarde</a>
              <a href="{{ route('prof.basic.schedule', ['periodo' => 'noite']) }}" class="text-sm font-semibold px-3 py-1 rounded-md transition-colors {{ $p==='noite' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600 hover:text-slate-800 dark:text-slate-300 dark:hover:text-slate-100' }}">Noite</a>
            </div>
          </div>
          <!-- LEGENDA -->
          <div class="flex flex-wrap gap-x-4 gap-y-2 text-xs text-slate-600 dark:text-slate-400">
            <div class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-slate-200 dark:bg-slate-700"></span>Indisponível</div>
            <div class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-emerald-400 dark:bg-emerald-500"></span>Disponível</div>
            <div class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-sky-400 dark:bg-sky-500"></span>Aula (fixo)</div>
          </div>
        </div>

        <!-- TABELA DE HORÁRIOS -->
        <div class="overflow-x-auto -mx-2">
          <table class="w-full border-separate border-spacing-1.5">
            <thead>
              <tr>
                <th class="w-24"></th>
                @foreach ($days as $d)
                  <th class="text-center text-sm font-semibold text-slate-800 dark:text-slate-200 pb-2">{{ $d }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @for ($r = 0; $r < $rows; $r++)
                <tr>
                  <td class="whitespace-nowrap pr-2 text-sm font-semibold text-slate-600 dark:text-slate-400 text-right">{{ $r + 1 }}ª aula</td>
                  @for ($c = 0; $c < $cols; $c++)
                    <td>
                      <button type="button"
                        class="w-full rounded-lg border text-xs font-semibold h-16 flex items-center justify-center text-center p-2 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-slate-900 focus:ring-indigo-500"
                        :class="{
                          'bg-slate-100 text-slate-500 border-slate-200 hover:bg-slate-200 hover:border-slate-300 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700 dark:hover:bg-slate-700': grid[{{ $r }}][{{ $c }}] === 0,
                          'bg-emerald-100 text-emerald-800 border-emerald-200 hover:bg-emerald-200 hover:border-emerald-300 dark:bg-emerald-500/20 dark:text-emerald-200 dark:border-emerald-500/30 dark:hover:bg-emerald-500/30': grid[{{ $r }}][{{ $c }}] === 1,
                          'bg-sky-100 text-sky-800 border-sky-200 cursor-not-allowed dark:bg-sky-500/20 dark:text-sky-200 dark:border-sky-500/30': grid[{{ $r }}][{{ $c }}] === 2
                        }"
                        @click="toggle({{ $r }}, {{ $c }})">
                        <span x-show="grid[{{ $r }}][{{ $c }}] === 0">Indisponível</span>
                        <span x-show="grid[{{ $r }}][{{ $c }}] === 1">Disponível</span>
                        <span x-show="grid[{{ $r }}][{{ $c }}] === 2">Aula</span>
                      </button>
                    </td>
                  @endfor
                </tr>
              @endfor
            </tbody>
          </table>
        </div>

        <!-- AÇÕES -->
        <div class="flex flex-wrap justify-end gap-3 pt-4 border-t border-slate-200/80 dark:border-white/10">
          <button type="button" @click="setAll(1)"
            class="rounded-lg h-9 px-3 inline-flex items-center justify-center text-sm font-medium bg-white/60 text-slate-700 hover:bg-white dark:bg-white/10 dark:text-slate-200 dark:hover:bg-white/20 ring-1 ring-slate-200/80 dark:ring-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-colors">
            Marcar todos como disponíveis
          </button>
          <button type="button" @click="setAll(0)"
            class="rounded-lg h-9 px-3 inline-flex items-center justify-center text-sm font-medium bg-white/60 text-slate-700 hover:bg-white dark:bg-white/10 dark:text-slate-200 dark:hover:bg-white/20 ring-1 ring-slate-200/80 dark:ring-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-colors">
            Limpar seleção
          </button>
          <button type="submit"
            class="rounded-lg h-9 px-5 inline-flex items-center justify-center text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-50 dark:focus:ring-offset-slate-950 focus:ring-indigo-500 transition-colors">
            Salvar alterações
          </button>
        </div>
      </form>
    </div>
  </div>

  <footer class="mt-12 text-center text-xs text-slate-500">
    © {{ date('Y') }} — GridFlow. Todos os direitos reservados.
  </footer>
</main>

<script>
  function schedule({ rows, cols, initial }) {
    return {
      rows, 
      cols, 
      grid: Array.from({ length: rows }, (_, i) =>
        Array.from({ length: cols }, (_, j) => {
          const v = Number(initial?.[i]?.[j] ?? 0);
          return v === 2 ? 2 : (v === 1 ? 1 : 0);
        })
      ),
      get payload() {
        return JSON.stringify(this.grid);
      },
      toggle(i, j) {
        if (this.grid[i][j] === 2) return;
        this.grid[i][j] = this.grid[i][j] ? 0 : 1;
      },
      setAll(v) {
        for (let i = 0; i < this.rows; i++) {
          for (let j = 0; j < this.cols; j++) {
            if (this.grid[i][j] !== 2) this.grid[i][j] = v ? 1 : 0;
          }
        }
      },
      submit() {
        this.$refs.form.submit();
      }
    }
  }
</script>
</body>
</html>