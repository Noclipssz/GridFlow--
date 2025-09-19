<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Turma • {{ $turma->nome }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800">
  <div class="max-w-6xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-semibold">Turma: {{ $turma->nome }}</h1>
        <p class="text-sm text-slate-500">Visualize suas aulas e as dos demais professores.</p>
      </div>
      <div class="flex gap-2">
        <a href="{{ route('prof.turmas.index') }}" class="rounded-xl bg-slate-800 px-4 py-2.5 text-white text-sm font-medium">Minhas turmas</a>
        <a href="{{ route('prof.basic.dashboard') }}" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-white text-sm font-medium">Dashboard</a>
      </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5">
      <div class="overflow-x-auto">
        <table class="w-full border-separate border-spacing-2">
          <thead>
            <tr>
              <th class="w-28"></th>
              @foreach ($days as $d)
                <th class="text-center text-sm font-bold text-slate-700">{{ $d }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @for ($a = 0; $a < 5; $a++)
              <tr>
                <td class="text-sm font-semibold text-slate-700 pr-2">{{ $a + 1 }}ª Aula</td>
                @for ($d = 0; $d < 5; $d++)
                  @php $cell = $grid[$a][$d] ?? null; @endphp
                  <td class="align-top">
                    @if (is_array($cell))
                      @php
                        $pid = (int) ($cell['professor_id'] ?? 0);
                        $mid = (int) ($cell['materia_id'] ?? 0);
                        $p = $profMap[$pid] ?? null;
                        $m = $matMap[$mid] ?? null;
                        $isMe = $pid === $prof->id;
                      @endphp
                      <div class="rounded-xl border px-4 py-3 h-24 flex items-center justify-center
                                  {{ $isMe ? 'bg-indigo-50 border-indigo-200' : 'bg-emerald-50 border-emerald-200' }}">
                        <div class="text-center">
                          <div class="text-sm font-semibold {{ $isMe ? 'text-indigo-800' : 'text-emerald-800' }}">
                            {{ $m?->nome ?? 'Matéria' }} — {{ $p?->nome ?? 'Professor' }}
                          </div>
                          <div class="mt-1 inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium
                                      {{ $isMe ? 'bg-indigo-600 text-white' : 'bg-emerald-600 text-white' }}">
                            {{ $isMe ? 'Minha aula' : 'Aula' }}
                          </div>
                        </div>
                      </div>
                    @else
                      <div class="rounded-xl border border-slate-200 px-4 py-3 h-24 bg-slate-50 text-slate-400 grid place-items-center">
                        —
                      </div>
                    @endif
                  </td>
                @endfor
              </tr>
            @endfor
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>

