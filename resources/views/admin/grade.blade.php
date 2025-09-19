<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Painel • Gerar Grade</title>
  {{-- Tailwind via CDN (ótimo p/ protótipo). Em prod, recomendo Vite. --}}
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800">
  <div class="max-w-6xl mx-auto p-6">
    @if ($errors->any())
      <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
        @foreach ($errors->all() as $e)
          <div>• {{ $e }}</div>
        @endforeach
      </div>
    @endif
    @if (session('ok'))
      <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
        {{ session('ok') }}
      </div>
    @endif
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-semibold">Gerar grade por matéria</h1>
      <div class="flex items-center gap-3">
        <a href="{{ route('admin.turmas.index', ['periodo' => $periodo ?? 'manha']) }}" class="text-sm rounded-xl bg-slate-800 px-4 py-2.5 text-white font-medium">Gerenciar turmas</a>
        <a href="/" class="text-sm text-slate-500 hover:text-slate-700">Voltar</a>
      </div>
    </div>

    {{-- Filtro de período --}}
    <form method="GET" action="{{ route('admin.grade.form') }}" class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl p-5 mb-6">
      <div class="flex items-center gap-3">
        <label class="text-sm font-semibold text-slate-700">Período</label>
        @php $p = old('periodo', $periodo ?? 'manha'); @endphp
        <select name="periodo" class="rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
          <option value="manha" @selected($p==='manha')>Manhã</option>
          <option value="tarde" @selected($p==='tarde')>Tarde</option>
          <option value="noite" @selected($p==='noite')>Noite</option>
        </select>
        <button type="submit" class="inline-flex items-center rounded-xl bg-slate-800 px-4 py-2.5 text-white text-sm font-medium shadow-sm hover:bg-slate-900">
          Aplicar período
        </button>
      </div>
    </form>

    {{-- Card: seleção de turma e professores --}}
    <form method="POST" action="{{ route('admin.grade.generate') }}"
          class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl p-5 mb-6">
      @csrf
      <input type="hidden" name="periodo" value="{{ $periodo ?? 'manha' }}">

      <div class="flex flex-col gap-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="md:col-span-1">
            <label class="block text-sm font-semibold text-slate-700 mb-1">Turma</label>
            <select name="turma_id" required
                    class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
              <option value="" disabled {{ empty(old('turma_id', $selected_turma_id ?? null)) ? 'selected' : '' }}>Selecione...</option>
              @foreach (($turmas ?? []) as $t)
                <option value="{{ $t->id }}" @selected((int)old('turma_id', $selected_turma_id ?? 0) === $t->id)>
                  [{{ $t->id }}] {{ $t->nome }}
                </option>
              @endforeach
            </select>
            @error('turma_id')
              <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-slate-700 mb-1">Instruções</label>
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
              Selecione uma turma e, abaixo, <span class="font-medium">um professor por matéria</span>.
              A grade será <span class="font-semibold text-rose-600">sobrescrita</span> para a turma escolhida e os horários dos professores alocados serão marcados como <span class="font-semibold">aula</span> (estado 2).
            </div>
          </div>
        </div>

        <div class="flex items-start justify-between gap-4">
          <p class="font-medium text-slate-900">
            Selecione <span class="font-semibold text-indigo-600">um professor por matéria</span>
          </p>
          <p class="text-sm text-slate-500">Deixe em branco para ignorar a matéria nesta geração.</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach ($materias as $m)
          <div class="flex flex-col gap-2 bg-slate-50/60 rounded-xl p-4 ring-1 ring-slate-200">
            <label class="text-sm font-semibold text-slate-700">
              [{{ $m->id }}] {{ $m->nome }}
              <span class="text-slate-400 font-normal">({{ $m->quant_aulas }} aulas)</span>
            </label>

            <select name="selected[{{ $m->id }}]"
                    class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
              <option value="">— Ignorar —</option>
              @foreach ($m->professores as $p)
                @php $sel = (int) old('selected.'.$m->id, $selected[$m->id] ?? 0); @endphp
                <option value="{{ $p->id }}" @selected($sel === $p->id)>
                  {{ $p->nome }} {{ $p->cpf ? '— CPF: '.$p->cpf : '' }}
                </option>
              @endforeach
            </select>
          </div>
        @endforeach
      </div>

      <div class="flex justify-end gap-3 mt-6">
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-white text-sm font-medium shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          Gerar grade
        </button>
      </div>
    </form>

    {{-- Turmas com grade salva (liberar) --}}
    @php $hasLocked = isset($turmasLocked) && count($turmasLocked) > 0; @endphp

    @if ($hasLocked)
      <div class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold">Turmas com grade salva</h2>
          <p class="text-sm text-slate-500">Libere uma turma para poder gerar novamente.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          @foreach ($turmasLocked as $t)
            <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 p-4">
              <div>
                <div class="text-sm font-semibold text-slate-800">[{{ $t->id }}] {{ $t->nome }}</div>
                <div class="text-xs text-slate-500">Possui grade salva</div>
              </div>
              <form method="POST" action="{{ route('admin.grade.clear', $t->id) }}">
                @csrf
                <input type="hidden" name="periodo" value="{{ $periodo ?? 'manha' }}">
                <button type="submit"
                        class="inline-flex items-center rounded-xl bg-rose-600 px-3 py-2 text-white text-xs font-medium shadow-sm hover:bg-rose-700"
                        onclick="return confirm('Liberar esta turma? Os professores voltarão a ficar disponíveis nesses horários (se não alocados em outras turmas).')">
                  Liberar turma
                </button>
              </form>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    {{-- Card: grade gerada --}}
    @if ($grid)
      <div class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold">Grade gerada</h2>
          @php
            $rest = $meta['remaining'] ?? [];
            $faltam = is_array($rest) ? array_sum($rest) : 0;
          @endphp
          <div class="text-xs text-slate-500">
            Iterações: <span class="font-semibold">{{ $meta['iterations'] ?? '-' }}</span> •
            Tempo: <span class="font-semibold">{{ $meta['duration_ms'] ?? '-' }}ms</span> •
            Restantes: <span class="font-semibold {{ $faltam ? 'text-amber-600':'text-emerald-600' }}">{{ $faltam }}</span>
          </div>
        </div>

        {{-- Tabela responsiva --}}
        <div class="overflow-x-auto">
          <table class="w-full border-separate border-spacing-2">
            <thead>
              <tr>
                <th class="w-28"></th>
                <th class="text-center text-sm font-bold text-slate-700">Segunda</th>
                <th class="text-center text-sm font-bold text-slate-700">Terça</th>
                <th class="text-center text-sm font-bold text-slate-700">Quarta</th>
                <th class="text-center text-sm font-bold text-slate-700">Quinta</th>
                <th class="text-center text-sm font-bold text-slate-700">Sexta</th>
              </tr>
            </thead>
            <tbody>
              @for ($a = 0; $a < count($grid); $a++)
                <tr>
                  <td class="text-sm font-semibold text-slate-700 pr-2">{{ $a + 1 }}ª Aula</td>
                  @for ($d = 0; $d < count($grid[$a]); $d++)
                    @php $txt = $grid[$a][$d]; @endphp
                    <td class="align-top">
                      <div class="rounded-xl border border-slate-200 px-4 py-3 h-20 flex items-center justify-center
                                  {{ $txt ? 'bg-emerald-50 ring-1 ring-emerald-200' : 'bg-slate-50' }}">
                        @if($txt)
                          <div class="text-center">
                            <div class="text-sm font-semibold text-emerald-800">{{ $txt }}</div>
                            <div class="mt-1 inline-flex items-center rounded-full bg-emerald-600/90 px-2 py-0.5 text-[11px] font-medium text-white">
                              Disponível
                            </div>
                          </div>
                        @else
                          <div class="text-center">
                            <div class="text-sm font-medium text-slate-400">—</div>
                            <div class="mt-1 inline-flex items-center rounded-full bg-slate-400/80 px-2 py-0.5 text-[11px] font-medium text-white">
                              Vago
                            </div>
                          </div>
                        @endif
                      </div>
                    </td>
                  @endfor
                </tr>
              @endfor
            </tbody>
          </table>
        </div>

        {{-- Ações: salvar/ exportar --}}
        <div class="mt-5 flex justify-end gap-3">
          <button type="button"
                  class="inline-flex items-center gap-2 rounded-xl bg-slate-800 px-4 py-2.5 text-white text-sm font-medium shadow-sm hover:bg-slate-900">
            Exportar (PDF/CSV)
          </button>
          <form method="POST" action="{{ route('admin.grade.save') }}">
            @csrf
            <input type="hidden" name="turma_id" value="{{ $selected_turma_id }}">
            <input type="hidden" name="grid_ids" value='@json($grid_ids ?? [])'>
            <input type="hidden" name="periodo" value="{{ $periodo ?? 'manha' }}">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-white text-sm font-medium shadow-sm hover:bg-emerald-700">
              Salvar grade
            </button>
          </form>
        </div>
      </div>
    @endif
  </div>
</body>
</html>
