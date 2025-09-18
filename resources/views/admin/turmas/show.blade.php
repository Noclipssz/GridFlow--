<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Painel • Detalhes da Turma</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800">
  <div class="max-w-4xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-semibold">Detalhes da Turma: {{ $turma->nome }}</h1>
      <a href="{{ route('admin.turmas.index') }}" class="text-sm text-slate-500 hover:text-slate-700">Voltar</a>
    </div>

    <div class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl p-5 mb-6">
        <p><strong>Ano Letivo:</strong> {{ $turma->ano_letivo }}</p>
        <p><strong>Período:</strong> {{ $turma->periodo }}</p>
        <p><strong>Capacidade:</strong> {{ $turma->capacidade_alunos }}</p>
    </div>

    <div class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl p-5">
        <h2 class="text-xl font-semibold mb-4">Horários Salvos</h2>
        @if ($turma->horarios->isEmpty())
            <p>Nenhum horário salvo para esta turma.</p>
        @else
            <table class="w-full border-separate border-spacing-y-2">
                <thead>
                <tr>
                    <th class="text-left text-sm font-bold text-slate-700 p-2">Nome do Horário</th>
                    <th class="text-left text-sm font-bold text-slate-700 p-2">Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($turma->horarios as $horario)
                    <tr class="bg-slate-50/60 hover:bg-slate-100">
                        <td class="p-2 rounded-l-lg">{{ $horario->nome }}</td>
                        <td class="p-2 rounded-r-lg">
                            <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800">Ver Horário</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>

  </div>
</body>
</html>
