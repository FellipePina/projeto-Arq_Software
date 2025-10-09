<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro de Usuário</title>
  <link rel="stylesheet" href="/style.css">
</head>

<body>
  <div class="container">
    <h1>Crie sua Conta</h1>
    <form action="/usuario/salvar" method="post">
      <div class="form-group">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>
      </div>
      <div class="form-group">
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="form-group">
        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>
      </div>
      <button type="submit" class="btn">Cadastrar</button>
    </form>
    <p>Já tem uma conta? <a href="/usuario/login">Faça login</a></p>
  </div>
</body>

</html>