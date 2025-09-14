<!doctype html>
<html>
<head><meta charset="utf-8"><title>Cadastrar Professor (básico)</title></head>
<body>
  <h1>Cadastrar Professor</h1>

  @if ($errors->any())
    <div style="color:red;">
      @foreach ($errors->all() as $e)
        <div>{{ $e }}</div>
      @endforeach
    </div>
  @endif

  <form method="POST" action="{{ route('prof.basic.register.post') }}">
    @csrf
    <label>Nome</label><br>
    <input type="text" name="nome" value="{{ old('nome') }}"><br><br>

    <label>CPF</label><br>
    <input type="text" name="cpf" value="{{ old('cpf') }}" placeholder="000.000.000-00"><br><br>

    <label>Senha</label><br>
    <input type="password" name="senha"><br><br>

    <label>Confirmar Senha</label><br>
    <input type="password" name="senha_confirmation"><br><br>

    <label>Matéria (opcional)</label><br>
    <input type="number" name="materia_id" value="{{ old('materia_id') }}" placeholder="ID da matéria"><br><br>

    <button type="submit">Cadastrar</button>
  </form>

  <p>Já tem conta? <a href="{{ route('prof.basic.login') }}">Entrar</a></p>
</body>
</html>
