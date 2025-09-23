@extends('layouts.prof')
@section('title', 'Minhas Turmas')
@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="page-title">Minhas turmas</h1>
                <p class="page-subtitle">Visualize rapidamente onde você leciona.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <x-ui.button href="{{ route('prof.basic.dashboard') }}" variant="ghost">Início</x-ui.button>
                <form method="POST" action="{{ route('prof.basic.logout') }}">
                    @csrf
                    <x-ui.button variant="danger">Sair</x-ui.button>
                </form>
            </div>
        </div>

        @if ($errors->any())
            <x-ui.alert type="danger">
                @foreach ($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </x-ui.alert>
        @endif

        @if (empty($mine))
            <div class="empty-state">
                Você ainda não possui aulas atribuídas em nenhuma turma.
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach ($mine as $item)
                    @php $t = $item['turma']; $aulas = $item['aulas']; @endphp
                    <a href="{{ route('prof.turmas.show', $t->id) }}" class="surface block p-5 transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="text-base font-semibold text-slate-900 dark:text-white">[{{ $t->id }}] {{ $t->nome }}</div>
                        <div class="mt-2 flex items-center justify-between text-sm text-slate-600 dark:text-slate-300">
                            <span>Aulas atribuídas</span>
                            <span class="badge-info">{{ $aulas }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
