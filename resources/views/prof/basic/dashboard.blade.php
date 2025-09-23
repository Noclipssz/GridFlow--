@extends('layouts.prof')
@section('title', 'Dashboard')
@section('content')
  <div class="max-w-6xl mx-auto">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
      <h1 class="text-2xl font-semibold">
        Bem-vindo, <span class="capitalize">{{ $prof->nome }}</span>
      </h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <x-ui.card class="p-5 md:col-span-2">
        <div class="flex items-start gap-4">
          <div class="h-12 w-12 rounded-xl bg-indigo-600 text-white grid place-items-center shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m3 7 9-4 9 4-9 4-9-4zm0 6l3-1.333M21 13l-9 4-9-4m18 0v4"/>
            </svg>
          </div>
          <div class="flex-1">
            <div class="flex flex-wrap items-center justify-between gap-2">
              <div>
                <div class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $prof->nome }}</div>
                <div class="text-sm text-slate-500">
                  Matéria:
                  <span class="font-medium text-slate-700 dark:text-slate-200">
                    {{ optional($prof->materia)->nome ?? '—' }}
                  </span>
                </div>
              </div>
              <x-ui.button href="{{ route('prof.basic.schedule') }}" variant="primary">Editar disponibilidade</x-ui.button>
              <x-ui.button href="{{ route('prof.turmas.index') }}" variant="secondary">Minhas turmas</x-ui.button>
            </div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:bg-slate-900 dark:border-slate-700">
                <div class="text-xs uppercase tracking-wide text-slate-500">CPF</div>
                <div class="mt-1 text-sm font-medium text-slate-800 dark:text-slate-200">{{ $prof->cpf ?? '—' }}</div>
              </div>

              <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:bg-slate-900 dark:border-slate-700">
                <div class="text-xs uppercase tracking-wide text-slate-500">Aulas/semana (matéria)</div>
                <div class="mt-1 text-sm font-medium text-slate-800 dark:text-slate-200">{{ optional($prof->materia)->quant_aulas ?? '—' }}</div>
              </div>

              @php
                $manhaSrc = $prof->horario_manha;
                $tardeSrc = $prof->horario_tarde;
                $noiteSrc = $prof->horario_noite;
                $manha = is_array($manhaSrc) ? $manhaSrc : (json_decode((string)($manhaSrc ?? '[]'), true) ?? []);
                $tarde = is_array($tardeSrc) ? $tardeSrc : (json_decode((string)($tardeSrc ?? '[]'), true) ?? []);
                $noite = is_array($noiteSrc) ? $noiteSrc : (json_decode((string)($noiteSrc ?? '[]'), true) ?? []);
                $periods = ['manha' => $manha, 'tarde' => $tarde, 'noite' => $noite];
                $labels = ['manha' => 'Manhã', 'tarde' => 'Tarde', 'noite' => 'Noite'];
                $stats = [];
                foreach ($periods as $key => $arr) {
                  $disp = 0; $aulas = 0;
                  foreach ($arr as $dia) if (is_array($dia)) foreach ($dia as $v) { $v=(int)$v; if ($v===1) $disp++; elseif ($v===2) $aulas++; }
                  $stats[$key] = [$disp, $aulas];
                }
              @endphp
              <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:bg-slate-900 dark:border-slate-700">
                <div class="text-xs uppercase tracking-wide text-slate-500">Resumo por período</div>
                <div class="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-2">
                  @foreach ($stats as $k => $v)
                    <a href="{{ route('prof.basic.schedule', ['periodo' => $k]) }}" class="rounded-lg border border-slate-200 bg-white p-3 hover:bg-slate-50 dark:bg-slate-800 dark:border-slate-700 dark:hover:bg-slate-700">
                      <div class="text-xs text-slate-500">{{ $labels[$k] }}</div>
                      <div class="mt-1 text-sm text-slate-800 dark:text-slate-200">
                        Disponíveis: <span class="text-emerald-700 dark:text-emerald-400 font-semibold">{{ $v[0] }}</span>
                        • Aulas: <span class="text-indigo-700 dark:text-indigo-400 font-semibold">{{ $v[1] }}</span>
                      </div>
                    </a>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
      </x-ui.card>

      <x-ui.card class="p-5">
        <div class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-3">Ações</div>
        <div class="space-y-3">
          <a href="{{ route('prof.basic.schedule') }}" class="block w-full text-left rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium hover:bg-slate-100 dark:bg-slate-900 dark:border-slate-700 dark:hover:bg-slate-800">Ver / editar disponibilidade</a>
          <a href="{{ route('prof.basic.dashboard') }}" class="block w-full text-left rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium hover:bg-slate-100 dark:bg-slate-900 dark:border-slate-700 dark:hover:bg-slate-800">Atualizar painel</a>
          <form method="POST" action="{{ route('prof.basic.logout') }}">
            @csrf
            <x-ui.button type="submit" variant="danger" class="w-full justify-center">Sair</x-ui.button>
          </form>
        </div>
      </x-ui.card>
    </div>

    <p class="mt-8 text-center text-xs text-slate-500">© {{ date('Y') }} — Sistema Escolar</p>
  </div>
@endsection
