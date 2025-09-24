<!doctype html>
<html lang="pt-BR" class="h-full antialiased">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cadastrar Professor</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    :root { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif; }
  </style>
</head>
<body class="h-full bg-slate-100 dark:bg-[#0b0f19] selection:bg-blue-200/60 dark:selection:bg-blue-400/30">

  <!-- Fundo sutil -->
  <div aria-hidden="true" class="pointer-events-none fixed inset-0 overflow-hidden">
    <div class="absolute -top-24 -right-24 h-80 w-80 rounded-full blur-3xl opacity-30 bg-blue-300/50 dark:bg-blue-500/25"></div>
    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-80 w-[42rem] bg-gradient-to-t from-blue-200/25 to-transparent dark:from-blue-500/10 blur-2xl"></div>
  </div>

  <main class="relative z-10">
    <div class="min-h-screen flex flex-col items-center justify-center py-14 px-4 sm:px-6 lg:px-8">
      <div class="max-w-lg w-full space-y-8 p-10 md:p-12 bg-white/95 dark:bg-slate-900/90 rounded-3xl shadow-2xl ring-1 ring-slate-200/60 dark:ring-slate-800">
        <div class="text-center space-y-4">
          <span class="inline-grid place-items-center h-12 w-12 rounded-2xl bg-blue-600 text-white text-xl font-bold shadow-sm">Gf</span>
          <h2 class="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50">Cadastrar Professor</h2>
          <p class="text-[15px] text-slate-600 dark:text-slate-400">Preencha seus dados para criar o acesso.</p>
        </div>

        @if ($errors->any())
          <div role="alert" class="rounded-2xl border border-rose-200/60 dark:border-rose-900/40 bg-rose-50/80 dark:bg-rose-950/50 p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-4 w-4 text-rose-500 dark:text-rose-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94l-1.72-1.72z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-semibold text-rose-800 dark:text-rose-200">Houve um problema com seu envio</h3>
                <div class="mt-2 text-sm text-rose-700 dark:text-rose-300">
                  <ul role="list" class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              </div>
            </div>
          </div>
        @endif

        <form method="POST" action="{{ route('prof.basic.register.post') }}" class="space-y-6">
          @csrf

          <!-- Nome -->
          <div>
            <label for="nome" class="sr-only">Nome</label>
            <x-ui.input
              id="nome"
              type="text"
              name="nome"
              value="{{ old('nome') }}"
              placeholder="Nome completo"
              required
              class="pl-12 h-12 text-base rounded-2xl border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-800/70 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 transition"
            />
          </div>

          <!-- CPF -->
          <div>
            <label for="cpf" class="sr-only">CPF</label>
            <div class="relative">
              <span class="pointer-events-none absolute inset-y-0 left-0 grid w-12 place-items-center text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Z"/>
                  <path d="M4 20a8 8 0 1 1 16 0H4Z"/>
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
                required
                class="pl-12 h-12 text-base rounded-2xl border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-800/70 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 transition"
              />
            </div>
          </div>

          <!-- Senhas -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Senha -->
            <div x-data="{ show:false }">
              <label for="senha" class="sr-only">Senha</label>
              <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 grid w-12 place-items-center text-slate-400">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M6 10V8a6 6 0 1 1 12 0v2h1a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V11a1 1 0 0 1 1-1h1Zm2 0h8V8a4 4 0 0 0-8 0v2Z"/>
                  </svg>
                </span>
                <x-ui.input
                  x-bind:type="show ? 'text' : 'password'"
                  id="senha"
                  name="senha"
                  autocomplete="new-password"
                  required
                  class="pl-12 pr-12 h-12 text-base rounded-2xl border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-800/70 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 transition"
                />
                <button type="button"
                        class="absolute inset-y-0 right-0 grid w-12 place-items-center text-slate-400 hover:text-slate-300 transition"
                        @click="show = !show" aria-label="Mostrar/ocultar senha">
                  <svg x-show="!show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Z"/>
                  </svg>
                  <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3.5 2.1 2.1 3.5l18.4 18.4 1.4-1.4L3.5 2.1ZM12 7a5 5 0 0 1 5 5c0 .6-.1 1.1-.3 1.6l2.5 2.5c2.1-1.5 3.4-3.1 4.1-4.1-1-1.5-4.7-6-11.3-6-1 0-1.9.1-2.8.3l2 2c.6-.2 1.2-.3 1.8-.3Z"/>
                  </svg>
                </button>
              </div>
              @error('senha')
                <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
              @enderror
            </div>

            <!-- Confirmar Senha (com olho também) -->
            <div x-data="{ show2:false }">
              <label for="senha_confirmation" class="sr-only">Confirmar Senha</label>
              <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 grid w-12 place-items-center text-slate-400">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M6 10V8a6 6 0 1 1 12 0v2h1a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V11a1 1 0 0 1 1-1h1Zm2 0h8V8a4 4 0 0 0-8 0v2Z"/>
                  </svg>
                </span>
                <x-ui.input
                  x-bind:type="show2 ? 'text' : 'password'"
                  id="senha_confirmation"
                  name="senha_confirmation"
                  autocomplete="new-password"
                  required
                  class="pl-12 pr-12 h-12 text-base rounded-2xl border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-800/70 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 transition"
                />
                <button type="button"
                        class="absolute inset-y-0 right-0 grid w-12 place-items-center text-slate-400 hover:text-slate-300 transition"
                        @click="show2 = !show2" aria-label="Mostrar/ocultar confirmação de senha">
                  <svg x-show="!show2" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Z"/>
                  </svg>
                  <svg x-show="show2" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3.5 2.1 2.1 3.5l18.4 18.4 1.4-1.4L3.5 2.1ZM12 7a5 5 0 0 1 5 5c0 .6-.1 1.1-.3 1.6l2.5 2.5c2.1-1.5 3.4-3.1 4.1-4.1-1-1.5-4.7-6-11.3-6-1 0-1.9.1-2.8.3l2 2c.6-.2 1.2-.3 1.8-.3Z"/>
                  </svg>
                </button>
              </div>
              @error('senha_confirmation')
                <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
              @enderror
            </div>
          </div>

          @isset($materias)
            <div>
              <label for="materia_id" class="sr-only">Matéria</label>
              <x-ui.select id="materia_id" name="materia_id" required
                class="pl-12 h-12 text-base rounded-2xl border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-800/70 text-slate-900 dark:text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 transition">
                <option value="" disabled {{ old('materia_id') ? '' : 'selected' }}>Selecione...</option>
                @foreach ($materias as $m)
                  <option value="{{ $m->id }}" @selected(old('materia_id') == $m->id)>
                    [{{ $m->id }}] {{ $m->nome }} — {{ $m->quant_aulas }} aulas
                  </option>
                @endforeach
              </x-ui.select>
            </div>
          @else
            <div>
              <label for="materia_id" class="sr-only">Matéria (ID)</label>
              <x-ui.input
                id="materia_id"
                type="number"
                name="materia_id"
                value="{{ old('materia_id') }}"
                placeholder="ID da matéria"
                required
                class="pl-12 h-12 text-base rounded-2xl border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-800/70 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 transition"
              />
            </div>
          @endisset

          <div>
            <x-ui.button
              type="submit"
              class="w-full justify-center h-12 text-base rounded-2xl bg-blue-600 hover:bg-blue-500 focus:ring-2 focus:ring-blue-500/50 focus:outline-none font-semibold shadow-lg disabled:opacity-60 disabled:cursor-not-allowed transition">
              Cadastrar
            </x-ui.button>
          </div>

          <div class="text-center text-[15px]">
            <a href="{{ route('prof.basic.login') }}" class="font-medium text-blue-400 hover:text-blue-300 underline-offset-4 hover:underline">
              Já tem conta? Acesse
            </a>
          </div>
        </form>
      </div>

      <footer class="relative z-10 mt-10 pb-8 text-center text-xs text-slate-500/80">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
          <span>&copy; {{ date('Y') }} — Portal do Professor</span>
        </div>
      </footer>
    </div>
  </main>
</body>
</html>
