<?php
$titulo = 'Minhas Disciplinas';
$active = 'disciplinas';
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Page Header - Design moderno -->
<div class="mb-8 animate-slide-in-up">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
        Minhas Disciplinas
      </h1>
      <p class="mt-2 text-sm text-gray-600">Organize e acompanhe o progresso de suas disciplinas</p>
    </div>
    <a href="/disciplinas/criar" class="btn-modern bg-gradient-to-r from-primary-600 to-primary-700 text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-soft-lg">
      <i class="ph ph-plus-circle text-lg"></i>
      <span>Nova Disciplina</span>
    </a>
  </div>
</div>

<!-- Filtros e Visualização - Design minimalista -->
<div class="mb-8 animate-slide-in-up" style="animation-delay: 0.1s;">
  <div class="modern-card p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="flex flex-col sm:flex-row gap-3 flex-1">
      <div class="relative flex-1 sm:max-w-xs">
        <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
        <input type="text"
          id="search-disciplinas"
          placeholder="Buscar disciplinas..."
          class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
      </div>

      <select id="filter-status" class="px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
        <option value="ativas">Ativas</option>
        <option value="arquivadas">Arquivadas</option>
        <option value="todas">Todas</option>
      </select>
    </div>

    <div class="flex items-center gap-2 border border-gray-200 rounded-lg p-1">
      <button type="button"
        class="p-2 rounded-md hover:bg-gray-100 transition-colors text-gray-600 hover:text-primary-600 active:bg-primary-50"
        id="view-grid"
        data-view="grid"
        title="Visualização em grade">
        <i class="ph ph-squares-four text-lg"></i>
      </button>
      <button type="button"
        class="p-2 rounded-md hover:bg-gray-100 transition-colors text-gray-600 hover:text-primary-600 active:bg-primary-50"
        id="view-list"
        data-view="list"
        title="Visualização em lista">
        <i class="ph ph-list text-lg"></i>
      </button>
    </div>
  </div>
</div>

<!-- Grid de Disciplinas - Design elegante -->
<div id="disciplinas-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <?php if (!empty($disciplinas)): ?>
    <?php foreach ($disciplinas as $disciplina): ?>
      <div class="modern-card group disciplina-card animate-scale-in"
        data-disciplina-id="<?= $disciplina['id'] ?>"
        style="animation-delay: <?= 0.2 + (array_search($disciplina, $disciplinas) * 0.05) ?>s;">

        <!-- Header com gradiente da cor -->
        <div class="h-2 rounded-t-xl bg-gradient-to-r"
          style="background: linear-gradient(to right, <?= htmlspecialchars($disciplina['cor']) ?>, <?= htmlspecialchars($disciplina['cor']) ?>dd);"></div>

        <div class="p-6">
          <!-- Título e ações -->
          <div class="flex items-start justify-between mb-4">
            <div class="flex-1 min-w-0">
              <h3 class="text-lg font-semibold text-gray-900 mb-2 truncate group-hover:text-primary-600 transition-colors">
                <?= htmlspecialchars($disciplina['nome']) ?>
              </h3>
              <?php if (!empty($disciplina['codigo'])): ?>
                <span class="badge-modern bg-gray-100 text-gray-700 text-xs">
                  <?= htmlspecialchars($disciplina['codigo']) ?>
                </span>
              <?php endif; ?>
            </div>

            <div class="dropdown ml-3" x-data="{ open: false }">
              <button @click="open = !open" type="button"
                class="p-2 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all">
                <i class="ph ph-dots-three-vertical text-lg"></i>
              </button>

              <div x-show="open"
                @click.away="open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-2 w-48 glass rounded-xl shadow-soft-lg py-2 z-10"
                style="display: none;">
                <a href="/disciplinas/<?= $disciplina['id'] ?>"
                  class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                  <i class="ph ph-eye text-base mr-3 text-primary-500"></i> Visualizar
                </a>
                <a href="/disciplinas/<?= $disciplina['id'] ?>/editar"
                  class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                  <i class="ph ph-pencil text-base mr-3 text-blue-500"></i> Editar
                </a>
                <button type="button"
                  class="w-full flex items-center px-4 py-2 text-sm text-yellow-600 hover:bg-yellow-50 transition-colors"
                  onclick="arquivarDisciplina(<?= $disciplina['id'] ?>)">
                  <i class="ph ph-archive text-base mr-3"></i> Arquivar
                </button>
                <div class="border-t border-gray-100 my-2"></div>
                <button type="button"
                  class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
                  onclick="excluirDisciplina(<?= $disciplina['id'] ?>)">
                  <i class="ph ph-trash text-base mr-3"></i> Excluir
                </button>
              </div>
            </div>
          </div>

          <!-- Descrição -->
          <?php if (!empty($disciplina['descricao'])): ?>
            <p class="text-sm text-gray-600 mb-5 line-clamp-2">
              <?= htmlspecialchars($disciplina['descricao']) ?>
            </p>
          <?php endif; ?>

          <!-- Estatísticas - Design moderno com gradientes -->
          <div class="grid grid-cols-3 gap-3 mb-5">
            <div class="text-center p-3 rounded-lg bg-gradient-to-br from-primary-50 to-primary-100/50">
              <div class="text-2xl font-bold bg-gradient-to-r from-primary-600 to-primary-700 bg-clip-text text-transparent">
                <?= $disciplina['tarefas_pendentes'] ?? 0 ?>
              </div>
              <div class="text-xs text-gray-600 mt-1">Tarefas</div>
            </div>
            <div class="text-center p-3 rounded-lg bg-gradient-to-br from-emerald-50 to-green-100/50">
              <div class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-green-700 bg-clip-text text-transparent">
                <?= $disciplina['sessoes_semana'] ?? 0 ?>
              </div>
              <div class="text-xs text-gray-600 mt-1">Sessões</div>
            </div>
            <div class="text-center p-3 rounded-lg bg-gradient-to-br from-amber-50 to-yellow-100/50">
              <div class="text-2xl font-bold bg-gradient-to-r from-amber-600 to-yellow-700 bg-clip-text text-transparent">
                <?= number_format($disciplina['tempo_total'] ?? 0, 1) ?>h
              </div>
              <div class="text-xs text-gray-600 mt-1">Horas</div>
            </div>
          </div>

          <!-- Progress Bar - Design minimalista -->
          <?php if (isset($disciplina['progresso'])): ?>
            <div class="mb-5">
              <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-600">Progresso</span>
                <span class="text-xs font-semibold text-gray-900">
                  <?= $disciplina['progresso'] ?>%
                </span>
              </div>
              <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500 bg-gradient-to-r <?= $disciplina['progresso'] >= 70 ? 'from-emerald-500 to-green-600' : ($disciplina['progresso'] >= 40 ? 'from-amber-500 to-yellow-600' : 'from-red-500 to-rose-600') ?>"
                  style="width: <?= $disciplina['progresso'] ?>%"></div>
              </div>
            </div>
          <?php endif; ?>

          <!-- Footer com ações rápidas - Design moderno -->
          <div class="flex items-center gap-3 pt-5 border-t border-gray-100">
            <a href="/tarefas?disciplina=<?= $disciplina['id'] ?>"
              class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-primary-700 bg-primary-50 rounded-lg hover:bg-primary-100 transition-all">
              <i class="ph ph-check-square text-base"></i>
              <span>Tarefas</span>
            </a>
            <a href="/pomodoro?disciplina=<?= $disciplina['id'] ?>"
              class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-all">
              <i class="ph ph-timer text-base"></i>
              <span>Pomodoro</span>
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <!-- Empty State - Design elegante -->
    <div class="col-span-full">
      <div class="modern-card p-12 text-center animate-scale-in">
        <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center">
          <i class="ph ph-books text-4xl text-primary-600"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Nenhuma disciplina encontrada</h3>
        <p class="text-gray-600 mb-6 max-w-md mx-auto">
          Comece sua jornada de estudos criando sua primeira disciplina
        </p>
        <a href="/disciplinas/criar" class="btn-modern bg-gradient-to-r from-primary-600 to-primary-700 text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-soft-lg inline-flex">
          <i class="ph ph-plus-circle text-lg"></i>
          <span>Criar Primeira Disciplina</span>
        </a>
      </div>
    </div>
  <?php endif; ?>
</div>

<script>
  // Toggle entre grid e list view - Modernizado
  const viewButtons = document.querySelectorAll('[data-view]');
  const container = document.getElementById('disciplinas-container');

  viewButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const view = btn.dataset.view;

      // Atualiza botões ativos com visual moderno
      viewButtons.forEach(b => {
        b.classList.remove('text-primary-600', 'bg-primary-50');
        b.classList.add('text-gray-600');
      });
      btn.classList.remove('text-gray-600');
      btn.classList.add('text-primary-600', 'bg-primary-50');

      // Muda visualização com animação
      container.style.opacity = '0';
      container.style.transform = 'translateY(10px)';

      setTimeout(() => {
        if (view === 'list') {
          container.classList.remove('grid-cols-1', 'md:grid-cols-2', 'lg:grid-cols-3');
          container.classList.add('grid-cols-1');
        } else {
          container.classList.remove('grid-cols-1');
          container.classList.add('grid-cols-1', 'md:grid-cols-2', 'lg:grid-cols-3');
        }

        container.style.opacity = '1';
        container.style.transform = 'translateY(0)';
      }, 200);

      // Salva preferência
      localStorage.setItem('disciplinas-view', view);
    });
  });

  // Carrega preferência salva
  const savedView = localStorage.getItem('disciplinas-view') || 'grid';
  const savedButton = document.querySelector(`[data-view="${savedView}"]`);
  if (savedButton) {
    savedButton.classList.remove('text-gray-600');
    savedButton.classList.add('text-primary-600', 'bg-primary-50');
  }

  // Busca em tempo real com animação
  const searchInput = document.getElementById('search-disciplinas');
  if (searchInput) {
    searchInput.addEventListener('input', debounce((e) => {
      const term = e.target.value.toLowerCase();
      const cards = document.querySelectorAll('.disciplina-card');

      cards.forEach((card, index) => {
        const text = card.textContent.toLowerCase();
        const matches = text.includes(term);

        if (matches) {
          card.style.display = '';
          card.style.animation = `scaleIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) ${index * 0.05}s both`;
        } else {
          card.style.display = 'none';
        }
      });
    }, 300));
  }

  // Filtro por status
  const filterStatus = document.getElementById('filter-status');
  if (filterStatus) {
    filterStatus.addEventListener('change', (e) => {
      const status = e.target.value;
      window.location.href = `/disciplinas?status=${status}`;
    });
  }

  // Arquivar disciplina com toast moderno
  async function arquivarDisciplina(id) {
    if (!confirm('Deseja arquivar esta disciplina? Ela será movida para arquivadas.')) return;

    try {
      const card = document.querySelector(`[data-disciplina-id="${id}"]`);
      card.style.opacity = '0';
      card.style.transform = 'scale(0.95)';

      setTimeout(() => card.remove(), 300);

      showModernToast('Disciplina arquivada com sucesso', 'success');
    } catch (error) {
      showModernToast('Erro ao arquivar disciplina', 'error');
    }
  }

  // Excluir disciplina com animação
  async function excluirDisciplina(id) {
    if (!confirm('Deseja realmente excluir esta disciplina? Esta ação não pode ser desfeita.')) return;

    try {
      const card = document.querySelector(`[data-disciplina-id="${id}"]`);
      card.style.opacity = '0';
      card.style.transform = 'scale(0.95)';

      setTimeout(() => card.remove(), 300);

      showModernToast('Disciplina excluída com sucesso', 'success');
    } catch (error) {
      showModernToast('Erro ao excluir disciplina', 'error');
    }
  }

  // Toast moderno
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
    toast.innerHTML = `
      <i class="ph-fill ${icons[type]} text-lg"></i>
      <span class="text-sm font-medium">${message}</span>
    `;

    document.body.appendChild(toast);
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(-10px)';
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }

  // Debounce helper
  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>