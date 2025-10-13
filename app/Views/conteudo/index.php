<!-- Gerenciar Conteúdos de Estudo -->
<div class="row">
  <div class="col-md-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>
        <i class="fas fa-book-open"></i>
        Conteúdos de Estudo
      </h1>
      <a href="/conteudo/create" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Novo Conteúdo
      </a>
    </div>
  </div>
</div>

<!-- Filtros -->
<div class="row mb-4">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <form method="get" class="row g-3">
          <div class="col-md-4">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
              <option value="">Todos os Status</option>
              <?php foreach ($status_opcoes as $valor => $label): ?>
                <option value="<?php echo $valor; ?>"
                  <?php echo ($filtro_status ?? '') === $valor ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($label); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label for="categoria" class="form-label">Categoria</label>
            <select name="categoria" id="categoria" class="form-select">
              <option value="">Todas as Categorias</option>
              <?php foreach ($categorias as $categoria): ?>
                <option value="<?php echo $categoria['id']; ?>"
                  <?php echo ($filtro_categoria ?? '') == $categoria['id'] ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($categoria['nome']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-outline-primary me-2">
              <i class="fas fa-filter me-1"></i>Filtrar
            </button>
            <a href="/conteudo" class="btn btn-outline-secondary">
              <i class="fas fa-times me-1"></i>Limpar
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Contadores -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="stat-card bg-info text-white">
      <span class="stat-number"><?php echo $contadores['total'] ?? 0; ?></span>
      <span class="stat-label">
        <i class="fas fa-book"></i>
        Total
      </span>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card bg-warning text-white">
      <span class="stat-number"><?php echo $contadores['pendente'] ?? 0; ?></span>
      <span class="stat-label">
        <i class="fas fa-clock"></i>
        Pendentes
      </span>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card bg-primary text-white">
      <span class="stat-number"><?php echo $contadores['em_andamento'] ?? 0; ?></span>
      <span class="stat-label">
        <i class="fas fa-play"></i>
        Em Andamento
      </span>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card bg-success text-white">
      <span class="stat-number"><?php echo $contadores['concluido'] ?? 0; ?></span>
      <span class="stat-label">
        <i class="fas fa-check"></i>
        Concluídos
      </span>
    </div>
  </div>
</div>

<!-- Lista de Conteúdos -->
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <?php if (empty($conteudos)): ?>
          <div class="text-center py-5">
            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Nenhum conteúdo encontrado</h5>
            <p class="text-muted">Comece criando seu primeiro conteúdo de estudo.</p>
            <a href="/conteudo/create" class="btn btn-primary">
              <i class="fas fa-plus me-1"></i>Criar Primeiro Conteúdo
            </a>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Título</th>
                  <th>Categoria</th>
                  <th>Status</th>
                  <th>Progresso</th>
                  <th>Criado em</th>
                  <th width="150">Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($conteudos as $conteudo): ?>
                  <tr>
                    <td>
                      <div>
                        <strong><?php echo htmlspecialchars($conteudo['titulo']); ?></strong>
                        <?php if (!empty($conteudo['descricao'])): ?>
                          <br>
                          <small class="text-muted">
                            <?php echo htmlspecialchars(substr($conteudo['descricao'], 0, 100)); ?>
                            <?php echo strlen($conteudo['descricao']) > 100 ? '...' : ''; ?>
                          </small>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td>
                      <?php if (!empty($conteudo['categoria_nome'])): ?>
                        <span class="badge bg-secondary">
                          <?php echo htmlspecialchars($conteudo['categoria_nome']); ?>
                        </span>
                      <?php else: ?>
                        <span class="text-muted">Sem categoria</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php
                      $statusClass = match ($conteudo['status']) {
                        'pendente' => 'bg-warning',
                        'em_andamento' => 'bg-primary',
                        'concluido' => 'bg-success',
                        default => 'bg-secondary'
                      };
                      ?>
                      <span class="badge <?php echo $statusClass; ?>">
                        <?php echo htmlspecialchars($status_opcoes[$conteudo['status']] ?? $conteudo['status']); ?>
                      </span>
                    </td>
                    <td>
                      <?php $progresso = $conteudo['progresso'] ?? 0; ?>
                      <div class="progress" style="height: 20px;">
                        <div class="progress-bar" role="progressbar"
                          style="width: <?php echo $progresso; ?>%"
                          aria-valuenow="<?php echo $progresso; ?>"
                          aria-valuemin="0" aria-valuemax="100">
                          <?php echo $progresso; ?>%
                        </div>
                      </div>
                    </td>
                    <td>
                      <small class="text-muted">
                        <?php echo date('d/m/Y H:i', strtotime($conteudo['criado_em'])); ?>
                      </small>
                    </td>
                    <td>
                      <div class="btn-group btn-group-sm" role="group">
                        <a href="/conteudo/view/<?php echo $conteudo['id']; ?>"
                          class="btn btn-outline-primary" title="Visualizar">
                          <i class="fas fa-eye"></i>
                        </a>
                        <a href="/conteudo/edit/<?php echo $conteudo['id']; ?>"
                          class="btn btn-outline-warning" title="Editar">
                          <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-outline-danger"
                          title="Excluir"
                          onclick="confirmarExclusao(<?php echo $conteudo['id']; ?>, '<?php echo htmlspecialchars($conteudo['titulo']); ?>')">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
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
        <p class="text-muted">Esta ação não pode ser desfeita.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <form method="post" id="formExcluir" style="display: inline;">
          <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?? ''; ?>">
          <input type="hidden" name="_method" value="DELETE">
          <button type="submit" class="btn btn-danger">Excluir</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  function confirmarExclusao(id, nome) {
    document.getElementById('nomeConteudo').textContent = nome;
    document.getElementById('formExcluir').action = '/conteudo/delete/' + id;
    new bootstrap.Modal(document.getElementById('modalExcluir')).show();
  }
</script>