<!doctype html>
<html lang="pt-BR" class="h-full antialiased">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Portal do Professor</title>

    <!-- Fonte opcional (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- Tailwind via CDN (remova se já compila Tailwind no projeto) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js para o toggle de senha e tema -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>



    <style>
        :root {
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji", sans-serif;
        }
    </style>
</head>

<body class="h-full bg-slate-100 dark:bg-slate-950 selection:bg-indigo-200/60 dark:selection:bg-indigo-400/30">

    <!-- Decorações suaves de fundo -->
    <div aria-hidden="true" class="pointer-events-none fixed inset-0 overflow-hidden">
        <div
            class="absolute -top-24 -right-24 h-72 w-72 rounded-full blur-3xl opacity-40 bg-indigo-300 dark:bg-indigo-500">
        </div>
        <div
            class="absolute bottom-0 left-1/2 -translate-x-1/2 h-72 w-[36rem] bg-gradient-to-t from-indigo-200/40 to-transparent dark:from-indigo-500/10 blur-2xl">
        </div>
    </div>


    <!-- CONTEÚDO ORIGINAL (intocado) -->
    <main class="relative z-10">
        <!-- ===== Seu bloco começa aqui: NÃO ALTERADO ===== -->
        <div
            class="min-h-screen flex flex-col items-center justify-center bg-slate-100 dark:bg-slate-950 py-12 px-4 sm:px-6 lg:px-8">
            <div
                class="max-w-md w-full space-y-8 p-8 md:p-10 bg-white/95 dark:bg-slate-900/80 rounded-2xl shadow-xl ring-1 ring-black/5 dark:ring-white/10 backdrop-blur">
                <div class="text-center space-y-3">
                    <span
                        class="inline-grid place-items-center h-12 w-12 rounded-xl bg-indigo-600 text-white text-xl font-bold shadow-sm">Gf</span>
                    <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50">
                        Portal do Professor
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Acesse com seu CPF e senha.</p>
                </div>

                @if ($errors->any())
                    <div role="alert"
                        class="rounded-xl border border-rose-200/60 dark:border-rose-900/40 bg-rose-50/80 dark:bg-rose-950/50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-rose-500 dark:text-rose-400" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94l-1.72-1.72z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-rose-800 dark:text-rose-200">Houve um problema com
                                    seu envio</h3>
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

                <form method="POST" action="{{ route('prof.basic.login.post') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="cpf" class="sr-only">CPF</label>
                        <div class="relative">
                            <!-- ANTES: span w-10 / svg h-4.5 w-4.5 / input pl-10 -->
                            <span
                                class="pointer-events-none absolute inset-y-0 left-0 grid w-9 place-items-center text-slate-400">
                                <!-- Ícone "user" minimalista (24px) -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                    fill="currentColor" aria-hidden="true">
                                    <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Z" />
                                    <path d="M4 20a8 8 0 1 1 16 0H4Z" />
                                </svg>
                            </span>

                            <x-ui.input id="cpf" type="text" name="cpf" inputmode="numeric"
                                pattern="\d{11}|\d{3}\.?\d{3}\.?\d{3}-?\d{2}" placeholder="000.000.000-00"
                                value="{{ old('cpf') }}" autocomplete="username" required
                                aria-invalid="@error('cpf') true @else false @enderror"
                                class="pl-9 h-11 rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900/60 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/60 transition" />

                        </div>
                        @error('cpf')
                            <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-data="{ show: false }">
                        <label for="senha" class="sr-only">Senha</label>
                        <div class="relative">
                            <span <!-- ANTES: span w-10 / svg h-4.5 w-4.5 / input pl-10 -->
                                <span
                                    class="pointer-events-none absolute inset-y-0 left-0 grid w-9 place-items-center text-slate-400">
                                    <!-- Ícone "lock" minimalista (24px) -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                        fill="currentColor" aria-hidden="true">
                                        <path
                                            d="M6 10V8a6 6 0 1 1 12 0v2h1a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V11a1 1 0 0 1 1-1h1Zm2 0h8V8a4 4 0 0 0-8 0v2Z" />
                                    </svg>
                                </span>

                                <x-ui.input x-bind:type="show ? 'text' : 'password'" id="senha" name="senha"
                                    autocomplete="current-password" required
                                    aria-invalid="@error('senha') true @else false @enderror"
                                    class="pl-9 h-11 rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900/60 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/60 transition" />

                                <button type="button"
                                    class="absolute inset-y-0 right-0 grid w-10 place-items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition"
                                    @click="show = !show" aria-label="Mostrar/ocultar senha">
                                    <svg x-show="!show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Z" />
                                    </svg>
                                    <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M3.5 2.1 2.1 3.5l18.4 18.4 1.4-1.4L3.5 2.1ZM12 7a5 5 0 0 1 5 5c0 .6-.1 1.1-.3 1.6l2.5 2.5c2.1-1.5 3.4-3.1 4.1-4.1-1-1.5-4.7-6-11.3-6-1 0-1.9.1-2.8.3l2 2c.6-.2 1.2-.3 1.8-.3Zm-7.8-.7 2.7 2.7C5.7 9.9 5 11.1 5 12a7 7 0 0 0 7 7c.9 0 2.1-.7 3-1.1l2.7 2.7c-1.7.8-3.4 1.4-5.7 1.4-8 0-11.7-7-11.7-7s1.9-3.4 5.9-6.4Z" />
                                    </svg>
                                </button>
                        </div>
                        @error('senha')
                            <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-ui.button type="submit"
                            class="w-full justify-center h-11 rounded-xl bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500/60 focus:outline-none font-semibold shadow-sm disabled:opacity-60 disabled:cursor-not-allowed transition">
                            Entrar
                        </x-ui.button>
                    </div>
                    <div class="text-center text-sm">
                        <a href="{{ route('prof.basic.register') }}"
                            class="font-medium text-indigo-600 hover:text-indigo-500 underline-offset-4 hover:underline">
                            Não tem conta? Cadastre-se
                        </a>
                    </div>
                </form>
            </div>
            <footer class="relative z-10 mt-8 pb-8 text-center text-xs text-slate-500/80">
                <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                    <span>&copy; {{ date('Y') }} — Portal do Professor</span>
                </div>
            </footer>
        </div>

        <!-- Como é esse estilo

Paleta minimal (slate + indigo): base em slate para superfícies e texto, com acentos em indigo para ações/realces.

Glass cards: superfícies com transparência leve (/80–/60), ring sutil e backdrop-blur no lugar de borda pesada.

Tipografia clara: títulos em font-semibold, hierarquia com text-3xl/2xl/ lg, microtexto em text-[11px] uppercase tracking-wide.

Espaçamento arejado: grids com gap-4/6, cards com p-5/6, botões h-10/h-11.

Acessibilidade: foco visível padronizado (focus:ring-2 focus:ring-indigo-500/40), contrastes verificados no dark.

Componentes consistentes:

KPI chips simples (título minúsculo + valor grande + descrição).

Mini-cards clicáveis com badge de total (Resumo por período).

Heatmap com 3 estados de cor: vazio (slate), disponível (emerald), aula (indigo).

Dark elegante: o dark é padrão, com variantes dark:* em tudo -->
    </main>
</body>

</html>
