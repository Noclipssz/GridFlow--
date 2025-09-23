@extends('layouts.prof')
@section('title', 'Turma • ' . $turma->nome)
@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="page-title">Turma: {{ $turma->nome }}</h1>
                <p class="page-subtitle">Confira suas aulas e dos demais professores neste período.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <x-ui.button href="{{ route('prof.turmas.index') }}" variant="ghost">Minhas turmas</x-ui.button>
                <x-ui.button href="{{ route('prof.basic.dashboard') }}" variant="ghost">Dashboard</x-ui.button>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">Período:</span>
            @php $p = request('periodo','manha'); @endphp
            <a href="{{ route('prof.turmas.show', [$turma->id, 'periodo' => 'manha']) }}"
                class="btn {{ $p==='manha' ? 'btn-secondary' : 'btn-ghost' }} px-3 py-1.5 text-sm">Manhã</a>
            <a href="{{ route('prof.turmas.show', [$turma->id, 'periodo' => 'tarde']) }}"
                class="btn {{ $p==='tarde' ? 'btn-secondary' : 'btn-ghost' }} px-3 py-1.5 text-sm">Tarde</a>
            <a href="{{ route('prof.turmas.show', [$turma->id, 'periodo' => 'noite']) }}"
                class="btn {{ $p==='noite' ? 'btn-secondary' : 'btn-ghost' }} px-3 py-1.5 text-sm">Noite</a>
        </div>

        <x-ui.card class="p-5">
            <div class="overflow-x-auto">
                <table class="table-grid">
                    <thead>
                        <tr>
                            <th class="w-28"></th>
                            @foreach ($days as $d)
                                <th class="text-center text-xs font-semibold uppercase tracking-wide">{{ $d }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @for ($a = 0; $a < 5; $a++)
                            <tr>
                                <td class="pr-2 text-sm font-semibold text-slate-600 dark:text-slate-300">{{ $a + 1 }}ª aula</td>
                                @for ($d = 0; $d < 5; $d++)
                                    @php $cell = $grid[$a][$d] ?? null; @endphp
                                    <td>
                                        @if (is_array($cell))
                                            @php
                                                $pid = (int) ($cell['professor_id'] ?? 0);
                                                $mid = (int) ($cell['materia_id'] ?? 0);
                                                $p = $profMap[$pid] ?? null;
                                                $m = $matMap[$mid] ?? null;
                                                $isMe = $pid === $prof->id;
                                            @endphp
                                            <div class="{{ $isMe ? 'grid-chip-my-class' : 'grid-chip-available' }} h-24 flex items-center justify-center">
                                                <div class="space-y-1 text-center">
                                                    <div class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                                        {{ $m?->nome ?? 'Matéria' }}
                                                    </div>
                                                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ $p?->nome ?? 'Professor' }}</div>
                                                    <span class="badge {{ $isMe ? 'badge-info' : 'badge-positive' }}">
                                                        {{ $isMe ? 'Sua aula' : 'Aula' }}
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="grid-chip-default h-24 grid place-items-center text-lg">—</div>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
@endsection
