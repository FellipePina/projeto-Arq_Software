<?php
$titulo = 'Nova Tarefa';
$active = 'tarefas';
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Page Header - Design moderno -->
<div class="mb-8 animate-slide-in-up">
  <div class="flex items-center gap-4">
    <a href="/tarefas" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 hover:text-gray-900 transition-all">
      <i class="ph ph-arrow-left text-xl"></i>
    </a>
    <div>
      <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
        Nova Tarefa
      </h1>
      <p class="mt-2 text-sm text-gray-600">Crie uma nova tarefa para seus estudos</p>
    </div>
  </div>
</div>

<!-- Formulário - Design elegante -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  <!-- Formulário Principal -->
  <div class="lg:col-span-2 animate-slide-in-up" style="animation-delay: 0.1s;">
    <form method="POST" action="/tarefas/criar" class="modern-card p-6">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

      <!-- Título -->
      <div class="mb-6">
        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">
          Título da Tarefa <span class="text-red-500">*</span>
        </label>
        <input type="text"
          id="titulo"
          name="titulo"
          required
          placeholder="Ex: Estudar Capítulo 3 de Álgebra Linear"
          class="modern-input"
          value="<?= htmlspecialchars($tarefa['titulo'] ?? '') ?>">
      </div>

      <!-- Descrição -->
      <div class="mb-6">
        <label for="descricao" class="block text-sm font-medium text-gray-700 mb-2">
          Descrição
        </label>
        <textarea id="descricao"
          name="descricao"
          rows="4"
          placeholder="Descreva os detalhes da tarefa..."
          class="modern-textarea"><?= htmlspecialchars($tarefa['descricao'] ?? '') ?></textarea>
      </div>

      <!-- Disciplina e Prioridade -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
        <div>
          <label for="disciplina_id" class="block text-sm font-medium text-gray-700 mb-2">
            Disciplina <span class="text-red-500">*</span>
          </label>
          <select id="disciplina_id" name="disciplina_id" required class="modern-select w-full">
            <option value="">Selecione uma disciplina</option>
            <?php if (!empty($disciplinas)): ?>
              <?php foreach ($disciplinas as $disciplina): ?>
                <option value="<?= $disciplina['id'] ?>"
                  <?= (isset($tarefa['disciplina_id']) && $tarefa['disciplina_id'] == $disciplina['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($disciplina['nome']) ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <div>
          <label for="prioridade" class="block text-sm font-medium text-gray-700 mb-2">
            Prioridade <span class="text-red-500">*</span>
          </label>
          <select id="prioridade" name="prioridade" required class="modern-select w-full">
            <option value="baixa" <?= (isset($tarefa['prioridade']) && $tarefa['prioridade'] === 'baixa') ? 'selected' : '' ?>>
              🟢 Baixa
            </option>
            <option value="media" <?= (isset($tarefa['prioridade']) && $tarefa['prioridade'] === 'media') ? 'selected' : '' ?> selected>
              🟡 Média
            </option>
            <option value="alta" <?= (isset($tarefa['prioridade']) && $tarefa['prioridade'] === 'alta') ? 'selected' : '' ?>>
              🔴 Alta
            </option>
          </select>
        </div>
      </div>

      <!-- Status e Prazo -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
        <div>
          <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
            Status
          </label>
          <select id="status" name="status" class="modern-select w-full">
            <option value="pendente" <?= (isset($tarefa['status']) && $tarefa['status'] === 'pendente') ? 'selected' : '' ?> selected>
              Pendente
            </option>
            <option value="em_andamento" <?= (isset($tarefa['status']) && $tarefa['status'] === 'em_andamento') ? 'selected' : '' ?>>
              Em Andamento
            </option>
            <option value="concluida" <?= (isset($tarefa['status']) && $tarefa['status'] === 'concluida') ? 'selected' : '' ?>>
              Concluída
            </option>
          </select>
        </div>

        <div>
          <label for="prazo" class="block text-sm font-medium text-gray-700 mb-2">
            Prazo
          </label>
          <input type="date"
            id="prazo"
            name="prazo"
            class="modern-input"
            value="<?= htmlspecialchars($tarefa['prazo'] ?? '') ?>">
        </div>
      </div>

      <!-- Tempo Estimado -->
      <div class="mb-6">
        <label for="tempo_estimado" class="block text-sm font-medium text-gray-700 mb-2">
          Tempo Estimado (em minutos)
        </label>
        <input type="number"
          id="tempo_estimado"
          name="tempo_estimado"
          min="0"
          step="15"
          placeholder="Ex: 60"
          class="modern-input"
          value="<?= htmlspecialchars($tarefa['tempo_estimado'] ?? '') ?>">
        <p class="mt-1 text-xs text-gray-500">Opcional: tempo estimado para conclusão da tarefa</p>
      </div>

      <!-- Subtarefas -->
      <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
          <label class="block text-sm font-medium text-gray-700">
            Subtarefas
          </label>
          <button type="button"
            onclick="addSubtarefa()"
            class="text-sm text-primary-600 hover:text-primary-700 flex items-center gap-1">
            <i class="ph ph-plus-circle"></i>
            Adicionar Subtarefa
          </button>
        </div>
        <div id="subtarefas-container" class="space-y-2">
          <!-- Subtarefas serão adicionadas aqui -->
        </div>
      </div>

      <!-- Botões -->
      <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
        <a href="/tarefas" class="btn-modern bg-gray-100 text-gray-700 hover:bg-gray-200">
          <i class="ph ph-x text-lg"></i>
          <span>Cancelar</span>
        </a>
        <button type="submit" class="btn-modern bg-gradient-to-r from-primary-600 to-primary-700 text-white hover:from-primary-700 hover:to-primary-800 shadow-soft hover:shadow-soft-lg">
          <i class="ph ph-check text-lg"></i>
          <span>Criar Tarefa</span>
        </button>
      </div>
    </form>
  </div>

  <!-- Preview Card -->
  <div class="animate-slide-in-up" style="animation-delay: 0.2s;">
    <div class="modern-card p-6 sticky top-6">
      <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
        <i class="ph ph-eye text-lg text-primary-600"></i>
        Preview da Tarefa
      </h3>

      <div id="preview-card" class="glass p-4 rounded-lg">
        <div class="mb-3">
          <h4 id="preview-titulo" class="font-semibold text-gray-900 mb-2">
            Título da tarefa
          </h4>
          <span id="preview-disciplina" class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-md bg-gray-100 text-gray-700">
            <i class="ph ph-book text-sm"></i>
            Disciplina
          </span>
        </div>

        <p id="preview-descricao" class="text-sm text-gray-600 mb-3">
          Descrição da tarefa aparecerá aqui...
        </p>

        <div class="flex items-center gap-2 mb-3">
          <span id="preview-prioridade" class="badge-modern bg-amber-100 text-amber-700 text-xs">
            🟡 Média
          </span>
          <span id="preview-prazo" class="text-xs text-gray-500 flex items-center gap-1">
            <i class="ph ph-calendar text-sm"></i>
            Sem prazo
          </span>
        </div>

        <div id="preview-tempo" class="text-xs text-gray-500 mb-3 flex items-center gap-1">
          <i class="ph ph-timer text-sm"></i>
          Tempo estimado não definido
        </div>

        <div id="preview-subtarefas" class="hidden">
          <div class="text-xs text-gray-600 mb-2">Subtarefas:</div>
          <div id="preview-subtarefas-list" class="space-y-1">
            <!-- Lista de subtarefas -->
          </div>
        </div>
      </div>

      <!-- Dicas -->
      <div class="mt-6 p-4 rounded-lg bg-gradient-to-br from-blue-50 to-cyan-50 border border-blue-100">
        <h4 class="text-sm font-semibold text-blue-900 mb-2 flex items-center gap-2">
          <i class="ph ph-lightbulb text-lg"></i>
          Dicas
        </h4>
        <ul class="text-xs text-blue-800 space-y-1">
          <li>• Use títulos claros e objetivos</li>
          <li>• Divida tarefas grandes em subtarefas</li>
          <li>• Defina prazos realistas</li>
          <li>• Priorize tarefas urgentes</li>
        </ul>
      </div>
    </div>
  </div>

</div>

<script>
  let subtarefaCount = 0;

  // Atualizar preview em tempo real
  document.getElementById('titulo').addEventListener('input', (e) => {
    document.getElementById('preview-titulo').textContent = e.target.value || 'Título da tarefa';
  });

  document.getElementById('descricao').addEventListener('input', (e) => {
    document.getElementById('preview-descricao').textContent = e.target.value || 'Descrição da tarefa aparecerá aqui...';
  });

  document.getElementById('disciplina_id').addEventListener('change', (e) => {
    const selectedOption = e.target.options[e.target.selectedIndex];
    document.getElementById('preview-disciplina').innerHTML = `
      <i class="ph ph-book text-sm"></i>
      ${selectedOption.text || 'Disciplina'}
    `;
  });

  document.getElementById('prioridade').addEventListener('change', (e) => {
    const prioridades = {
      'alta': {
        label: '🔴 Alta',
        class: 'bg-red-100 text-red-700'
      },
      'media': {
        label: '🟡 Média',
        class: 'bg-amber-100 text-amber-700'
      },
      'baixa': {
        label: '🟢 Baixa',
        class: 'bg-blue-100 text-blue-700'
      }
    };
    const p = prioridades[e.target.value];
    document.getElementById('preview-prioridade').className = `badge-modern ${p.class} text-xs`;
    document.getElementById('preview-prioridade').textContent = p.label;
  });

  document.getElementById('prazo').addEventListener('change', (e) => {
    if (e.target.value) {
      const date = new Date(e.target.value);
      document.getElementById('preview-prazo').innerHTML = `
        <i class="ph ph-calendar text-sm"></i>
        ${date.toLocaleDateString('pt-BR', { day: '2-digit', month: 'short' })}
      `;
    } else {
      document.getElementById('preview-prazo').innerHTML = `
        <i class="ph ph-calendar text-sm"></i>
        Sem prazo
      `;
    }
  });

  document.getElementById('tempo_estimado').addEventListener('input', (e) => {
    if (e.target.value) {
      const horas = Math.floor(e.target.value / 60);
      const minutos = e.target.value % 60;
      let texto = '';
      if (horas > 0) texto += `${horas}h `;
      if (minutos > 0) texto += `${minutos}min`;
      document.getElementById('preview-tempo').innerHTML = `
        <i class="ph ph-timer text-sm"></i>
        ${texto}
      `;
    } else {
      document.getElementById('preview-tempo').innerHTML = `
        <i class="ph ph-timer text-sm"></i>
        Tempo estimado não definido
      `;
    }
  });

  // Adicionar subtarefa
  function addSubtarefa() {
    const container = document.getElementById('subtarefas-container');
    const id = ++subtarefaCount;

    const div = document.createElement('div');
    div.className = 'flex items-center gap-2 animate-scale-in';
    div.innerHTML = `
      <input type="text"
             name="subtarefas[]"
             placeholder="Nome da subtarefa"
             class="flex-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
             onchange="updateSubtarefasPreview()">
      <button type="button"
              onclick="removeSubtarefa(this)"
              class="p-2 rounded-lg hover:bg-red-50 text-red-600 transition-all">
        <i class="ph ph-trash text-lg"></i>
      </button>
    `;

    container.appendChild(div);
    updateSubtarefasPreview();
  }

  // Remover subtarefa
  function removeSubtarefa(button) {
    button.parentElement.remove();
    updateSubtarefasPreview();
  }

  // Atualizar preview de subtarefas
  function updateSubtarefasPreview() {
    const inputs = document.querySelectorAll('input[name="subtarefas[]"]');
    const list = document.getElementById('preview-subtarefas-list');
    const container = document.getElementById('preview-subtarefas');

    list.innerHTML = '';

    if (inputs.length > 0) {
      container.classList.remove('hidden');
      inputs.forEach(input => {
        if (input.value.trim()) {
          const item = document.createElement('div');
          item.className = 'flex items-center gap-2 text-xs text-gray-600';
          item.innerHTML = `
            <i class="ph ph-circle text-xs"></i>
            ${input.value}
          `;
          list.appendChild(item);
        }
      });
    } else {
      container.classList.add('hidden');
    }
  }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>