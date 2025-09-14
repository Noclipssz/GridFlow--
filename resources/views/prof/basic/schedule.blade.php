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

    /* Mantém APENAS as classes dinâmicas usadas pelo seu JS */
    .on  { background:#ecfdf5; border-color:#a7f3d0; } /* emerald-50 / emerald-200 */
    .off { background:#f8fafc; color:#64748b; }       /* slate-50  / slate-500   */

    .pill-on  { background:#16a34a; color:#fff; }     /* emerald-600 */
    .pill-off { background:#94a3b8; color:#fff; }     /* slate-400   */
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
      <form method="POST" action="{{ route('prof.basic.schedule.save') }}" @submit.prevent="submit" x-ref="form" class="space-y-5">
        @csrf
        <input type="hidden" name="grid" x-model="payload">

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
                        :class="grid[{{ $r }}][{{ $c }}] ? 'on' : 'off'"
                        @click="toggle({{ $r }}, {{ $c }})"
                      >
                        <div class="text-center">
                          <div class="font-medium mb-1"
                               :class="grid[{{ $r }}][{{ $c }}] ? 'text-emerald-800' : 'text-slate-500'">
                            <span x-text="grid[{{ $r }}][{{ $c }}] ? 'Aula disponível' : 'Aula indisponível'"></span>
                          </div>

                          <span class="inline-flex items-center gap-2 rounded-full px-2.5 py-0.5 text-[11px] font-medium"
                                :class="grid[{{ $r }}][{{ $c }}] ? 'pill-on' : 'pill-off'">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-white/90"></span>
                            <span x-text="grid[{{ $r }}][{{ $c }}] ? 'Disponível' : 'Indisponível'"></span>
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
      const grid = Array.from({ length: rows }, (_, i) =>
        Array.from({ length: cols }, (_, j) => (initial?.[i]?.[j] ? 1 : 0))
      );

      return {
        rows,
        cols,
        grid,
        payload: JSON.stringify(grid),
        toggle(i, j) {
          this.grid[i][j] = this.grid[i][j] ? 0 : 1;
        },
        setAll(v) {
          for (let i = 0; i < this.rows; i++)
            for (let j = 0; j < this.cols; j++)
              this.grid[i][j] = v ? 1 : 0;
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
