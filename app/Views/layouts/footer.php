    </main>

    <!-- Footer -->
    <footer class="footer mt-5" style="background-color: #2c3e50; color: white; padding: 2rem 0; text-align: center;">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <p>&copy; <?= date('Y') ?> Auxílio Estudos. Sistema de gerenciamento de estudos.</p>
            <p class="text-muted">
              Desenvolvido com <i class="fas fa-heart" style="color: #e74c3c;"></i>
              usando PHP, MySQL e princípios SOLID & Clean Code
            </p>
          </div>
        </div>
      </div>
    </footer>

    <!-- JavaScript -->
    <script src="/js/main.js"></script>
    </script>

    <!-- Scripts específicos da página -->
    <?php if (isset($scripts)): ?>
      <?php foreach ($scripts as $script): ?>
        <script src="<?= $script ?>"></script>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- Script inline se necessário -->
    <?php if (isset($inline_scripts)): ?>
      <script>
        <?= $inline_scripts ?>
      </script>
    <?php endif; ?>
    </body>

    </html>