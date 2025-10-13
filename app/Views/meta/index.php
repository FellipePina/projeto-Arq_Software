<!-- Metas de Estudo -->
<div class="row">
  <div class="col-md-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>
        <i class="fas fa-target"></i>
        Metas de Estudo
      </h1>
      <a href="/meta/create" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Nova Meta
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
              <option value="ativa" <?php echo ($filtro_status ?? '') === 'ativa' ? 'selected' : ''; ?>>Ativa</option>
              <option value="concluida" <?php echo ($filtro_status ?? '') === 'concluida' ? 'selected' : ''; ?>>Concluída</option>
              <option value="pausada" <?php echo ($filtro_status ?? '') === 'pausada' ? 'selected' : ''; ?>>Pausada</option>
            </select>
          </div>
          <div class="col-md-4">
            <label for="prazo" class="form-label">Prazo</label>
            <select name="prazo" id="prazo" class="form-select">
              <option value="">Todos os Prazos</option>
              <option value="vencidas" <?php echo ($filtro_prazo ?? '') === 'vencidas' ? 'selected' : ''; ?>>Vencidas</option>
              <option value="esta_semana" <?php echo ($filtro_prazo ?? '') === 'esta_semana' ? 'selected' : ''; ?>>Esta Semana</option>
              <option value="proximo_mes" <?php echo ($filtro_prazo ?? '') === 'proximo_mes' ? 'selected' : ''; ?>>Próximo Mês</option>
            </select>
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-outline-primary me-2">
              <i class="fas fa-filter me-1"></i>Filtrar
            </button>
            <a href="/meta" class="btn btn-outline-secondary">
              <i class="fas fa-times me-1"></i>Limpar
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Estatísticas -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="stat-card bg-info text-white">
      <span class="stat-number"><?php echo $estatisticas['total_metas'] ?? 0; ?></span>
      <span class="stat-label">
        <i class="fas fa-list"></i>
        Total de Metas
      </span>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card bg-success text-white">
      <span class="stat-number"><?php echo $estatisticas['metas_ativas'] ?? 0; ?></span>
      <span class="stat-label">
        <i class="fas fa-target"></i>
        Metas Ativas
      </span>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card bg-primary text-white">
      <span class="stat-number"><?php echo $estatisticas['metas_concluidas'] ?? 0; ?></span>
      <span class="stat-label">
        <i class="fas fa-check-circle"></i>
        Concluídas
      </span>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card bg-warning text-white">
      <span class="stat-number"><?php echo number_format($estatisticas['progresso_medio'] ?? 0, 1); ?>%</span>
      <span class="stat-label">
        <i class="fas fa-chart-line"></i>
        Progresso Médio
      </span>
    </div>
  </div>
</div>

<!-- Lista de Metas -->
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <?php if (empty($metas)): ?>
          <div class="text-center py-5">
            <i class="fas fa-target fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Nenhuma meta encontrada</h5>
            <p class="text-muted">Defina suas metas de estudo para acompanhar seu progresso.</p>
            <a href="/meta/create" class="btn btn-primary">
              <i class="fas fa-plus me-1"></i>Criar Primeira Meta
            </a>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Título</th>
                  <th>Status</th>
                  <th>Progresso</th>
                  <th>Prazo</th>
                  <th>Conteúdos</th>
                  <th width="120">Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($metas as $meta): ?>
                  <tr>
                    <td>
                      <div>
                        <strong><?php echo htmlspecialchars($meta['titulo']); ?></strong>
                        <?php if (!empty($meta['descricao'])): ?>
                          <br>
                          <small class="text-muted">
                            <?php echo htmlspecialchars(substr($meta['descricao'], 0, 60)); ?>
                            <?php echo strlen($meta['descricao']) > 60 ? '...' : ''; ?>
                          </small>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td>
                      <?php
                      $statusClass = match ($meta['status']) {
                        'ativa' => 'bg-success',
                        'concluida' => 'bg-primary',
                        'pausada' => 'bg-warning',
                        default => 'bg-secondary'
                      };
                      ?>
                      <span class="badge <?php echo $statusClass; ?>">
                        <?php echo ucfirst($meta['status']); ?>
                      </span>
                    </td>
                    <td>
                      <?php $progresso = $meta['progresso'] ?? 0; ?>
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
                      <?php if ($meta['data_prazo']): ?>
                        <div>
                          <?php
                          $prazo = new DateTime($meta['data_prazo']);
                          $hoje = new DateTime();
                          $diff = $hoje->diff($prazo);
                          ?>
                          <small><?php echo $prazo->format('d/m/Y'); ?></small>
                          <br>
                          <?php if ($prazo < $hoje): ?>
                            <span class="badge bg-danger">Vencida</span>
                          <?php elseif ($diff->days <= 7): ?>
                            <span class="badge bg-warning">Próxima</span>
                          <?php else: ?>
                            <span class="badge bg-success"><?php echo $diff->days; ?> dias</span>
                          <?php endif; ?>
                        </div>
                      <?php else: ?>
                        <span class="text-muted">Sem prazo</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="badge bg-info"><?php echo $meta['total_conteudos'] ?? 0; ?> conteúdos</span>
                    </td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <a href="/meta/view/<?php echo $meta['id']; ?>"
                          class="btn btn-outline-primary" title="Visualizar">
                          <i class="fas fa-eye"></i>
                        </a>
                        <a href="/meta/edit/<?php echo $meta['id']; ?>"
                          class="btn btn-outline-warning" title="Editar">
                          <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-outline-danger"
                          title="Excluir"
                          onclick="confirmarExclusao(<?php echo $meta['id']; ?>, '<?php echo htmlspecialchars($meta['titulo']); ?>')">
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
        <p>Tem certeza que deseja excluir a meta <strong id="nomeMeta"></strong>?</p>
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
    document.getElementById('nomeMeta').textContent = nome;
    document.getElementById('formExcluir').action = '/meta/delete/' + id;
    new bootstrap.Modal(document.getElementById('modalExcluir')).show();
  }
</script>