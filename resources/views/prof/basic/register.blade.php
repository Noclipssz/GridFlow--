@extends('layouts.prof')
@section('title', 'Cadastrar Professor')
@section('layout_mode', 'auth')
@section('auth_main_class', 'w-full max-w-2xl mx-auto py-12')
@section('content')
    <div class="surface w-full p-8">
        <header class="mb-6 flex flex-col gap-4">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-600 text-sm font-semibold uppercase tracking-wide text-white">GF</span>
                <div>
                    <h1 class="text-lg font-semibold text-slate-900 dark:text-white">Criar acesso</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Informe seus dados para acessar o GridFlow.</p>
                </div>
            </div>
            <div class="theme-toggle w-fit" role="group" aria-label="Selecionar tema">
                <button type="button" class="theme-option" data-theme-value="light">Claro</button>
                <button type="button" class="theme-option" data-theme-value="dark">Escuro</button>
            </div>
        </header>

            @if ($errors->any())
                <x-ui.alert type="danger" class="mb-5">
                    @foreach ($errors->all() as $e)
                        <div>• {{ $e }}</div>
                    @endforeach
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('prof.basic.register.post') }}" class="space-y-5">
                @csrf

                <div class="space-y-1">
                    <label for="nome" class="field-label">Nome completo</label>
                    <x-ui.input
                        id="nome"
                        type="text"
                        name="nome"
                        value="{{ old('nome') }}"
                        placeholder="Seu nome"
                        required />
                    @error('nome')
                        <p class="text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label for="cpf" class="field-label">CPF</label>
                    <x-ui.input
                        id="cpf"
                        type="text"
                        name="cpf"
                        inputmode="numeric"
                        pattern="\d{11}|\d{3}\.?\d{3}\.?\d{3}-?\d{2}"
                        value="{{ old('cpf') }}"
                        placeholder="000.000.000-00"
                        required />
                    <p class="text-xs text-slate-500 dark:text-slate-400">Aceita com ou sem pontuação.</p>
                    @error('cpf')
                        <p class="text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="space-y-1" x-data="{ show: false }">
                        <label for="senha" class="field-label">Senha</label>
                        <div class="relative">
                            <x-ui.input
                                x-bind:type="show ? 'text' : 'password'"
                                id="senha"
                                name="senha"
                                autocomplete="new-password"
                                class="pr-10"
                                required />
                            <button type="button"
                                class="absolute inset-y-0 right-0 flex w-9 items-center justify-center text-slate-400 hover:text-slate-600"
                                @click="show = !show" aria-label="Mostrar ou ocultar senha">
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 5C5 5 2 12 2 12s3 7 10 7 10-7 10-7-3-7-10-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Z" />
                                </svg>
                                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3.5 2.1 2.1 3.5l18.4 18.4 1.4-1.4L3.5 2.1ZM12 7a5 5 0 0 1 5 5c0 .6-.1 1.1-.3 1.6l2.5 2.5c2.1-1.5 3.4-3.1 4.1-4.1-1-1.5-4.7-6-11.3-6-1 0-1.9.1-2.8.3l2 2c.6-.2 1.2-.3 1.8-.3Zm-7.8-.7 2.7 2.7C5.7 9.9 5 11.1 5 12a7 7 0 0 0 7 7c.9 0 2.1-.7 3-1.1l2.7 2.7c-1.7.8-3.4 1.4-5.7 1.4-8 0-11.7-7-11.7-7s1.9-3.4 5.9-6.4Z" />
                                </svg>
                            </button>
                        </div>
                        @error('senha')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="senha_confirmation" class="field-label">Confirmar senha</label>
                        <x-ui.input
                            id="senha_confirmation"
                            type="password"
                            name="senha_confirmation"
                            autocomplete="new-password"
                            required />
                    </div>
                </div>

                @isset($materias)
                    <div class="space-y-1">
                        <label for="materia_id" class="field-label">Matéria</label>
                        <x-ui.select id="materia_id" name="materia_id" required>
                            <option value="" disabled {{ old('materia_id') ? '' : 'selected' }}>Selecione...</option>
                            @foreach ($materias as $m)
                                <option value="{{ $m->id }}" @selected(old('materia_id') == $m->id)>
                                    [{{ $m->id }}] {{ $m->nome }} — {{ $m->quant_aulas }} aulas
                                </option>
                            @endforeach
                        </x-ui.select>
                        @error('materia_id')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <div class="space-y-1">
                        <label for="materia_id" class="field-label">Matéria (ID)</label>
                        <x-ui.input
                            id="materia_id"
                            type="number"
                            name="materia_id"
                            value="{{ old('materia_id') }}"
                            placeholder="ID da matéria"
                            required />
                        @error('materia_id')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endisset

                <x-ui.button type="submit" class="w-full justify-center">Cadastrar</x-ui.button>
                <p class="text-center text-sm text-slate-600 dark:text-slate-400">
                    Já tem conta?
                    <a href="{{ route('prof.basic.login') }}" class="font-medium text-indigo-600 hover:text-indigo-700">Entrar</a>
                </p>
            </form>
    </div>

    <script>
        (function () {
            const el = document.getElementById('cpf');
            if (!el) return;
            el.addEventListener('input', () => {
                const raw = (el.value || '').replace(/\D+/g, '').slice(0, 11);
                let out = '';
                if (raw.length > 0) out += raw.substring(0, 3);
                if (raw.length >= 4) out += '.' + raw.substring(3, 6);
                if (raw.length >= 7) out += '.' + raw.substring(6, 9);
                if (raw.length >= 10) out += '-' + raw.substring(9, 11);
                el.value = out;
            });
        })();
    </script>
@endsection
