@extends('layouts.prof')
@section('title', 'Dashboard')
@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="page-title">Bem-vindo(a), <span class="capitalize">{{ $prof->nome }}</span></h1>
                <p class="page-subtitle">Acesse rapidamente suas principais ações.</p>
            </div>
            <form method="POST" action="{{ route('prof.basic.logout') }}">
                @csrf
                <x-ui.button variant="ghost">Sair</x-ui.button>
            </form>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <x-ui.card class="p-5">
                <div class="mb-3 text-sm font-semibold text-slate-700 dark:text-slate-300">Meu perfil</div>
                <div class="space-y-1 text-sm">
                    <div class="text-slate-900 dark:text-slate-100">{{ $prof->nome }}</div>
                    <div class="text-slate-600 dark:text-slate-400">Matéria: {{ $prof->materia->nome ?? '—' }}</div>
                </div>
            </x-ui.card>

            <a href="{{ route('prof.basic.schedule') }}" class="surface p-5 transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="mb-1 text-sm font-semibold text-slate-700 dark:text-slate-300">Disponibilidade</div>
                <div class="text-slate-900 dark:text-slate-100">Gerencie seus horários</div>
            </a>

            <a href="{{ route('prof.turmas.index') }}" class="surface p-5 transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="mb-1 text-sm font-semibold text-slate-700 dark:text-slate-300">Turmas</div>
                <div class="text-slate-900 dark:text-slate-100">Veja onde você leciona</div>
            </a>
        </div>
    </div>
@endsection

