@extends('layouts.prof')
@section('title', 'Login Professor')
@section('content')
<div class="min-h-[70vh] flex items-center justify-center">

  {{-- Decoração de fundo --}}
  <div aria-hidden="true" class="pointer-events-none absolute -top-24 -right-24 w-96 h-96 rounded-full bg-indigo-300/30 blur-3xl"></div>
  <div aria-hidden="true" class="pointer-events-none absolute -bottom-20 -left-20 w-96 h-96 rounded-full bg-emerald-300/30 blur-3xl"></div>

    <div class="w-full max-w-md p-6">
      {{-- Card --}}
      <x-ui.card class="p-8">

        {{-- Header / Marca --}}
        <div class="flex items-center gap-3 mb-6">
          <div class="h-11 w-11 rounded-xl bg-indigo-600 text-white grid place-items-center shadow-sm">
            {{-- mortarboard icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="m3 7 9-4 9 4-9 4-9-4zm0 6l3-1.333M21 13l-9 4-9-4m18 0v4"/>
            </svg>
          </div>
          <div>
            <h1 class="text-xl font-semibold text-slate-900">Portal do Professor</h1>
            <p class="text-sm text-slate-500">Acesse com seu CPF e senha.</p>
          </div>
        </div>

        {{-- Alerts de erro --}}
        @if ($errors->any())
          <x-ui.alert type="danger" class="mb-5">
            @foreach ($errors->all() as $e)
              <div>• {{ $e }}</div>
            @endforeach
          </x-ui.alert>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('prof.basic.login.post') }}" class="space-y-5">
          @csrf

          {{-- CPF --}}
          <div>
            <label for="cpf" class="block text-sm font-medium text-slate-700 mb-1">CPF</label>
            <div class="relative">
              <span class="pointer-events-none absolute inset-y-0 left-0 grid w-10 place-items-center text-slate-400">
                {{-- id-card icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M20 4H4a2 2 0 0 0-2 2v1h20V6a2 2 0 0 0-2-2Z"/>
                  <path d="M22 9H2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9ZM7 18H5v-2h2v2Zm4 0H9v-2h2v2Zm4 0h-2v-2h2v2Z"/>
                </svg>
              </span>
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
            </div>
            <p class="mt-1 text-xs text-slate-500">Apenas números ou com pontuação. Ex.: 000.000.000-00</p>
            @error('cpf')
              <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
          </div>

          {{-- Senha --}}
          <div x-data="{ show:false }">
            <label for="senha" class="block text-sm font-medium text-slate-700 mb-1">Senha</label>
            <div class="relative">
              <span class="pointer-events-none absolute inset-y-0 left-0 grid w-10 place-items-center text-slate-400">
                {{-- lock icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M17 8V7a5 5 0 0 0-10 0v1H5v14h14V8h-2Zm-8 0V7a3 3 0 0 1 6 0v1H9Z"/>
                </svg>
              </span>
              <x-ui.input
                x-bind:type="show ? 'text' : 'password'"
                id="senha"
                name="senha"
                autocomplete="current-password"
                required />
              <button type="button"
                      class="absolute inset-y-0 right-0 grid w-10 place-items-center text-slate-400 hover:text-slate-600"
                      @click="show = !show" aria-label="Mostrar/ocultar senha">
                {{-- eye icon --}}
                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Z"/>
                </svg>
                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M3.5 2.1 2.1 3.5l18.4 18.4 1.4-1.4L3.5 2.1ZM12 7a5 5 0 0 1 5 5c0 .6-.1 1.1-.3 1.6l2.5 2.5c2.1-1.5 3.4-3.1 4.1-4.1-1-1.5-4.7-6-11.3-6-1 0-1.9.1-2.8.3l2 2c.6-.2 1.2-.3 1.8-.3Zm-7.8-.7 2.7 2.7C5.7 9.9 5 11.1 5 12a7 7 0 0 0 7 7c.9 0 2.1-.7 3-1.1l2.7 2.7c-1.7.8-3.4 1.4-5.7 1.4-8 0-11.7-7-11.7-7s1.9-3.4 5.9-6.4Z"/>
                </svg>
              </button>
            </div>
            @error('senha')
              <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
          </div>

          {{-- Ações --}}
          <div class="space-y-3">
            <x-ui.button type="submit" class="w-full justify-center">Entrar</x-ui.button>

            <p class="text-center text-sm text-slate-600">
              Não tem conta?
              <a href="{{ route('prof.basic.register') }}" class="font-medium text-indigo-600 hover:text-indigo-700">
                Cadastrar
              </a>
            </p>
          </div>
        </form>
      </x-ui.card>
      <script>
        (function(){
          const el = document.getElementById('cpf');
          if (!el) return;
          el.addEventListener('input', () => {
            let v = (el.value || '').replace(/\D+/g, '').slice(0,11);
            let out = '';
            if (v.length > 0) out += v.substring(0,3);
            if (v.length >= 4) out += '.' + v.substring(3,6);
            if (v.length >= 7) out += '.' + v.substring(6,9);
            if (v.length >= 10) out += '-' + v.substring(9,11);
            el.value = out;
          });
        })();
      </script>

      {{-- rodapé minimal --}}
      <p class="mt-6 text-center text-xs text-slate-500">
        © {{ date('Y') }} — Sistema Escolar
      </p>
    </div>
</div>
@endsection
