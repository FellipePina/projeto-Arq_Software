    </main>

    <!-- Footer -->
    <footer style="margin-top:3rem;padding:2rem 0;background:#2c3e50;color:white;text-align:center">
      <div class="container">
        <p>&copy; <?= date('Y') ?> Sistema de Estudos</p>
      </div>
    </footer>

    <!-- JavaScript - Load Defer -->
    <script defer src="/js/performance.js?v=<?= filemtime(__DIR__ . '/../../../public/js/performance.js') ?>"></script>

    <?php if (isset($additionalScripts)): ?>
      <?php foreach ($additionalScripts as $script): ?>
        <script defer src="<?= $script ?>"></script>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- Inline scripts if needed -->
    <?php if (isset($inlineScript)): ?>
      <script>
        <?= $inlineScript ?>
      </script>
    <?php endif; ?>

    <!-- Service Worker para PWA (opcional) -->
    <script>
      if ('serviceWorker' in navigator && location.protocol === 'https:') {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
      }
    </script>
    </body>

    </html>
    <?php
    // Fecha buffer de compressão GZIP
    if (ob_get_level() > 0) {
      ob_end_flush();
    }
    ?>