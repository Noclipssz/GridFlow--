@extends('layouts.prof')
@section('title', 'Minhas Turmas')
@section('content')
  <div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-semibold">Minhas turmas</h1>
      <div class="flex gap-2">
        <x-ui.button variant="secondary" href="{{ route('prof.basic.dashboard') }}">Dashboard</x-ui.button>
        <form method="POST" action="{{ route('prof.basic.logout') }}">@csrf
          <x-ui.button variant="danger">Sair</x-ui.button>
        </form>
      </div>
    </div>

    @if ($errors->any())
      <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
        @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
      </div>
    @endif

    @if (empty($mine))
      <div class="rounded-2xl border border-slate-200 bg-white p-6 text-slate-600 dark:bg-slate-800 dark:border-slate-700">
        Você ainda não possui aulas atribuídas em nenhuma turma.
      </div>
    @else
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach ($mine as $item)
          @php $t = $item['turma']; $aulas = $item['aulas']; @endphp
          <a href="{{ route('prof.turmas.show', $t->id) }}" class="block rounded-2xl border border-slate-200 bg-white p-5 hover:bg-slate-50 dark:bg-slate-800 dark:border-slate-700 dark:hover:bg-slate-700">
            <div class="text-lg font-semibold text-slate-900">[{{ $t->id }}] {{ $t->nome }}</div>
            <div class="mt-1 text-sm text-slate-600">Aulas suas nesta turma: <span class="font-medium">{{ $aulas }}</span></div>
          </a>
        @endforeach
      </div>
    @endif
  </div>
@endsection
