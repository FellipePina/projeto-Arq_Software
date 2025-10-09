<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerenciar Categorias</title>
  <link rel="stylesheet" href="/style.css">
</head>

<body>
  <?php require_once '../app/Views/layout/header.php'; ?>

  <main class="container">
    <h2>Minhas Categorias</h2>

    <div class="card">
      <h3>Nova Categoria</h3>
      <form action="/categoria/salvar" method="post">
        <input type="hidden" name="id" value="">
        <div class="form-group">
          <label for="nome">Nome:</label>
          <input type="text" id="nome" name="nome" required>
        </div>
        <div class="form-group">
          <label for="descricao">Descrição:</label>
          <textarea id="descricao" name="descricao"></textarea>
        </div>
        <button type="submit" class="btn">Salvar Categoria</button>
      </form>
    </div>

    <div class="card">
      <h3>Categorias Existentes</h3>
      <table>
        <thead>
          <tr>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($categorias)): ?>
            <?php foreach ($categorias as $categoria): ?>
              <tr>
                <td><?php echo htmlspecialchars($categoria['nome']); ?></td>
                <td><?php echo htmlspecialchars($categoria['descricao']); ?></td>
                <td>
                  <a href="/categoria/editar/<?php echo $categoria['id']; ?>" class="btn-sm">Editar</a>
                  <a href="/categoria/deletar/<?php echo $categoria['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Tem certeza?');">Excluir</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="3">Nenhuma categoria encontrada.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>

  <?php require_once '../app/Views/layout/footer.php'; ?>
</body>

</html>