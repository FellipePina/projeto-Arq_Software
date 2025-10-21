<?php
$titulo = 'Minhas Anotações';
$active = 'anotacoes';
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Page Header - Design moderno -->
<div class="mb-8 animate-slide-in-up">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
        Minhas Anotações
      </h1>
      <p class="mt-2 text-sm text-gray-600">Organize suas notas e ideias de estudo</p>
    </div>
    <button type="button"
      onclick="openNoteModal()"
      class="btn-modern bg-gradient-to-r from-primary-600 to-primary-700 text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-soft-lg">
      <i class="ph ph-plus-circle text-lg"></i>
      <span>Nova Anotação</span>
    </button>
  </div>
</div>

<!-- Filtros e Busca -->
<div class="mb-8 animate-slide-in-up" style="animation-delay: 0.1s;">
  <div class="modern-card p-4">
    <div class="flex flex-col sm:flex-row gap-3">
      <div class="relative flex-1">
        <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
        <input type="text"
          id="search-notes"
          placeholder="Buscar anotações..."
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

      <div class="flex items-center gap-2 border border-gray-200 rounded-lg p-1">
        <button type="button"
          class="px-3 py-2 rounded-md text-sm transition-all text-primary-600 bg-primary-50"
          id="view-grid"
          data-view="grid"
          title="Grade">
          <i class="ph ph-squares-four"></i>
        </button>
        <button type="button"
          class="px-3 py-2 rounded-md text-sm transition-all text-gray-600 hover:text-primary-600"
          id="view-list"
          data-view="list"
          title="Lista">
          <i class="ph ph-list"></i>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Grid de Anotações -->
<div id="notes-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <?php if (!empty($anotacoes)): ?>
    <?php foreach ($anotacoes as $index => $nota): ?>
      <div class="note-card modern-card p-5 cursor-pointer hover:shadow-soft-lg transition-all group animate-scale-in"
        style="animation-delay: <?= 0.1 + ($index * 0.05) ?>s; border-top: 4px solid <?= htmlspecialchars($nota['cor'] ?? '#3b82f6') ?>;"
        data-note-id="<?= $nota['id'] ?>"
        onclick="viewNote(<?= $nota['id'] ?>)">

        <!-- Header -->
        <div class="flex items-start justify-between mb-3">
          <div class="flex-1 min-w-0">
            <h3 class="text-lg font-semibold text-gray-900 mb-1 truncate group-hover:text-primary-600 transition-colors">
              <?= htmlspecialchars($nota['titulo']) ?>
            </h3>
            <?php if (!empty($nota['disciplina_nome'])): ?>
              <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-md bg-gray-100 text-gray-700">
                <i class="ph ph-book text-sm"></i>
                <?= htmlspecialchars($nota['disciplina_nome']) ?>
              </span>
            <?php endif; ?>
          </div>

          <div class="flex items-center gap-1 ml-2" onclick="event.stopPropagation()">
            <button type="button"
              onclick="togglePin(<?= $nota['id'] ?>, <?= $nota['fixada'] ? 'false' : 'true' ?>)"
              class="p-2 rounded-lg hover:bg-gray-100 transition-all <?= $nota['fixada'] ? 'text-amber-500' : 'text-gray-400' ?>"
              title="<?= $nota['fixada'] ? 'Desafixar' : 'Fixar' ?>">
              <i class="ph<?= $nota['fixada'] ? '-fill' : '' ?> ph-push-pin text-lg"></i>
            </button>
            <div class="dropdown" x-data="{ open: false }">
              <button @click="open = !open" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400 transition-all">
                <i class="ph ph-dots-three-vertical text-lg"></i>
              </button>
              <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 glass rounded-xl shadow-soft-lg py-2 z-10" style="display: none;">
                <button onclick="editNote(<?= $nota['id'] ?>)" class="w-full flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                  <i class="ph ph-pencil text-base mr-3 text-blue-500"></i> Editar
                </button>
                <button onclick="duplicateNote(<?= $nota['id'] ?>)" class="w-full flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                  <i class="ph ph-copy text-base mr-3 text-primary-500"></i> Duplicar
                </button>
                <div class="border-t border-gray-100 my-2"></div>
                <button onclick="deleteNote(<?= $nota['id'] ?>)" class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                  <i class="ph ph-trash text-base mr-3"></i> Excluir
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Conteúdo (preview) -->
        <div class="text-sm text-gray-600 mb-4 line-clamp-4">
          <?= nl2br(htmlspecialchars(substr($nota['conteudo'], 0, 200))) ?>
          <?= strlen($nota['conteudo']) > 200 ? '...' : '' ?>
        </div>

        <!-- Tags -->
        <?php if (!empty($nota['tags'])): ?>
          <div class="flex flex-wrap gap-2 mb-4">
            <?php
            $tags = explode(',', $nota['tags']);
            foreach (array_slice($tags, 0, 3) as $tag):
            ?>
              <span class="text-xs px-2 py-1 rounded-md bg-primary-50 text-primary-700">
                #<?= htmlspecialchars(trim($tag)) ?>
              </span>
            <?php endforeach; ?>
            <?php if (count($tags) > 3): ?>
              <span class="text-xs text-gray-500">+<?= count($tags) - 3 ?></span>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-100 text-xs text-gray-500">
          <span><?= date('d/m/Y H:i', strtotime($nota['data_atualizacao'])) ?></span>
          <?php if ($nota['fixada']): ?>
            <span class="flex items-center gap-1 text-amber-600">
              <i class="ph-fill ph-push-pin"></i>
              Fixada
            </span>
          <?php endif; ?>
        </div>

      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <!-- Empty State -->
    <div class="col-span-full">
      <div class="modern-card p-12 text-center animate-scale-in">
        <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center">
          <i class="ph ph-note text-4xl text-primary-600"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Nenhuma anotação encontrada</h3>
        <p class="text-gray-600 mb-6 max-w-md mx-auto">
          Comece criando sua primeira anotação para organizar suas ideias
        </p>
        <button type="button"
          onclick="openNoteModal()"
          class="btn-modern bg-gradient-to-r from-primary-600 to-primary-700 text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-soft-lg inline-flex">
          <i class="ph ph-plus-circle text-lg"></i>
          <span>Criar Primeira Anotação</span>
        </button>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- Modal de Anotação -->
<div id="note-modal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-soft-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto animate-scale-in">

    <!-- Header -->
    <div class="flex items-center justify-between p-6 border-b border-gray-100">
      <h3 id="modal-title" class="text-lg font-semibold text-gray-900">Nova Anotação</h3>
      <button type="button"
        onclick="closeNoteModal()"
        class="p-2 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all">
        <i class="ph ph-x text-xl"></i>
      </button>
    </div>

    <!-- Body -->
    <form id="note-form" class="p-6 space-y-4">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
      <input type="hidden" id="note-id" name="id" value="">

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Título <span class="text-red-500">*</span>
        </label>
        <input type="text"
          name="titulo"
          id="note-titulo"
          required
          placeholder="Digite o título da anotação"
          class="modern-input">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Conteúdo <span class="text-red-500">*</span>
        </label>
        <textarea name="conteudo"
          id="note-conteudo"
          rows="8"
          required
          placeholder="Escreva suas anotações aqui..."
          class="modern-textarea"></textarea>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Disciplina
          </label>
          <select name="disciplina_id" id="note-disciplina" class="modern-select w-full">
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
            Cor
          </label>
          <div class="flex gap-2">
            <button type="button" class="w-8 h-8 rounded-lg bg-blue-500 hover:scale-110 transition-transform" onclick="selectColor('#3b82f6', this)"></button>
            <button type="button" class="w-8 h-8 rounded-lg bg-red-500 hover:scale-110 transition-transform" onclick="selectColor('#ef4444', this)"></button>
            <button type="button" class="w-8 h-8 rounded-lg bg-green-500 hover:scale-110 transition-transform" onclick="selectColor('#10b981', this)"></button>
            <button type="button" class="w-8 h-8 rounded-lg bg-yellow-500 hover:scale-110 transition-transform" onclick="selectColor('#f59e0b', this)"></button>
            <button type="button" class="w-8 h-8 rounded-lg bg-purple-500 hover:scale-110 transition-transform" onclick="selectColor('#8b5cf6', this)"></button>
            <button type="button" class="w-8 h-8 rounded-lg bg-pink-500 hover:scale-110 transition-transform" onclick="selectColor('#ec4899', this)"></button>
          </div>
          <input type="hidden" name="cor" id="note-cor" value="#3b82f6">
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Tags (separadas por vírgula)
        </label>
        <input type="text"
          name="tags"
          id="note-tags"
          placeholder="ex: importante, revisão, prova"
          class="modern-input">
      </div>

    </form>

    <!-- Footer -->
    <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-100">
      <button type="button"
        onclick="closeNoteModal()"
        class="btn-modern bg-gray-100 text-gray-700 hover:bg-gray-200">
        <i class="ph ph-x text-lg"></i>
        <span>Cancelar</span>
      </button>
      <button type="button"
        onclick="saveNote()"
        class="btn-modern bg-gradient-to-r from-primary-600 to-primary-700 text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-soft-lg">
        <i class="ph ph-check text-lg"></i>
        <span>Salvar</span>
      </button>
    </div>

  </div>
</div>

<script>
  // Modal
  function openNoteModal() {
    document.getElementById('note-modal').classList.remove('hidden');
    document.getElementById('modal-title').textContent = 'Nova Anotação';
    document.getElementById('note-form').reset();
    document.getElementById('note-id').value = '';
  }

  function closeNoteModal() {
    document.getElementById('note-modal').classList.add('hidden');
  }

  // Selecionar cor
  function selectColor(color, button) {
    document.getElementById('note-cor').value = color;
    document.querySelectorAll('[onclick^="selectColor"]').forEach(btn => {
      btn.classList.remove('ring-2', 'ring-offset-2', 'ring-primary-500');
    });
    button.classList.add('ring-2', 'ring-offset-2', 'ring-primary-500');
  }

  // Salvar anotação
  async function saveNote() {
    const form = document.getElementById('note-form');
    const formData = new FormData(form);
    const noteId = document.getElementById('note-id').value;
    const url = noteId ? `/anotacoes/${noteId}/editar` : '/anotacoes/criar';

    try {
      const response = await fetch(url, {
        method: 'POST',
        body: formData
      });

      if (response.ok) {
        closeNoteModal();
        showModernToast(noteId ? 'Anotação atualizada!' : 'Anotação criada!', 'success');
        setTimeout(() => location.reload(), 1000);
      } else {
        showModernToast('Erro ao salvar anotação', 'error');
      }
    } catch (error) {
      showModernToast('Erro ao salvar anotação', 'error');
    }
  }

  // Visualizar nota
  function viewNote(id) {
    window.location.href = `/anotacoes/${id}`;
  }

  // Editar nota
  async function editNote(id) {
    event.stopPropagation();
    // Carregar dados e abrir modal
    // Implementação simplificada - em produção, carregar via AJAX
    window.location.href = `/anotacoes/${id}/editar`;
  }

  // Fixar/Desafixar
  async function togglePin(id, fixada) {
    event.stopPropagation();
    try {
      const response = await fetch(`/anotacoes/${id}/fixar`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          fixada,
          csrf_token: '<?= $_SESSION['csrf_token'] ?? '' ?>'
        })
      });

      if (response.ok) {
        showModernToast(fixada ? 'Anotação fixada!' : 'Anotação desfixada!', 'success');
        setTimeout(() => location.reload(), 500);
      }
    } catch (error) {
      showModernToast('Erro ao fixar anotação', 'error');
    }
  }

  // Duplicar nota
  async function duplicateNote(id) {
    event.stopPropagation();
    if (!confirm('Deseja duplicar esta anotação?')) return;

    try {
      const response = await fetch(`/anotacoes/${id}/duplicar`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          csrf_token: '<?= $_SESSION['csrf_token'] ?? '' ?>'
        })
      });

      if (response.ok) {
        showModernToast('Anotação duplicada!', 'success');
        setTimeout(() => location.reload(), 1000);
      }
    } catch (error) {
      showModernToast('Erro ao duplicar anotação', 'error');
    }
  }

  // Excluir nota
  async function deleteNote(id) {
    event.stopPropagation();
    if (!confirm('Deseja realmente excluir esta anotação?')) return;

    try {
      const response = await fetch(`/anotacoes/${id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          csrf_token: '<?= $_SESSION['csrf_token'] ?? '' ?>'
        })
      });

      if (response.ok) {
        showModernToast('Anotação excluída!', 'success');
        document.querySelector(`[data-note-id="${id}"]`).remove();
      }
    } catch (error) {
      showModernToast('Erro ao excluir anotação', 'error');
    }
  }

  // Busca
  document.getElementById('search-notes')?.addEventListener('input', (e) => {
    const term = e.target.value.toLowerCase();
    document.querySelectorAll('.note-card').forEach(card => {
      const text = card.textContent.toLowerCase();
      card.style.display = text.includes(term) ? '' : 'none';
    });
  });

  // Filtro por disciplina
  document.getElementById('filter-disciplina')?.addEventListener('change', (e) => {
    const disciplinaId = e.target.value;
    window.location.href = disciplinaId ? `/anotacoes?disciplina=${disciplinaId}` : '/anotacoes';
  });

  // Toggle views
  document.querySelectorAll('[data-view]').forEach(btn => {
    btn.addEventListener('click', () => {
      const view = btn.dataset.view;
      const grid = document.getElementById('notes-grid');

      document.querySelectorAll('[data-view]').forEach(b => {
        b.classList.remove('text-primary-600', 'bg-primary-50');
        b.classList.add('text-gray-600');
      });
      btn.classList.remove('text-gray-600');
      btn.classList.add('text-primary-600', 'bg-primary-50');

      if (view === 'list') {
        grid.classList.remove('md:grid-cols-2', 'lg:grid-cols-3');
        grid.classList.add('grid-cols-1');
      } else {
        grid.classList.remove('grid-cols-1');
        grid.classList.add('md:grid-cols-2', 'lg:grid-cols-3');
      }
    });
  });

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
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>