<?php

/**
 * View: Configurações do Usuário
 * Design: Painel de configurações com seções organizadas
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($titulo ?? 'Configurações') ?> - Sistema de Estudos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
      max-width: 1200px;
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

    /* Settings Grid */
    .settings-grid {
      display: grid;
      grid-template-columns: 250px 1fr;
      gap: 2rem;
    }

    /* Navigation Tabs */
    .settings-nav {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 20px;
      padding: 1.5rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.3);
      height: fit-content;
    }

    .nav-title {
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      color: #94a3b8;
      margin-bottom: 1rem;
      padding: 0 0.75rem;
    }

    .nav-item {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.875rem 0.75rem;
      border-radius: 12px;
      cursor: pointer;
      transition: all 0.3s ease;
      color: #64748b;
      font-weight: 500;
      font-size: 0.875rem;
      text-decoration: none;
      margin-bottom: 0.5rem;
    }

    .nav-item i {
      font-size: 1.25rem;
    }

    .nav-item:hover {
      background: #f1f5f9;
      color: #1e293b;
    }

    .nav-item.active {
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    /* Settings Content */
    .settings-content {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 20px;
      padding: 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .section-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 0.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .section-title i {
      font-size: 1.75rem;
      color: #667eea;
    }

    .section-description {
      color: #64748b;
      font-size: 0.875rem;
      margin-bottom: 2rem;
    }

    /* Form Groups */
    .form-section {
      margin-bottom: 2.5rem;
    }

    .form-section:last-child {
      margin-bottom: 0;
    }

    .form-section-title {
      font-size: 1.125rem;
      font-weight: 600;
      color: #1e293b;
      margin-bottom: 1.5rem;
      padding-bottom: 0.75rem;
      border-bottom: 2px solid #e2e8f0;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-label {
      display: block;
      font-weight: 600;
      color: #334155;
      margin-bottom: 0.5rem;
      font-size: 0.875rem;
    }

    .form-hint {
      display: block;
      font-size: 0.75rem;
      color: #94a3b8;
      margin-top: 0.25rem;
    }

    .form-input {
      width: 100%;
      padding: 0.875rem 1rem;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      font-size: 0.875rem;
      font-family: inherit;
      transition: all 0.3s ease;
      background: white;
    }

    .form-input:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .form-select {
      width: 100%;
      padding: 0.875rem 1rem;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      font-size: 0.875rem;
      font-family: inherit;
      transition: all 0.3s ease;
      background: white;
      cursor: pointer;
    }

    .form-select:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    /* Toggle Switch */
    .toggle-group {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem;
      background: #f8fafc;
      border-radius: 12px;
      margin-bottom: 1rem;
    }

    .toggle-info {
      flex: 1;
    }

    .toggle-label {
      font-weight: 600;
      color: #1e293b;
      font-size: 0.875rem;
      display: block;
      margin-bottom: 0.25rem;
    }

    .toggle-description {
      font-size: 0.75rem;
      color: #64748b;
    }

    .toggle-switch {
      position: relative;
      width: 52px;
      height: 28px;
    }

    .toggle-switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .toggle-slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #cbd5e1;
      transition: 0.4s;
      border-radius: 34px;
    }

    .toggle-slider:before {
      position: absolute;
      content: "";
      height: 20px;
      width: 20px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: 0.4s;
      border-radius: 50%;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .toggle-switch input:checked+.toggle-slider {
      background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .toggle-switch input:checked+.toggle-slider:before {
      transform: translateX(24px);
    }

    /* Range Slider */
    .range-group {
      margin-bottom: 1.5rem;
    }

    .range-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0.5rem;
    }

    .range-input {
      width: 100%;
      height: 8px;
      border-radius: 5px;
      background: #e2e8f0;
      outline: none;
      -webkit-appearance: none;
      appearance: none;
    }

    .range-input::-webkit-slider-thumb {
      -webkit-appearance: none;
      appearance: none;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      background: linear-gradient(135deg, #667eea, #764ba2);
      cursor: pointer;
      box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
    }

    .range-input::-moz-range-thumb {
      width: 20px;
      height: 20px;
      border-radius: 50%;
      background: linear-gradient(135deg, #667eea, #764ba2);
      cursor: pointer;
      border: none;
      box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
    }

    .range-value {
      font-weight: 700;
      color: #667eea;
      font-size: 1rem;
    }

    /* Button */
    .btn-save {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      padding: 1rem 2rem;
      border-radius: 12px;
      font-weight: 600;
      font-size: 0.875rem;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .btn-save:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-save:active {
      transform: translateY(0);
    }

    /* Quick Actions */
    .quick-actions {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-top: 2rem;
      padding-top: 2rem;
      border-top: 2px solid #e2e8f0;
    }

    .quick-action {
      padding: 1.25rem;
      background: linear-gradient(135deg, #f8fafc, #f1f5f9);
      border-radius: 12px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      border: 2px solid transparent;
      text-decoration: none;
      display: block;
    }

    .quick-action:hover {
      background: linear-gradient(135deg, #e0e7ff, #ddd6fe);
      border-color: #667eea;
      transform: translateY(-2px);
    }

    .quick-action i {
      font-size: 2rem;
      color: #667eea;
      margin-bottom: 0.5rem;
    }

    .quick-action-title {
      font-weight: 600;
      color: #1e293b;
      font-size: 0.875rem;
      display: block;
      margin-bottom: 0.25rem;
    }

    .quick-action-desc {
      font-size: 0.75rem;
      color: #64748b;
    }

    /* Responsive */
    @media (max-width: 768px) {
      body {
        padding: 1rem;
      }

      .settings-grid {
        grid-template-columns: 1fr;
      }

      .settings-nav {
        display: flex;
        overflow-x: auto;
        padding: 1rem;
      }

      .nav-title {
        display: none;
      }

      .nav-item {
        white-space: nowrap;
        margin-right: 0.5rem;
        margin-bottom: 0;
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

    .page-header,
    .settings-nav,
    .settings-content {
      animation: slideInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .page-header {
      animation-delay: 0.1s;
    }

    .settings-nav {
      animation-delay: 0.2s;
    }

    .settings-content {
      animation-delay: 0.3s;
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <h1><i class="ph-fill ph-gear"></i> Configurações</h1>
        <p>Personalize sua experiência no sistema de estudos</p>
      </div>
    </div>

    <!-- Settings Grid -->
    <div class="settings-grid">
      <!-- Navigation -->
      <div class="settings-nav">
        <div class="nav-title">Menu</div>
        <div class="nav-item active">
          <i class="ph-fill ph-sliders"></i>
          <span>Geral</span>
        </div>
        <a href="/configuracoes/gamificacao" class="nav-item">
          <i class="ph-fill ph-game-controller"></i>
          <span>Gamificação</span>
        </a>
      </div>

      <!-- Content -->
      <div class="settings-content">
        <div class="section-title">
          <i class="ph-fill ph-sliders"></i>
          Configurações Gerais
        </div>
        <div class="section-description">
          Ajuste as configurações do sistema de acordo com suas preferências
        </div>

        <form method="POST" action="/configuracoes/atualizar">
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

          <!-- Pomodoro Settings -->
          <div class="form-section">
            <div class="form-section-title">
              <i class="ph ph-timer"></i> Técnica Pomodoro
            </div>

            <div class="range-group">
              <div class="range-header">
                <label class="form-label">Duração do Foco</label>
                <span class="range-value" id="duracao-foco-value"><?= $configuracoes['pomodoro_foco_minutos'] ?? 25 ?> min</span>
              </div>
              <input
                type="range"
                name="duracao_pomodoro"
                id="duracao-foco"
                class="range-input"
                min="15"
                max="60"
                step="5"
                value="<?= $configuracoes['pomodoro_foco_minutos'] ?? 25 ?>"
                oninput="document.getElementById('duracao-foco-value').textContent = this.value + ' min'">
            </div>

            <div class="range-group">
              <div class="range-header">
                <label class="form-label">Pausa Curta</label>
                <span class="range-value" id="pausa-curta-value"><?= $configuracoes['pomodoro_pausa_curta_minutos'] ?? 5 ?> min</span>
              </div>
              <input
                type="range"
                name="duracao_pausa_curta"
                id="pausa-curta"
                class="range-input"
                min="3"
                max="10"
                step="1"
                value="<?= $configuracoes['pomodoro_pausa_curta_minutos'] ?? 5 ?>"
                oninput="document.getElementById('pausa-curta-value').textContent = this.value + ' min'">
            </div>

            <div class="range-group">
              <div class="range-header">
                <label class="form-label">Pausa Longa</label>
                <span class="range-value" id="pausa-longa-value"><?= $configuracoes['pomodoro_pausa_longa_minutos'] ?? 15 ?> min</span>
              </div>
              <input
                type="range"
                name="duracao_pausa_longa"
                id="pausa-longa"
                class="range-input"
                min="10"
                max="30"
                step="5"
                value="<?= $configuracoes['pomodoro_pausa_longa_minutos'] ?? 15 ?>"
                oninput="document.getElementById('pausa-longa-value').textContent = this.value + ' min'">
            </div>

            <div class="range-group">
              <div class="range-header">
                <label class="form-label">Pomodoros até Pausa Longa</label>
                <span class="range-value" id="ciclos-value"><?= $configuracoes['pomodoro_ciclos_ate_pausa_longa'] ?? 4 ?></span>
              </div>
              <input
                type="range"
                name="pomodoros_ate_pausa_longa"
                id="ciclos"
                class="range-input"
                min="2"
                max="8"
                step="1"
                value="<?= $configuracoes['pomodoro_ciclos_ate_pausa_longa'] ?? 4 ?>"
                oninput="document.getElementById('ciclos-value').textContent = this.value">
            </div>
          </div>

          <!-- Notifications -->
          <div class="form-section">
            <div class="form-section-title">
              <i class="ph ph-bell"></i> Notificações
            </div>

            <div class="toggle-group">
              <div class="toggle-info">
                <label class="toggle-label">Sons do Pomodoro</label>
                <span class="toggle-description">Reproduzir som ao iniciar/finalizar sessões</span>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" name="pomodoro_som_ativo" <?= !empty($configuracoes['pomodoro_som_ativo']) ? 'checked' : '' ?>>
                <span class="toggle-slider"></span>
              </label>
            </div>

            <div class="toggle-group">
              <div class="toggle-info">
                <label class="toggle-label">Notificações do Pomodoro</label>
                <span class="toggle-description">Receber notificações do navegador</span>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" name="pomodoro_notificacao_ativa" <?= !empty($configuracoes['pomodoro_notificacao_ativa']) ? 'checked' : '' ?>>
                <span class="toggle-slider"></span>
              </label>
            </div>

            <div class="toggle-group">
              <div class="toggle-info">
                <label class="toggle-label">Notificações de Tarefas</label>
                <span class="toggle-description">Alertas de prazos próximos</span>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" name="notificacao_tarefas" <?= !empty($configuracoes['notificacao_tarefas']) ? 'checked' : '' ?>>
                <span class="toggle-slider"></span>
              </label>
            </div>

            <div class="toggle-group">
              <div class="toggle-info">
                <label class="toggle-label">Notificações de Eventos</label>
                <span class="toggle-description">Lembrete de eventos do calendário</span>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" name="notificacao_eventos" <?= !empty($configuracoes['notificacao_eventos']) ? 'checked' : '' ?>>
                <span class="toggle-slider"></span>
              </label>
            </div>

            <div class="toggle-group">
              <div class="toggle-info">
                <label class="toggle-label">Notificações de Metas</label>
                <span class="toggle-description">Alertas sobre progresso de metas</span>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" name="notificacao_metas" <?= !empty($configuracoes['notificacao_metas']) ? 'checked' : '' ?>>
                <span class="toggle-slider"></span>
              </label>
            </div>
          </div>

          <!-- Appearance -->
          <div class="form-section">
            <div class="form-section-title">
              <i class="ph ph-palette"></i> Aparência
            </div>

            <div class="form-group">
              <label class="form-label">Tema</label>
              <select name="tema" class="form-select">
                <option value="claro" <?= ($configuracoes['tema'] ?? 'claro') === 'claro' ? 'selected' : '' ?>>Claro</option>
                <option value="escuro" <?= ($configuracoes['tema'] ?? 'claro') === 'escuro' ? 'selected' : '' ?>>Escuro</option>
                <option value="auto" <?= ($configuracoes['tema'] ?? 'claro') === 'auto' ? 'selected' : '' ?>>Automático</option>
              </select>
              <span class="form-hint">Escolha o tema visual do sistema</span>
            </div>

            <div class="form-group">
              <label class="form-label">Idioma</label>
              <select name="idioma" class="form-select">
                <option value="pt-BR" <?= ($configuracoes['idioma'] ?? 'pt-BR') === 'pt-BR' ? 'selected' : '' ?>>Português (Brasil)</option>
                <option value="en" <?= ($configuracoes['idioma'] ?? 'pt-BR') === 'en' ? 'selected' : '' ?>>English</option>
                <option value="es" <?= ($configuracoes['idioma'] ?? 'pt-BR') === 'es' ? 'selected' : '' ?>>Español</option>
              </select>
              <span class="form-hint">Idioma da interface do sistema</span>
            </div>
          </div>

          <!-- Save Button -->
          <button type="submit" class="btn-save">
            <i class="ph-fill ph-check"></i>
            Salvar Configurações
          </button>
        </form>

        <!-- Quick Actions -->
        <div class="quick-actions">
          <a href="/configuracoes/gamificacao" class="quick-action">
            <i class="ph-fill ph-game-controller"></i>
            <span class="quick-action-title">Gamificação</span>
            <span class="quick-action-desc">Conquistas e ranking</span>
          </a>
          <a href="/relatorios" class="quick-action">
            <i class="ph-fill ph-chart-line"></i>
            <span class="quick-action-title">Relatórios</span>
            <span class="quick-action-desc">Estatísticas e gráficos</span>
          </a>
          <a href="/perfil" class="quick-action">
            <i class="ph-fill ph-user-circle"></i>
            <span class="quick-action-title">Perfil</span>
            <span class="quick-action-desc">Dados pessoais</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Show success/error messages
    <?php if (isset($_SESSION['flash_message'])): ?>
      showModernToast('<?= $_SESSION['flash_message']['type'] ?>', '<?= $_SESSION['flash_message']['message'] ?>');
      <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    function showModernToast(type, message) {
      const toast = document.createElement('div');
      toast.style.cssText = `
        position: fixed;
        top: 2rem;
        right: 2rem;
        background: ${type === 'success' ? 'linear-gradient(135deg, #10b981, #34d399)' : 'linear-gradient(135deg, #ef4444, #f87171)'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        z-index: 10000;
        animation: slideInRight 0.3s ease;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.75rem;
      `;

      const icon = document.createElement('i');
      icon.className = type === 'success' ? 'ph-fill ph-check-circle' : 'ph-fill ph-x-circle';
      icon.style.fontSize = '1.5rem';

      toast.appendChild(icon);
      toast.appendChild(document.createTextNode(message));
      document.body.appendChild(toast);

      setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => toast.remove(), 300);
      }, 3000);
    }
  </script>
</body>

</html>