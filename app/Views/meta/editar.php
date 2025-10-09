<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Meta</title>
  <link rel="stylesheet" href="/style.css">
</head>

<body>
  <?php require_once '../app/Views/layout/header.php'; ?>

  <main class="container">
    <h2>Editar Meta</h2>
    <div class="card">
      <form action="/meta/salvar" method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($meta['id']); ?>">
        <div class="form-group">
          <label for="titulo">Título:</label>
          <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($meta['titulo']); ?>" required>
        </div>
        <div class="form-group">
          <label for="data_alvo">Data Alvo:</label>
          <input type="date" id="data_alvo" name="data_alvo" value="<?php echo htmlspecialchars($meta['data_alvo']); ?>" required>
        </div>
        <div class="form-group">
          <label for="status">Status:</label>
          <select id="status" name="status">
            <option value="ativa" <?php echo $meta['status'] === 'ativa' ? 'selected' : ''; ?>>Ativa</option>
            <option value="concluida" <?php echo $meta['status'] === 'concluida' ? 'selected' : ''; ?>>Concluída</option>
            <option value="cancelada" <?php echo $meta['status'] === 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
          </select>
        </div>
        <button type="submit" class="btn">Salvar Alterações</button>
        <a href="/metas" class="btn btn-secondary">Cancelar</a>
      </form>
    </div>
  </main>

  <?php require_once '../app/Views/layout/footer.php'; ?>
</body>

</html>