<?php

/**
 * View: Gamificação e Conquistas
 * Design: Dashboard de gamificação com conquistas, ranking e estatísticas
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($titulo ?? 'Gamificação') ?> - Sistema de Estudos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
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
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .header-content p {
      color: #64748b;
      font-size: 0.95rem;
    }

    /* Grid Layout */
    .gamification-grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 2rem;
      margin-bottom: 2rem;
    }

    /* Player Card */
    .player-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 24px;
      padding: 2.5rem;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.3);
      position: relative;
      overflow: hidden;
    }

    .player-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: linear-gradient(90deg, #f59e0b, #fbbf24, #f59e0b);
    }

    .player-info {
      display: flex;
      align-items: center;
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .player-avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background: linear-gradient(135deg, #667eea, #764ba2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3rem;
      color: white;
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
      position: relative;
    }

    .level-badge {
      position: absolute;
      bottom: -5px;
      right: -5px;
      width: 45px;
      height: 45px;
      border-radius: 50%;
      background: linear-gradient(135deg, #f59e0b, #fbbf24);
      border: 4px solid white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 1rem;
      color: white;
      box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
    }

    .player-details h2 {
      font-size: 1.75rem;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 0.5rem;
    }

    .player-stats {
      display: flex;
      gap: 2rem;
      color: #64748b;
      font-size: 0.875rem;
    }

    .player-stat {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .player-stat i {
      color: #f59e0b;
    }

    .player-stat strong {
      color: #1e293b;
      font-weight: 600;
    }

    /* Level Progress */
    .level-progress {
      margin-top: 1.5rem;
    }

    .progress-header {
      display: flex;
      justify-content: space-between;
      margin-bottom: 0.75rem;
    }

    .progress-label {
      font-size: 0.875rem;
      font-weight: 600;
      color: #64748b;
    }

    .progress-value {
      font-size: 0.875rem;
      font-weight: 700;
      color: #f59e0b;
    }

    .progress-bar-container {
      width: 100%;
      height: 16px;
      background: #e2e8f0;
      border-radius: 999px;
      overflow: hidden;
      position: relative;
    }

    .progress-bar {
      height: 100%;
      background: linear-gradient(90deg, #f59e0b, #fbbf24);
      border-radius: 999px;
      transition: width 1s cubic-bezier(0.16, 1, 0.3, 1);
      position: relative;
      overflow: hidden;
    }

    .progress-bar::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
      animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
      0% {
        transform: translateX(-100%);
      }

      100% {
        transform: translateX(100%);
      }
    }

    /* Stats Grid */
    .stats-mini-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
      margin-top: 2rem;
    }

    .stat-mini {
      text-align: center;
      padding: 1rem;
      background: linear-gradient(135deg, #f8fafc, #f1f5f9);
      border-radius: 16px;
    }

    .stat-mini-value {
      font-size: 1.75rem;
      font-weight: 800;
      color: #1e293b;
      display: block;
      margin-bottom: 0.25rem;
    }

    .stat-mini-label {
      font-size: 0.75rem;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      font-weight: 600;
    }

    /* Ranking Card */
    .ranking-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 24px;
      padding: 2rem;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .ranking-header {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 1.5rem;
    }

    .ranking-header h3 {
      font-size: 1.25rem;
      font-weight: 700;
      color: #1e293b;
    }

    .ranking-header i {
      font-size: 1.5rem;
      color: #f59e0b;
    }

    .ranking-list {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    .ranking-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem;
      background: #f8fafc;
      border-radius: 12px;
      transition: all 0.3s ease;
    }

    .ranking-item:hover {
      background: #f1f5f9;
      transform: translateX(4px);
    }

    .ranking-item.current-user {
      background: linear-gradient(135deg, #fef3c7, #fde68a);
      border: 2px solid #f59e0b;
    }

    .ranking-position {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 0.875rem;
      flex-shrink: 0;
    }

    .ranking-position.gold {
      background: linear-gradient(135deg, #fbbf24, #f59e0b);
      color: white;
      box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
    }

    .ranking-position.silver {
      background: linear-gradient(135deg, #cbd5e1, #94a3b8);
      color: white;
    }

    .ranking-position.bronze {
      background: linear-gradient(135deg, #fb923c, #f97316);
      color: white;
    }

    .ranking-position.other {
      background: #e2e8f0;
      color: #64748b;
    }

    .ranking-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, #667eea, #764ba2);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      font-size: 0.875rem;
      flex-shrink: 0;
    }

    .ranking-info {
      flex: 1;
    }

    .ranking-name {
      font-weight: 600;
      color: #1e293b;
      font-size: 0.875rem;
    }

    .ranking-level {
      font-size: 0.75rem;
      color: #64748b;
    }

    .ranking-points {
      font-weight: 700;
      color: #f59e0b;
      font-size: 0.875rem;
    }

    /* Conquistas Section */
    .conquistas-section {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 24px;
      padding: 2rem;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .section-header h3 {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1e293b;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .section-header i {
      font-size: 1.75rem;
      color: #8b5cf6;
    }

    .conquistas-stats {
      display: flex;
      gap: 1rem;
      font-size: 0.875rem;
      color: #64748b;
    }

    .conquistas-stats span {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .conquistas-stats strong {
      color: #1e293b;
      font-weight: 700;
    }

    /* Conquistas Grid */
    .conquistas-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 1.5rem;
    }

    .conquista-card {
      background: #f8fafc;
      border-radius: 16px;
      padding: 1.5rem;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      border: 2px solid transparent;
      position: relative;
      overflow: hidden;
    }

    .conquista-card.unlocked {
      background: linear-gradient(135deg, #fef3c7, #fde68a);
      border-color: #f59e0b;
    }

    .conquista-card.locked {
      opacity: 0.6;
    }

    .conquista-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }

    .conquista-icon {
      width: 64px;
      height: 64px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      margin-bottom: 1rem;
      background: white;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .conquista-card.unlocked .conquista-icon {
      background: linear-gradient(135deg, #fbbf24, #f59e0b);
      animation: pulse 2s infinite;
    }

    @keyframes pulse {

      0%,
      100% {
        transform: scale(1);
      }

      50% {
        transform: scale(1.05);
      }
    }

    .conquista-card.locked .conquista-icon {
      filter: grayscale(1);
    }

    .conquista-name {
      font-size: 1.125rem;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 0.5rem;
    }

    .conquista-description {
      font-size: 0.875rem;
      color: #64748b;
      margin-bottom: 1rem;
      line-height: 1.5;
    }

    .conquista-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .conquista-points {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 700;
      color: #f59e0b;
      font-size: 0.875rem;
    }

    .conquista-date {
      font-size: 0.75rem;
      color: #64748b;
    }

    .lock-icon {
      color: #94a3b8;
      font-size: 1.25rem;
    }

    /* Responsive */
    @media (max-width: 1024px) {
      .gamification-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      body {
        padding: 1rem;
      }

      .player-avatar {
        width: 80px;
        height: 80px;
        font-size: 2rem;
      }

      .level-badge {
        width: 35px;
        height: 35px;
        font-size: 0.875rem;
      }

      .stats-mini-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
      }

      .conquistas-grid {
        grid-template-columns: 1fr;
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

    .player-card,
    .ranking-card,
    .conquistas-section {
      animation: slideInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .player-card {
      animation-delay: 0.1s;
    }

    .ranking-card {
      animation-delay: 0.2s;
    }

    .conquistas-section {
      animation-delay: 0.3s;
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <h1><i class="ph-fill ph-game-controller"></i> Gamificação e Conquistas</h1>
        <p>Acompanhe seu progresso, conquistas e posição no ranking</p>
      </div>
    </div>

    <!-- Gamification Grid -->
    <div class="gamification-grid">
      <!-- Player Card -->
      <div class="player-card">
        <div class="player-info">
          <div class="player-avatar">
            <i class="ph-fill ph-user"></i>
            <div class="level-badge"><?= $gamificacao['nivel'] ?? 1 ?></div>
          </div>
          <div class="player-details">
            <h2><?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Jogador') ?></h2>
            <div class="player-stats">
              <div class="player-stat">
                <i class="ph-fill ph-trophy"></i>
                <span>Nível <strong><?= $gamificacao['nivel'] ?? 1 ?></strong></span>
              </div>
              <div class="player-stat">
                <i class="ph-fill ph-star"></i>
                <span><strong><?= number_format($gamificacao['pontos_total'] ?? 0, 0, ',', '.') ?></strong> XP</span>
              </div>
              <div class="player-stat">
                <i class="ph-fill ph-fire"></i>
                <span>Streak de <strong><?= $gamificacao['streak_dias'] ?? 0 ?></strong> dias</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Level Progress -->
        <div class="level-progress">
          <div class="progress-header">
            <span class="progress-label">Progresso para Nível <?= ($gamificacao['nivel'] ?? 1) + 1 ?></span>
            <span class="progress-value"><?= $pontos_atual ?? 0 ?> / 100 XP</span>
          </div>
          <div class="progress-bar-container">
            <div class="progress-bar" style="width: <?= $progresso_nivel ?? 0 ?>%"></div>
          </div>
        </div>

        <!-- Mini Stats -->
        <div class="stats-mini-grid">
          <div class="stat-mini">
            <span class="stat-mini-value"><?= $gamificacao['pomodoros_concluidos'] ?? 0 ?></span>
            <span class="stat-mini-label">Pomodoros</span>
          </div>
          <div class="stat-mini">
            <span class="stat-mini-value"><?= $gamificacao['tarefas_concluidas'] ?? 0 ?></span>
            <span class="stat-mini-label">Tarefas</span>
          </div>
          <div class="stat-mini">
            <span class="stat-mini-value"><?= $gamificacao['melhor_streak'] ?? 0 ?></span>
            <span class="stat-mini-label">Melhor Streak</span>
          </div>
        </div>
      </div>

      <!-- Ranking Card -->
      <div class="ranking-card">
        <div class="ranking-header">
          <h3>Ranking Global</h3>
          <i class="ph-fill ph-ranking"></i>
        </div>
        <div class="ranking-list">
          <?php if (!empty($ranking)): ?>
            <?php foreach ($ranking as $index => $player): ?>
              <?php
              $position = $index + 1;
              $isCurrentUser = ($player['id'] ?? 0) == ($_SESSION['usuario_id'] ?? 0);
              $positionClass = match ($position) {
                1 => 'gold',
                2 => 'silver',
                3 => 'bronze',
                default => 'other'
              };
              ?>
              <div class="ranking-item <?= $isCurrentUser ? 'current-user' : '' ?>">
                <div class="ranking-position <?= $positionClass ?>">
                  <?= $position ?>
                </div>
                <div class="ranking-avatar">
                  <?= strtoupper(substr($player['nome'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="ranking-info">
                  <div class="ranking-name">
                    <?= htmlspecialchars($player['nome'] ?? 'Usuário') ?>
                    <?php if ($isCurrentUser): ?>
                      <i class="ph-fill ph-check-circle" style="color: #10b981;"></i>
                    <?php endif; ?>
                  </div>
                  <div class="ranking-level">Nível <?= $player['nivel'] ?? 1 ?></div>
                </div>
                <div class="ranking-points">
                  <?= number_format($player['pontos_total'] ?? 0, 0, ',', '.') ?> XP
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div style="text-align: center; padding: 2rem; color: #94a3b8;">
              <i class="ph ph-users" style="font-size: 3rem; margin-bottom: 1rem;"></i>
              <p>Ranking em breve!</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Conquistas Section -->
    <div class="conquistas-section">
      <div class="section-header">
        <h3>
          <i class="ph-fill ph-medal"></i>
          Conquistas
        </h3>
        <div class="conquistas-stats">
          <span>
            <i class="ph ph-check-circle"></i>
            <strong><?= count(array_filter($conquistas ?? [], fn($c) => !empty($c['obtida']))) ?></strong> desbloqueadas
          </span>
          <span>
            <i class="ph ph-circle"></i>
            <strong><?= count($conquistas ?? []) ?></strong> totais
          </span>
        </div>
      </div>

      <div class="conquistas-grid">
        <?php if (!empty($conquistas)): ?>
          <?php foreach ($conquistas as $conquista): ?>
            <?php $unlocked = !empty($conquista['obtida']); ?>
            <div class="conquista-card <?= $unlocked ? 'unlocked' : 'locked' ?>">
              <div class="conquista-icon">
                <?= $conquista['icone'] ?? '🏆' ?>
              </div>
              <div class="conquista-name">
                <?= htmlspecialchars($conquista['nome']) ?>
              </div>
              <div class="conquista-description">
                <?= htmlspecialchars($conquista['descricao']) ?>
              </div>
              <div class="conquista-footer">
                <div class="conquista-points">
                  <i class="ph-fill ph-star"></i>
                  <?= $conquista['pontos'] ?> XP
                </div>
                <?php if ($unlocked): ?>
                  <div class="conquista-date">
                    <?= date('d/m/Y', strtotime($conquista['data_obtencao'])) ?>
                  </div>
                <?php else: ?>
                  <i class="ph-fill ph-lock lock-icon"></i>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: #94a3b8;">
            <i class="ph ph-medal" style="font-size: 4rem; margin-bottom: 1rem;"></i>
            <h3 style="margin-bottom: 0.5rem;">Nenhuma conquista disponível</h3>
            <p>Continue estudando para desbloquear conquistas!</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script>
    // Animate progress bar on load
    window.addEventListener('load', () => {
      const progressBar = document.querySelector('.progress-bar');
      if (progressBar) {
        const targetWidth = progressBar.style.width;
        progressBar.style.width = '0%';
        setTimeout(() => {
          progressBar.style.width = targetWidth;
        }, 300);
      }
    });

    // Add hover effect to unlocked conquistas
    document.querySelectorAll('.conquista-card.unlocked').forEach(card => {
      card.addEventListener('mouseenter', () => {
        const icon = card.querySelector('.conquista-icon');
        icon.style.transform = 'rotate(360deg) scale(1.1)';
        icon.style.transition = 'transform 0.6s cubic-bezier(0.16, 1, 0.3, 1)';
      });

      card.addEventListener('mouseleave', () => {
        const icon = card.querySelector('.conquista-icon');
        icon.style.transform = 'rotate(0deg) scale(1)';
      });
    });
  </script>
</body>

</html>