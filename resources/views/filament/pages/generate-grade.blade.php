<x-filament::page>
  <div class="space-y-6">
    <form wire:submit.prevent="generate" class="space-y-4">
      {{ $this->form }}
      @php
        $turmaOptionsEmpty = empty($this->availableTurmaOptions());
        $hasAnySubject = !empty($this->buildDefaultSelectionRows());
        $hasSelectedProf = collect($selected ?? [])->contains(fn ($r) => !empty($r['professor_id'] ?? null));
        $canGenerate = (bool) ($turma_id && $hasSelectedProf);
      @endphp

      @if ($turmaOptionsEmpty)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-100">
          Nenhuma turma disponível neste período. Crie uma nova turma ou libere uma existente em <a href="/admin/turmas" class="underline">Turmas</a>.
        </div>
      @endif

      @unless ($hasAnySubject)
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
          Não há matérias ativas para seleção. Ative matérias em <a href="/admin/materias" class="underline">Matérias</a>.
        </div>
      @endunless
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2">
          <x-filament::button wire:click="fillDefaults" color="gray" icon="heroicon-m-sparkles">Preencher automaticamente</x-filament::button>
          <x-filament::button wire:click="clearSelection" color="gray" icon="heroicon-m-arrow-path">Limpar</x-filament::button>
        </div>
        <x-filament::button type="submit" color="primary" icon="heroicon-m-cog-6-tooth" :disabled="!$canGenerate">Gerar grade</x-filament::button>
      </div>
    </form>

    @if (!empty($grid))
      <div class="surface space-y-4 p-4">
        <div class="flex flex-wrap gap-3 text-sm text-slate-600 dark:text-slate-300">
          <span>Iterações: <span class="badge-muted">{{ $meta['iterations'] ?? '-' }}</span></span>
          <span>Tempo: <span class="badge-muted">{{ $meta['duration_ms'] ?? '-' }}ms</span></span>
          <span>Desejadas: <span class="badge-muted">{{ $meta['desired_total'] ?? '-' }}</span></span>
          <span>Alocadas: <span class="badge-positive">{{ $meta['placed_total'] ?? '-' }}</span></span>
          <span>Faltantes: <span class="badge-muted">{{ $meta['missing_total'] ?? '-' }}</span></span>
          <span>Estratégia: <span class="badge-muted">{{ strtoupper($meta['strategy'] ?? 'N/A') }}</span></span>
        </div>

        @if (!empty($meta['problems']))
          <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-100">
            Algumas matérias/professores não foram totalmente alocadas. Ajuste disponibilidades ou seleções.
          </div>
          <div class="overflow-x-auto">
            <table class="table-grid">
              <thead>
                <tr>
                  <th class="py-2 pr-3">Professor</th>
                  <th class="py-2 pr-3">Matéria</th>
                  <th class="py-2 pr-3">Desejadas</th>
                  <th class="py-2 pr-3">Disponíveis</th>
                  <th class="py-2 pr-3">Faltantes</th>
                </tr>
              </thead>
              <tbody>
                @foreach (($meta['problems'] ?? []) as $p)
                  <tr class="border-t border-slate-100 dark:border-slate-800/60">
                    <td class="py-2 pr-3 font-medium text-slate-900 dark:text-slate-100">{{ $p['professor'] }}</td>
                    <td class="py-2 pr-3 text-slate-700 dark:text-slate-300">{{ $p['materia'] }}</td>
                    <td class="py-2 pr-3">{{ $p['desired'] }}</td>
                    <td class="py-2 pr-3">{{ $p['available'] }}</td>
                    <td class="py-2 pr-3 font-semibold text-amber-700 dark:text-amber-300">{{ $p['missing'] }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif

        <div class="overflow-x-auto">
          <table class="table-grid">
            <thead>
              <tr>
                <th class="w-32"></th>
                <th class="text-center text-xs font-semibold uppercase tracking-wide">Segunda</th>
                <th class="text-center text-xs font-semibold uppercase tracking-wide">Terça</th>
                <th class="text-center text-xs font-semibold uppercase tracking-wide">Quarta</th>
                <th class="text-center text-xs font-semibold uppercase tracking-wide">Quinta</th>
                <th class="text-center text-xs font-semibold uppercase tracking-wide">Sexta</th>
              </tr>
            </thead>
            <tbody>
              @for ($a = 0; $a < count($grid); $a++)
                <tr>
                  <td class="pr-2 text-sm font-semibold text-slate-600 dark:text-slate-300">{{ $a + 1 }}ª aula</td>
                  @for ($d = 0; $d < count($grid[$a]); $d++)
                    @php $txt = $grid[$a][$d]; @endphp
                    <td>
                      <div class="{{ $txt ? 'grid-chip-available' : 'grid-chip-default' }} h-20 flex items-center justify-center text-sm">
                        {{ $txt ?: '—' }}
                      </div>
                    </td>
                  @endfor
                </tr>
              @endfor
            </tbody>
          </table>
        </div>

        <div class="flex flex-wrap justify-end gap-2">
          <x-filament::button wire:click="validateGrid" color="gray" icon="heroicon-m-check-circle">Validar grade</x-filament::button>
          <x-filament::button wire:click="save" color="primary" :disabled="(($meta['missing_total'] ?? 0) > 0)" icon="heroicon-m-arrow-down-tray">Salvar grade</x-filament::button>
        </div>
      </div>
    @endif
  </div>
</x-filament::page>
