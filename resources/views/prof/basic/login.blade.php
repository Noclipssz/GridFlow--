@extends('layouts.prof')
@section('title', 'Login Professor')
@section('layout_mode', 'auth')
@section('auth_main_class', 'w-full max-w-md mx-auto py-12')
@section('content')
    <div class="surface w-full p-8">
        <header class="mb-6 flex flex-col gap-4">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-600 text-sm font-semibold uppercase tracking-wide text-white">GF</span>
                <div>
                    <h1 class="text-lg font-semibold text-slate-900 dark:text-white">Portal do Professor</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Entre com seu CPF e senha para continuar.</p>
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

            <form method="POST" action="{{ route('prof.basic.login.post') }}" class="space-y-5">
                @csrf

                <div class="space-y-1">
                    <label for="cpf" class="field-label">CPF</label>
                    <x-ui.input
                        id="cpf"
                        type="text"
                        name="cpf"
                        inputmode="numeric"
                        pattern="\d{11}|\d{3}\.?\d{3}\.?\d{3}-?\d{2}"
                        placeholder="000.000.000-00"
                        value="{{ old('cpf') }}"
                        autocomplete="username"
                        required />
                    <p class="text-xs text-slate-500 dark:text-slate-400">Aceita com ou sem pontuação.</p>
                    @error('cpf')
                        <p class="text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1" x-data="{ show: false }">
                    <label for="senha" class="field-label">Senha</label>
                    <div class="relative">
                        <x-ui.input
                            x-bind:type="show ? 'text' : 'password'"
                            id="senha"
                            name="senha"
                            autocomplete="current-password"
                            class="pr-10"
                            required />
                        <button type="button"
                            class="absolute inset-y-0 right-0 flex w-10 items-center justify-center text-slate-400 hover:text-slate-600"
                            @click="show = !show" aria-label="Mostrar ou ocultar senha">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Z" />
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

                <div class="space-y-3">
                    <x-ui.button type="submit" class="w-full justify-center">Entrar</x-ui.button>
                    <p class="text-center text-sm text-slate-600 dark:text-slate-400">
                        Não tem conta?
                        <a href="{{ route('prof.basic.register') }}" class="font-medium text-indigo-600 hover:text-indigo-700">Cadastre-se</a>
                    </p>
                </div>
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
