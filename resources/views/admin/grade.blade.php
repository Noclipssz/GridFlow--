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
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-semibold">Gerar grade por matéria</h1>
      <a href="/" class="text-sm text-slate-500 hover:text-slate-700">Voltar</a>
    </div>

    {{-- Card: seleção de professores --}}
    <form method="POST" action="{{ route('admin.grade.generate') }}"
          class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl p-5 mb-6">
      @csrf

      <div class="flex items-start justify-between gap-4 mb-4">
        <div>
          <p class="font-medium text-slate-900">
            Selecione <span class="font-semibold text-indigo-600">um professor por matéria</span>
          </p>
          <p class="text-sm text-slate-500">Deixe em branco para ignorar a matéria nesta geração.</p>
        </div>
        <div>
          <label for="turma_id" class="text-sm font-semibold text-slate-700">Filtrar por Turma</label>
          <select name="turma_id" id="turma_id" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm mt-1">
            <option value="">— Todas —</option>
            @foreach ($turmas as $turma)
              <option value="{{ $turma->id }}" @selected(isset($selectedTurma) && (int)$selectedTurma === $turma->id)>
                {{ $turma->nome }}
              </option>
            @endforeach
          </select>
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
                <option value="{{ $p->id }}"
                  @selected( (isset($selected[$m->id]) && (int)$selected[$m->id] === $p->id) )>
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

        {{-- Ações (ex.: salvar a grade no futuro) --}}
        @if (isset($selectedTurma))
        <form method="POST" action="{{ route('admin.grade.store') }}" class="mt-5">
            @csrf
            <input type="hidden" name="turma_id" value="{{ $selectedTurma }}">
            <input type="hidden" name="grid" value="{{ json_encode($grid) }}">

            <div class="flex justify-end gap-3 items-center">
                <div>
                    <label for="nome" class="text-sm font-semibold text-slate-700">Nome do Horário</label>
                    <input type="text" name="nome" id="nome" required class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm mt-1" placeholder="Ex: Horário 2025/1">
                </div>
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-white text-sm font-medium shadow-sm hover:bg-emerald-700">
                    Salvar grade
                </button>
            </div>
        </form>
        @endif
      </div>
    @endif
  </div>
</body>
</html>
