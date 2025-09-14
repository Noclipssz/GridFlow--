<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Cadastrar Professor</title>

  {{-- Tailwind via CDN --}}
  <script src="https://cdn.tailwindcss.com"></script>
  {{-- Alpine (apenas para mostrar/ocultar senha) --}}
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100 relative overflow-hidden">

  {{-- Decoração de fundo (sutil) --}}
  <div aria-hidden="true" class="pointer-events-none absolute -top-28 -right-28 w-[28rem] h-[28rem] rounded-full bg-indigo-300/30 blur-3xl"></div>
  <div aria-hidden="true" class="pointer-events-none absolute -bottom-24 -left-24 w-[28rem] h-[28rem] rounded-full bg-emerald-300/30 blur-3xl"></div>

  <main class="relative z-10 flex min-h-screen items-center justify-center p-6">
    <div class="w-full max-w-lg">
      <div class="bg-white/90 backdrop-blur shadow-xl ring-1 ring-slate-200 rounded-2xl p-8">
        {{-- Cabeçalho --}}
        <div class="flex items-center gap-3 mb-6">
          <div class="h-11 w-11 rounded-xl bg-indigo-600 text-white grid place-items-center shadow-sm">
            {{-- user-plus icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M15 7a4 4 0 11-8 0 4 4 0 018 0zm6 8h-3m0 0h-3m3 0v-3m0 3v3M3 21a9 9 0 1118 0v0H3z" />
            </svg>
          </div>
          <div>
            <h1 class="text-xl font-semibold text-slate-900">Cadastrar Professor</h1>
            <p class="text-sm text-slate-500">Preencha seus dados para criar o acesso.</p>
          </div>
        </div>

        {{-- Alertas de erro (geral) --}}
        @if ($errors->any())
          <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800 text-sm">
            @foreach ($errors->all() as $e)
              <div>• {{ $e }}</div>
            @endforeach
          </div>
        @endif

        <form method="POST" action="{{ route('prof.basic.register.post') }}" class="space-y-5">
          @csrf

          {{-- Nome --}}
          <div>
            <label for="nome" class="block text-sm font-medium text-slate-700 mb-1">Nome</label>
            <input
              id="nome"
              type="text"
              name="nome"
              value="{{ old('nome') }}"
              class="block w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm placeholder:text-slate-400
                     focus:border-indigo-500 focus:ring-indigo-500"
              placeholder="Nome completo"
              required
            >
            @error('nome')
              <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
          </div>

          {{-- CPF --}}
          <div>
            <label for="cpf" class="block text-sm font-medium text-slate-700 mb-1">CPF</label>
            <input
              id="cpf"
              type="text"
              name="cpf"
              inputmode="numeric"
              pattern="\d{11}|\d{3}\.?\d{3}\.?\d{3}-?\d{2}"
              value="{{ old('cpf') }}"
              class="block w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm placeholder:text-slate-400
                     focus:border-indigo-500 focus:ring-indigo-500"
              placeholder="000.000.000-00"
              required
            >
            <p class="mt-1 text-xs text-slate-500">Aceita com ou sem pontuação.</p>
            @error('cpf')
              <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
          </div>

          {{-- Senha + Confirmar --}}
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{ show:false }">
            <div>
              <label for="senha" class="block text-sm font-medium text-slate-700 mb-1">Senha</label>
              <div class="relative">
                <input
                  :type="show ? 'text' : 'password'"
                  id="senha"
                  name="senha"
                  class="block w-full rounded-xl border-slate-300 py-2.5 pl-3 pr-10 text-sm
                         focus:border-indigo-500 focus:ring-indigo-500"
                  autocomplete="new-password"
                  required
                >
                <button type="button"
                        class="absolute inset-y-0 right-0 grid w-9 place-items-center text-slate-400 hover:text-slate-600"
                        @click="show = !show" aria-label="Mostrar/ocultar senha">
                  <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 5C5 5 2 12 2 12s3 7 10 7 10-7 10-7-3-7-10-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Z"/>
                  </svg>
                  <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3.5 2.1 2.1 3.5l18.4 18.4 1.4-1.4L3.5 2.1ZM12 7a5 5 0 0 1 5 5c0 .6-.1 1.1-.3 1.6l2.5 2.5c2.1-1.5 3.4-3.1 4.1-4.1-1-1.5-4.7-6-11.3-6-1 0-1.9.1-2.8.3l2 2c.6-.2 1.2-.3 1.8-.3Zm-7.8-.7 2.7 2.7C5.7 9.9 5 11.1 5 12a7 7 0 0 0 7 7c.9 0 2.1-.7 3-1.1l2.7 2.7c-1.7.8-3.4 1.4-5.7 1.4-8 0-11.7-7-11.7-7s1.9-3.4 5.9-6.4Z"/>
                  </svg>
                </button>
              </div>
              @error('senha')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="senha_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirmar Senha</label>
              <input
                id="senha_confirmation"
                type="password"
                name="senha_confirmation"
                class="block w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm
                       focus:border-indigo-500 focus:ring-indigo-500"
                autocomplete="new-password"
                required
              >
            </div>
          </div>

          {{-- Matéria (dropdown se $materias existir; senão, input numérico) --}}
          @isset($materias)
            <div>
              <label for="materia_id" class="block text-sm font-medium text-slate-700 mb-1">Matéria</label>
              <select
                id="materia_id"
                name="materia_id"
                class="block w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm
                       focus:border-indigo-500 focus:ring-indigo-500"
                required
              >
                <option value="" disabled {{ old('materia_id') ? '' : 'selected' }}>Selecione...</option>
                @foreach ($materias as $m)
                  <option value="{{ $m->id }}" @selected(old('materia_id') == $m->id)>
                    [{{ $m->id }}] {{ $m->nome }} — {{ $m->quant_aulas }} aulas
                  </option>
                @endforeach
              </select>
              @error('materia_id')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
              @enderror
            </div>
          @else
            <div>
              <label for="materia_id" class="block text-sm font-medium text-slate-700 mb-1">Matéria (ID)</label>
              <input
                id="materia_id"
                type="number"
                name="materia_id"
                value="{{ old('materia_id') }}"
                class="block w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm
                       focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="ID da matéria"
                required
              >
              @error('materia_id')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
              @enderror
            </div>
          @endisset

          {{-- Botão --}}
          <button type="submit"
                  class="w-full inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-white text-sm font-medium shadow-sm hover:bg-emerald-700">
            Cadastrar
          </button>

          <p class="text-center text-sm text-slate-600">
            Já tem conta?
            <a href="{{ route('prof.basic.login') }}" class="font-medium text-indigo-600 hover:text-indigo-700">
              Entrar
            </a>
          </p>
        </form>
      </div>

      <p class="mt-6 text-center text-xs text-slate-500">
        © {{ date('Y') }} — Sistema Escolar
      </p>
    </div>
  </main>
</body>
</html>
