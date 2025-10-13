<?php
$title = $conteudo['titulo'];
ob_start();
?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h4 class="mb-0"><?php echo htmlspecialchars($conteudo['titulo']); ?></h4>
              <?php if ($conteudo['categoria_nome']): ?>
                <span class="badge bg-secondary mt-1">
                  <?php echo htmlspecialchars($conteudo['categoria_nome']); ?>
                </span>
              <?php endif; ?>
            </div>
            <div class="btn-group">
              <a href="/conteudo/edit/<?php echo $conteudo['id']; ?>" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>Editar
              </a>
              <a href="/conteudo" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Voltar
              </a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row mb-4">
            <div class="col-md-4">
              <strong>Status:</strong><br>
              <?php
              $statusClass = match ($conteudo['status']) {
                'pendente' => 'bg-warning',
                'em_andamento' => 'bg-primary',
                'concluido' => 'bg-success',
                default => 'bg-secondary'
              };
              $statusLabel = match ($conteudo['status']) {
                'pendente' => 'Pendente',
                'em_andamento' => 'Em Andamento',
                'concluido' => 'Concluído',
                default => $conteudo['status']
              };
              ?>
              <span class="badge <?php echo $statusClass; ?> fs-6">
                <?php echo $statusLabel; ?>
              </span>
            </div>
            <div class="col-md-4">
              <strong>Progresso:</strong><br>
              <div class="progress mt-1" style="height: 25px;">
                <div class="progress-bar" role="progressbar"
                  style="width: <?php echo $conteudo['progresso']; ?>%"
                  aria-valuenow="<?php echo $conteudo['progresso']; ?>"
                  aria-valuemin="0" aria-valuemax="100">
                  <?php echo $conteudo['progresso']; ?>%
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <strong>Tempo Estimado:</strong><br>
              <?php if ($conteudo['tempo_estimado']): ?>
                <span class="text-muted">
                  <i class="fas fa-clock me-1"></i>
                  <?php echo $conteudo['tempo_estimado']; ?> minutos
                </span>
              <?php else: ?>
                <span class="text-muted">Não definido</span>
              <?php endif; ?>
            </div>
          </div>

          <?php if ($conteudo['descricao']): ?>
            <div class="mb-4">
              <strong>Descrição:</strong>
              <div class="mt-2 p-3 bg-light rounded">
                <?php echo nl2br(htmlspecialchars($conteudo['descricao'])); ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if ($conteudo['link']): ?>
            <div class="mb-4">
              <strong>Link:</strong><br>
              <a href="<?php echo htmlspecialchars($conteudo['link']); ?>"
                target="_blank" class="btn btn-outline-primary btn-sm mt-1">
                <i class="fas fa-external-link-alt me-1"></i>
                Acessar Link
              </a>
            </div>
          <?php endif; ?>

          <?php if ($conteudo['anotacoes']): ?>
            <div class="mb-4">
              <strong>Anotações:</strong>
              <div class="mt-2 p-3 bg-light rounded">
                <?php echo nl2br(htmlspecialchars($conteudo['anotacoes'])); ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="row text-muted">
            <div class="col-md-6">
              <small>
                <i class="fas fa-calendar-plus me-1"></i>
                Criado em: <?php echo date('d/m/Y H:i', strtotime($conteudo['criado_em'])); ?>
              </small>
            </div>
            <div class="col-md-6">
              <small>
                <i class="fas fa-calendar-edit me-1"></i>
                Atualizado em: <?php echo date('d/m/Y H:i', strtotime($conteudo['atualizado_em'])); ?>
              </small>
            </div>
          </div>
        </div>
      </div>

      <?php if (!empty($sessoes)): ?>
        <div class="card mt-4">
          <div class="card-header">
            <h5 class="mb-0">
              <i class="fas fa-history me-2"></i>Sessões de Estudo
            </h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>Data</th>
                    <th>Duração</th>
                    <th>Observações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($sessoes as $sessao): ?>
                    <tr>
                      <td>
                        <?php echo date('d/m/Y H:i', strtotime($sessao['data_inicio'])); ?>
                      </td>
                      <td>
                        <?php if ($sessao['duracao']): ?>
                          <?php echo $sessao['duracao']; ?> min
                        <?php else: ?>
                          <span class="text-muted">Em andamento</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php echo htmlspecialchars($sessao['observacoes'] ?? '-'); ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0">
            <i class="fas fa-chart-pie me-2"></i>Estatísticas
          </h6>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <div class="d-flex justify-content-between">
              <span>Total de Sessões:</span>
              <strong><?php echo count($sessoes ?? []); ?></strong>
            </div>
          </div>

          <?php
          $tempoTotal = 0;
          if (!empty($sessoes)) {
            foreach ($sessoes as $sessao) {
              if ($sessao['duracao']) {
                $tempoTotal += $sessao['duracao'];
              }
            }
          }
          ?>

          <div class="mb-3">
            <div class="d-flex justify-content-between">
              <span>Tempo Total:</span>
              <strong><?php echo $tempoTotal; ?> min</strong>
            </div>
          </div>

          <?php if ($conteudo['tempo_estimado'] && $tempoTotal > 0): ?>
            <div class="mb-3">
              <div class="d-flex justify-content-between">
                <span>% do Tempo Estimado:</span>
                <strong>
                  <?php echo round(($tempoTotal / $conteudo['tempo_estimado']) * 100, 1); ?>%
                </strong>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-header">
          <h6 class="mb-0">
            <i class="fas fa-tools me-2"></i>Ações Rápidas
          </h6>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <a href="/sessao/create?conteudo_id=<?php echo $conteudo['id']; ?>"
              class="btn btn-success">
              <i class="fas fa-play me-1"></i>Iniciar Sessão
            </a>

            <button type="button" class="btn btn-warning"
              onclick="atualizarProgresso(<?php echo $conteudo['id']; ?>)">
              <i class="fas fa-percentage me-1"></i>Atualizar Progresso
            </button>

            <button type="button" class="btn btn-outline-danger"
              onclick="confirmarExclusao(<?php echo $conteudo['id']; ?>, '<?php echo htmlspecialchars($conteudo['titulo']); ?>')">
              <i class="fas fa-trash me-1"></i>Excluir
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para atualizar progresso -->
<div class="modal fade" id="modalProgresso" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Atualizar Progresso</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" action="/conteudo/update-progress/<?php echo $conteudo['id']; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <div class="modal-body">
          <div class="mb-3">
            <label for="novo_progresso" class="form-label">Novo Progresso (%)</label>
            <input type="range"
              class="form-range"
              id="novo_progresso"
              name="progresso"
              min="0"
              max="100"
              value="<?php echo $conteudo['progresso']; ?>"
              oninput="this.nextElementSibling.value = this.value + '%'">
            <output class="form-text"><?php echo $conteudo['progresso']; ?>%</output>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Atualizar</button>
        </div>
      </form>
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
  function atualizarProgresso(id) {
    new bootstrap.Modal(document.getElementById('modalProgresso')).show();
  }

  function confirmarExclusao(id, nome) {
    document.getElementById('nomeConteudo').textContent = nome;
    document.getElementById('formExcluir').action = '/conteudo/delete/' + id;
    new bootstrap.Modal(document.getElementById('modalExcluir')).show();
  }
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../Views/layouts/app.php';
?>