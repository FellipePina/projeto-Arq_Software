<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($titulo ?? 'Auxílio Estudos') ?></title>

  <!-- CSS Principal -->
  <link rel="stylesheet" href="/css/style.css">

  <!-- Font Awesome para ícones -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <!-- Meta tags para SEO -->
  <meta name="description" content="Sistema de auxílio para gerenciamento de estudos">
  <meta name="author" content="Auxílio Estudos">
</head>

<body>
  <!-- Header Principal -->
  <header class="header">
    <div class="container">
      <nav class="navbar">
        <!-- Logo -->
        <a href="/dashboard" class="logo">
          <i class="fas fa-graduation-cap"></i>
          Auxílio Estudos
        </a>

        <!-- Menu Principal (apenas se usuário estiver logado) -->
        <?php if (isset($usuario_logado)): ?>
          <ul class="nav-menu">
            <li><a href="/dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="/conteudos"><i class="fas fa-book"></i> Conteúdos</a></li>
            <li><a href="/sessoes"><i class="fas fa-clock"></i> Sessões</a></li>
            <li><a href="/metas"><i class="fas fa-target"></i> Metas</a></li>
          </ul>

          <!-- Informações do Usuário -->
          <div class="user-info">
            <span>
              <i class="fas fa-user"></i>
              Olá, <?= htmlspecialchars($usuario_logado['nome']) ?>!
            </span>
            <a href="/logout" class="btn btn-secondary btn-sm">
              <i class="fas fa-sign-out-alt"></i> Sair
            </a>
          </div>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <!-- Container Principal -->
  <main class="container mt-3">
    <!-- Mensagens Flash -->
    <?php if (isset($flash_messages) && !empty($flash_messages)): ?>
      <?php foreach ($flash_messages as $message): ?>
        <div class="alert alert-<?= $message['type'] === 'error' ? 'error' : $message['type'] ?> fade-in">
          <?php
          $icons = [
            'success' => 'fa-check-circle',
            'error' => 'fa-exclamation-circle',
            'warning' => 'fa-exclamation-triangle',
            'info' => 'fa-info-circle'
          ];
          $icon = $icons[$message['type']] ?? 'fa-info-circle';
          ?>
          <i class="fas <?= $icon ?>"></i>
          <?= htmlspecialchars($message['message']) ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>