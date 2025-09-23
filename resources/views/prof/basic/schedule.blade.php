@extends('layouts.prof')
@section('title', 'Minha Disponibilidade')
@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="page-title">Olá, <span class="capitalize">{{ $prof->nome }}</span></h1>
                <p class="page-subtitle">Defina os horários em que você está disponível para dar aula.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <form method="POST" action="{{ route('prof.basic.logout') }}">
                    @csrf
                    <x-ui.button type="submit" variant="ghost">Sair</x-ui.button>
                </form>
                <x-ui.button href="{{ route('prof.basic.dashboard') }}" variant="ghost">Início</x-ui.button>
            </div>
        </div>

        <x-ui.card class="p-4">
            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600 dark:text-slate-400">
                <span class="badge-muted">Indisponível</span>
                <span class="badge-positive">Disponível</span>
                <span class="badge-info">Aula fixa</span>
            </div>
        </x-ui.card>

        <div x-data="schedule({ rows: {{ $rows }}, cols: {{ $cols }}, initial: @json($grid) })" x-cloak>
            <x-ui.card class="p-6">
                <form method="POST" action="{{ route('prof.basic.schedule.save', ['periodo' => $periodo ?? 'manha']) }}" @submit.prevent="submit" x-ref="form" class="space-y-5">
                    @csrf
                    <input type="hidden" name="grid" x-model="payload">
                    <input type="hidden" name="periodo" value="{{ $periodo ?? 'manha' }}">

                    <div class="flex flex-wrap items-center gap-3">
                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">Período:</span>
                        @php $p = $periodo ?? 'manha'; @endphp
                        <a href="{{ route('prof.basic.schedule', ['periodo' => 'manha']) }}"
                            class="btn {{ $p === 'manha' ? 'btn-secondary' : 'btn-ghost' }} px-3 py-1.5 text-sm">Manhã</a>
                        <a href="{{ route('prof.basic.schedule', ['periodo' => 'tarde']) }}"
                            class="btn {{ $p === 'tarde' ? 'btn-secondary' : 'btn-ghost' }} px-3 py-1.5 text-sm">Tarde</a>
                        <a href="{{ route('prof.basic.schedule', ['periodo' => 'noite']) }}"
                            class="btn {{ $p === 'noite' ? 'btn-secondary' : 'btn-ghost' }} px-3 py-1.5 text-sm">Noite</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table-grid max-w-4xl mx-auto">
                            <thead>
                                <tr>
                                    <th class="w-32"></th>
                                    @foreach ($days as $d)
                                        <th class="text-center text-xs font-semibold uppercase tracking-wide">{{ $d }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @for ($r = 0; $r < $rows; $r++)
                                    <tr>
                                        <td class="pr-2 text-sm font-semibold text-slate-600 dark:text-slate-300">{{ $r + 1 }}ª aula</td>
                                        @for ($c = 0; $c < $cols; $c++)
                                            <td>
                                                <div :class="grid[{{ $r }}][{{ $c }}] === 2 ? 'schedule-lock' : (grid[{{ $r }}][{{ $c }}] === 1 ? 'schedule-on' : 'schedule-off')"
                                                    @click="toggle({{ $r }}, {{ $c }})"
                                                    class="schedule-cell">
                                                    <div class="space-y-1">
                                                        <template x-if="grid[{{ $r }}][{{ $c }}] === 2">
                                                            <div class="font-medium">Aula fixa</div>
                                                        </template>
                                                        <template x-if="grid[{{ $r }}][{{ $c }}] === 1">
                                                            <div class="font-medium">Disponível</div>
                                                        </template>
                                                        <template x-if="grid[{{ $r }}][{{ $c }}] === 0">
                                                            <div class="font-medium">Indisponível</div>
                                                        </template>
                                                        <template x-if="grid[{{ $r }}][{{ $c }}] === 2">
                                                            <span class="badge-info">Bloqueado</span>
                                                        </template>
                                                        <template x-if="grid[{{ $r }}][{{ $c }}] === 1">
                                                            <span class="badge-positive">Aceitando aulas</span>
                                                        </template>
                                                        <template x-if="grid[{{ $r }}][{{ $c }}] === 0">
                                                            <span class="badge-muted">Livre</span>
                                                        </template>
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
                        <x-ui.button type="button" @click="setAll(1)">Marcar tudo</x-ui.button>
                        <x-ui.button type="button" variant="ghost" @click="setAll(0)">Limpar tudo</x-ui.button>
                        <x-ui.button type="submit">Salvar alterações</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>

    <script>
        function schedule({ rows, cols, initial }) {
            const grid = Array.from({ length: rows }, (_, i) =>
                Array.from({ length: cols }, (_, j) => {
                    const v = Number(initial?.[i]?.[j] ?? 0);
                    return v === 2 ? 2 : v === 1 ? 1 : 0;
                })
            );
            return {
                rows,
                cols,
                grid,
                payload: JSON.stringify(grid),
                toggle(i, j) {
                    if (this.grid[i][j] === 2) return;
                    this.grid[i][j] = this.grid[i][j] ? 0 : 1;
                },
                setAll(v) {
                    for (let i = 0; i < this.rows; i++) {
                        for (let j = 0; j < this.cols; j++) {
                            if (this.grid[i][j] !== 2) {
                                this.grid[i][j] = v ? 1 : 0;
                            }
                        }
                    }
                },
                submit() {
                    this.$refs.form.querySelector('input[name="grid"]').value = JSON.stringify(this.grid);
                    this.$refs.form.submit();
                },
            };
        }
    </script>
@endsection
