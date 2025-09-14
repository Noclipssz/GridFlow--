<!doctype html>
<html>
<head><meta charset="utf-8"><title>Dashboard Professor</title></head>
<body>
  <h1>Bem-vindo, {{ $prof->nome }}</h1>
  <p>CPF: {{ $prof->cpf }}</p>
  <p>Matéria: {{ optional($prof->materia)->nome ?? '—' }}</p>

  <form method="POST" action="{{ route('prof.basic.logout') }}">
    @csrf
    <button type="submit">Sair</button>
  </form>
  <p><a href="{{ route('prof.basic.schedule') }}">Aulas</a></p>
</body>
</html>
