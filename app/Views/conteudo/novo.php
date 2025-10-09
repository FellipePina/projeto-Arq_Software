<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Novo Conteúdo de Estudo</title>
  <link rel="stylesheet" href="/style.css">
</head>

<body>
  <?php require_once '../app/Views/layout/header.php'; ?>

  <main class="container">
    <h2>Adicionar Novo Conteúdo</h2>
    <div class="card">
      <form action="/conteudo/salvar" method="post">
        <div class="form-group">
          <label for="titulo">Título:</label>
          <input type="text" id="titulo" name="titulo" required>
        </div>
        <div class="form-group">
          <label for="descricao">Descrição:</label>
          <textarea id="descricao" name="descricao"></textarea>
        </div>
        <div class="form-group">
          <label for="link">Link (URL):</label>
          <input type="url" id="link" name="link">
        </div>
        <div class="form-group">
          <label for="tipo">Tipo:</label>
          <select id="tipo" name="tipo" required>
            <option value="artigo">Artigo</option>
            <option value="video">Vídeo</option>
            <option value="livro">Livro</option>
            <option value="curso">Curso</option>
            <option value="outro">Outro</option>
          </select>
        </div>
        <div class="form-group">
          <label for="status">Status:</label>
          <select id="status" name="status" required>
            <option value="pendente">Pendente</option>
            <option value="em_andamento">Em Andamento</option>
            <option value="concluido">Concluído</option>
          </select>
        </div>
        <div class="form-group">
          <label for="categoria_id">Categoria:</label>
          <select id="categoria_id" name="categoria_id">
            <option value="">Sem Categoria</option>
            <?php foreach ($categorias as $categoria): ?>
              <option value="<?php echo $categoria['id']; ?>"><?php echo htmlspecialchars($categoria['nome']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="btn">Salvar Conteúdo</button>
      </form>
    </div>
  </main>

  <?php require_once '../app/Views/layout/footer.php'; ?>
</body>

</html>