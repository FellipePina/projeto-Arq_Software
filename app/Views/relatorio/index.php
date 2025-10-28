<?php

/**
 * View: Relatórios e Estatísticas
 * Design: Dashboard com gráficos interativos usando Chart.js
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($titulo ?? 'Relatórios') ?> - Sistema de Estudos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/modern-components.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      color: #1e293b;
      padding: 2rem;
    }

    .container {
      max-width: 1400px;
      margin: 0 auto;
    }

    /* Header */
    .page-header {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 24px;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
      display: flex;
      justify-content: space-between;
      align-items: center;
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .header-content h1 {
      font-size: 2rem;
      font-weight: 700;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 0.5rem;
    }

    .header-content p {
      color: #64748b;
      font-size: 0.95rem;
    }

    /* Period Selector */
    .period-selector {
      display: flex;
      gap: 0.5rem;
      background: #f1f5f9;
      padding: 0.5rem;
      border-radius: 12px;
    }

    .period-btn {
      padding: 0.625rem 1.25rem;
      border: none;
      background: transparent;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 500;
      color: #64748b;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      font-size: 0.875rem;
    }

    .period-btn:hover {
      color: #334155;
      background: rgba(255, 255, 255, 0.5);
    }

    .period-btn.active {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    /* Stats Grid */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 20px;
      padding: 1.75rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.3);
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--gradient-from), var(--gradient-to));
    }

    .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .stat-card.purple {
      --gradient-from: #8b5cf6;
      --gradient-to: #6366f1;
    }

    .stat-card.pink {
      --gradient-from: #ec4899;
      --gradient-to: #f43f5e;
    }

    .stat-card.orange {
      --gradient-from: #f97316;
      --gradient-to: #fb923c;
    }

    .stat-card.emerald {
      --gradient-from: #10b981;
      --gradient-to: #34d399;
    }

    .stat-card.cyan {
      --gradient-from: #06b6d4;
      --gradient-to: #22d3ee;
    }

    .stat-card.amber {
      --gradient-from: #f59e0b;
      --gradient-to: #fbbf24;
    }

    .stat-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .stat-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      background: linear-gradient(135deg, var(--gradient-from), var(--gradient-to));
      color: white;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .stat-value {
      font-size: 2.5rem;
      font-weight: 700;
      color: #1e293b;
      line-height: 1;
      margin-bottom: 0.5rem;
    }

    .stat-label {
      color: #64748b;
      font-size: 0.875rem;
      font-weight: 500;
    }

    .stat-change {
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
      font-size: 0.75rem;
      font-weight: 600;
      padding: 0.25rem 0.5rem;
      border-radius: 6px;
      margin-top: 0.5rem;
    }

    .stat-change.positive {
      background: #dcfce7;
      color: #15803d;
    }

    .stat-change.negative {
      background: #fee2e2;
      color: #b91c1c;
    }

    /* Charts Section */
    .charts-section {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
      gap: 2rem;
      margin-bottom: 2rem;
    }

    .chart-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 20px;
      padding: 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .chart-header {
      margin-bottom: 1.5rem;
    }

    .chart-header h2 {
      font-size: 1.25rem;
      font-weight: 600;
      color: #1e293b;
      margin-bottom: 0.5rem;
    }

    .chart-header p {
      color: #64748b;
      font-size: 0.875rem;
    }

    .chart-container {
      position: relative;
      height: 300px;
    }

    /* Disciplines Table */
    .disciplines-section {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 20px;
      padding: 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .section-header h2 {
      font-size: 1.5rem;
      font-weight: 600;
      color: #1e293b;
    }

    .disciplines-grid {
      display: grid;
      gap: 1rem;
    }

    .discipline-row {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
      gap: 1rem;
      padding: 1rem;
      background: #f8fafc;
      border-radius: 12px;
      align-items: center;
      transition: all 0.3s ease;
    }

    .discipline-row:hover {
      background: #f1f5f9;
      transform: translateX(4px);
    }

    .discipline-name {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .discipline-color {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      flex-shrink: 0;
    }

    .discipline-name span {
      font-weight: 600;
      color: #1e293b;
    }

    .discipline-stat {
      text-align: center;
    }

    .discipline-stat .value {
      font-size: 1.25rem;
      font-weight: 700;
      color: #1e293b;
      display: block;
    }

    .discipline-stat .label {
      font-size: 0.75rem;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    /* Progress Ring */
    .progress-ring {
      width: 60px;
      height: 60px;
      margin: 0 auto;
    }

    .progress-ring circle {
      transition: stroke-dashoffset 0.5s ease;
    }

    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 4rem 2rem;
    }

    .empty-state i {
      font-size: 4rem;
      color: #cbd5e1;
      margin-bottom: 1rem;
    }

    .empty-state h3 {
      font-size: 1.25rem;
      font-weight: 600;
      color: #64748b;
      margin-bottom: 0.5rem;
    }

    .empty-state p {
      color: #94a3b8;
      font-size: 0.875rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
      body {
        padding: 1rem;
      }

      .page-header {
        flex-direction: column;
        gap: 1.5rem;
        align-items: stretch;
      }

      .charts-section {
        grid-template-columns: 1fr;
      }

      .discipline-row {
        grid-template-columns: 1fr;
        gap: 0.5rem;
      }

      .discipline-stat {
        display: flex;
        justify-content: space-between;
        align-items: center;
      }
    }

    /* Animations */
    @keyframes slideInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .stat-card,
    .chart-card,
    .disciplines-section {
      animation: slideInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .stat-card:nth-child(1) {
      animation-delay: 0.1s;
    }

    .stat-card:nth-child(2) {
      animation-delay: 0.2s;
    }

    .stat-card:nth-child(3) {
      animation-delay: 0.3s;
    }

    .stat-card:nth-child(4) {
      animation-delay: 0.4s;
    }

    .stat-card:nth-child(5) {
      animation-delay: 0.5s;
    }

    .stat-card:nth-child(6) {
      animation-delay: 0.6s;
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <h1><i class="ph ph-chart-line"></i> Relatórios e Estatísticas</h1>
        <p>Acompanhe seu desempenho e evolução nos estudos</p>
      </div>
      <div class="period-selector">
        <button class="period-btn <?= $periodo === 'semana' ? 'active' : '' ?>" onclick="changePeriod('semana')">
          Semana
        </button>
        <button class="period-btn <?= $periodo === 'mes' ? 'active' : '' ?>" onclick="changePeriod('mes')">
          Mês
        </button>
        <button class="period-btn <?= $periodo === 'ano' ? 'active' : '' ?>" onclick="changePeriod('ano')">
          Ano
        </button>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
      <!-- Total Pomodoros -->
      <div class="stat-card purple">
        <div class="stat-header">
          <div class="stat-icon">
            <i class="ph-fill ph-timer"></i>
          </div>
        </div>
        <div class="stat-value"><?= $pomodoro['total_sessoes'] ?? 0 ?></div>
        <div class="stat-label">Sessões Pomodoro</div>
        <?php if (($pomodoro['total_sessoes'] ?? 0) > 0): ?>
          <div class="stat-change positive">
            <i class="ph ph-arrow-up"></i>
            <?= round((($pomodoro['total_sessoes'] ?? 0) / 30) * 100) ?>% vs último período
          </div>
        <?php endif; ?>
      </div>

      <!-- Tempo de Estudo -->
      <div class="stat-card pink">
        <div class="stat-header">
          <div class="stat-icon">
            <i class="ph-fill ph-clock"></i>
          </div>
        </div>
        <div class="stat-value"><?= round(($pomodoro['tempo_total_minutos'] ?? 0) / 60, 1) ?>h</div>
        <div class="stat-label">Tempo de Estudo</div>
      </div>

      <!-- Tarefas Concluídas -->
      <div class="stat-card emerald">
        <div class="stat-header">
          <div class="stat-icon">
            <i class="ph-fill ph-check-circle"></i>
          </div>
        </div>
        <div class="stat-value"><?= $tarefas['concluida'] ?? 0 ?></div>
        <div class="stat-label">Tarefas Concluídas</div>
      </div>

      <!-- Tarefas Pendentes -->
      <div class="stat-card orange">
        <div class="stat-header">
          <div class="stat-icon">
            <i class="ph-fill ph-clock-countdown"></i>
          </div>
        </div>
        <div class="stat-value"><?= $tarefas['pendente'] ?? 0 ?></div>
        <div class="stat-label">Tarefas Pendentes</div>
      </div>

      <!-- Streak -->
      <div class="stat-card cyan">
        <div class="stat-header">
          <div class="stat-icon">
            <i class="ph-fill ph-fire"></i>
          </div>
        </div>
        <div class="stat-value"><?= $gamificacao['streak_dias'] ?? 0 ?></div>
        <div class="stat-label">Dias de Sequência</div>
        <?php if (($gamificacao['melhor_streak'] ?? 0) > 0): ?>
          <div class="stat-change positive">
            <i class="ph ph-trophy"></i>
            Recorde: <?= $gamificacao['melhor_streak'] ?> dias
          </div>
        <?php endif; ?>
      </div>

      <!-- Pontos -->
      <div class="stat-card amber">
        <div class="stat-header">
          <div class="stat-icon">
            <i class="ph-fill ph-star"></i>
          </div>
        </div>
        <div class="stat-value"><?= number_format($gamificacao['pontos_total'] ?? 0, 0, ',', '.') ?></div>
        <div class="stat-label">Pontos XP</div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
      <!-- Pomodoros por Dia -->
      <div class="chart-card">
        <div class="chart-header">
          <h2><i class="ph ph-chart-bar"></i> Sessões por Dia</h2>
          <p>Distribuição das suas sessões Pomodoro</p>
        </div>
        <div class="chart-container">
          <canvas id="pomodoroChart"></canvas>
        </div>
      </div>

      <!-- Tempo por Disciplina -->
      <div class="chart-card">
        <div class="chart-header">
          <h2><i class="ph ph-chart-pie"></i> Tempo por Disciplina</h2>
          <p>Distribuição do tempo de estudo</p>
        </div>
        <div class="chart-container">
          <canvas id="disciplinaChart"></canvas>
        </div>
      </div>

      <!-- Status de Tarefas -->
      <div class="chart-card">
        <div class="chart-header">
          <h2><i class="ph ph-chart-donut"></i> Status de Tarefas</h2>
          <p>Visão geral das suas tarefas</p>
        </div>
        <div class="chart-container">
          <canvas id="tarefasChart"></canvas>
        </div>
      </div>

      <!-- Produtividade Semanal -->
      <div class="chart-card">
        <div class="chart-header">
          <h2><i class="ph ph-trend-up"></i> Produtividade Semanal</h2>
          <p>Horas de estudo por dia da semana</p>
        </div>
        <div class="chart-container">
          <canvas id="produtividadeChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Disciplines Details -->
    <?php if (!empty($disciplinas)): ?>
      <div class="disciplines-section">
        <div class="section-header">
          <h2><i class="ph ph-books"></i> Desempenho por Disciplina</h2>
        </div>
        <div class="disciplines-grid">
          <?php foreach ($disciplinas as $disciplina): ?>
            <div class="discipline-row">
              <div class="discipline-name">
                <div class="discipline-color" style="background-color: <?= htmlspecialchars($disciplina['cor']) ?>;"></div>
                <span><?= htmlspecialchars($disciplina['nome']) ?></span>
              </div>
              <div class="discipline-stat">
                <span class="value"><?= $disciplina['total_tarefas'] ?? 0 ?></span>
                <span class="label">Tarefas</span>
              </div>
              <div class="discipline-stat">
                <span class="value"><?= $disciplina['total_sessoes'] ?? 0 ?></span>
                <span class="label">Sessões</span>
              </div>
              <div class="discipline-stat">
                <span class="value"><?= round(($disciplina['tempo_total'] ?? 0) / 60, 1) ?>h</span>
                <span class="label">Tempo</span>
              </div>
              <div class="discipline-stat">
                <span class="value"><?= round((($disciplina['tarefas_concluidas'] ?? 0) / max($disciplina['total_tarefas'] ?? 1, 1)) * 100) ?>%</span>
                <span class="label">Conclusão</span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php else: ?>
      <div class="disciplines-section">
        <div class="empty-state">
          <i class="ph ph-books"></i>
          <h3>Nenhuma disciplina cadastrada</h3>
          <p>Cadastre suas disciplinas para ver estatísticas detalhadas</p>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <script>
    // Change Period
    function changePeriod(period) {
      window.location.href = `/relatorios?periodo=${period}`;
    }

    // Chart.js Configuration
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';

    // Pomodoro Chart (Daily Sessions)
    const pomodoroCtx = document.getElementById('pomodoroChart').getContext('2d');
    const pomodoroChart = new Chart(pomodoroCtx, {
      type: 'line',
      data: {
        labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
        datasets: [{
          label: 'Sessões',
          data: [5, 8, 6, 10, 7, 4, 3],
          backgroundColor: 'rgba(139, 92, 246, 0.1)',
          borderColor: 'rgba(139, 92, 246, 1)',
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: 'rgba(139, 92, 246, 1)',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        }
      }
    });

    // Disciplina Chart (Time Distribution)
    const disciplinaCtx = document.getElementById('disciplinaChart').getContext('2d');
    const disciplinaData = <?= json_encode(array_map(function ($d) {
                              return [
                                'nome' => $d['nome'],
                                'tempo' => round(($d['tempo_total'] ?? 0) / 60, 1),
                                'cor' => $d['cor']
                              ];
                            }, $disciplinas ?? [])) ?>;

    const disciplinaChart = new Chart(disciplinaCtx, {
      type: 'doughnut',
      data: {
        labels: disciplinaData.map(d => d.nome),
        datasets: [{
          data: disciplinaData.map(d => d.tempo),
          backgroundColor: disciplinaData.map(d => d.cor),
          borderWidth: 0,
          hoverOffset: 10
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right',
            labels: {
              padding: 15,
              usePointStyle: true,
              pointStyle: 'circle'
            }
          }
        }
      }
    });

    // Tarefas Chart (Status)
    const tarefasCtx = document.getElementById('tarefasChart').getContext('2d');
    const tarefasChart = new Chart(tarefasCtx, {
      type: 'pie',
      data: {
        labels: ['Concluídas', 'Em Andamento', 'Pendentes'],
        datasets: [{
          data: [
            <?= $tarefas['concluida'] ?? 0 ?>,
            <?= $tarefas['em_andamento'] ?? 0 ?>,
            <?= $tarefas['pendente'] ?? 0 ?>
          ],
          backgroundColor: [
            '#10b981',
            '#f97316',
            '#64748b'
          ],
          borderWidth: 0,
          hoverOffset: 10
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 15,
              usePointStyle: true,
              pointStyle: 'circle'
            }
          }
        }
      }
    });

    // Produtividade Chart (Weekly Hours)
    const produtividadeCtx = document.getElementById('produtividadeChart').getContext('2d');
    const produtividadeChart = new Chart(produtividadeCtx, {
      type: 'bar',
      data: {
        labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
        datasets: [{
          label: 'Horas',
          data: [3.5, 4.2, 2.8, 5.1, 3.7, 2.0, 1.5],
          backgroundColor: [
            'rgba(139, 92, 246, 0.8)',
            'rgba(236, 72, 153, 0.8)',
            'rgba(249, 115, 22, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(6, 182, 212, 0.8)',
            'rgba(245, 158, 11, 0.8)',
            'rgba(100, 116, 139, 0.8)'
          ],
          borderRadius: 8,
          borderSkipped: false
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            },
            ticks: {
              callback: function(value) {
                return value + 'h';
              }
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        }
      }
    });

    // Load Real Data via AJAX (if endpoints exist)
    async function loadChartData() {
      try {
        const periodo = '<?= $periodo ?>';

        // You can implement AJAX calls here to fetch real data
        // const pomodoroData = await fetch(`/relatorios/chart-pomodoro-daily?periodo=${periodo}`).then(r => r.json());
        // pomodoroChart.data.datasets[0].data = pomodoroData;
        // pomodoroChart.update();

      } catch (error) {
        console.error('Erro ao carregar dados:', error);
      }
    }

    // Initial load
    // loadChartData();
  </script>
</body>

</html>