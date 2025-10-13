<?php
$title = 'Novo Conteúdo de Estudo';
ob_start();
?>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
              <i class="fas fa-plus-circle me-2"></i><?php echo $title; ?>
            </h4>
            <a href="/conteudo" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-arrow-left me-1"></i>Voltar
            </a>
          </div>
        </div>
        <div class="card-body">
          <form method="post" action="/conteudo/create">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <div class="row">
              <div class="col-md-8">
                <div class="mb-3">
                  <label for="titulo" class="form-label">
                    Título <span class="text-danger">*</span>
                  </label>
                  <input type="text"
                    class="form-control"
                    id="titulo"
                    name="titulo"
                    value="<?php echo htmlspecialchars($_POST['titulo'] ?? ''); ?>"
                    required>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="categoria_id" class="form-label">Categoria</label>
                  <select class="form-select" id="categoria_id" name="categoria_id">
                    <option value="">Selecione uma categoria</option>
                    <?php foreach ($categorias as $categoria): ?>
                      <option value="<?php echo $categoria['id']; ?>"
                        <?php echo ($_POST['categoria_id'] ?? '') == $categoria['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($categoria['nome']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="status" class="form-label">Status</label>
                  <select class="form-select" id="status" name="status">
                    <?php foreach ($status_opcoes as $valor => $label): ?>
                      <option value="<?php echo $valor; ?>"
                        <?php echo ($_POST['status'] ?? 'pendente') === $valor ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($label); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="progresso" class="form-label">Progresso (%)</label>
                  <input type="number"
                    class="form-control"
                    id="progresso"
                    name="progresso"
                    min="0"
                    max="100"
                    value="<?php echo htmlspecialchars($_POST['progresso'] ?? '0'); ?>">
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="descricao" class="form-label">Descrição</label>
              <textarea class="form-control"
                id="descricao"
                name="descricao"
                rows="4"
                placeholder="Descreva o conteúdo do estudo..."><?php echo htmlspecialchars($_POST['descricao'] ?? ''); ?></textarea>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="link" class="form-label">Link/URL</label>
                  <input type="url"
                    class="form-control"
                    id="link"
                    name="link"
                    value="<?php echo htmlspecialchars($_POST['link'] ?? ''); ?>"
                    placeholder="https://exemplo.com">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="tempo_estimado" class="form-label">Tempo Estimado (minutos)</label>
                  <input type="number"
                    class="form-control"
                    id="tempo_estimado"
                    name="tempo_estimado"
                    min="1"
                    value="<?php echo htmlspecialchars($_POST['tempo_estimado'] ?? ''); ?>"
                    placeholder="60">
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="anotacoes" class="form-label">Anotações</label>
              <textarea class="form-control"
                id="anotacoes"
                name="anotacoes"
                rows="3"
                placeholder="Anotações pessoais sobre o conteúdo..."><?php echo htmlspecialchars($_POST['anotacoes'] ?? ''); ?></textarea>
            </div>

            <div class="d-flex justify-content-between">
              <a href="/conteudo" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i>Cancelar
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Salvar Conteúdo
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Atualizar progresso automaticamente baseado no status
  document.getElementById('status').addEventListener('change', function() {
    const progressoInput = document.getElementById('progresso');
    switch (this.value) {
      case 'pendente':
        progressoInput.value = 0;
        break;
      case 'concluido':
        progressoInput.value = 100;
        break;
        // Em andamento mantém o valor atual ou define um padrão
      case 'em_andamento':
        if (progressoInput.value == 0 || progressoInput.value == 100) {
          progressoInput.value = 50;
        }
        break;
    }
  });

  // Validação do formulário
  document.querySelector('form').addEventListener('submit', function(e) {
    const titulo = document.getElementById('titulo').value.trim();
    if (!titulo) {
      e.preventDefault();
      alert('O título é obrigatório!');
      document.getElementById('titulo').focus();
      return false;
    }
  });
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../Views/layouts/app.php';
?>