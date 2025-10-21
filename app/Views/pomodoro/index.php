<?php
$titulo = 'Pomodoro Timer';
$active = 'pomodoro';
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Page Header - Design moderno -->
<div class="mb-8 animate-slide-in-up">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
        Pomodoro Timer
      </h1>
      <p class="mt-2 text-sm text-gray-600">Técnica de produtividade com foco e pausas programadas</p>
    </div>
    <a href="/pomodoro/historico" class="btn-modern bg-gradient-to-r from-primary-600 to-primary-700 text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-soft-lg">
      <i class="ph ph-clock-countdown text-lg"></i>
      <span>Histórico</span>
    </a>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  <!-- Timer Principal -->
  <div class="lg:col-span-2 animate-slide-in-up" style="animation-delay: 0.1s;">
    <div class="modern-card p-8">

      <!-- Tipo de Sessão -->
      <div class="flex justify-center gap-2 mb-8">
        <button type="button"
          class="px-6 py-2 rounded-lg font-medium text-sm transition-all focus-timer-type active"
          data-type="foco"
          onclick="changeTimerType('foco')">
          🍅 Foco
        </button>
        <button type="button"
          class="px-6 py-2 rounded-lg font-medium text-sm transition-all focus-timer-type"
          data-type="pausa_curta"
          onclick="changeTimerType('pausa_curta')">
          ☕ Pausa Curta
        </button>
        <button type="button"
          class="px-6 py-2 rounded-lg font-medium text-sm transition-all focus-timer-type"
          data-type="pausa_longa"
          onclick="changeTimerType('pausa_longa')">
          🌴 Pausa Longa
        </button>
      </div>

      <!-- Timer Display -->
      <div class="text-center mb-8">
        <div id="timer-display" class="text-8xl font-bold bg-gradient-to-r from-red-600 to-rose-700 bg-clip-text text-transparent mb-4 tabular-nums">
          25:00
        </div>
        <div id="timer-label" class="text-lg text-gray-600">
          Sessão de Foco
        </div>
      </div>

      <!-- Barra de Progresso -->
      <div class="mb-8">
        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
          <div id="timer-progress"
            class="h-full rounded-full transition-all duration-1000 bg-gradient-to-r from-red-500 to-rose-600"
            style="width: 0%"></div>
        </div>
      </div>

      <!-- Controles -->
      <div class="flex justify-center gap-4 mb-8">
        <button type="button"
          id="btn-start"
          onclick="startTimer()"
          class="btn-modern bg-gradient-to-r from-emerald-600 to-green-700 text-white hover:from-emerald-700 hover:to-green-800 shadow-soft hover:shadow-soft-lg px-8 py-3 text-lg">
          <i class="ph ph-play text-xl"></i>
          <span>Iniciar</span>
        </button>
        <button type="button"
          id="btn-pause"
          onclick="pauseTimer()"
          class="btn-modern bg-gradient-to-r from-amber-600 to-yellow-700 text-white hover:from-amber-700 hover:to-yellow-800 shadow-soft hover:shadow-soft-lg px-8 py-3 text-lg hidden">
          <i class="ph ph-pause text-xl"></i>
          <span>Pausar</span>
        </button>
        <button type="button"
          id="btn-reset"
          onclick="resetTimer()"
          class="btn-modern bg-gray-100 text-gray-700 hover:bg-gray-200 px-8 py-3 text-lg">
          <i class="ph ph-arrow-counter-clockwise text-xl"></i>
          <span>Resetar</span>
        </button>
      </div>

      <!-- Seleção de Tarefa -->
      <div class="border-t border-gray-100 pt-6">
        <label class="block text-sm font-medium text-gray-700 mb-3">
          Vincular a uma tarefa (opcional)
        </label>
        <select id="tarefa-select" class="modern-select w-full">
          <option value="">Nenhuma tarefa selecionada</option>
          <?php if (!empty($tarefas)): ?>
            <?php foreach ($tarefas as $tarefa): ?>
              <option value="<?= $tarefa['id'] ?>">
                <?= htmlspecialchars($tarefa['titulo']) ?>
                <?= $tarefa['disciplina_nome'] ? ' - ' . htmlspecialchars($tarefa['disciplina_nome']) : '' ?>
              </option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
      </div>

    </div>
  </div>

  <!-- Sidebar -->
  <div class="space-y-6">

    <!-- Estatísticas de Hoje -->
    <div class="modern-card p-6 animate-slide-in-up" style="animation-delay: 0.2s;">
      <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
        <i class="ph ph-chart-line text-lg text-primary-600"></i>
        Hoje
      </h3>

      <div class="space-y-4">
        <div>
          <div class="flex items-center justify-between mb-1">
            <span class="text-xs text-gray-600">Pomodoros Completos</span>
            <span class="text-sm font-bold text-gray-900"><?= $estatisticas['sessoes_hoje'] ?? 0 ?></span>
          </div>
          <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full bg-gradient-to-r from-red-500 to-rose-600"
              style="width: <?= min(100, (($estatisticas['sessoes_hoje'] ?? 0) / 8) * 100) ?>%"></div>
          </div>
        </div>

        <div class="flex items-center justify-between p-3 rounded-lg bg-gradient-to-br from-blue-50 to-cyan-50">
          <div class="flex items-center gap-2">
            <i class="ph ph-timer text-xl text-blue-600"></i>
            <span class="text-sm text-blue-900">Tempo de Foco</span>
          </div>
          <span class="text-lg font-bold text-blue-900"><?= $estatisticas['tempo_foco_hoje'] ?? 0 ?>min</span>
        </div>

        <div class="flex items-center justify-between p-3 rounded-lg bg-gradient-to-br from-emerald-50 to-green-50">
          <div class="flex items-center gap-2">
            <i class="ph ph-fire text-xl text-emerald-600"></i>
            <span class="text-sm text-emerald-900">Sequência</span>
          </div>
          <span class="text-lg font-bold text-emerald-900"><?= $estatisticas['sequencia'] ?? 0 ?></span>
        </div>
      </div>
    </div>

    <!-- Ciclos Pomodoro -->
    <div class="modern-card p-6 animate-slide-in-up" style="animation-delay: 0.3s;">
      <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
        <i class="ph ph-circle-dashed text-lg text-primary-600"></i>
        Ciclos de Hoje
      </h3>

      <div class="flex items-center gap-2" id="pomodoro-cycles">
        <?php
        $ciclosHoje = $estatisticas['sessoes_hoje'] ?? 0;
        $ciclosAteProxima = $configuracoes['pomodoro_ciclos_ate_pausa_longa'] ?? 4;
        for ($i = 0; $i < $ciclosAteProxima; $i++):
        ?>
          <div class="flex-1 h-3 rounded-full <?= $i < $ciclosHoje ? 'bg-gradient-to-r from-red-500 to-rose-600' : 'bg-gray-200' ?>"></div>
        <?php endfor; ?>
      </div>
      <p class="text-xs text-gray-500 mt-3 text-center">
        <?= $ciclosHoje % $ciclosAteProxima ?> de <?= $ciclosAteProxima ?> até pausa longa
      </p>
    </div>

    <!-- Configurações Rápidas -->
    <div class="modern-card p-6 animate-slide-in-up" style="animation-delay: 0.4s;">
      <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
        <i class="ph ph-gear text-lg text-primary-600"></i>
        Configurações
      </h3>

      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <label class="text-sm text-gray-700">Som</label>
          <label class="modern-toggle">
            <input type="checkbox" id="toggle-sound" <?= ($configuracoes['pomodoro_som_ativo'] ?? true) ? 'checked' : '' ?>>
            <span class="modern-toggle-slider"></span>
          </label>
        </div>

        <div class="flex items-center justify-between">
          <label class="text-sm text-gray-700">Notificações</label>
          <label class="modern-toggle">
            <input type="checkbox" id="toggle-notifications" <?= ($configuracoes['pomodoro_notificacao_ativa'] ?? true) ? 'checked' : '' ?>>
            <span class="modern-toggle-slider"></span>
          </label>
        </div>

        <div class="pt-3 border-t border-gray-100">
          <a href="/configuracoes" class="text-sm text-primary-600 hover:text-primary-700 flex items-center gap-1">
            <span>Ver todas as configurações</span>
            <i class="ph ph-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
  // Configurações do timer
  const config = {
    foco: <?= $configuracoes['pomodoro_foco_minutos'] ?? 25 ?>,
    pausa_curta: <?= $configuracoes['pomodoro_pausa_curta_minutos'] ?? 5 ?>,
    pausa_longa: <?= $configuracoes['pomodoro_pausa_longa_minutos'] ?? 15 ?>,
    somAtivo: <?= ($configuracoes['pomodoro_som_ativo'] ?? true) ? 'true' : 'false' ?>,
    notificacaoAtiva: <?= ($configuracoes['pomodoro_notificacao_ativa'] ?? true) ? 'true' : 'false' ?>
  };

  let timerState = {
    type: 'foco',
    minutes: config.foco,
    seconds: 0,
    isRunning: false,
    isPaused: false,
    interval: null,
    totalSeconds: config.foco * 60,
    startTime: null,
    sessaoId: null
  };

  // Mudar tipo de timer
  function changeTimerType(type) {
    if (timerState.isRunning) {
      if (!confirm('Deseja parar o timer atual?')) return;
      resetTimer();
    }

    timerState.type = type;
    timerState.minutes = config[type];
    timerState.seconds = 0;
    timerState.totalSeconds = config[type] * 60;

    updateDisplay();
    updateProgress();

    // Atualizar botões
    document.querySelectorAll('.focus-timer-type').forEach(btn => {
      btn.classList.remove('active');
      if (btn.dataset.type === type) {
        btn.classList.add('active');
      }
    });

    // Atualizar label
    const labels = {
      foco: 'Sessão de Foco',
      pausa_curta: 'Pausa Curta',
      pausa_longa: 'Pausa Longa'
    };
    document.getElementById('timer-label').textContent = labels[type];
  }

  // Iniciar timer
  async function startTimer() {
    if (timerState.isRunning) return;

    timerState.isRunning = true;
    timerState.isPaused = false;
    timerState.startTime = Date.now();

    // Criar sessão no backend
    if (timerState.type === 'foco') {
      const tarefaId = document.getElementById('tarefa-select').value || null;
      await criarSessao(tarefaId);
    }

    // Atualizar UI
    document.getElementById('btn-start').classList.add('hidden');
    document.getElementById('btn-pause').classList.remove('hidden');

    // Iniciar contagem
    timerState.interval = setInterval(() => {
      if (timerState.seconds === 0) {
        if (timerState.minutes === 0) {
          finishTimer();
          return;
        }
        timerState.minutes--;
        timerState.seconds = 59;
      } else {
        timerState.seconds--;
      }

      updateDisplay();
      updateProgress();
    }, 1000);
  }

  // Pausar timer
  function pauseTimer() {
    if (!timerState.isRunning) return;

    timerState.isRunning = false;
    timerState.isPaused = true;
    clearInterval(timerState.interval);

    document.getElementById('btn-start').classList.remove('hidden');
    document.getElementById('btn-pause').classList.add('hidden');
  }

  // Resetar timer
  async function resetTimer() {
    if (timerState.sessaoId) {
      await interromperSessao(timerState.sessaoId);
    }

    clearInterval(timerState.interval);
    timerState.isRunning = false;
    timerState.isPaused = false;
    timerState.minutes = config[timerState.type];
    timerState.seconds = 0;
    timerState.sessaoId = null;

    updateDisplay();
    updateProgress();

    document.getElementById('btn-start').classList.remove('hidden');
    document.getElementById('btn-pause').classList.add('hidden');
  }

  // Finalizar timer
  async function finishTimer() {
    clearInterval(timerState.interval);
    timerState.isRunning = false;

    // Tocar som
    if (config.somAtivo) {
      playSound();
    }

    // Mostrar notificação
    if (config.notificacaoAtiva && timerState.type === 'foco') {
      showNotification('Pomodoro Concluído!', 'Hora de fazer uma pausa!');
    }

    // Finalizar sessão no backend
    if (timerState.sessaoId) {
      await finalizarSessao(timerState.sessaoId);
    }

    // Mostrar toast
    showModernToast('Timer finalizado! 🎉', 'success');

    // Auto-avançar para próximo tipo
    autoAdvanceTimer();
  }

  // Auto-avançar para próximo timer
  function autoAdvanceTimer() {
    const ciclosHoje = <?= $estatisticas['sessoes_hoje'] ?? 0 ?>;
    const ciclosAteProxima = <?= $configuracoes['pomodoro_ciclos_ate_pausa_longa'] ?? 4 ?>;

    if (timerState.type === 'foco') {
      // Se completou ciclo, pausa longa, senão pausa curta
      const nextType = (ciclosHoje % ciclosAteProxima === 0) ? 'pausa_longa' : 'pausa_curta';
      setTimeout(() => changeTimerType(nextType), 1000);
    } else {
      setTimeout(() => changeTimerType('foco'), 1000);
    }
  }

  // Atualizar display
  function updateDisplay() {
    const mins = String(timerState.minutes).padStart(2, '0');
    const secs = String(timerState.seconds).padStart(2, '0');
    document.getElementById('timer-display').textContent = `${mins}:${secs}`;
    document.title = `${mins}:${secs} - Pomodoro Timer`;
  }

  // Atualizar progresso
  function updateProgress() {
    const totalSeconds = timerState.totalSeconds;
    const currentSeconds = (timerState.minutes * 60) + timerState.seconds;
    const progress = ((totalSeconds - currentSeconds) / totalSeconds) * 100;
    document.getElementById('timer-progress').style.width = `${progress}%`;
  }

  // Criar sessão no backend
  async function criarSessao(tarefaId) {
    try {
      const response = await fetch('/pomodoro/iniciar', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          tipo: 'foco',
          duracao_planejada: config.foco,
          tarefa_id: tarefaId
        })
      });
      const data = await response.json();
      if (data.success) {
        timerState.sessaoId = data.sessao_id;
      }
    } catch (error) {
      console.error('Erro ao criar sessão:', error);
    }
  }

  // Finalizar sessão
  async function finalizarSessao(sessaoId) {
    try {
      await fetch(`/pomodoro/${sessaoId}/finalizar`, {
        method: 'POST'
      });
      location.reload(); // Recarregar para atualizar estatísticas
    } catch (error) {
      console.error('Erro ao finalizar sessão:', error);
    }
  }

  // Interromper sessão
  async function interromperSessao(sessaoId) {
    try {
      await fetch(`/pomodoro/${sessaoId}/interromper`, {
        method: 'POST'
      });
    } catch (error) {
      console.error('Erro ao interromper sessão:', error);
    }
  }

  // Sons e notificações
  function playSound() {
    const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwNUrDk7K1bGAo+ltv0yoEtBSdxy/LUhzENEWS2...'); // Simplified example
    audio.play().catch(e => console.log('Audio play failed:', e));
  }

  function showNotification(title, body) {
    if ('Notification' in window && Notification.permission === 'granted') {
      new Notification(title, {
        body,
        icon: '/assets/images/pomodoro-icon.png'
      });
    }
  }

  // Toast
  function showModernToast(message, type = 'info') {
    const icons = {
      success: 'ph-check-circle',
      error: 'ph-x-circle',
      warning: 'ph-warning-circle',
      info: 'ph-info'
    };
    const colors = {
      success: 'from-emerald-50 to-green-50 border-emerald-200 text-emerald-800',
      error: 'from-red-50 to-rose-50 border-red-200 text-red-800',
      warning: 'from-amber-50 to-yellow-50 border-amber-200 text-amber-800',
      info: 'from-blue-50 to-cyan-50 border-blue-200 text-blue-800'
    };

    const toast = document.createElement('div');
    toast.className = `fixed top-20 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-xl border bg-gradient-to-r ${colors[type]} shadow-soft-lg animate-slide-in-up`;
    toast.innerHTML = `<i class="ph-fill ${icons[type]} text-lg"></i><span class="text-sm font-medium">${message}</span>`;

    document.body.appendChild(toast);
    setTimeout(() => {
      toast.style.opacity = '0';
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }

  // Estilos para botões de tipo
  document.querySelectorAll('.focus-timer-type').forEach(btn => {
    if (!btn.classList.contains('active')) {
      btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
    } else {
      btn.classList.add('bg-gradient-to-r', 'from-red-500', 'to-rose-600', 'text-white', 'shadow-soft');
    }
  });

  // Requisitar permissão de notificação
  if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
  }

  // Inicializar
  updateDisplay();
  updateProgress();
</script>

<style>
  .focus-timer-type.active {
    @apply bg-gradient-to-r from-red-500 to-rose-600 text-white shadow-soft;
  }

  .focus-timer-type:not(.active) {
    @apply bg-gray-100 text-gray-700 hover:bg-gray-200;
  }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>