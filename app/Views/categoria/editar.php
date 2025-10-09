<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Categoria</title>
  <link rel="stylesheet" href="/style.css">
</head>

<body>
  <?php require_once '../app/Views/layout/header.php'; ?>

  <main class="container">
    <h2>Editar Categoria</h2>

    <div class="card">
      <form action="/categoria/salvar" method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($categoria['id']); ?>">
        <div class="form-group">
          <label for="nome">Nome:</label>
          <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($categoria['nome']); ?>" required>
        </div>
        <div class="form-group">
          <label for="descricao">Descrição:</label>
          <textarea id="descricao" name="descricao"><?php echo htmlspecialchars($categoria['descricao']); ?></textarea>
        </div>
        <div class="form-group">
          <label for="cor">Cor:</label>
          <input type="color" id="cor" name="cor" value="<?php echo htmlspecialchars($categoria['cor']); ?>">
        </div>
        <button type="submit" class="btn">Salvar Alterações</button>
        <a href="/categorias" class="btn btn-secondary">Cancelar</a>
      </form>
    </div>
  </main>

  <?php require_once '../app/Views/layout/footer.php'; ?>
</body>

</html>