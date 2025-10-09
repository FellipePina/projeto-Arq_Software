<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Conteúdo de Estudo</title>
  <link rel="stylesheet" href="/style.css">
</head>

<body>
  <?php require_once '../app/Views/layout/header.php'; ?>

  <main class="container">
    <h2>Editar Conteúdo</h2>
    <div class="card">
      <form action="/conteudo/salvar" method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($conteudo['id']); ?>">
        <div class="form-group">
          <label for="titulo">Título:</label>
          <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($conteudo['titulo']); ?>" required>
        </div>
        <div class="form-group">
          <label for="descricao">Descrição:</label>
          <textarea id="descricao" name="descricao"><?php echo htmlspecialchars($conteudo['descricao']); ?></textarea>
        </div>
        <div class="form-group">
          <label for="status">Status:</label>
          <select id="status" name="status">
            <option value="pendente" <?php echo $conteudo['status'] === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
            <option value="em_andamento" <?php echo $conteudo['status'] === 'em_andamento' ? 'selected' : ''; ?>>Em Andamento</option>
            <option value="concluido" <?php echo $conteudo['status'] === 'concluido' ? 'selected' : ''; ?>>Concluído</option>
          </select>
        </div>
        <div class="form-group">
          <label for="categoria_id">Categoria:</label>
          <select id="categoria_id" name="categoria_id">
            <option value="">Sem categoria</option>
            <?php foreach ($categorias as $categoria): ?>
              <option value="<?php echo $categoria['id']; ?>" <?php echo ($conteudo['categoria_id'] == $categoria['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($categoria['nome']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="btn">Salvar Alterações</button>
        <a href="/conteudos" class="btn btn-secondary">Cancelar</a>
      </form>
    </div>
  </main>

  <?php require_once '../app/Views/layout/footer.php'; ?>
</body>

</html>