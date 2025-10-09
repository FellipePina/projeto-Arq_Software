<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerenciar Metas</title>
  <link rel="stylesheet" href="/style.css">
</head>

<body>
  <?php require_once '../app/Views/layout/header.php'; ?>

  <main class="container">
    <h2>Minhas Metas</h2>

    <div class="card">
      <h3>Nova Meta</h3>
      <form action="/meta/salvar" method="post">
        <input type="hidden" name="id" value="">
        <div class="form-group">
          <label for="descricao">Descrição:</label>
          <input type="text" id="descricao" name="descricao" required>
        </div>
        <div class="form-group">
          <label for="tipo">Tipo:</label>
          <select id="tipo" name="tipo" required>
            <option value="diaria">Diária</option>
            <option value="semanal">Semanal</option>
            <option value="mensal">Mensal</option>
          </select>
        </div>
        <div class="form-group">
          <label for="data_inicio">Data de Início:</label>
          <input type="date" id="data_inicio" name="data_inicio" required>
        </div>
        <div class="form-group">
          <label for="data_fim">Data de Fim:</label>
          <input type="date" id="data_fim" name="data_fim" required>
        </div>
        <input type="hidden" name="status" value="ativa">
        <button type="submit" class="btn">Salvar Meta</button>
      </form>
    </div>

    <div class="card">
      <h3>Metas Atuais</h3>
      <table>
        <thead>
          <tr>
            <th>Descrição</th>
            <th>Tipo</th>
            <th>Período</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($metas)): ?>
            <?php foreach ($metas as $meta): ?>
              <tr>
                <td><?php echo htmlspecialchars($meta['descricao']); ?></td>
                <td><?php echo ucfirst(htmlspecialchars($meta['tipo'])); ?></td>
                <td><?php echo date('d/m/Y', strtotime($meta['data_inicio'])); ?> - <?php echo date('d/m/Y', strtotime($meta['data_fim'])); ?></td>
                <td><span class="status status-<?php echo strtolower(htmlspecialchars($meta['status'])); ?>"><?php echo ucfirst(htmlspecialchars($meta['status'])); ?></span></td>
                <td>
                  <a href="/meta/editar/<?php echo $meta['id']; ?>" class="btn-sm">Editar</a>
                  <a href="/meta/deletar/<?php echo $meta['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Tem certeza?');">Excluir</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5">Nenhuma meta encontrada.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>

  <?php require_once '../app/Views/layout/footer.php'; ?>
</body>

</html>