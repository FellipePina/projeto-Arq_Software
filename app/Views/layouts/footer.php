      </div>
      </main>
      </div>

      <!-- Alpine.js para interatividade -->
      <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

      <!-- Custom JavaScript Modules -->
      <script src="/assets/js/ajax-helpers.js"></script>
      <script src="/assets/js/chart-handler.js"></script>
      <script src="/assets/js/pomodoro-timer.js"></script>

      <!-- Custom JavaScript -->
      <script src="/js/main.js"></script>

      <script>
        // Atualiza dados de gamificação no header
        async function updateGamificationHeader() {
          try {
            const response = await fetch('/gamificacao/dados');
            const data = await response.json();

            if (data.success) {
              document.getElementById('pontos-header').textContent = data.pontos_totais;
              document.getElementById('nivel-header').textContent = data.nivel;
              document.getElementById('streak-header').textContent = data.sequencia_dias;
            }
          } catch (error) {
            console.error('Erro ao atualizar gamificação:', error);
          }
        }

        // Atualiza ao carregar página
        document.addEventListener('DOMContentLoaded', updateGamificationHeader);

        // Menu mobile toggle
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
          document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // Auto-hide flash messages
        setTimeout(() => {
          document.querySelectorAll('.flash-message').forEach(msg => {
            msg.style.transition = 'opacity 0.5s';
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 500);
          });
        }, 5000);
      </script>
      </body>

      </html>