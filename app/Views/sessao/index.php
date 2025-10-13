<!-- Sessões de Estudo -->
<div class="row">
  <div class="col-md-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>
        <i class="fas fa-clock"></i>
        Sessões de Estudo
      </h1>
      <a href="/sessao/iniciar" class="btn btn-primary">
        <i class="fas fa-play me-1"></i>Iniciar Sessão
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
            <label for="conteudo" class="form-label">Conteúdo</label>
            <select name="conteudo" id="conteudo" class="form-select">
              <option value="">Todos os Conteúdos</option>
              <?php foreach ($conteudos as $conteudo): ?>
                <option value="<?php echo $conteudo['id']; ?>"
                  <?php echo ($filtro_conteudo ?? '') == $conteudo['id'] ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($conteudo['titulo']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label for="data" class="form-label">Data</label>
            <input type="date" name="data" id="data" class="form-control"
              value="<?php echo $filtro_data ?? ''; ?>">
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-outline-primary me-2">
              <i class="fas fa-filter me-1"></i>Filtrar
            </button>
            <a href="/sessao" class="btn btn-outline-secondary">
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
      <span class="stat-number"><?php echo $estatisticas['total_sessoes'] ?? 0; ?></span>
      <span class="stat-label">
        <i class="fas fa-list"></i>
        Total de Sessões
      </span>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card bg-primary text-white">
      <span class="stat-number"><?php echo number_format($estatisticas['tempo_total'] ?? 0, 1); ?>h</span>
      <span class="stat-label">
        <i class="fas fa-clock"></i>
        Tempo Total
      </span>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card bg-success text-white">
      <span class="stat-number"><?php echo number_format($estatisticas['media_sessao'] ?? 0, 0); ?>min</span>
      <span class="stat-label">
        <i class="fas fa-chart-line"></i>
        Média por Sessão
      </span>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card bg-warning text-white">
      <span class="stat-number"><?php echo number_format($estatisticas['tempo_semana'] ?? 0, 1); ?>h</span>
      <span class="stat-label">
        <i class="fas fa-calendar-week"></i>
        Esta Semana
      </span>
    </div>
  </div>
</div>

<!-- Lista de Sessões -->
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <?php if (empty($sessoes)): ?>
          <div class="text-center py-5">
            <i class="fas fa-clock fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Nenhuma sessão encontrada</h5>
            <p class="text-muted">Comece uma sessão de estudo agora.</p>
            <a href="/sessao/iniciar" class="btn btn-primary">
              <i class="fas fa-play me-1"></i>Iniciar Primeira Sessão
            </a>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Conteúdo</th>
                  <th>Data/Hora</th>
                  <th>Duração</th>
                  <th>Observações</th>
                  <th width="120">Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($sessoes as $sessao): ?>
                  <tr>
                    <td>
                      <strong><?php echo htmlspecialchars($sessao['conteudo_titulo'] ?? 'N/A'); ?></strong>
                      <?php if (!empty($sessao['categoria_nome'])): ?>
                        <br>
                        <small class="text-muted">
                          <span class="badge bg-secondary">
                            <?php echo htmlspecialchars($sessao['categoria_nome']); ?>
                          </span>
                        </small>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div>
                        <?php echo date('d/m/Y', strtotime($sessao['data_inicio'])); ?>
                        <br>
                        <small class="text-muted">
                          <?php echo date('H:i', strtotime($sessao['data_inicio'])); ?>
                          <?php if ($sessao['data_fim']): ?>
                            - <?php echo date('H:i', strtotime($sessao['data_fim'])); ?>
                          <?php else: ?>
                            <span class="badge bg-warning">Em andamento</span>
                          <?php endif; ?>
                        </small>
                      </div>
                    </td>
                    <td>
                      <?php if ($sessao['duracao']): ?>
                        <span class="badge bg-primary"><?php echo $sessao['duracao']; ?> min</span>
                      <?php else: ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if (!empty($sessao['observacoes'])): ?>
                        <small><?php echo htmlspecialchars(substr($sessao['observacoes'], 0, 50)); ?></small>
                        <?php if (strlen($sessao['observacoes']) > 50): ?>...<?php endif; ?>
                      <?php else: ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <a href="/sessao/view/<?php echo $sessao['id']; ?>"
                          class="btn btn-outline-primary" title="Visualizar">
                          <i class="fas fa-eye"></i>
                        </a>
                        <?php if (!$sessao['data_fim']): ?>
                          <a href="/sessao/cronometro/<?php echo $sessao['id']; ?>"
                            class="btn btn-outline-success" title="Continuar">
                            <i class="fas fa-play"></i>
                          </a>
                        <?php endif; ?>
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