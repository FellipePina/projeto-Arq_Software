<?php

/**
 * Header Otimizado - Template Performance
 * Use este header nas views que precisam de melhor performance
 */

// Inicia compressão GZIP
if (!headers_sent() && extension_loaded('zlib')) {
  ob_start('ob_gzhandler');
}

// Carrega helper de performance se existir
if (file_exists(__DIR__ . '/../../Helpers/PerformanceHelper.php')) {
  require_once __DIR__ . '/../../Helpers/PerformanceHelper.php';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="theme-color" content="#667eea">

  <!-- SEO e Performance -->
  <meta name="description" content="Sistema de Gerenciamento de Estudos - Organize, acompanhe e melhore seus estudos">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <!-- DNS Prefetch para recursos externos -->
  <link rel="dns-prefetch" href="https://fonts.googleapis.com">
  <link rel="dns-prefetch" href="https://fonts.gstatic.com">
  <link rel="dns-prefetch" href="https://unpkg.com">
  <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

  <!-- Preconnect para recursos críticos -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <title><?= htmlspecialchars($titulo ?? 'Sistema de Estudos') ?></title>

  <!-- Critical CSS Inline (mínimo para first paint) -->
  <style>
    /* Critical CSS - Apenas o essencial para first paint */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
      line-height: 1.6;
      color: #1e293b;
      background: #f8fafc
    }

    .container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 1rem
    }

    .sr-only {
      position: absolute;
      width: 1px;
      height: 1px;
      padding: 0;
      margin: -1px;
      overflow: hidden;
      clip: rect(0, 0, 0, 0);
      white-space: nowrap;
      border-width: 0
    }
  </style>

  <!-- Preload para fontes críticas -->
  <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" as="style">

  <!-- CSS Principal - Load Async -->
  <link rel="preload" href="/css/performance.css?v=<?= filemtime(__DIR__ . '/../../../public/css/performance.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link rel="stylesheet" href="/css/performance.css">
  </noscript>

  <!-- Fontes - Load Async -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" media="print" onload="this.media='all'">

  <!-- Phosphor Icons - Load Async -->
  <link rel="preload" href="https://unpkg.com/@phosphor-icons/web@2.0.3/src/regular/style.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <script defer src="https://unpkg.com/@phosphor-icons/web@2.0.3"></script>

  <!-- Detecção de Performance Mode -->
  <script>
    // Detecta preferências do usuário imediatamente
    (function() {
      const perfMode = localStorage.getItem('performanceMode') === 'true' ||
        window.matchMedia('(prefers-reduced-motion: reduce)').matches ||
        /Mobi|Android/i.test(navigator.userAgent) ||
        navigator.hardwareConcurrency < 4 ||
        (navigator.deviceMemory && navigator.deviceMemory < 4);

      if (perfMode) {
        document.documentElement.classList.add('performance-mode');
        document.body.classList.add('performance-mode');
      }
    })();
  </script>

  <?php if (isset($additionalHead)): ?>
    <?= $additionalHead ?>
  <?php endif; ?>
</head>

<body>
  <!-- Skip to content para acessibilidade -->
  <a href="#main-content" class="sr-only">Pular para o conteúdo principal</a>

  <!-- Header será carregado aqui se necessário -->
  <?php if (!isset($noHeader) || !$noHeader): ?>
    <!-- Seu header aqui -->
  <?php endif; ?>

  <!-- Main Content -->
  <main id="main-content">