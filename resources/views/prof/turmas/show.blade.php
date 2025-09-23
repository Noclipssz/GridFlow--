@extends('layouts.prof')
@section('title', 'Turma • ' . $turma->nome)
@section('content')
  <div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-semibold">Turma: {{ $turma->nome }}</h1>
        <p class="text-sm text-slate-500">Visualize suas aulas e as dos demais professores.</p>
      </div>
      <div class="flex gap-2">
        <x-ui.button variant="secondary" href="{{ route('prof.turmas.index') }}">Minhas turmas</x-ui.button>
        <x-ui.button href="{{ route('prof.basic.dashboard') }}">Dashboard</x-ui.button>
      </div>
    </div>

    <div class="flex items-center gap-3 mb-4">
      <span class="text-sm font-semibold text-slate-700">Período:</span>
      @php $p = request('periodo','manha'); @endphp
      <a href="{{ route('prof.turmas.show', [$turma->id, 'periodo' => 'manha']) }}" class="text-sm px-3 py-1 rounded-lg {{ $p==='manha' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700' }}">Manhã</a>
      <a href="{{ route('prof.turmas.show', [$turma->id, 'periodo' => 'tarde']) }}" class="text-sm px-3 py-1 rounded-lg {{ $p==='tarde' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700' }}">Tarde</a>
      <a href="{{ route('prof.turmas.show', [$turma->id, 'periodo' => 'noite']) }}" class="text-sm px-3 py-1 rounded-lg {{ $p==='noite' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700' }}">Noite</a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:bg-slate-800 dark:border-slate-700">
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
                      <div class="rounded-xl border px-4 py-3 h-24 flex items-center justify-center {{ $isMe ? 'bg-indigo-50 border-indigo-200' : 'bg-emerald-50 border-emerald-200' }}">
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
@endsection
