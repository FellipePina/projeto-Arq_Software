<?php
$titulo = 'Minhas Tarefas';
$active = 'tarefas';
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Page Header - Design moderno -->
<div class="mb-8 animate-slide-in-up">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
        Minhas Tarefas
      </h1>
      <p class="mt-2 text-sm text-gray-600">Organize e acompanhe suas tarefas de estudo</p>
    </div>
    <a href="/tarefas/criar" class="btn-modern bg-gradient-to-r from-primary-600 to-primary-700 text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-soft-lg">
      <i class="ph ph-plus-circle text-lg"></i>
      <span>Nova Tarefa</span>
    </a>
  </div>
</div>

<!-- Filtros e Visualização - Design minimalista -->
<div class="mb-8 animate-slide-in-up" style="animation-delay: 0.1s;">
  <div class="modern-card p-4">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      <!-- Filtros -->
      <div class="flex flex-col sm:flex-row gap-3 flex-1">
        <div class="relative flex-1 sm:max-w-xs">
          <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
          <input type="text"
            id="search-tarefas"
            placeholder="Buscar tarefas..."
            class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
        </div>

        <select id="filter-disciplina" class="px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
          <option value="">Todas as Disciplinas</option>
          <?php if (!empty($disciplinas)): ?>
            <?php foreach ($disciplinas as $disc): ?>
              <option value="<?= $disc['id'] ?>"><?= htmlspecialchars($disc['nome']) ?></option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>

        <select id="filter-status" class="px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
          <option value="pendente">Pendentes</option>
          <option value="em_andamento">Em Andamento</option>
          <option value="concluida">Concluídas</option>
          <option value="todas">Todas</option>
        </select>

        <select id="filter-prioridade" class="px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
          <option value="">Todas Prioridades</option>
          <option value="alta">Alta</option>
          <option value="media">Média</option>
          <option value="baixa">Baixa</option>
        </select>
      </div>

      <!-- Visualização -->
      <div class="flex items-center gap-2 border border-gray-200 rounded-lg p-1">
        <button type="button"
          class="p-2 rounded-md hover:bg-gray-100 transition-colors text-primary-600 bg-primary-50"
          id="view-kanban"
          data-view="kanban"
          title="Visualização Kanban">
          <i class="ph ph-kanban text-lg"></i>
        </button>
        <button type="button"
          class="p-2 rounded-md hover:bg-gray-100 transition-colors text-gray-600 hover:text-primary-600"
          id="view-list"
          data-view="list"
          title="Visualização em lista">
          <i class="ph ph-list text-lg"></i>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Kanban Board - Design elegante -->
<div id="kanban-view" class="animate-slide-in-up" style="animation-delay: 0.2s;">
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Coluna: Pendente -->
    <div class="modern-card p-4">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <div class="w-3 h-3 rounded-full bg-gradient-to-r from-gray-400 to-gray-500"></div>
          <h3 class="font-semibold text-gray-900">Pendente</h3>
          <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
            <span class="status-count" data-status="pendente">0</span>
          </span>
        </div>
      </div>
      <div class="space-y-3 min-h-[400px]" id="column-pendente" data-status="pendente">
        <!-- Tarefas pendentes serão inseridas aqui -->
      </div>
    </div>

    <!-- Coluna: Em Andamento -->
    <div class="modern-card p-4">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <div class="w-3 h-3 rounded-full bg-gradient-to-r from-blue-400 to-primary-500"></div>
          <h3 class="font-semibold text-gray-900">Em Andamento</h3>
          <span class="text-xs text-gray-500 bg-blue-100 px-2 py-0.5 rounded-full">
            <span class="status-count" data-status="em_andamento">0</span>
          </span>
        </div>
      </div>
      <div class="space-y-3 min-h-[400px]" id="column-em_andamento" data-status="em_andamento">
        <!-- Tarefas em andamento serão inseridas aqui -->
      </div>
    </div>

    <!-- Coluna: Concluída -->
    <div class="modern-card p-4">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <div class="w-3 h-3 rounded-full bg-gradient-to-r from-emerald-400 to-green-500"></div>
          <h3 class="font-semibold text-gray-900">Concluída</h3>
          <span class="text-xs text-gray-500 bg-emerald-100 px-2 py-0.5 rounded-full">
            <span class="status-count" data-status="concluida">0</span>
          </span>
        </div>
      </div>
      <div class="space-y-3 min-h-[400px]" id="column-concluida" data-status="concluida">
        <!-- Tarefas concluídas serão inseridas aqui -->
      </div>
    </div>

  </div>
</div>

<!-- List View (oculta por padrão) -->
<div id="list-view" class="hidden">
  <div class="modern-card">
    <div class="divide-y divide-gray-100" id="tarefas-list">
      <!-- Tarefas em lista serão inseridas aqui -->
    </div>
  </div>
</div>

<!-- Empty State -->
<div id="empty-state" class="hidden animate-scale-in">
  <div class="modern-card p-12 text-center">
    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center">
      <i class="ph ph-check-square text-4xl text-primary-600"></i>
    </div>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">Nenhuma tarefa encontrada</h3>
    <p class="text-gray-600 mb-6 max-w-md mx-auto">
      Comece criando sua primeira tarefa para organizar seus estudos
    </p>
    <a href="/tarefas/criar" class="btn-modern bg-gradient-to-r from-primary-600 to-primary-700 text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-soft-lg inline-flex">
      <i class="ph ph-plus-circle text-lg"></i>
      <span>Criar Primeira Tarefa</span>
    </a>
  </div>
</div>

<script>
  // Dados das tarefas vindos do PHP
  const tarefasData = <?= json_encode($tarefas ?? []) ?>;

  // Renderizar tarefas no Kanban
  function renderKanban() {
    // Limpar colunas
    ['pendente', 'em_andamento', 'concluida'].forEach(status => {
      const column = document.getElementById(`column-${status}`);
      if (column) column.innerHTML = '';
    });

    // Filtrar tarefas
    const filteredTarefas = filterTarefas(tarefasData);

    // Atualizar contadores
    updateStatusCounts(filteredTarefas);

    if (filteredTarefas.length === 0) {
      document.getElementById('kanban-view').classList.add('hidden');
      document.getElementById('empty-state').classList.remove('hidden');
      return;
    }

    document.getElementById('kanban-view').classList.remove('hidden');
    document.getElementById('empty-state').classList.add('hidden');

    // Renderizar cada tarefa na coluna correspondente
    filteredTarefas.forEach((tarefa, index) => {
      const column = document.getElementById(`column-${tarefa.status}`);
      if (column) {
        column.innerHTML += createTarefaCard(tarefa, index);
      }
    });
  }

  // Criar card de tarefa
  function createTarefaCard(tarefa, index) {
    const prioridadeColors = {
      'alta': 'from-red-500 to-rose-600',
      'media': 'from-amber-500 to-yellow-600',
      'baixa': 'from-blue-500 to-cyan-600'
    };

    const prioridadeLabels = {
      'alta': 'Alta',
      'media': 'Média',
      'baixa': 'Baixa'
    };

    const prioridadeBg = {
      'alta': 'bg-red-100 text-red-700',
      'media': 'bg-amber-100 text-amber-700',
      'baixa': 'bg-blue-100 text-blue-700'
    };

    return `
      <div class="glass p-4 rounded-lg hover:shadow-soft transition-all cursor-move tarefa-card animate-scale-in"
           data-tarefa-id="${tarefa.id}"
           style="animation-delay: ${index * 0.05}s;">

        <!-- Header -->
        <div class="flex items-start justify-between mb-3">
          <div class="flex-1 min-w-0">
            <h4 class="font-semibold text-gray-900 mb-1 truncate">${escapeHtml(tarefa.titulo)}</h4>
            ${tarefa.disciplina_nome ? `
              <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-md bg-gray-100 text-gray-700">
                <i class="ph ph-book text-sm"></i>
                ${escapeHtml(tarefa.disciplina_nome)}
              </span>
            ` : ''}
          </div>

          <div class="dropdown ml-2" x-data="{ open: false }">
            <button @click="open = !open" class="p-1 rounded hover:bg-gray-100 text-gray-400">
              <i class="ph ph-dots-three-vertical"></i>
            </button>
            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 glass rounded-xl shadow-soft-lg py-2 z-10" style="display: none;">
              <a href="/tarefas/${tarefa.id}/editar" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                <i class="ph ph-pencil text-base mr-3 text-blue-500"></i> Editar
              </a>
              <button onclick="excluirTarefa(${tarefa.id})" class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                <i class="ph ph-trash text-base mr-3"></i> Excluir
              </button>
            </div>
          </div>
        </div>

        <!-- Descrição -->
        ${tarefa.descricao ? `
          <p class="text-sm text-gray-600 mb-3 line-clamp-2">${escapeHtml(tarefa.descricao)}</p>
        ` : ''}

        <!-- Prioridade e Data -->
        <div class="flex items-center justify-between text-xs mb-3">
          <span class="badge-modern ${prioridadeBg[tarefa.prioridade] || 'bg-gray-100 text-gray-700'}">
            ${prioridadeLabels[tarefa.prioridade] || tarefa.prioridade}
          </span>
          ${tarefa.prazo ? `
            <span class="text-gray-500 flex items-center gap-1">
              <i class="ph ph-calendar text-sm"></i>
              ${formatDate(tarefa.prazo)}
            </span>
          ` : ''}
        </div>

        <!-- Progress (se houver subtarefas) -->
        ${tarefa.subtarefas_total > 0 ? `
          <div class="mb-3">
            <div class="flex items-center justify-between mb-1 text-xs text-gray-600">
              <span>Progresso</span>
              <span>${Math.round((tarefa.subtarefas_concluidas / tarefa.subtarefas_total) * 100)}%</span>
            </div>
            <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
              <div class="h-full rounded-full bg-gradient-to-r ${prioridadeColors[tarefa.prioridade] || 'from-gray-400 to-gray-500'}"
                   style="width: ${(tarefa.subtarefas_concluidas / tarefa.subtarefas_total) * 100}%"></div>
            </div>
            <div class="text-xs text-gray-500 mt-1">
              ${tarefa.subtarefas_concluidas} de ${tarefa.subtarefas_total} subtarefas
            </div>
          </div>
        ` : ''}

        <!-- Actions -->
        <div class="flex items-center gap-2">
          <button onclick="toggleStatus(${tarefa.id}, '${tarefa.status}')"
                  class="flex-1 text-xs px-3 py-2 rounded-lg ${tarefa.status === 'concluida' ? 'bg-gray-100 text-gray-700' : 'bg-primary-50 text-primary-700'} hover:bg-primary-100 transition-all">
            <i class="ph ${tarefa.status === 'concluida' ? 'ph-arrow-counter-clockwise' : 'ph-check'} text-sm"></i>
            ${tarefa.status === 'concluida' ? 'Reabrir' : 'Concluir'}
          </button>
          <a href="/tarefas/${tarefa.id}"
             class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition-all">
            <i class="ph ph-eye text-sm"></i>
          </a>
        </div>
      </div>
    `;
  }

  // Filtrar tarefas
  function filterTarefas(tarefas) {
    const search = document.getElementById('search-tarefas').value.toLowerCase();
    const disciplina = document.getElementById('filter-disciplina').value;
    const status = document.getElementById('filter-status').value;
    const prioridade = document.getElementById('filter-prioridade').value;

    return tarefas.filter(tarefa => {
      const matchSearch = !search ||
        tarefa.titulo.toLowerCase().includes(search) ||
        (tarefa.descricao && tarefa.descricao.toLowerCase().includes(search));

      const matchDisciplina = !disciplina || tarefa.disciplina_id == disciplina;
      const matchStatus = status === 'todas' || tarefa.status === status;
      const matchPrioridade = !prioridade || tarefa.prioridade === prioridade;

      return matchSearch && matchDisciplina && matchStatus && matchPrioridade;
    });
  }

  // Atualizar contadores
  function updateStatusCounts(tarefas) {
    const counts = {
      pendente: 0,
      em_andamento: 0,
      concluida: 0
    };
    tarefas.forEach(t => counts[t.status]++);

    Object.keys(counts).forEach(status => {
      const el = document.querySelector(`.status-count[data-status="${status}"]`);
      if (el) el.textContent = counts[status];
    });
  }

  // Toggle status da tarefa
  async function toggleStatus(id, currentStatus) {
    const newStatus = currentStatus === 'concluida' ? 'pendente' : 'concluida';

    try {
      // Atualizar no array local
      const tarefa = tarefasData.find(t => t.id === id);
      if (tarefa) {
        tarefa.status = newStatus;
        renderKanban();
        showModernToast(newStatus === 'concluida' ? 'Tarefa concluída!' : 'Tarefa reaberta!', 'success');
      }
    } catch (error) {
      showModernToast('Erro ao atualizar tarefa', 'error');
    }
  }

  // Excluir tarefa
  async function excluirTarefa(id) {
    if (!confirm('Deseja realmente excluir esta tarefa?')) return;

    try {
      const index = tarefasData.findIndex(t => t.id === id);
      if (index > -1) {
        tarefasData.splice(index, 1);
        renderKanban();
        showModernToast('Tarefa excluída com sucesso', 'success');
      }
    } catch (error) {
      showModernToast('Erro ao excluir tarefa', 'error');
    }
  }

  // Helpers
  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR', {
      day: '2-digit',
      month: 'short'
    });
  }

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

  // Event listeners
  document.getElementById('search-tarefas').addEventListener('input', () => renderKanban());
  document.getElementById('filter-disciplina').addEventListener('change', () => renderKanban());
  document.getElementById('filter-status').addEventListener('change', () => renderKanban());
  document.getElementById('filter-prioridade').addEventListener('change', () => renderKanban());

  // Toggle views
  document.querySelectorAll('[data-view]').forEach(btn => {
    btn.addEventListener('click', () => {
      const view = btn.dataset.view;

      document.querySelectorAll('[data-view]').forEach(b => {
        b.classList.remove('text-primary-600', 'bg-primary-50');
        b.classList.add('text-gray-600');
      });
      btn.classList.remove('text-gray-600');
      btn.classList.add('text-primary-600', 'bg-primary-50');

      if (view === 'list') {
        document.getElementById('kanban-view').classList.add('hidden');
        document.getElementById('list-view').classList.remove('hidden');
      } else {
        document.getElementById('kanban-view').classList.remove('hidden');
        document.getElementById('list-view').classList.add('hidden');
      }
    });
  });

  // Inicializar
  renderKanban();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>