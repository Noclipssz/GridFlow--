<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login Professor (básico)</title></head>
<body>
  <h1>Login do Professor</h1>

  @if ($errors->any())
    <div style="color:red;">
      @foreach ($errors->all() as $e)
        <div>{{ $e }}</div>
      @endforeach
    </div>
  @endif

  <form method="POST" action="{{ route('prof.basic.login.post') }}">
    @csrf {{-- evita 419 --}}
    <label>CPF</label><br>
    <input type="text" name="cpf" value="{{ old('cpf') }}" placeholder="000.000.000-00"><br><br>

    <label>Senha</label><br>
    <input type="password" name="senha"><br><br>

    <button type="submit">Entrar</button>
  </form>

  <p>Não tem conta? <a href="{{ route('prof.basic.register') }}">Cadastrar</a></p>
</body>
</html>
