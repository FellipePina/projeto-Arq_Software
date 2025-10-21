<?php

namespace App\Controllers;

use App\Models\Disciplina;
use App\Models\Tarefa;

/**
 * DisciplinaController - Gerencia disciplinas/matérias
 *
 * Princípios SOLID:
 * - Single Responsibility: apenas operações de disciplina
 * - Dependency Inversion: depende de abstrações (Models)
 */
class DisciplinaController extends BaseController
{
  private Disciplina $disciplinaModel;
  private Tarefa $tarefaModel;

  public function __construct()
  {
    parent::__construct();
    $this->disciplinaModel = new Disciplina();
    $this->tarefaModel = new Tarefa();
  }

  /**
   * Lista todas as disciplinas
   */
  public function index(): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $disciplinas = $this->disciplinaModel->buscarPorUsuario($usuarioId);

    // Busca estatísticas para cada disciplina
    foreach ($disciplinas as &$disciplina) {
      $disciplina['estatisticas'] = $this->disciplinaModel->buscarEstatisticas($disciplina['id']);
      $disciplina['tarefas_pendentes'] = $this->disciplinaModel->contarTarefasPendentes($disciplina['id']);
    }

    $this->render('disciplina/index', [
      'disciplinas' => $disciplinas,
      'titulo' => 'Minhas Disciplinas'
    ]);
  }

  /**
   * Exibe formulário de nova disciplina
   */
  public function create(): void
  {
    $this->requireLogin();

    $this->render('disciplina/create', [
      'titulo' => 'Nova Disciplina'
    ]);
  }

  /**
   * Salva nova disciplina
   */
  public function store(): void
  {
    $this->requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('/disciplinas');
      return;
    }

    if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
      $this->setFlashMessage('error', 'Token de segurança inválido');
      $this->redirect('/disciplinas/create');
      return;
    }

    $dados = [
      'nome' => $this->sanitizeInput($_POST['nome'] ?? ''),
      'descricao' => $this->sanitizeInput($_POST['descricao'] ?? ''),
      'cor' => $this->sanitizeInput($_POST['cor'] ?? '#3B82F6'),
      'usuario_id' => $_SESSION['usuario_id']
    ];

    if (empty($dados['nome'])) {
      $this->setFlashMessage('error', 'O nome da disciplina é obrigatório');
      $this->redirect('/disciplinas/create');
      return;
    }

    $id = $this->disciplinaModel->criar($dados);

    if ($id) {
      $this->setFlashMessage('success', 'Disciplina criada com sucesso!');
      $this->redirect('/disciplinas');
    } else {
      $this->setFlashMessage('error', 'Erro ao criar disciplina');
      $this->redirect('/disciplinas/create');
    }
  }

  /**
   * Exibe formulário de edição
   */
  public function edit(int $id): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $disciplina = $this->disciplinaModel->buscarPorIdEUsuario($id, $usuarioId);

    if (!$disciplina) {
      $this->setFlashMessage('Disciplina não encontrada', 'error');
      $this->redirect('/disciplinas');
      return;
    }

    $this->render('disciplina/edit', [
      'disciplina' => $disciplina,
      'titulo' => 'Editar Disciplina'
    ]);
  }

  /**
   * Atualiza disciplina
   */
  public function update(int $id): void
  {
    $this->requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('/disciplinas');
      return;
    }

    if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
      $this->setFlashMessage('error', 'Token de segurança inválido');
      $this->redirect("/disciplinas/edit/{$id}");
      return;
    }

    $usuarioId = $_SESSION['usuario_id'];
    $dados = [
      'nome' => $this->sanitizeInput($_POST['nome'] ?? ''),
      'descricao' => $this->sanitizeInput($_POST['descricao'] ?? ''),
      'cor' => $this->sanitizeInput($_POST['cor'] ?? '#3B82F6')
    ];

    if (empty($dados['nome'])) {
      $this->setFlashMessage('error', 'O nome da disciplina é obrigatório');
      $this->redirect("/disciplinas/edit/{$id}");
      return;
    }

    $sucesso = $this->disciplinaModel->atualizar($id, $dados, $usuarioId);

    if ($sucesso) {
      $this->setFlashMessage('success', 'Disciplina atualizada com sucesso!');
      $this->redirect('/disciplinas');
    } else {
      $this->setFlashMessage('error', 'Erro ao atualizar disciplina');
      $this->redirect("/disciplinas/edit/{$id}");
    }
  }

  /**
   * Arquiva disciplina
   */
  public function archive(int $id): void
  {
    $this->requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('/disciplinas');
      return;
    }

    if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
      $this->setFlashMessage('error', 'Token de segurança inválido');
      $this->redirect('/disciplinas');
      return;
    }

    $usuarioId = $_SESSION['usuario_id'];
    $sucesso = $this->disciplinaModel->arquivar($id, $usuarioId);

    if ($sucesso) {
      $this->setFlashMessage('success', 'Disciplina arquivada com sucesso!');
    } else {
      $this->setFlashMessage('error', 'Erro ao arquivar disciplina');
    }

    $this->redirect('/disciplinas');
  }

  /**
   * Exibe detalhes da disciplina com estatísticas
   */
  public function show(int $id): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $disciplina = $this->disciplinaModel->buscarPorIdEUsuario($id, $usuarioId);

    if (!$disciplina) {
      $this->setFlashMessage('error', 'Disciplina não encontrada');
      $this->redirect('/disciplinas');
      return;
    }

    $disciplina['estatisticas'] = $this->disciplinaModel->buscarEstatisticas($id);
    $tarefas = $this->tarefaModel->buscarPorUsuario($usuarioId, ['disciplina_id' => $id]);

    $this->render('disciplina/show', [
      'disciplina' => $disciplina,
      'tarefas' => $tarefas,
      'titulo' => $disciplina['nome']
    ]);
  }
}
