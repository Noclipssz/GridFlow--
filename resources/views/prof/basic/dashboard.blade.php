<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Dashboard Professor</title>

  {{-- Tailwind via CDN (sem Vite) --}}
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800">
  <div class="max-w-6xl mx-auto p-6">
    {{-- Topbar --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
      <h1 class="text-2xl font-semibold">
        Bem-vindo, <span class="capitalize">{{ $prof->nome }}</span>
      </h1>
    </div>

    {{-- Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      {{-- Perfil --}}
      <div class="bg-white rounded-2xl ring-1 ring-slate-200 shadow-sm p-5 md:col-span-2">
        <div class="flex items-start gap-4">
          <div class="h-12 w-12 rounded-xl bg-indigo-600 text-white grid place-items-center shadow-sm">
            {{-- ícone livro/mortarboard --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="m3 7 9-4 9 4-9 4-9-4zm0 6l3-1.333M21 13l-9 4-9-4m18 0v4"/>
            </svg>
          </div>

          <div class="flex-1">
            <div class="flex flex-wrap items-center justify-between gap-2">
              <div>
                <div class="text-lg font-semibold text-slate-900">{{ $prof->nome }}</div>
                <div class="text-sm text-slate-500">
                  Matéria:
                  <span class="font-medium text-slate-700">
                    {{ optional($prof->materia)->nome ?? '—' }}
                  </span>
                </div>
              </div>
              <a href="{{ route('prof.basic.schedule') }}"
                 class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-white text-sm font-medium shadow-sm hover:bg-indigo-700">
                Editar disponibilidade
              </a>
              <a href="{{ route('prof.turmas.index') }}"
                 class="inline-flex items-center rounded-xl bg-slate-800 px-4 py-2.5 text-white text-sm font-medium shadow-sm hover:bg-slate-900">
                Minhas turmas
              </a>
            </div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="text-xs uppercase tracking-wide text-slate-500">CPF</div>
                <div class="mt-1 text-sm font-medium text-slate-800">
                  {{ $prof->cpf ?? '—' }}
                </div>
              </div>

              <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="text-xs uppercase tracking-wide text-slate-500">Aulas/semana (matéria)</div>
                <div class="mt-1 text-sm font-medium text-slate-800">
                  {{ optional($prof->materia)->quant_aulas ?? '—' }}
                </div>
              </div>

              @php
                $manhaSrc = $prof->horario_manha;
                $tardeSrc = $prof->horario_tarde;
                $noiteSrc = $prof->horario_noite;

                $manha = is_array($manhaSrc) ? $manhaSrc : (json_decode((string)($manhaSrc ?? '[]'), true) ?? []);
                $tarde = is_array($tardeSrc) ? $tardeSrc : (json_decode((string)($tardeSrc ?? '[]'), true) ?? []);
                $noite = is_array($noiteSrc) ? $noiteSrc : (json_decode((string)($noiteSrc ?? '[]'), true) ?? []);

                $periods = [
                  'manha' => $manha,
                  'tarde' => $tarde,
                  'noite' => $noite,
                ];
                $labels = ['manha' => 'Manhã', 'tarde' => 'Tarde', 'noite' => 'Noite'];
                $stats = [];
                foreach ($periods as $key => $arr) {
                  $disp = 0; $aulas = 0;
                  foreach ($arr as $dia) {
                    if (is_array($dia)) {
                      foreach ($dia as $v) {
                        $v = (int)$v;
                        if ($v === 1) $disp++;
                        elseif ($v === 2) $aulas++;
                      }
                    }
                  }
                  $stats[$key] = [$disp, $aulas];
                }
              @endphp
              <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="text-xs uppercase tracking-wide text-slate-500">Resumo por período</div>
                <div class="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-2">
                  @foreach ($stats as $k => $v)
                    <a href="{{ route('prof.basic.schedule', ['periodo' => $k]) }}" class="rounded-lg border border-slate-200 bg-white p-3 hover:bg-slate-50">
                      <div class="text-xs text-slate-500">{{ $labels[$k] }}</div>
                      <div class="mt-1 text-sm text-slate-800">
                        Disponíveis: <span class="text-emerald-700 font-semibold">{{ $v[0] }}</span>
                        • Aulas: <span class="text-indigo-700 font-semibold">{{ $v[1] }}</span>
                      </div>
                    </a>
                  @endforeach
                </div>
              </div>
            </div>

            @if(!empty($arr))
              <div class="mt-5">
                <div class="text-sm font-semibold text-slate-700 mb-2">Prévia de disponibilidade</div>
                <div class="overflow-x-auto">
                  <table class="w-full border-separate border-spacing-2">
                    <thead>
                      <tr>
                        <th class="w-28"></th>
                        @foreach (['Segunda','Terça','Quarta','Quinta','Sexta'] as $d)
                          <th class="text-center text-xs font-bold text-slate-700">{{ $d }}</th>
                        @endforeach
                      </tr>
                    </thead>
                    <tbody>
                      @for ($a = 0; $a < 5; $a++)
                        <tr>
                          <td class="text-xs font-semibold text-slate-700 pr-2 whitespace-nowrap">{{ $a+1 }}ª Aula</td>
                          @for ($d = 0; $d < 5; $d++)
                          @php
                              // Banco: [dia][aula] — aqui transpomos p/ [aula][dia]
                              $v = (int) ($arr[$d][$a] ?? 0);
                              $isDisp = $v === 1; $isAula = $v === 2;
                          @endphp
                            <td class="align-top">
                              <div class="h-8 rounded-lg border text-[11px] grid place-items-center px-2
                                          {{ $isAula ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : ($isDisp ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-slate-50 border-slate-200 text-slate-500') }}">
                                {{ $isAula ? 'Aula' : ($isDisp ? 'Disponível' : '—') }}
                              </div>
                            </td>
                          @endfor
                        </tr>
                      @endfor
                    </tbody>
                  </table>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>

      {{-- Ações rápidas --}}
      <div class="bg-white rounded-2xl ring-1 ring-slate-200 shadow-sm p-5">
        <div class="text-sm font-semibold text-slate-700 mb-3">Ações</div>
        <div class="space-y-3">
          <a href="{{ route('prof.basic.schedule') }}"
             class="block w-full text-left rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium hover:bg-slate-100">
            Ver / editar disponibilidade
          </a>
          <a href="{{ route('prof.basic.dashboard') }}"
             class="block w-full text-left rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium hover:bg-slate-100">
            Atualizar painel
          </a>
          <form method="POST" action="{{ route('prof.basic.logout') }}">
            @csrf
            <button type="submit"
                    class="w-full rounded-xl bg-rose-600 px-4 py-2.5 text-white text-sm font-medium shadow-sm hover:bg-rose-700">
              Sair
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- Rodapé --}}
    <p class="mt-8 text-center text-xs text-slate-500">© {{ date('Y') }} — Sistema Escolar</p>
  </div>
</body>
</html>
