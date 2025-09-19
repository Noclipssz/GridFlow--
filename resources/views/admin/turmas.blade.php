<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Painel • Turmas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>html{color-scheme:light dark}</style>
  <meta name="color-scheme" content="light dark">
  <meta name="supported-color-schemes" content="light dark">
  <meta name="prefers-color-scheme" content="light dark">
  <meta name="theme-color" content="#111827">
</head>
<body class="bg-slate-50 text-slate-800">
  <div class="max-w-6xl mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-semibold">Gerenciar turmas</h1>
      <div class="flex items-center gap-3">
        <a href="{{ route('admin.grade.form', ['periodo' => $periodo]) }}" class="text-sm rounded-xl bg-slate-800 px-4 py-2.5 text-white font-medium">Gerar grade</a>
      </div>
    </div>

    @if ($errors->any())
      <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
        @foreach ($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
      </div>
    @endif
    @if (session('ok'))
      <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
        {{ session('ok') }}
      </div>
    @endif

    <form method="GET" action="{{ route('admin.turmas.index') }}" class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl p-5 mb-6">
      <div class="flex items-center gap-3">
        <label class="text-sm font-semibold text-slate-700">Período</label>
        @php $p = old('periodo', $periodo ?? 'manha'); @endphp
        <select name="periodo" class="rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
          <option value="manha" @selected($p==='manha')>Manhã</option>
          <option value="tarde" @selected($p==='tarde')>Tarde</option>
          <option value="noite" @selected($p==='noite')>Noite</option>
        </select>
        <button type="submit" class="inline-flex items-center rounded-xl bg-slate-800 px-4 py-2.5 text-white text-sm font-medium shadow-sm hover:bg-slate-900">
          Filtrar
        </button>
      </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl p-5">
        <h2 class="text-lg font-semibold mb-4">Nova turma</h2>
        <form method="POST" action="{{ route('admin.turmas.store') }}" class="space-y-4">
          @csrf
          <input type="hidden" name="periodo" value="{{ $periodo }}">
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Nome</label>
            <input type="text" name="nome" required class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Ex.: 1ºA - Informática" value="{{ old('nome') }}">
          </div>
          <div class="flex justify-end">
            <button class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2.5 text-white text-sm font-medium shadow-sm hover:bg-emerald-700">Criar</button>
          </div>
        </form>
      </div>

      <div class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold">Turmas ({{ strtoupper($periodo) }})</h2>
          <span class="text-sm text-slate-500">Total: {{ count($turmas) }}</span>
        </div>
        @if (count($turmas) === 0)
          <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 text-slate-600">Nenhuma turma cadastrada neste período.</div>
        @else
          <div class="grid grid-cols-1 gap-3">
            @foreach ($turmas as $t)
              <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div>
                  <div class="text-sm font-semibold text-slate-800">[{{ $t->id }}] {{ $t->nome }}</div>
                  <div class="text-xs text-slate-500">Período: {{ $t->periodo }}</div>
                </div>
                <a class="text-xs rounded-xl bg-slate-800 px-3 py-2 text-white font-medium" href="{{ route('admin.grade.form', ['periodo' => $t->periodo]) }}">Gerar grade</a>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
</body>
</html>

