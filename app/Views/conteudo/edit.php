<?php
$title = 'Editar Conteúdo - ' . $conteudo['titulo'];
ob_start();
?>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
              <i class="fas fa-edit me-2"></i>Editar Conteúdo
            </h4>
            <div class="btn-group">
              <a href="/conteudo/view/<?php echo $conteudo['id']; ?>" class="btn btn-outline-info btn-sm">
                <i class="fas fa-eye me-1"></i>Visualizar
              </a>
              <a href="/conteudo" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Voltar
              </a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <form method="post" action="/conteudo/edit/<?php echo $conteudo['id']; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="_method" value="PUT">

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
                    value="<?php echo htmlspecialchars($_POST['titulo'] ?? $conteudo['titulo']); ?>"
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
                        <?php echo ($_POST['categoria_id'] ?? $conteudo['categoria_id']) == $categoria['id'] ? 'selected' : ''; ?>>
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
                        <?php echo ($_POST['status'] ?? $conteudo['status']) === $valor ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($label); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="progresso" class="form-label">Progresso (%)</label>
                  <div class="input-group">
                    <input type="range"
                      class="form-range me-3"
                      id="progresso-range"
                      min="0"
                      max="100"
                      value="<?php echo htmlspecialchars($_POST['progresso'] ?? $conteudo['progresso']); ?>"
                      oninput="document.getElementById('progresso').value = this.value">
                    <input type="number"
                      class="form-control"
                      id="progresso"
                      name="progresso"
                      min="0"
                      max="100"
                      value="<?php echo htmlspecialchars($_POST['progresso'] ?? $conteudo['progresso']); ?>"
                      oninput="document.getElementById('progresso-range').value = this.value"
                      style="width: 80px;">
                    <span class="input-group-text">%</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="descricao" class="form-label">Descrição</label>
              <textarea class="form-control"
                id="descricao"
                name="descricao"
                rows="4"
                placeholder="Descreva o conteúdo do estudo..."><?php echo htmlspecialchars($_POST['descricao'] ?? $conteudo['descricao']); ?></textarea>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="link" class="form-label">Link/URL</label>
                  <input type="url"
                    class="form-control"
                    id="link"
                    name="link"
                    value="<?php echo htmlspecialchars($_POST['link'] ?? $conteudo['link']); ?>"
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
                    value="<?php echo htmlspecialchars($_POST['tempo_estimado'] ?? $conteudo['tempo_estimado']); ?>"
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
                placeholder="Anotações pessoais sobre o conteúdo..."><?php echo htmlspecialchars($_POST['anotacoes'] ?? $conteudo['anotacoes']); ?></textarea>
            </div>

            <div class="row">
              <div class="col-md-6">
                <small class="text-muted">
                  <i class="fas fa-calendar-plus me-1"></i>
                  Criado em: <?php echo date('d/m/Y H:i', strtotime($conteudo['criado_em'])); ?>
                </small>
              </div>
              <div class="col-md-6">
                <small class="text-muted">
                  <i class="fas fa-calendar-edit me-1"></i>
                  Última atualização: <?php echo date('d/m/Y H:i', strtotime($conteudo['atualizado_em'])); ?>
                </small>
              </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-between">
              <div class="btn-group">
                <a href="/conteudo/view/<?php echo $conteudo['id']; ?>" class="btn btn-outline-info">
                  <i class="fas fa-eye me-1"></i>Visualizar
                </a>
                <a href="/conteudo" class="btn btn-secondary">
                  <i class="fas fa-times me-1"></i>Cancelar
                </a>
              </div>
              <div class="btn-group">
                <button type="button" class="btn btn-outline-danger"
                  onclick="confirmarExclusao(<?php echo $conteudo['id']; ?>, '<?php echo htmlspecialchars($conteudo['titulo']); ?>')">
                  <i class="fas fa-trash me-1"></i>Excluir
                </button>
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-save me-1"></i>Salvar Alterações
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalExcluir" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmar Exclusão</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Tem certeza que deseja excluir o conteúdo <strong id="nomeConteudo"></strong>?</p>
        <p class="text-muted">Esta ação não pode ser desfeita e todas as sessões relacionadas também serão excluídas.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <form method="post" id="formExcluir" style="display: inline;">
          <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
          <input type="hidden" name="_method" value="DELETE">
          <button type="submit" class="btn btn-danger">Excluir</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  // Atualizar progresso automaticamente baseado no status
  document.getElementById('status').addEventListener('change', function() {
    const progressoInput = document.getElementById('progresso');
    const progressoRange = document.getElementById('progresso-range');

    switch (this.value) {
      case 'pendente':
        progressoInput.value = 0;
        progressoRange.value = 0;
        break;
      case 'concluido':
        progressoInput.value = 100;
        progressoRange.value = 100;
        break;
        // Em andamento mantém o valor atual ou define um padrão
      case 'em_andamento':
        if (progressoInput.value == 0 || progressoInput.value == 100) {
          progressoInput.value = 50;
          progressoRange.value = 50;
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

  function confirmarExclusao(id, nome) {
    document.getElementById('nomeConteudo').textContent = nome;
    document.getElementById('formExcluir').action = '/conteudo/delete/' + id;
    new bootstrap.Modal(document.getElementById('modalExcluir')).show();
  }

  // Auto-resize textareas
  document.querySelectorAll('textarea').forEach(textarea => {
    textarea.addEventListener('input', function() {
      this.style.height = 'auto';
      this.style.height = this.scrollHeight + 'px';
    });
  });
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../Views/layouts/app.php';
?>