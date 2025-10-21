<?php

/**
 * View: Perfil do Usuário
 * Design: Página de perfil com informações e edição
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($titulo ?? 'Perfil') ?></title>
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
      max-width: 1000px;
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

    /* Profile Grid */
    .profile-grid {
      display: grid;
      gap: 2rem;
    }

    /* Profile Card */
    .profile-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 24px;
      padding: 2.5rem;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .profile-header {
      display: flex;
      align-items: center;
      gap: 2rem;
      margin-bottom: 2.5rem;
      padding-bottom: 2rem;
      border-bottom: 2px solid #e2e8f0;
    }

    .profile-avatar-wrapper {
      position: relative;
    }

    .profile-avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background: linear-gradient(135deg, #667eea, #764ba2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3rem;
      color: white;
      font-weight: 700;
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .avatar-badge {
      position: absolute;
      bottom: 5px;
      right: 5px;
      width: 35px;
      height: 35px;
      border-radius: 50%;
      background: linear-gradient(135deg, #10b981, #34d399);
      border: 4px solid white;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 0.875rem;
      box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }

    .profile-info h2 {
      font-size: 1.75rem;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 0.5rem;
    }

    .profile-email {
      color: #64748b;
      font-size: 0.95rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 0.75rem;
    }

    .profile-meta {
      display: flex;
      gap: 1.5rem;
      font-size: 0.875rem;
      color: #64748b;
    }

    .profile-meta-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .profile-meta-item i {
      color: #667eea;
    }

    /* Form Section */
    .form-section {
      margin-bottom: 2rem;
    }

    .section-title {
      font-size: 1.25rem;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .section-title i {
      color: #667eea;
      font-size: 1.5rem;
    }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
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

    .form-required {
      color: #ef4444;
      margin-left: 0.25rem;
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

    .form-input:disabled {
      background: #f8fafc;
      cursor: not-allowed;
      opacity: 0.6;
    }

    .form-hint {
      display: block;
      font-size: 0.75rem;
      color: #94a3b8;
      margin-top: 0.25rem;
    }

    /* Password Section */
    .password-section {
      background: #f8fafc;
      border-radius: 16px;
      padding: 1.5rem;
      margin-top: 2rem;
    }

    .password-toggle {
      position: relative;
    }

    .toggle-password {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #64748b;
      cursor: pointer;
      font-size: 1.25rem;
      transition: color 0.3s ease;
    }

    .toggle-password:hover {
      color: #334155;
    }

    /* Buttons */
    .btn-group {
      display: flex;
      gap: 1rem;
      margin-top: 2rem;
    }

    .btn {
      padding: 0.875rem 1.75rem;
      border: none;
      border-radius: 12px;
      font-weight: 600;
      font-size: 0.875rem;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
    }

    .btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
      background: #f1f5f9;
      color: #64748b;
    }

    .btn-secondary:hover {
      background: #e2e8f0;
      color: #334155;
    }

    .btn-danger {
      background: linear-gradient(135deg, #ef4444, #f87171);
      color: white;
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .btn-danger:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
    }

    /* Stats Cards */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
      margin-top: 2rem;
    }

    .stat-card {
      background: linear-gradient(135deg, #f8fafc, #f1f5f9);
      border-radius: 16px;
      padding: 1.5rem;
      text-align: center;
      transition: all 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
      font-size: 1.5rem;
      color: white;
    }

    .stat-card:nth-child(1) .stat-icon {
      background: linear-gradient(135deg, #8b5cf6, #6366f1);
    }

    .stat-card:nth-child(2) .stat-icon {
      background: linear-gradient(135deg, #ec4899, #f43f5e);
    }

    .stat-card:nth-child(3) .stat-icon {
      background: linear-gradient(135deg, #10b981, #34d399);
    }

    .stat-card:nth-child(4) .stat-icon {
      background: linear-gradient(135deg, #f59e0b, #fbbf24);
    }

    .stat-value {
      font-size: 2rem;
      font-weight: 800;
      color: #1e293b;
      display: block;
      margin-bottom: 0.25rem;
    }

    .stat-label {
      font-size: 0.75rem;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      font-weight: 600;
    }

    /* Alert */
    .alert {
      padding: 1rem 1.25rem;
      border-radius: 12px;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      font-size: 0.875rem;
      font-weight: 500;
    }

    .alert-success {
      background: #dcfce7;
      color: #15803d;
      border: 2px solid #86efac;
    }

    .alert-error {
      background: #fee2e2;
      color: #b91c1c;
      border: 2px solid #fca5a5;
    }

    .alert i {
      font-size: 1.25rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
      body {
        padding: 1rem;
      }

      .profile-header {
        flex-direction: column;
        text-align: center;
      }

      .profile-meta {
        flex-direction: column;
        gap: 0.5rem;
      }

      .btn-group {
        flex-direction: column;
      }

      .btn {
        width: 100%;
        justify-content: center;
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
    .profile-card {
      animation: slideInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .page-header {
      animation-delay: 0.1s;
    }

    .profile-card {
      animation-delay: 0.2s;
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <h1><i class="ph-fill ph-user-circle"></i> Meu Perfil</h1>
        <p>Gerencie suas informações pessoais e configurações de conta</p>
      </div>
    </div>

    <!-- Flash Messages -->
    <?php if (!empty($flash_messages)): ?>
      <?php foreach ($flash_messages as $message): ?>
        <div class="alert alert-<?= $message['type'] ?>">
          <i class="ph-fill <?= $message['type'] === 'success' ? 'ph-check-circle' : 'ph-warning-circle' ?>"></i>
          <?= htmlspecialchars($message['message']) ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- Profile Grid -->
    <div class="profile-grid">
      <!-- Profile Card -->
      <div class="profile-card">
        <!-- Profile Header -->
        <div class="profile-header">
          <div class="profile-avatar-wrapper">
            <div class="profile-avatar">
              <?= strtoupper(substr($usuario['nome'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="avatar-badge">
              <i class="ph-fill ph-check"></i>
            </div>
          </div>
          <div class="profile-info">
            <h2><?= htmlspecialchars($usuario['nome'] ?? 'Usuário') ?></h2>
            <div class="profile-email">
              <i class="ph ph-envelope"></i>
              <?= htmlspecialchars($usuario['email'] ?? '') ?>
            </div>
            <div class="profile-meta">
              <div class="profile-meta-item">
                <i class="ph-fill ph-calendar"></i>
                Membro desde <?= date('d/m/Y', strtotime($usuario['data_criacao'] ?? 'now')) ?>
              </div>
              <div class="profile-meta-item">
                <i class="ph-fill ph-clock"></i>
                Último acesso: <?= date('d/m/Y H:i', strtotime($usuario['ultimo_acesso'] ?? 'now')) ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Edit Form -->
        <form method="POST" action="/perfil/atualizar">
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

          <!-- Personal Info -->
          <div class="form-section">
            <div class="section-title">
              <i class="ph-fill ph-user"></i>
              Informações Pessoais
            </div>
            <div class="form-grid">
              <div class="form-group">
                <label class="form-label">
                  Nome Completo
                  <span class="form-required">*</span>
                </label>
                <input
                  type="text"
                  name="nome"
                  class="form-input"
                  value="<?= htmlspecialchars($usuario['nome'] ?? '') ?>"
                  required>
              </div>
              <div class="form-group">
                <label class="form-label">
                  Email
                  <span class="form-required">*</span>
                </label>
                <input
                  type="email"
                  name="email"
                  class="form-input"
                  value="<?= htmlspecialchars($usuario['email'] ?? '') ?>"
                  required>
                <span class="form-hint">Usado para login e notificações</span>
              </div>
            </div>
          </div>

          <!-- Change Password -->
          <div class="form-section password-section">
            <div class="section-title">
              <i class="ph-fill ph-lock"></i>
              Alterar Senha
            </div>
            <p style="color: #64748b; font-size: 0.875rem; margin-bottom: 1.5rem;">
              Deixe em branco para manter a senha atual
            </p>
            <div class="form-grid">
              <div class="form-group password-toggle">
                <label class="form-label">Senha Atual</label>
                <input
                  type="password"
                  name="senha_atual"
                  id="senha-atual"
                  class="form-input"
                  autocomplete="current-password">
                <button type="button" class="toggle-password" onclick="togglePassword('senha-atual')">
                  <i class="ph ph-eye"></i>
                </button>
              </div>
              <div class="form-group password-toggle">
                <label class="form-label">Nova Senha</label>
                <input
                  type="password"
                  name="nova_senha"
                  id="nova-senha"
                  class="form-input"
                  autocomplete="new-password">
                <button type="button" class="toggle-password" onclick="togglePassword('nova-senha')">
                  <i class="ph ph-eye"></i>
                </button>
                <span class="form-hint">Mínimo 6 caracteres</span>
              </div>
              <div class="form-group password-toggle">
                <label class="form-label">Confirmar Nova Senha</label>
                <input
                  type="password"
                  name="confirmar_senha"
                  id="confirmar-senha"
                  class="form-input"
                  autocomplete="new-password">
                <button type="button" class="toggle-password" onclick="togglePassword('confirmar-senha')">
                  <i class="ph ph-eye"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Buttons -->
          <div class="btn-group">
            <button type="submit" class="btn btn-primary">
              <i class="ph-fill ph-floppy-disk"></i>
              Salvar Alterações
            </button>
            <a href="/dashboard" class="btn btn-secondary">
              <i class="ph ph-x"></i>
              Cancelar
            </a>
          </div>
        </form>

        <!-- Stats -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">
              <i class="ph-fill ph-trophy"></i>
            </div>
            <span class="stat-value">0</span>
            <span class="stat-label">Conquistas</span>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <i class="ph-fill ph-timer"></i>
            </div>
            <span class="stat-value">0</span>
            <span class="stat-label">Pomodoros</span>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <i class="ph-fill ph-check-circle"></i>
            </div>
            <span class="stat-value">0</span>
            <span class="stat-label">Tarefas</span>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <i class="ph-fill ph-fire"></i>
            </div>
            <span class="stat-value">0</span>
            <span class="stat-label">Dias de Streak</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Toggle password visibility
    function togglePassword(inputId) {
      const input = document.getElementById(inputId);
      const button = input.parentElement.querySelector('.toggle-password i');

      if (input.type === 'password') {
        input.type = 'text';
        button.classList.remove('ph-eye');
        button.classList.add('ph-eye-slash');
      } else {
        input.type = 'password';
        button.classList.remove('ph-eye-slash');
        button.classList.add('ph-eye');
      }
    }

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
      const novaSenha = document.getElementById('nova-senha').value;
      const confirmarSenha = document.getElementById('confirmar-senha').value;

      if (novaSenha && novaSenha !== confirmarSenha) {
        e.preventDefault();
        alert('As senhas não coincidem!');
        return false;
      }

      if (novaSenha && novaSenha.length < 6) {
        e.preventDefault();
        alert('A senha deve ter no mínimo 6 caracteres!');
        return false;
      }
    });
  </script>
</body>

</html>