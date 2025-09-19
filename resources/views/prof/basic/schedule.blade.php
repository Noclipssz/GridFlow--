<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Minha Disponibilidade</title>

  <!-- Tailwind (CDN) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- AlpineJS -->
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    [x-cloak]{display:none!important}

    /* Estados: 0=indisponível, 1=disponível, 2=aula */
    .state-off  { background:#f8fafc; color:#64748b; border-color:#e2e8f0; }
    .state-on   { background:#ecfdf5; color:#065f46; border-color:#a7f3d0; }
    .state-aula { background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe; }

    .pill-off  { background:#94a3b8; color:#fff; }
    .pill-on   { background:#16a34a; color:#fff; }
    .pill-aula { background:#2563eb; color:#fff; }
  </style>
</head>

<body class="bg-slate-50 text-slate-800">
  <div class="max-w-5xl mx-auto p-6">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
      <h1 class="text-2xl font-semibold">
        Olá, <span class="capitalize">{{ $prof->nome }}</span> — selecione suas <span class="text-indigo-600">disponibilidades</span>
      </h1>

      <div class="flex items-center gap-2">
        <form method="POST" action="{{ route('prof.basic.logout') }}">
          @csrf
          <button type="submit"
                  class="inline-flex items-center rounded-xl bg-slate-800 px-4 py-2.5 text-white text-sm font-medium shadow-sm hover:bg-slate-900">
            Sair
          </button>
        </form>

        <a href="{{ route('prof.basic.dashboard') }}"
           class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-white text-sm font-medium shadow-sm hover:bg-indigo-700">
          Dashboard
        </a>
      </div>
    </div>

    @if (session('ok'))
      <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
        {{ session('ok') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
        @foreach ($errors->all() as $e)
          <div>{{ $e }}</div>
        @endforeach
      </div>
    @endif

    <div
      x-data="schedule({ rows: {{ $rows }}, cols: {{ $cols }}, initial: @json($grid) })"
      x-cloak
      class="bg-white rounded-2xl ring-1 ring-slate-200 shadow-sm p-5"
    >
      <form method="POST" action="{{ route('prof.basic.schedule.save', ['periodo' => $periodo ?? 'manha']) }}" @submit.prevent="submit" x-ref="form" class="space-y-5">
        @csrf
        <input type="hidden" name="grid" x-model="payload">
        <input type="hidden" name="periodo" value="{{ $periodo ?? 'manha' }}">

        <div class="flex items-center gap-3 mb-3">
          <label class="text-sm font-semibold text-slate-700">Período:</label>
          @php $p = $periodo ?? 'manha'; @endphp
          <a href="{{ route('prof.basic.schedule', ['periodo' => 'manha']) }}" class="text-sm px-3 py-1 rounded-lg {{ $p==='manha' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700' }}">Manhã</a>
          <a href="{{ route('prof.basic.schedule', ['periodo' => 'tarde']) }}" class="text-sm px-3 py-1 rounded-lg {{ $p==='tarde' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700' }}">Tarde</a>
          <a href="{{ route('prof.basic.schedule', ['periodo' => 'noite']) }}" class="text-sm px-3 py-1 rounded-lg {{ $p==='noite' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700' }}">Noite</a>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full border-separate border-spacing-2 max-w-[980px] mx-auto">
            <thead>
              <tr>
                <th class="w-28"></th>
                @foreach ($days as $d)
                  <th class="text-center text-sm font-bold text-slate-700 py-2">{{ $d }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @for ($r = 0; $r < $rows; $r++)
                <tr>
                  <td class="whitespace-nowrap pr-2 text-sm font-semibold text-slate-700">{{ $r + 1 }}ª Aula</td>

                  @for ($c = 0; $c < $cols; $c++)
                    <td class="align-top">
                      <div
                        class="rounded-xl border px-4 py-3 h-24 flex items-center justify-center cursor-pointer select-none transition hover:ring-1 hover:ring-slate-300"
                        :class="grid[{{ $r }}][{{ $c }}] === 2 ? 'state-aula' : (grid[{{ $r }}][{{ $c }}] === 1 ? 'state-on' : 'state-off')"
                        @click="toggle({{ $r }}, {{ $c }})"
                      >
                        <div class="text-center">
                          <div class="font-medium mb-1">
                            <template x-if="grid[{{ $r }}][{{ $c }}] === 2"><span>Aula</span></template>
                            <template x-if="grid[{{ $r }}][{{ $c }}] === 1"><span>Aula disponível</span></template>
                            <template x-if="grid[{{ $r }}][{{ $c }}] === 0"><span>Aula indisponível</span></template>
                          </div>

                          <span class="inline-flex items-center gap-2 rounded-full px-2.5 py-0.5 text-[11px] font-medium"
                                :class="grid[{{ $r }}][{{ $c }}] === 2 ? 'pill-aula' : (grid[{{ $r }}][{{ $c }}] === 1 ? 'pill-on' : 'pill-off')">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-white/90"></span>
                            <template x-if="grid[{{ $r }}][{{ $c }}] === 2"><span>Aula</span></template>
                            <template x-if="grid[{{ $r }}][{{ $c }}] === 1"><span>Disponível</span></template>
                            <template x-if="grid[{{ $r }}][{{ $c }}] === 0"><span>Indisponível</span></template>
                          </span>
                        </div>
                      </div>
                    </td>
                  @endfor
                </tr>
              @endfor
            </tbody>
          </table>
        </div>

        <div class="flex flex-wrap justify-end gap-3">
          <button type="button"
                  class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-white text-sm font-medium shadow-sm hover:bg-indigo-700"
                  @click="setAll(1)">
            Marcar tudo
          </button>

          <button type="button"
                  class="inline-flex items-center rounded-xl bg-slate-800 px-4 py-2.5 text-white text-sm font-medium shadow-sm hover:bg-slate-900"
                  @click="setAll(0)">
            Limpar tudo
          </button>

          <button type="submit"
                  class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2.5 text-white text-sm font-medium shadow-sm hover:bg-emerald-700">
            Salvar
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- JS ORIGINAL (inalterado) -->
  <script>
    function schedule({ rows, cols, initial }) {
      // 0=indisp, 1=disp, 2=aula (travado)
      const grid = Array.from({ length: rows }, (_, i) =>
        Array.from({ length: cols }, (_, j) => {
          const v = Number(initial?.[i]?.[j] ?? 0);
          return v === 2 ? 2 : (v === 1 ? 1 : 0);
        })
      );

      return {
        rows,
        cols,
        grid,
        payload: JSON.stringify(grid),
        toggle(i, j) {
          if (this.grid[i][j] === 2) return; // não altera aula alocada
          this.grid[i][j] = this.grid[i][j] ? 0 : 1;
        },
        setAll(v) {
          for (let i = 0; i < this.rows; i++)
            for (let j = 0; j < this.cols; j++)
              if (this.grid[i][j] !== 2) this.grid[i][j] = v ? 1 : 0;
        },
        submit(e) {
          this.$refs.form.querySelector('input[name="grid"]').value = JSON.stringify(this.grid);
          this.$refs.form.submit();
        }
      }
    }
  </script>
</body>
</html>
