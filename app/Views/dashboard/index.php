<!-- Dashboard Principal -->
<div class="row">
  <div class="col-md-12">
    <h1>
      <i class="fas fa-tachometer-alt"></i>
      Dashboard
    </h1>
    <p class="text-muted">Bem-vindo de volta! Acompanhe seu progresso nos estudos.</p>
  </div>
</div>

<!-- Estatísticas Principais -->
<div class="row">
  <div class="col-md-3">
    <div class="stat-card">
      <span class="stat-number"><?= $estatisticas['total_conteudos'] ?></span>
      <span class="stat-label">
        <i class="fas fa-book"></i>
        Total de Conteúdos
      </span>
    </div>
  </div>

  <div class="col-md-3">
    <div class="stat-card">
      <span class="stat-number"><?= number_format($estatisticas['horas_semana'], 1) ?>h</span>
      <span class="stat-label">
        <i class="fas fa-clock"></i>
        Horas esta Semana
      </span>
    </div>
  </div>

  <div class="col-md-3">
    <div class="stat-card">
      <span class="stat-number"><?= $estatisticas['conteudos_concluidos'] ?></span>
      <span class="stat-label">
        <i class="fas fa-check-circle"></i>
        Conteúdos Concluídos
      </span>
    </div>
  </div>

  <div class="col-md-3">
    <div class="stat-card">
      <span class="stat-number"><?= $estatisticas['metas_ativas'] ?></span>
      <span class="stat-label">
        <i class="fas fa-target"></i>
        Metas Ativas
      </span>
    </div>
  </div>
</div>

<!-- Progresso Semanal e Ações Rápidas -->
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-chart-line"></i>
          Progresso Semanal
        </h3>
      </div>

      <div style="height: 200px; display: flex; align-items: end; gap: 10px; padding: 20px;">
        <?php foreach ($progresso_semanal as $dia): ?>
          <div style="flex: 1; text-align: center;">
            <div
              style="
                                height: <?= $dia['horas'] > 0 ? min(($dia['horas'] / 4) * 150, 150) : 5 ?>px;
                                background: linear-gradient(45deg, #667eea, #764ba2);
                                border-radius: 5px;
                                margin-bottom: 5px;
                                display: flex;
                                align-items: end;
                                justify-content: center;
                                color: white;
                                font-size: 0.8rem;
                                font-weight: bold;
                            "
              title="<?= $dia['horas'] ?>h em <?= $dia['data_formatada'] ?>">
              <?php if ($dia['horas'] > 0): ?>
                <?= $dia['horas'] ?>h
              <?php endif; ?>
            </div>
            <small class="text-muted">
              <?= $dia['dia_semana'] ?><br>
              <?= $dia['data_formatada'] ?>
            </small>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-plus"></i>
          Ações Rápidas
        </h3>
      </div>

      <div style="padding: 20px; text-align: center;">
        <a href="/conteudos/criar" class="btn btn-primary btn-sm mb-2" style="width: 100%;">
          <i class="fas fa-plus"></i>
          Novo Conteúdo
        </a>

        <a href="/sessoes/iniciar" class="btn btn-success btn-sm mb-2" style="width: 100%;">
          <i class="fas fa-play"></i>
          Iniciar Sessão
        </a>

        <a href="/metas/criar" class="btn btn-warning btn-sm mb-2" style="width: 100%;">
          <i class="fas fa-target"></i>
          Nova Meta
        </a>

        <hr>

        <div class="text-center">
          <h4>Hoje</h4>
          <p class="text-muted">
            <strong><?= number_format($estatisticas['horas_hoje'], 1) ?>h</strong> estudadas
          </p>

          <?php if ($estatisticas['media_horas_dia'] > 0): ?>
            <small class="text-muted">
              Média: <?= $estatisticas['media_horas_dia'] ?>h/dia
            </small>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Conteúdos Recentes e Metas Ativas -->
<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">
          <i class="fas fa-book"></i>
          Conteúdos Recentes
        </h3>
        <a href="/conteudos" class="btn btn-primary btn-sm">
          Ver Todos
        </a>
      </div>

      <?php if (!empty($conteudos_recentes)): ?>
        <div class="table">
          <?php foreach ($conteudos_recentes as $conteudo): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; border-bottom: 1px solid #eee;">
              <div>
                <h4 style="margin: 0; font-size: 1rem;">
                  <?= htmlspecialchars($conteudo['titulo']) ?>
                </h4>
                <?php if ($conteudo['categoria_nome']): ?>
                  <small class="badge badge-info">
                    <?= htmlspecialchars($conteudo['categoria_nome']) ?>
                  </small>
                <?php endif; ?>
              </div>

              <div>
                <?php
                $badgeClass = [
                  'pendente' => 'badge-secondary',
                  'em_andamento' => 'badge-warning',
                  'concluido' => 'badge-success'
                ][$conteudo['status']] ?? 'badge-secondary';

                $statusNome = [
                  'pendente' => 'Pendente',
                  'em_andamento' => 'Em Andamento',
                  'concluido' => 'Concluído'
                ][$conteudo['status']] ?? 'Indefinido';
                ?>
                <span class="badge <?= $badgeClass ?>">
                  <?= $statusNome ?>
                </span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div style="text-align: center; padding: 40px;">
          <i class="fas fa-book fa-3x text-muted"></i>
          <p class="text-muted mt-2">
            Nenhum conteúdo cadastrado ainda.
            <br>
            <a href="/conteudos/criar" class="text-primary">Crie seu primeiro conteúdo!</a>
          </p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">
          <i class="fas fa-target"></i>
          Metas Ativas
        </h3>
        <a href="/metas" class="btn btn-primary btn-sm">
          Ver Todas
        </a>
      </div>

      <?php if (!empty($metas_ativas)): ?>
        <div style="padding: 20px;">
          <?php foreach (array_slice($metas_ativas, 0, 3) as $meta): ?>
            <div style="margin-bottom: 20px;">
              <div class="d-flex justify-content-between align-items-center">
                <h4 style="margin: 0; font-size: 1rem;">
                  <?= htmlspecialchars($meta['titulo']) ?>
                </h4>
                <small class="text-muted">
                  <?= date('d/m/Y', strtotime($meta['data_alvo'])) ?>
                </small>
              </div>

              <div class="progress">
                <div
                  class="progress-bar"
                  style="width: <?= $meta['progresso_atual'] ?>%">
                  <?= number_format($meta['progresso_atual'], 1) ?>%
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div style="text-align: center; padding: 40px;">
          <i class="fas fa-target fa-3x text-muted"></i>
          <p class="text-muted mt-2">
            Nenhuma meta definida ainda.
            <br>
            <a href="/metas/criar" class="text-primary">Crie sua primeira meta!</a>
          </p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Sessões Recentes -->
<?php if (!empty($sessoes_recentes)): ?>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <h3 class="card-title">
            <i class="fas fa-clock"></i>
            Últimas Sessões de Estudo
          </h3>
          <a href="/sessoes" class="btn btn-primary btn-sm">
            Ver Todas
          </a>
        </div>

        <table class="table">
          <thead>
            <tr>
              <th>Conteúdo</th>
              <th>Categoria</th>
              <th>Data/Hora</th>
              <th>Duração</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach (array_slice($sessoes_recentes, 0, 5) as $sessao): ?>
              <tr>
                <td><?= htmlspecialchars($sessao['conteudo_titulo']) ?></td>
                <td>
                  <?php if ($sessao['categoria_nome']): ?>
                    <span class="badge badge-info">
                      <?= htmlspecialchars($sessao['categoria_nome']) ?>
                    </span>
                  <?php else: ?>
                    <span class="text-muted">Sem categoria</span>
                  <?php endif; ?>
                </td>
                <td><?= date('d/m/Y H:i', strtotime($sessao['data_inicio'])) ?></td>
                <td>
                  <?php if ($sessao['duracao_minutos']): ?>
                    <?php
                    $horas = floor($sessao['duracao_minutos'] / 60);
                    $minutos = $sessao['duracao_minutos'] % 60;
                    ?>
                    <?= $horas > 0 ? "{$horas}h {$minutos}m" : "{$minutos}m" ?>
                  <?php else: ?>
                    <span class="badge badge-warning">Em andamento</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>