<?php
$titulo = isset($disciplina) ? 'Editar Disciplina' : 'Nova Disciplina';
$active = 'disciplinas';
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Breadcrumb -->
<nav class="breadcrumb mb-6">
  <a href="/disciplinas" class="breadcrumb-link">Disciplinas</a>
  <span class="breadcrumb-separator">/</span>
  <span class="breadcrumb-current"><?= isset($disciplina) ? 'Editar' : 'Nova' ?></span>
</nav>

<div class="max-w-3xl mx-auto">
  <!-- Card do Formulário -->
  <div class="card">
    <div class="card-header">
      <h2 class="card-title">
        <i class="fas fa-book mr-2"></i>
        <?= isset($disciplina) ? 'Editar Disciplina' : 'Nova Disciplina' ?>
      </h2>
      <p class="card-subtitle">Preencha as informações da disciplina</p>
    </div>

    <form method="POST" action="<?= isset($disciplina) ? "/disciplinas/{$disciplina['id']}/atualizar" : '/disciplinas/salvar' ?>"
      id="form-disciplina">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

      <div class="card-body space-y-6">
        <!-- Nome -->
        <div>
          <label for="nome" class="form-label required">
            Nome da Disciplina *
          </label>
          <input type="text" id="nome" name="nome"
            class="form-input <?= isset($erros['nome']) ? 'border-red-500' : '' ?>"
            value="<?= htmlspecialchars($disciplina['nome'] ?? '') ?>"
            placeholder="Ex: Matemática, História, Programação..."
            required>
          <?php if (isset($erros['nome'])): ?>
            <p class="form-error"><?= $erros['nome'] ?></p>
          <?php endif; ?>
        </div>

        <!-- Código -->
        <div>
          <label for="codigo" class="form-label">
            Código/Sigla
          </label>
          <input type="text" id="codigo" name="codigo"
            class="form-input"
            value="<?= htmlspecialchars($disciplina['codigo'] ?? '') ?>"
            placeholder="Ex: MAT101, HIST202, PROG301..."
            maxlength="20">
          <p class="form-help">Código ou sigla da disciplina (opcional)</p>
        </div>

        <!-- Descrição -->
        <div>
          <label for="descricao" class="form-label">
            Descrição
          </label>
          <textarea id="descricao" name="descricao" rows="4"
            class="form-textarea"
            placeholder="Descreva os tópicos, objetivos ou informações importantes..."><?= htmlspecialchars($disciplina['descricao'] ?? '') ?></textarea>
          <p class="form-help">Adicione detalhes sobre o conteúdo da disciplina</p>
        </div>

        <!-- Cor -->
        <div>
          <label for="cor" class="form-label">
            Cor de Identificação
          </label>
          <div class="flex items-center space-x-4">
            <input type="color" id="cor" name="cor"
              class="h-12 w-20 rounded cursor-pointer border-2 border-gray-300"
              value="<?= $disciplina['cor'] ?? '#3b82f6' ?>">

            <!-- Cores predefinidas -->
            <div class="flex flex-wrap gap-2">
              <?php
              $cores = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'];
              foreach ($cores as $cor): ?>
                <button type="button"
                  class="w-8 h-8 rounded-full border-2 border-gray-200 hover:border-gray-400 transition"
                  style="background-color: <?= $cor ?>"
                  onclick="document.getElementById('cor').value = '<?= $cor ?>'"></button>
              <?php endforeach; ?>
            </div>
          </div>
          <p class="form-help">Escolha uma cor para identificar esta disciplina</p>
        </div>

        <!-- Professor -->
        <div>
          <label for="professor" class="form-label">
            Professor
          </label>
          <input type="text" id="professor" name="professor"
            class="form-input"
            value="<?= htmlspecialchars($disciplina['professor'] ?? '') ?>"
            placeholder="Nome do professor ou instrutor">
        </div>

        <!-- Categoria -->
        <div>
          <label for="categoria_id" class="form-label">
            Categoria
          </label>
          <select id="categoria_id" name="categoria_id" class="form-select">
            <option value="">Sem categoria</option>
            <?php if (!empty($categorias)): ?>
              <?php foreach ($categorias as $categoria): ?>
                <option value="<?= $categoria['id'] ?>"
                  <?= isset($disciplina) && $disciplina['categoria_id'] == $categoria['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($categoria['nome']) ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
          <p class="form-help">Organize suas disciplinas por categoria</p>
        </div>

        <!-- Meta de Horas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="meta_horas_semana" class="form-label">
              Meta Semanal (horas)
            </label>
            <input type="number" id="meta_horas_semana" name="meta_horas_semana"
              class="form-input"
              value="<?= $disciplina['meta_horas_semana'] ?? '' ?>"
              placeholder="0"
              min="0"
              max="168"
              step="0.5">
          </div>

          <div>
            <label for="meta_horas_mes" class="form-label">
              Meta Mensal (horas)
            </label>
            <input type="number" id="meta_horas_mes" name="meta_horas_mes"
              class="form-input"
              value="<?= $disciplina['meta_horas_mes'] ?? '' ?>"
              placeholder="0"
              min="0"
              step="1">
          </div>
        </div>

        <!-- Status -->
        <?php if (isset($disciplina)): ?>
          <div>
            <label class="form-label">Status</label>
            <div class="flex items-center space-x-4">
              <label class="inline-flex items-center">
                <input type="radio" name="status" value="ativa"
                  class="form-radio"
                  <?= ($disciplina['status'] ?? 'ativa') === 'ativa' ? 'checked' : '' ?>>
                <span class="ml-2">Ativa</span>
              </label>
              <label class="inline-flex items-center">
                <input type="radio" name="status" value="arquivada"
                  class="form-radio"
                  <?= ($disciplina['status'] ?? 'ativa') === 'arquivada' ? 'checked' : '' ?>>
                <span class="ml-2">Arquivada</span>
              </label>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <div class="card-footer flex items-center justify-between">
        <a href="/disciplinas" class="btn btn-outline">
          <i class="fas fa-arrow-left mr-2"></i>
          Cancelar
        </a>

        <button type="submit" class="btn btn-primary" id="btn-salvar">
          <i class="fas fa-save mr-2"></i>
          <?= isset($disciplina) ? 'Atualizar' : 'Criar' ?> Disciplina
        </button>
      </div>
    </form>
  </div>

  <!-- Preview Card -->
  <div class="card mt-6" id="preview-card">
    <div class="card-header">
      <h3 class="text-sm font-semibold text-gray-700">
        <i class="fas fa-eye mr-2"></i>
        Pré-visualização
      </h3>
    </div>

    <div class="card-body">
      <div class="card card-hover">
        <div id="preview-cor" class="h-3 rounded-t-lg" style="background-color: #3b82f6;"></div>

        <div class="p-4">
          <h3 class="text-lg font-semibold text-gray-900 mb-1" id="preview-nome">
            Nome da Disciplina
          </h3>
          <span class="badge badge-gray" id="preview-codigo" style="display: none;"></span>

          <p class="text-sm text-gray-600 mt-2" id="preview-descricao"></p>

          <?php if (isset($disciplina)): ?>
            <div class="grid grid-cols-3 gap-4 mt-4">
              <div class="text-center">
                <div class="text-2xl font-bold text-primary-600"><?= $disciplina['tarefas_pendentes'] ?? 0 ?></div>
                <div class="text-xs text-gray-500">Tarefas</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-green-600"><?= $disciplina['sessoes_semana'] ?? 0 ?></div>
                <div class="text-xs text-gray-500">Sessões</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600"><?= number_format($disciplina['tempo_total'] ?? 0, 1) ?>h</div>
                <div class="text-xs text-gray-500">Horas</div>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Preview em tempo real
  const nomeInput = document.getElementById('nome');
  const codigoInput = document.getElementById('codigo');
  const descricaoInput = document.getElementById('descricao');
  const corInput = document.getElementById('cor');

  const previewNome = document.getElementById('preview-nome');
  const previewCodigo = document.getElementById('preview-codigo');
  const previewDescricao = document.getElementById('preview-descricao');
  const previewCor = document.getElementById('preview-cor');

  function updatePreview() {
    const nome = nomeInput.value || 'Nome da Disciplina';
    const codigo = codigoInput.value;
    const descricao = descricaoInput.value;
    const cor = corInput.value;

    previewNome.textContent = nome;
    previewCor.style.backgroundColor = cor;

    if (codigo) {
      previewCodigo.textContent = codigo;
      previewCodigo.style.display = 'inline-block';
    } else {
      previewCodigo.style.display = 'none';
    }

    previewDescricao.textContent = descricao;
  }

  nomeInput.addEventListener('input', updatePreview);
  codigoInput.addEventListener('input', updatePreview);
  descricaoInput.addEventListener('input', updatePreview);
  corInput.addEventListener('input', updatePreview);

  // Inicializa preview
  updatePreview();

  // Validação do formulário
  const form = document.getElementById('form-disciplina');
  form.addEventListener('submit', (e) => {
    const nome = nomeInput.value.trim();

    if (!nome) {
      e.preventDefault();
      AjaxHelper.showToast('O nome da disciplina é obrigatório', 'error');
      nomeInput.focus();
      return;
    }

    // Mostra loading no botão
    const btn = document.getElementById('btn-salvar');
    AjaxHelper.showLoading(btn);
  });
</script>

<style>
  .required::after {
    content: '';
    margin-left: 0.25rem;
  }

  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>