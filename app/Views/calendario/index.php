<?php
$titulo = 'Calendário';
$active = 'calendario';
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Page Header - Design moderno -->
<div class="mb-8 animate-slide-in-up">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
        Calendário de Estudos
      </h1>
      <p class="mt-2 text-sm text-gray-600">Organize eventos, prazos e sessões de estudo</p>
    </div>
    <button type="button"
      onclick="openEventModal()"
      class="btn-modern bg-gradient-to-r from-primary-600 to-primary-700 text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-soft-lg">
      <i class="ph ph-plus-circle text-lg"></i>
      <span>Novo Evento</span>
    </button>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

  <!-- Calendário Principal -->
  <div class="lg:col-span-3 animate-slide-in-up" style="animation-delay: 0.1s;">
    <div class="modern-card p-6">

      <!-- Navegação do Calendário -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
          <button type="button"
            onclick="changeMonth(-1)"
            class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 hover:text-gray-900 transition-all">
            <i class="ph ph-caret-left text-xl"></i>
          </button>
          <h2 id="calendar-month" class="text-xl font-bold text-gray-900">
            <!-- Será preenchido via JS -->
          </h2>
          <button type="button"
            onclick="changeMonth(1)"
            class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 hover:text-gray-900 transition-all">
            <i class="ph ph-caret-right text-xl"></i>
          </button>
        </div>
        <button type="button"
          onclick="goToToday()"
          class="btn-modern bg-primary-50 text-primary-700 hover:bg-primary-100">
          <i class="ph ph-calendar-check text-lg"></i>
          <span>Hoje</span>
        </button>
      </div>

      <!-- Grid do Calendário -->
      <div class="mb-4">
        <!-- Dias da Semana -->
        <div class="grid grid-cols-7 gap-2 mb-2">
          <div class="text-center text-xs font-semibold text-gray-600 py-2">Dom</div>
          <div class="text-center text-xs font-semibold text-gray-600 py-2">Seg</div>
          <div class="text-center text-xs font-semibold text-gray-600 py-2">Ter</div>
          <div class="text-center text-xs font-semibold text-gray-600 py-2">Qua</div>
          <div class="text-center text-xs font-semibold text-gray-600 py-2">Qui</div>
          <div class="text-center text-xs font-semibold text-gray-600 py-2">Sex</div>
          <div class="text-center text-xs font-semibold text-gray-600 py-2">Sáb</div>
        </div>

        <!-- Dias do Mês -->
        <div id="calendar-grid" class="grid grid-cols-7 gap-2">
          <!-- Será preenchido via JS -->
        </div>
      </div>

      <!-- Legenda -->
      <div class="flex flex-wrap items-center gap-4 pt-4 border-t border-gray-100 text-xs">
        <div class="flex items-center gap-2">
          <div class="w-3 h-3 rounded-full bg-gradient-to-r from-blue-500 to-cyan-600"></div>
          <span class="text-gray-600">Evento</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-3 h-3 rounded-full bg-gradient-to-r from-red-500 to-rose-600"></div>
          <span class="text-gray-600">Prazo de Tarefa</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-3 h-3 rounded-full bg-gradient-to-r from-emerald-500 to-green-600"></div>
          <span class="text-gray-600">Sessão de Estudo</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-3 h-3 rounded-full bg-gradient-to-r from-purple-500 to-violet-600"></div>
          <span class="text-gray-600">Meta</span>
        </div>
      </div>

    </div>
  </div>

  <!-- Sidebar - Eventos do Dia -->
  <div class="space-y-6">

    <!-- Eventos de Hoje -->
    <div class="modern-card p-6 animate-slide-in-up" style="animation-delay: 0.2s;">
      <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
        <i class="ph ph-calendar-dots text-lg text-primary-600"></i>
        <span id="sidebar-date">Hoje</span>
      </h3>

      <div id="events-list" class="space-y-3">
        <!-- Será preenchido via JS -->
      </div>

      <div id="no-events" class="hidden text-center py-8">
        <i class="ph ph-calendar-x text-4xl text-gray-300 mb-2"></i>
        <p class="text-sm text-gray-500">Nenhum evento neste dia</p>
      </div>
    </div>

    <!-- Próximos Prazos -->
    <div class="modern-card p-6 animate-slide-in-up" style="animation-delay: 0.3s;">
      <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
        <i class="ph ph-clock-countdown text-lg text-red-600"></i>
        Próximos Prazos
      </h3>

      <div class="space-y-3">
        <?php if (!empty($proximosPrazos)): ?>
          <?php foreach (array_slice($proximosPrazos, 0, 5) as $prazo): ?>
            <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
              <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-red-50 to-rose-100 flex items-center justify-center">
                <i class="ph ph-warning text-lg text-red-600"></i>
              </div>
              <div class="flex-1 min-w-0">
                <h4 class="text-sm font-medium text-gray-900 truncate">
                  <?= htmlspecialchars($prazo['titulo']) ?>
                </h4>
                <p class="text-xs text-gray-500">
                  <?= date('d/m/Y', strtotime($prazo['prazo'])) ?>
                </p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="text-center py-4">
            <i class="ph ph-check-circle text-3xl text-emerald-500 mb-2"></i>
            <p class="text-sm text-gray-500">Nenhum prazo próximo</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Estatísticas do Mês -->
    <div class="modern-card p-6 animate-slide-in-up" style="animation-delay: 0.4s;">
      <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
        <i class="ph ph-chart-bar text-lg text-primary-600"></i>
        Este Mês
      </h3>

      <div class="space-y-3">
        <div class="flex items-center justify-between p-3 rounded-lg bg-gradient-to-br from-blue-50 to-cyan-50">
          <span class="text-sm text-blue-900">Eventos</span>
          <span class="text-lg font-bold text-blue-900"><?= $estatisticas['eventos_mes'] ?? 0 ?></span>
        </div>
        <div class="flex items-center justify-between p-3 rounded-lg bg-gradient-to-br from-emerald-50 to-green-50">
          <span class="text-sm text-emerald-900">Sessões</span>
          <span class="text-lg font-bold text-emerald-900"><?= $estatisticas['sessoes_mes'] ?? 0 ?></span>
        </div>
        <div class="flex items-center justify-between p-3 rounded-lg bg-gradient-to-br from-purple-50 to-violet-50">
          <span class="text-sm text-purple-900">Tarefas</span>
          <span class="text-lg font-bold text-purple-900"><?= $estatisticas['tarefas_mes'] ?? 0 ?></span>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Modal de Evento -->
<div id="event-modal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-soft-lg max-w-lg w-full max-h-[90vh] overflow-y-auto animate-scale-in">

    <!-- Header -->
    <div class="flex items-center justify-between p-6 border-b border-gray-100">
      <h3 class="text-lg font-semibold text-gray-900">Novo Evento</h3>
      <button type="button"
        onclick="closeEventModal()"
        class="p-2 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all">
        <i class="ph ph-x text-xl"></i>
      </button>
    </div>

    <!-- Body -->
    <form id="event-form" class="p-6 space-y-4">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Título do Evento <span class="text-red-500">*</span>
        </label>
        <input type="text"
          name="titulo"
          required
          placeholder="Ex: Prova de Cálculo"
          class="modern-input">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Descrição
        </label>
        <textarea name="descricao"
          rows="3"
          placeholder="Detalhes do evento..."
          class="modern-textarea"></textarea>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Data <span class="text-red-500">*</span>
          </label>
          <input type="date"
            name="data_evento"
            required
            class="modern-input">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Horário
          </label>
          <input type="time"
            name="horario"
            class="modern-input">
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Disciplina
        </label>
        <select name="disciplina_id" class="modern-select w-full">
          <option value="">Sem disciplina</option>
          <?php if (!empty($disciplinas)): ?>
            <?php foreach ($disciplinas as $disciplina): ?>
              <option value="<?= $disciplina['id'] ?>">
                <?= htmlspecialchars($disciplina['nome']) ?>
              </option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Tipo
        </label>
        <select name="tipo" class="modern-select w-full">
          <option value="evento">Evento</option>
          <option value="prova">Prova</option>
          <option value="trabalho">Trabalho</option>
          <option value="aula">Aula</option>
          <option value="outro">Outro</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Cor
        </label>
        <div class="grid grid-cols-8 gap-2">
          <button type="button"
            class="w-8 h-8 rounded-lg bg-blue-500 hover:scale-110 transition-transform"
            onclick="selectColor('#3b82f6', this)"></button>
          <button type="button"
            class="w-8 h-8 rounded-lg bg-red-500 hover:scale-110 transition-transform"
            onclick="selectColor('#ef4444', this)"></button>
          <button type="button"
            class="w-8 h-8 rounded-lg bg-green-500 hover:scale-110 transition-transform"
            onclick="selectColor('#10b981', this)"></button>
          <button type="button"
            class="w-8 h-8 rounded-lg bg-yellow-500 hover:scale-110 transition-transform"
            onclick="selectColor('#f59e0b', this)"></button>
          <button type="button"
            class="w-8 h-8 rounded-lg bg-purple-500 hover:scale-110 transition-transform"
            onclick="selectColor('#8b5cf6', this)"></button>
          <button type="button"
            class="w-8 h-8 rounded-lg bg-pink-500 hover:scale-110 transition-transform"
            onclick="selectColor('#ec4899', this)"></button>
          <button type="button"
            class="w-8 h-8 rounded-lg bg-indigo-500 hover:scale-110 transition-transform"
            onclick="selectColor('#6366f1', this)"></button>
          <button type="button"
            class="w-8 h-8 rounded-lg bg-gray-500 hover:scale-110 transition-transform"
            onclick="selectColor('#6b7280', this)"></button>
        </div>
        <input type="hidden" name="cor" value="#3b82f6">
      </div>

    </form>

    <!-- Footer -->
    <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-100">
      <button type="button"
        onclick="closeEventModal()"
        class="btn-modern bg-gray-100 text-gray-700 hover:bg-gray-200">
        <i class="ph ph-x text-lg"></i>
        <span>Cancelar</span>
      </button>
      <button type="button"
        onclick="saveEvent()"
        class="btn-modern bg-gradient-to-r from-primary-600 to-primary-700 text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-soft-lg">
        <i class="ph ph-check text-lg"></i>
        <span>Salvar Evento</span>
      </button>
    </div>

  </div>
</div>

<script>
  // Dados dos eventos
  const eventos = <?= json_encode($eventos ?? []) ?>;

  let currentDate = new Date();
  let selectedDate = new Date();

  // Inicializar calendário
  function initCalendar() {
    renderCalendar();
    renderEventsList(selectedDate);
  }

  // Renderizar calendário
  function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    // Atualizar título
    document.getElementById('calendar-month').textContent =
      currentDate.toLocaleDateString('pt-BR', {
        month: 'long',
        year: 'numeric'
      });

    // Primeiro e último dia do mês
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);

    const grid = document.getElementById('calendar-grid');
    grid.innerHTML = '';

    // Dias vazios antes do início do mês
    for (let i = 0; i < firstDay.getDay(); i++) {
      const emptyDay = document.createElement('div');
      emptyDay.className = 'aspect-square';
      grid.appendChild(emptyDay);
    }

    // Dias do mês
    for (let day = 1; day <= lastDay.getDate(); day++) {
      const dayDate = new Date(year, month, day);
      const isToday = dayDate.toDateString() === new Date().toDateString();
      const isSelected = dayDate.toDateString() === selectedDate.toDateString();

      const dayEl = document.createElement('button');
      dayEl.type = 'button';
      dayEl.className = `aspect-square p-2 rounded-lg text-sm transition-all ${
        isToday ? 'bg-primary-500 text-white font-bold' :
        isSelected ? 'bg-primary-100 text-primary-900 font-semibold' :
        'hover:bg-gray-100 text-gray-700'
      }`;

      // Eventos do dia
      const dayEvents = eventos.filter(e => {
        const eventDate = new Date(e.data_evento);
        return eventDate.toDateString() === dayDate.toDateString();
      });

      dayEl.innerHTML = `
        <div class="font-semibold">${day}</div>
        ${dayEvents.length > 0 ? `
          <div class="flex gap-1 mt-1 justify-center">
            ${dayEvents.slice(0, 3).map(e => `
              <div class="w-1.5 h-1.5 rounded-full" style="background-color: ${e.cor || '#3b82f6'}"></div>
            `).join('')}
            ${dayEvents.length > 3 ? `<div class="text-xs">+${dayEvents.length - 3}</div>` : ''}
          </div>
        ` : ''}
      `;

      dayEl.onclick = () => selectDay(dayDate);
      grid.appendChild(dayEl);
    }
  }

  // Selecionar dia
  function selectDay(date) {
    selectedDate = date;
    renderCalendar();
    renderEventsList(date);
  }

  // Renderizar lista de eventos
  function renderEventsList(date) {
    const dateStr = date.toLocaleDateString('pt-BR', {
      day: 'numeric',
      month: 'long'
    });
    document.getElementById('sidebar-date').textContent = dateStr;

    const dayEvents = eventos.filter(e => {
      const eventDate = new Date(e.data_evento);
      return eventDate.toDateString() === date.toDateString();
    });

    const list = document.getElementById('events-list');
    const noEvents = document.getElementById('no-events');

    if (dayEvents.length === 0) {
      list.classList.add('hidden');
      noEvents.classList.remove('hidden');
      return;
    }

    list.classList.remove('hidden');
    noEvents.classList.add('hidden');

    list.innerHTML = dayEvents.map(evento => `
      <div class="p-3 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
        <div class="flex items-start gap-3">
          <div class="w-3 h-3 rounded-full mt-1" style="background-color: ${evento.cor || '#3b82f6'}"></div>
          <div class="flex-1">
            <h4 class="text-sm font-semibold text-gray-900">${evento.titulo}</h4>
            ${evento.horario ? `<p class="text-xs text-gray-500 mt-1">${evento.horario}</p>` : ''}
            ${evento.descricao ? `<p class="text-xs text-gray-600 mt-1">${evento.descricao}</p>` : ''}
          </div>
        </div>
      </div>
    `).join('');
  }

  // Mudar mês
  function changeMonth(delta) {
    currentDate.setMonth(currentDate.getMonth() + delta);
    renderCalendar();
  }

  // Ir para hoje
  function goToToday() {
    currentDate = new Date();
    selectedDate = new Date();
    renderCalendar();
    renderEventsList(selectedDate);
  }

  // Modal
  function openEventModal() {
    document.getElementById('event-modal').classList.remove('hidden');
  }

  function closeEventModal() {
    document.getElementById('event-modal').classList.add('hidden');
    document.getElementById('event-form').reset();
  }

  // Selecionar cor
  function selectColor(color, button) {
    document.querySelector('input[name="cor"]').value = color;
    document.querySelectorAll('[onclick^="selectColor"]').forEach(btn => {
      btn.classList.remove('ring-2', 'ring-offset-2', 'ring-primary-500');
    });
    button.classList.add('ring-2', 'ring-offset-2', 'ring-primary-500');
  }

  // Salvar evento
  async function saveEvent() {
    const form = document.getElementById('event-form');
    const formData = new FormData(form);

    try {
      const response = await fetch('/calendario/criar', {
        method: 'POST',
        body: formData
      });

      if (response.ok) {
        closeEventModal();
        showModernToast('Evento criado com sucesso!', 'success');
        setTimeout(() => location.reload(), 1000);
      } else {
        showModernToast('Erro ao criar evento', 'error');
      }
    } catch (error) {
      showModernToast('Erro ao criar evento', 'error');
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

  // Inicializar
  initCalendar();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>