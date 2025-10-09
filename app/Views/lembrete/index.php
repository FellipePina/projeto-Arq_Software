<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerenciar Lembretes</title>
  <link rel="stylesheet" href="/style.css">
</head>

<body>
  <?php require_once '../app/Views/layout/header.php'; ?>

  <main class="container">
    <h2>Meus Lembretes</h2>

    <div class="card">
      <h3>Novo Lembrete</h3>
      <form action="/lembrete/salvar" method="post">
        <div class="form-group">
          <label for="titulo">Título:</label>
          <input type="text" id="titulo" name="titulo" required>
        </div>
        <div class="form-group">
          <label for="data_lembrete">Data e Hora:</label>
          <input type="datetime-local" id="data_lembrete" name="data_lembrete" required>
        </div>
        <div class="form-group">
          <label for="conteudo_id">Associar ao Conteúdo (Opcional):</label>
          <select id="conteudo_id" name="conteudo_id">
            <option value="">Nenhum</option>
            <?php foreach ($conteudos as $conteudo): ?>
              <option value="<?php echo $conteudo['id']; ?>"><?php echo htmlspecialchars($conteudo['titulo']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="btn">Salvar Lembrete</button>
      </form>
    </div>

    <div class="card">
      <h3>Lembretes Pendentes</h3>
      <table>
        <thead>
          <tr>
            <th>Título</th>
            <th>Data</th>
            <th>Conteúdo Associado</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($lembretes)): ?>
            <?php foreach ($lembretes as $lembrete): ?>
              <tr>
                <td><?php echo htmlspecialchars($lembrete['titulo']); ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($lembrete['data_lembrete'])); ?></td>
                <td><?php echo htmlspecialchars($lembrete['conteudo_titulo'] ?? 'N/A'); ?></td>
                <td>
                  <a href="/lembrete/deletar/<?php echo $lembrete['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Tem certeza?');">Excluir</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4">Nenhum lembrete encontrado.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>

  <?php require_once '../app/Views/layout/footer.php'; ?>
</body>

</html>