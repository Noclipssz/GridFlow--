<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Minha Disponibilidade</title>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    [x-cloak]{display:none!important}
    .cell { border-radius: 10px; padding: 12px; text-align: center; cursor: pointer; user-select: none; border: 1px solid #e5e7eb; }
    .on  { background: #dcfce7; border-color:#86efac; }
    .off { background: #f3f4f6; color:#6b7280; }
    .hdr { font-weight: 600; text-align:center; padding: 10px 8px; }
    .rowlbl { white-space:nowrap; padding-right:10px; font-weight:600; }
    .grid { width: 100%; max-width: 980px; margin: 0 auto; border-collapse: separate; border-spacing: 8px 10px; }
    .pill { border-radius: 9999px; padding: 4px 10px; font-size: 12px; }
    .pill-on  { background:#16a34a; color:#fff; }
    .pill-off { background:#9ca3af; color:#fff; }
    .bar { display:flex; justify-content:space-between; align-items:center; gap:12px; }
    .btn { padding: 10px 16px; border-radius: 10px; border:0; cursor:pointer; background:#111827; color:#fff; }
    .alert-ok { background:#ecfdf5; border:1px solid #34d399; color:#065f46; border-radius:10px; padding:10px 12px; margin-bottom:16px; }
    .alert-err { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:10px; padding:10px 12px; margin-bottom:16px; }
  </style>
</head>
<body>
  <div style="max-width:1040px;margin:24px auto;">
    <div class="bar" style="margin-bottom:16px;">
      <h1 style="font-size:20px;">Olá, {{ $prof->nome }} — selecione suas <strong>disponibilidades</strong></h1>
      <form method="POST" action="{{ route('prof.basic.logout') }}">
        @csrf
        <button class="btn" type="submit">Sair</button>
      </form>
    </div>

    @if (session('ok'))
      <div class="alert-ok">{{ session('ok') }}</div>
    @endif

    @if ($errors->any())
      <div class="alert-err">
        @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
      </div>
    @endif

    <div
      x-data="schedule({
        rows: {{ $rows }},
        cols: {{ $cols }},
        initial: @json($grid)
      })"
      x-cloak
    >
      <form method="POST" action="{{ route('prof.basic.schedule.save') }}" @submit.prevent="submit" x-ref="form">
        @csrf
        <input type="hidden" name="grid" x-model="payload">

        <table class="grid">
          <thead>
            <tr>
              <th></th>
              @foreach ($days as $d)
                <th class="hdr">{{ $d }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @for ($r = 0; $r < $rows; $r++)
              <tr>
                <td class="rowlbl">{{ $r + 1 }}ª Aula</td>
                @for ($c = 0; $c < $cols; $c++)
                  <td>
                    <div
                      class="cell"
                      :class="grid[{{ $r }}][{{ $c }}] ? 'on' : 'off'"
                      @click="toggle({{ $r }}, {{ $c }})"
                    >
                      <div style="font-weight:600; margin-bottom:6px;"
                           x-text="grid[{{ $r }}][{{ $c }}] ? 'Aula disponível' : 'Aula indisponível'"></div>
                      <span class="pill" :class="grid[{{ $r }}][{{ $c }}] ? 'pill-on' : 'pill-off'">
                        <span x-text="grid[{{ $r }}][{{ $c }}] ? 'Disponível' : 'Indisponível'"></span>
                      </span>
                    </div>
                  </td>
                @endfor
              </tr>
            @endfor
          </tbody>
        </table>

        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:18px;">
          <button type="button" class="btn" @click="setAll(1)">Marcar tudo</button>
          <button type="button" class="btn" @click="setAll(0)">Limpar tudo</button>
          <button type="submit" class="btn">Salvar</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function schedule({rows, cols, initial}) {
      const grid = Array.from({length: rows}, (_, i) =>
        Array.from({length: cols}, (_, j) => (initial?.[i]?.[j] ? 1 : 0))
      );

      return {
        rows, cols, grid,
        payload: JSON.stringify(grid),
        toggle(i, j) {
          this.grid[i][j] = this.grid[i][j] ? 0 : 1;
        },
        setAll(v) {
          for (let i=0;i<this.rows;i++)
            for (let j=0;j<this.cols;j++)
              this.grid[i][j] = v ? 1 : 0;
        },
        submit(e) {
          this.payload = JSON.stringify(this.grid);
          this.$refs.form.submit();
        }
      }
    }
  </script>
</body>
</html>
