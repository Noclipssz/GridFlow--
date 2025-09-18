<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Painel • Associar Matérias e Professores</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800">
  <div class="max-w-2xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-semibold">Associar à Turma "{{ $turma->nome }}"</h1>
      <a href="{{ route('admin.turmas.index') }}" class="text-sm text-slate-500 hover:text-slate-700">Voltar</a>
    </div>

    <form method="POST" action="{{ route('admin.turmas.salvarAssociacao', $turma) }}"
          class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl p-5 mb-6">
      @csrf

      <div class="grid grid-cols-1 gap-6">
        <div>
          <label for="materias" class="text-sm font-semibold text-slate-700">Matérias</label>
          <select name="materias[]" id="materias" multiple
                  class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm mt-1 h-48">
            @foreach ($materias as $materia)
              <option value="{{ $materia->id }}" {{ $turma->materias->contains($materia) ? 'selected' : '' }}>
                {{ $materia->nome }}
              </option>
            @endforeach
          </select>
        </div>

        <div>
          <label for="professores" class="text-sm font-semibold text-slate-700">Professores</label>
          <select name="professores[]" id="professores" multiple
                  class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm mt-1 h-48">
            @foreach ($professores as $professor)
              <option value="{{ $professor->id }}" {{ $turma->professors->contains($professor) ? 'selected' : '' }}>
                {{ $professor->nome }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="flex justify-end gap-3 mt-6">
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-white text-sm font-medium shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          Salvar Associações
        </button>
      </div>
    </form>
  </div>
</body>
</html>
