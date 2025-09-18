<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Painel • Turmas</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800">
  <div class="max-w-4xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-semibold">Turmas</h1>
      <a href="{{ route('admin.turmas.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-white text-sm font-medium shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Criar Turma</a>
    </div>

    @if (session('success'))
      <div class="bg-emerald-100 text-emerald-800 p-4 rounded-lg mb-6">
        {{ session('success') }}
      </div>
    @endif

    <div class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl p-5">
      <table class="w-full border-separate border-spacing-y-2">
        <thead>
          <tr>
            <th class="text-left text-sm font-bold text-slate-700 p-2">ID</th>
            <th class="text-left text-sm font-bold text-slate-700 p-2">Nome</th>
            <th class="text-left text-sm font-bold text-slate-700 p-2">Ano Letivo</th>
            <th class="text-left text-sm font-bold text-slate-700 p-2">Período</th>
            <th class="text-left text-sm font-bold text-slate-700 p-2">Capacidade</th>
            <th class="text-left text-sm font-bold text-slate-700 p-2">Ações</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($turmas as $turma)
            <tr class="bg-slate-50/60 hover:bg-slate-100">
              <td class="p-2 rounded-l-lg">{{ $turma->id }}</td>
              <td class="p-2">{{ $turma->nome }}</td>
              <td class="p-2">{{ $turma->ano_letivo }}</td>
              <td class="p-2">{{ $turma->periodo }}</td>
              <td class="p-2">{{ $turma->capacidade_alunos }}</td>
              <td class="p-2 rounded-r-lg">
                <div class="flex gap-2">
                  <a href="{{ route('admin.turmas.show', $turma) }}" class="text-sm text-blue-600 hover:text-blue-800">Detalhes</a>
                  <a href="{{ route('admin.turmas.edit', $turma) }}" class="text-sm text-indigo-600 hover:text-indigo-800">Editar</a>
                  <a href="{{ route('admin.turmas.associar', $turma) }}" class="text-sm text-emerald-600 hover:text-emerald-800">Associar</a>
                  <form action="{{ route('admin.turmas.destroy', $turma) }}" method="POST" onsubmit="return confirm('Tem certeza?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">Excluir</button>
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
