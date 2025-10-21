<?php

namespace App\Controllers;

use App\Models\Anotacao;
use App\Models\Disciplina;

/**
 * AnotacaoController - Gerencia anotações
 *
 * Princípios SOLID:
 * - Single Responsibility: apenas operações de anotações
 */
class AnotacaoController extends BaseController
{
  private Anotacao $anotacaoModel;
  private Disciplina $disciplinaModel;

  public function __construct()
  {
    parent::__construct();
    $this->anotacaoModel = new Anotacao();
    $this->disciplinaModel = new Disciplina();
  }

  /**
   * Lista todas as anotações
   */
  public function index(): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];

    $filtros = [
      'disciplina_id' => $_GET['disciplina'] ?? null,
      'fixada' => isset($_GET['fixada']) ? (int) $_GET['fixada'] : null,
      'busca' => $_GET['busca'] ?? null
    ];

    $filtros = array_filter($filtros, fn($v) => $v !== null);

    $anotacoes = $this->anotacaoModel->buscarPorUsuario($usuarioId, $filtros);
    $disciplinas = $this->disciplinaModel->buscarPorUsuario($usuarioId);

    $this->render('anotacao/index', [
      'anotacoes' => $anotacoes,
      'disciplinas' => $disciplinas,
      'filtros' => $filtros,
      'titulo' => 'Minhas Anotações'
    ]);
  }

  /**
   * Exibe formulário de nova anotação
   */
  public function create(): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $disciplinas = $this->disciplinaModel->buscarPorUsuario($usuarioId);

    $this->render('anotacao/create', [
      'disciplinas' => $disciplinas,
      'titulo' => 'Nova Anotação'
    ]);
  }

  /**
   * Salva nova anotação
   */
  public function store(): void
  {
    $this->requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('/anotacoes');
      return;
    }

    if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
      $this->setFlashMessage('error', 'Token de segurança inválido');
      $this->redirect('/anotacoes/create');
      return;
    }

    $dados = [
      'titulo' => $this->sanitizeInput($_POST['titulo'] ?? ''),
      'conteudo' => $_POST['conteudo'] ?? '', // Não sanitiza para manter formatação
      'disciplina_id' => !empty($_POST['disciplina_id']) ? (int) $_POST['disciplina_id'] : null,
      'usuario_id' => $_SESSION['usuario_id']
    ];

    if (empty($dados['titulo'])) {
      $this->setFlashMessage('error', 'O título da anotação é obrigatório');
      $this->redirect('/anotacoes/create');
      return;
    }

    $id = $this->anotacaoModel->criar($dados);

    if ($id) {
      $this->setFlashMessage('success', 'Anotação criada com sucesso!');
      $this->redirect('/anotacoes');
    } else {
      $this->setFlashMessage('error', 'Erro ao criar anotação');
      $this->redirect('/anotacoes/create');
    }
  }

  /**
   * Exibe detalhes da anotação
   */
  public function show(int $id): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $anotacao = $this->anotacaoModel->buscarPorIdEUsuario($id, $usuarioId);

    if (!$anotacao) {
      $this->setFlashMessage('error', 'Anotação não encontrada');
      $this->redirect('/anotacoes');
      return;
    }

    $this->render('anotacao/show', [
      'anotacao' => $anotacao,
      'titulo' => $anotacao['titulo']
    ]);
  }

  /**
   * Exibe formulário de edição
   */
  public function edit(int $id): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $anotacao = $this->anotacaoModel->buscarPorIdEUsuario($id, $usuarioId);

    if (!$anotacao) {
      $this->setFlashMessage('error', 'Anotação não encontrada');
      $this->redirect('/anotacoes');
      return;
    }

    $disciplinas = $this->disciplinaModel->buscarPorUsuario($usuarioId);

    $this->render('anotacao/edit', [
      'anotacao' => $anotacao,
      'disciplinas' => $disciplinas,
      'titulo' => 'Editar Anotação'
    ]);
  }

  /**
   * Atualiza anotação
   */
  public function update(int $id): void
  {
    $this->requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('/anotacoes');
      return;
    }

    if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
      $this->setFlashMessage('error', 'Token de segurança inválido');
      $this->redirect("/anotacoes/edit/{$id}");
      return;
    }

    $usuarioId = $_SESSION['usuario_id'];
    $dados = [
      'titulo' => $this->sanitizeInput($_POST['titulo'] ?? ''),
      'conteudo' => $_POST['conteudo'] ?? '',
      'disciplina_id' => !empty($_POST['disciplina_id']) ? (int) $_POST['disciplina_id'] : null
    ];

    if (empty($dados['titulo'])) {
      $this->setFlashMessage('error', 'O título da anotação é obrigatório');
      $this->redirect("/anotacoes/edit/{$id}");
      return;
    }

    $sucesso = $this->anotacaoModel->atualizar($id, $dados, $usuarioId);

    if ($sucesso) {
      $this->setFlashMessage('success', 'Anotação atualizada com sucesso!');
      $this->redirect('/anotacoes');
    } else {
      $this->setFlashMessage('error', 'Erro ao atualizar anotação');
      $this->redirect("/anotacoes/edit/{$id}");
    }
  }

  /**
   * Exclui anotação
   */
  public function delete(int $id): void
  {
    $this->requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('/anotacoes');
      return;
    }

    if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
      $this->setFlashMessage('error', 'Token de segurança inválido');
      $this->redirect('/anotacoes');
      return;
    }

    $usuarioId = $_SESSION['usuario_id'];
    $sucesso = $this->anotacaoModel->excluir($id, $usuarioId);

    if ($sucesso) {
      $this->setFlashMessage('success', 'Anotação excluída!');
    } else {
      $this->setFlashMessage('error', 'Erro ao excluir anotação');
    }

    $this->redirect('/anotacoes');
  }

  /**
   * Alterna fixada (AJAX)
   */
  public function togglePin(int $id): void
  {
    $this->requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->jsonResponse(['success' => false, 'message' => 'Método inválido'], 400);
      return;
    }

    if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
      $this->jsonResponse(['success' => false, 'message' => 'Token inválido'], 403);
      return;
    }

    $usuarioId = $_SESSION['usuario_id'];
    $fixada = isset($_POST['fixada']) ? (bool) $_POST['fixada'] : true;

    $sucesso = $this->anotacaoModel->alternarFixada($id, $usuarioId, $fixada);

    if ($sucesso) {
      $this->jsonResponse([
        'success' => true,
        'message' => $fixada ? 'Anotação fixada' : 'Anotação desfixada'
      ]);
    } else {
      $this->jsonResponse(['success' => false, 'message' => 'Erro ao atualizar'], 500);
    }
  }
}
