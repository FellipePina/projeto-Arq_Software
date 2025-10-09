<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meus Conteúdos de Estudo</title>
  <link rel="stylesheet" href="/style.css">
</head>

<body>
  <?php require_once '../app/Views/layout/header.php'; ?>

  <main class="container">
    <h2>Meus Conteúdos de Estudo</h2>
    <a href="/conteudo/novo" class="btn">Adicionar Novo Conteúdo</a>

    <div class="card-container">
      <?php if (!empty($conteudos)): ?>
        <?php foreach ($conteudos as $conteudo): ?>
          <div class="card">
            <h3><?php echo htmlspecialchars($conteudo['titulo']); ?></h3>
            <p><strong>Categoria:</strong> <?php echo htmlspecialchars($conteudo['categoria_nome'] ?? 'Sem categoria'); ?></p>
            <p><strong>Tipo:</strong> <?php echo ucfirst(htmlspecialchars($conteudo['tipo'])); ?></p>
            <p><strong>Status:</strong> <span class="status status-<?php echo strtolower(htmlspecialchars($conteudo['status'])); ?>"><?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($conteudo['status']))); ?></span></p>
            <div class="card-actions">
              <a href="/conteudo/editar/<?php echo $conteudo['id']; ?>" class="btn-sm">Editar</a>
              <a href="/conteudo/deletar/<?php echo $conteudo['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este conteúdo?');">Excluir</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Nenhum conteúdo de estudo encontrado. Que tal <a href="/conteudo/novo">adicionar um novo</a>?</p>
      <?php endif; ?>
    </div>
  </main>

  <?php require_once '../app/Views/layout/footer.php'; ?>
</body>

</html>