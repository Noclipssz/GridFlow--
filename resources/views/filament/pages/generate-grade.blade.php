<x-filament::page>
  <div id="generate-grade" class="space-y-6">
    <style>
      /* Repeater item: keep fields on same row and roomy */
      #generate-grade .filament-forms-field-wrapper { margin-bottom: .5rem; }
      /* TomSelect safety: hide extra clear/remove buttons */
      #generate-grade .ts-wrapper .remove, #generate-grade .ts-wrapper .ts-clear-button { display: none !important; }
      /* Height + alignment to mirror top selects */
      #generate-grade .ts-control { min-height: 100px; border-radius: 0.75rem; }
      #generate-grade .ts-wrapper.single .ts-control { display: flex; align-items: center; }
    </style>
    <form wire:submit.prevent="generate">
      {{ $this->form }}
      <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2">
          <x-filament::button wire:click="fillDefaults" color="gray" icon="heroicon-m-sparkles">Preencher automaticamente</x-filament::button>
          <x-filament::button wire:click="clearSelection" color="gray" icon="heroicon-m-arrow-path">Limpar</x-filament::button>
        </div>
        <div class="flex items-center gap-2">
          <x-filament::button type="submit" color="primary" icon="heroicon-m-cog-6-tooth">Gerar grade</x-filament::button>
        </div>
      </div>
    </form>

    @if (!empty($grid))
      <div class="filament-card p-4">
        <div class="text-sm text-gray-600 mb-2 space-x-3">
          <span>Iterações: <span class="font-semibold">{{ $meta['iterations'] ?? '-' }}</span></span>
          <span>Tempo: <span class="font-semibold">{{ $meta['duration_ms'] ?? '-' }}ms</span></span>
          <span>Desejadas: <span class="font-semibold">{{ $meta['desired_total'] ?? '-' }}</span></span>
          <span>Alocadas: <span class="font-semibold">{{ $meta['placed_total'] ?? '-' }}</span></span>
          <span>Faltantes: <span class="font-semibold">{{ $meta['missing_total'] ?? '-' }}</span></span>
          <span>Estratégia: <span class="font-semibold">{{ strtoupper($meta['strategy'] ?? 'N/A') }}</span></span>
        </div>
        @if (!empty($meta['problems']))
          <div class="mb-3 rounded-md border border-warning-300 bg-warning-50 p-3 text-sm text-warning-900">
            Algumas matérias/professores não foram totalmente alocadas. Ajuste disponibilidades ou seleções.
          </div>
          <div class="overflow-x-auto mb-3">
            <table class="w-full border-separate border-spacing-0 text-sm">
              <thead>
                <tr class="text-left text-gray-600">
                  <th class="py-2 pr-3">Professor</th>
                  <th class="py-2 pr-3">Matéria</th>
                  <th class="py-2 pr-3">Desejadas</th>
                  <th class="py-2 pr-3">Disponíveis</th>
                  <th class="py-2 pr-3">Faltantes</th>
                </tr>
              </thead>
              <tbody>
                @foreach (($meta['problems'] ?? []) as $p)
                  <tr class="border-t border-gray-100">
                    <td class="py-2 pr-3 font-medium text-gray-900">{{ $p['professor'] }}</td>
                    <td class="py-2 pr-3 text-gray-800">{{ $p['materia'] }}</td>
                    <td class="py-2 pr-3">{{ $p['desired'] }}</td>
                    <td class="py-2 pr-3">{{ $p['available'] }}</td>
                    <td class="py-2 pr-3 font-semibold text-warning-800">{{ $p['missing'] }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif

        <div class="overflow-x-auto">
          <table class="w-full border-separate border-spacing-2">
            <thead>
              <tr>
                <th class="w-28"></th>
                <th class="text-center text-sm font-bold">Segunda</th>
                <th class="text-center text-sm font-bold">Terça</th>
                <th class="text-center text-sm font-bold">Quarta</th>
                <th class="text-center text-sm font-bold">Quinta</th>
                <th class="text-center text-sm font-bold">Sexta</th>
              </tr>
            </thead>
            <tbody>
              @for ($a = 0; $a < count($grid); $a++)
                <tr>
                  <td class="text-sm font-semibold pr-2">{{ $a + 1 }}ª Aula</td>
                  @for ($d = 0; $d < count($grid[$a]); $d++)
                    @php $txt = $grid[$a][$d]; @endphp
                    <td class="align-top">
                      <div class="rounded-xl border px-4 py-3 h-20 flex items-center justify-center {{ $txt ? 'bg-success-50 border-success-200' : 'bg-gray-50 border-gray-200' }}">
                        {{ $txt ?: '—' }}
                      </div>
                    </td>
                  @endfor
                </tr>
              @endfor
            </tbody>
          </table>
        </div>

        <div class="mt-4 flex flex-wrap justify-end gap-2">
          <x-filament::button wire:click="validateGrid" color="gray" icon="heroicon-m-check-circle">Validar grade</x-filament::button>
          <x-filament::button wire:click="save" color="primary" :disabled="(($meta['missing_total'] ?? 0) > 0)" icon="heroicon-m-arrow-down-tray">Salvar grade</x-filament::button>
        </div>
      </div>
    @endif
  </div>
</x-filament::page>
