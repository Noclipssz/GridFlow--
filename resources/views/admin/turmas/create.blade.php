<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Painel • Criar Turma</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800">
  <div class="max-w-2xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-semibold">Criar Nova Turma</h1>
      <a href="{{ route('admin.turmas.index') }}" class="text-sm text-slate-500 hover:text-slate-700">Voltar</a>
    </div>

    <form method="POST" action="{{ route('admin.turmas.store') }}"
          class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl p-5 mb-6">
      @csrf

      <div class="grid grid-cols-1 gap-4">
        <div>
          <label for="nome" class="text-sm font-semibold text-slate-700">Nome da Turma</label>
          <input type="text" name="nome" id="nome" required
                 class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm mt-1">
        </div>

        <div>
          <label for="ano_letivo" class="text-sm font-semibold text-slate-700">Ano Letivo</label>
          <input type="number" name="ano_letivo" id="ano_letivo" required
                 class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm mt-1">
        </div>

        <div>
          <label for="periodo" class="text-sm font-semibold text-slate-700">Período</label>
          <input type="text" name="periodo" id="periodo" required
                 class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm mt-1">
        </div>

        <div>
          <label for="capacidade_alunos" class="text-sm font-semibold text-slate-700">Capacidade de Alunos</label>
          <input type="number" name="capacidade_alunos" id="capacidade_alunos" required
                 class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm mt-1">
        </div>
      </div>

      <div class="flex justify-end gap-3 mt-6">
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-white text-sm font-medium shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          Criar Turma
        </button>
      </div>
    </form>
  </div>
</body>
</html>
