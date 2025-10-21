<?php

namespace App\Controllers;

use App\Models\Tarefa;
use App\Models\Subtarefa;
use App\Models\Disciplina;

/**
 * TarefaController - Gerencia tarefas e subtarefas
 *
 * Princípios SOLID:
 * - Single Responsibility: apenas operações de tarefas
 */
class TarefaController extends BaseController
{
  private Tarefa $tarefaModel;
  private Subtarefa $subtarefaModel;
  private Disciplina $disciplinaModel;

  public function __construct()
  {
    parent::__construct();
    $this->tarefaModel = new Tarefa();
    $this->subtarefaModel = new Subtarefa();
    $this->disciplinaModel = new Disciplina();
  }

  /**
   * Lista todas as tarefas
   */
  public function index(): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];

    // Filtros
    $filtros = [
      'disciplina_id' => $_GET['disciplina'] ?? null,
      'status' => $_GET['status'] ?? null,
      'prioridade' => $_GET['prioridade'] ?? null,
      'concluida' => isset($_GET['concluida']) ? (int) $_GET['concluida'] : null
    ];

    // Remove filtros vazios
    $filtros = array_filter($filtros, fn($v) => $v !== null);

    $tarefas = $this->tarefaModel->buscarPorUsuario($usuarioId, $filtros);
    $atrasadas = $this->tarefaModel->buscarAtrasadas($usuarioId);
    $proximasDoPrazo = $this->tarefaModel->buscarProximasDoPrazo($usuarioId);
    $disciplinas = $this->disciplinaModel->buscarPorUsuario($usuarioId);
    $estatisticas = $this->tarefaModel->contarPorStatus($usuarioId);

    // Adiciona progresso de subtarefas
    foreach ($tarefas as &$tarefa) {
      $tarefa['progresso'] = $this->subtarefaModel->calcularProgresso($tarefa['id']);
    }

    $this->render('tarefa/index', [
      'tarefas' => $tarefas,
      'atrasadas' => $atrasadas,
      'proximas_prazo' => $proximasDoPrazo,
      'disciplinas' => $disciplinas,
      'estatisticas' => $estatisticas,
      'filtros' => $filtros,
      'titulo' => 'Minhas Tarefas'
    ]);
  }

  /**
   * Exibe formulário de nova tarefa
   */
  public function create(): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $disciplinas = $this->disciplinaModel->buscarPorUsuario($usuarioId);

    $this->render('tarefa/create', [
      'disciplinas' => $disciplinas,
      'titulo' => 'Nova Tarefa'
    ]);
  }

  /**
   * Salva nova tarefa
   */
  public function store(): void
  {
    $this->requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('/tarefas');
      return;
    }

    if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
      $this->setFlashMessage('error', 'Token de segurança inválido');
      $this->redirect('/tarefas/create');
      return;
    }

    $dados = [
      'titulo' => trim(strip_tags($_POST['titulo'] ?? '')),
      'descricao' => trim(strip_tags($_POST['descricao'] ?? '')),
      'disciplina_id' => !empty($_POST['disciplina_id']) ? (int) $_POST['disciplina_id'] : null,
      'data_entrega' => !empty($_POST['data_entrega']) ? $_POST['data_entrega'] : null,
      'prioridade' => $_POST['prioridade'] ?? 'media',
      'usuario_id' => $_SESSION['usuario_id']
    ];

    if (empty($dados['titulo'])) {
      $this->setFlashMessage('error', 'O título da tarefa é obrigatório');
      $this->redirect('/tarefas/create');
      return;
    }

    $id = $this->tarefaModel->criar($dados);

    if ($id) {
      $this->setFlashMessage('success', 'Tarefa criada com sucesso!');
      $this->redirect('/tarefas');
    } else {
      $this->setFlashMessage('error', 'Erro ao criar tarefa');
      $this->redirect('/tarefas/create');
    }
  }

  /**
   * Exibe detalhes da tarefa
   */
  public function show(int $id): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $tarefa = $this->tarefaModel->buscarPorIdEUsuario($id, $usuarioId);

    if (!$tarefa) {
      $this->setFlashMessage('Tarefa não encontrada', 'error');
      $this->redirect('/tarefas');
      return;
    }

    $subtarefas = $this->subtarefaModel->buscarPorTarefa($id);
    $tarefa['progresso'] = $this->subtarefaModel->calcularProgresso($id);

    $this->render('tarefa/show', [
      'tarefa' => $tarefa,
      'subtarefas' => $subtarefas,
      'titulo' => $tarefa['titulo']
    ]);
  }

  /**
   * Exibe formulário de edição
   */
  public function edit(int $id): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $tarefa = $this->tarefaModel->buscarPorIdEUsuario($id, $usuarioId);

    if (!$tarefa) {
      $this->setFlashMessage('Tarefa não encontrada', 'error');
      $this->redirect('/tarefas');
      return;
    }

    $disciplinas = $this->disciplinaModel->buscarPorUsuario($usuarioId);

    $this->render('tarefa/edit', [
      'tarefa' => $tarefa,
      'disciplinas' => $disciplinas,
      'titulo' => 'Editar Tarefa'
    ]);
  }

  /**
   * Atualiza tarefa
   */
  public function update(int $id): void
  {
    $this->requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('/tarefas');
      return;
    }

    if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
      $this->setFlashMessage('error', 'Token de segurança inválido');
      $this->redirect("/tarefas/edit/{$id}");
      return;
    }

    $usuarioId = $_SESSION['usuario_id'];
    $dados = [
      'titulo' => trim(strip_tags($_POST['titulo'] ?? '')),
      'descricao' => trim(strip_tags($_POST['descricao'] ?? '')),
      'disciplina_id' => !empty($_POST['disciplina_id']) ? (int) $_POST['disciplina_id'] : null,
      'data_entrega' => !empty($_POST['data_entrega']) ? $_POST['data_entrega'] : null,
      'prioridade' => $_POST['prioridade'] ?? 'media',
      'status' => $_POST['status'] ?? 'pendente'
    ];

    if (empty($dados['titulo'])) {
      $this->setFlashMessage('error', 'O título da tarefa é obrigatório');
      $this->redirect("/tarefas/edit/{$id}");
      return;
    }

    $sucesso = $this->tarefaModel->atualizar($id, $dados, $usuarioId);

    if ($sucesso) {
      $this->setFlashMessage('success', 'Tarefa atualizada com sucesso!');
      $this->redirect('/tarefas');
    } else {
      $this->setFlashMessage('error', 'Erro ao atualizar tarefa');
      $this->redirect("/tarefas/edit/{$id}");
    }
  }

  /**
   * Marca tarefa como concluída
   */
  public function complete(int $id): void
  {
    $this->requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('/tarefas');
      return;
    }

    if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
      $this->setFlashMessage('error', 'Token de segurança inválido');
      $this->redirect('/tarefas');
      return;
    }

    $usuarioId = $_SESSION['usuario_id'];
    $sucesso = $this->tarefaModel->marcarConcluida($id, $usuarioId);

    if ($sucesso) {
      // Adiciona pontos de gamificação
      $gamificacao = new \App\Models\Gamificacao();
      $gamificacao->adicionarPontos($usuarioId, 10);
      $gamificacao->atualizarSequencia($usuarioId);
      $gamificacao->verificarConquistas($usuarioId);

      $this->setFlashMessage('success', 'Tarefa concluída! +10 pontos');
    } else {
      $this->setFlashMessage('error', 'Erro ao concluir tarefa');
    }

    $this->redirect('/tarefas');
  }

  /**
   * Marca tarefa como pendente
   */
  public function uncomplete(int $id): void
  {
    $this->requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('/tarefas');
      return;
    }

    if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
      $this->setFlashMessage('error', 'Token de segurança inválido');
      $this->redirect('/tarefas');
      return;
    }

    $usuarioId = $_SESSION['usuario_id'];
    $sucesso = $this->tarefaModel->marcarPendente($id, $usuarioId);

    if ($sucesso) {
      $this->setFlashMessage('success', 'Tarefa marcada como pendente');
    } else {
      $this->setFlashMessage('error', 'Erro ao atualizar tarefa');
    }

    $this->redirect('/tarefas');
  }

  /**
   * Adiciona subtarefa
   */
  public function addSubtask(int $tarefaId): void
  {
    $this->requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect("/tarefas/show/{$tarefaId}");
      return;
    }

    if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
      $this->setFlashMessage('error', 'Token de segurança inválido');
      $this->redirect("/tarefas/show/{$tarefaId}");
      return;
    }

    $dados = [
      'tarefa_id' => $tarefaId,
      'titulo' => trim(strip_tags($_POST['titulo'] ?? ''))
    ];

    if (empty($dados['titulo'])) {
      $this->setFlashMessage('error', 'O título da subtarefa é obrigatório');
      $this->redirect("/tarefas/show/{$tarefaId}");
      return;
    }

    $id = $this->subtarefaModel->criar($dados);

    if ($id) {
      $this->setFlashMessage('success', 'Subtarefa adicionada!');
    } else {
      $this->setFlashMessage('error', 'Erro ao adicionar subtarefa');
    }

    $this->redirect("/tarefas/show/{$tarefaId}");
  }

  /**
   * Marca/desmarca subtarefa como concluída
   */
  public function toggleSubtask(int $tarefaId, int $subtarefaId): void
  {
    $this->requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect("/tarefas/show/{$tarefaId}");
      return;
    }

    if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
      echo json_encode(['success' => false, 'message' => 'Token inválido']);
      return;
    }

    $acao = $_POST['acao'] ?? 'marcar';

    if ($acao === 'marcar') {
      $sucesso = $this->subtarefaModel->marcarConcluida($subtarefaId);
    } else {
      $sucesso = $this->subtarefaModel->marcarPendente($subtarefaId);
    }

    // Retorna JSON para requisições AJAX
    if (
      !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) {

      $progresso = $this->subtarefaModel->calcularProgresso($tarefaId);

      echo json_encode([
        'success' => $sucesso,
        'progresso' => $progresso
      ]);
      return;
    }

    $this->redirect("/tarefas/show/{$tarefaId}");
  }
}
