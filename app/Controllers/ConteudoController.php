<?php

namespace App\Controllers;

use App\Models\ConteudoEstudo;
use App\Models\Categoria;

/**
 * Classe ConteudoController - Controlador para operações de conteúdo de estudo
 *
 * Princípios aplicados:
 * - Single Responsibility: apenas operações de conteúdo
 * - Dependency Injection: recebe dependências via construtor
 * - Clean Code: métodos bem nomeados e com propósito específico
 */
class ConteudoController extends BaseController
{
  private ConteudoEstudo $conteudoModel;
  private Categoria $categoriaModel;

  /**
   * Construtor - inicializa os modelos necessários
   */
  public function __construct()
  {
    $this->conteudoModel = new ConteudoEstudo();
    $this->categoriaModel = new Categoria();
  }

  /**
   * Lista todos os conteúdos do usuário
   */
  public function index(): void
  {
    $this->requireLogin();

    $usuarioId = $this->getLoggedUserId();

    // Filtros
    $status = $_GET['status'] ?? null;
    $categoriaId = $_GET['categoria'] ?? null;

    // Busca conteúdos
    if ($status) {
      $conteudos = $this->conteudoModel->buscarPorStatus($usuarioId, $status);
    } elseif ($categoriaId) {
      $conteudos = $this->conteudoModel->buscarPorCategoria((int) $categoriaId);
    } else {
      $conteudos = $this->conteudoModel->buscarPorUsuario($usuarioId);
    }

    // Busca categorias para filtro
    $categorias = $this->categoriaModel->buscarPorUsuario($usuarioId);

    // Contadores por status
    $contadores = $this->conteudoModel->contarPorStatus($usuarioId);

    $data = [
      'titulo' => 'Meus Conteúdos - ' . APP_NAME,
      'flash_messages' => $this->getFlashMessages(),
      'conteudos' => $conteudos,
      'categorias' => $categorias,
      'contadores' => $contadores,
      'filtro_status' => $status,
      'filtro_categoria' => $categoriaId,
      'status_opcoes' => [
        ConteudoEstudo::STATUS_PENDENTE => 'Pendente',
        ConteudoEstudo::STATUS_EM_ANDAMENTO => 'Em Andamento',
        ConteudoEstudo::STATUS_CONCLUIDO => 'Concluído'
      ]
    ];

    $this->render('conteudo/index', $data);
  }

  /**
   * Exibe formulário para criar novo conteúdo
   */
  public function create(): void
  {
    $this->requireLogin();

    $usuarioId = $this->getLoggedUserId();

    // Se for POST, processa a criação
    if ($this->isPost()) {
      $this->processarCriacao();
      return;
    }

    $categorias = $this->categoriaModel->buscarPorUsuario($usuarioId);

    $data = [
      'titulo' => 'Novo Conteúdo - ' . APP_NAME,
      'flash_messages' => $this->getFlashMessages(),
      'categorias' => $categorias,
      'csrf_token' => $this->generateCsrfToken(),
      'status_opcoes' => [
        ConteudoEstudo::STATUS_PENDENTE => 'Pendente',
        ConteudoEstudo::STATUS_EM_ANDAMENTO => 'Em Andamento',
        ConteudoEstudo::STATUS_CONCLUIDO => 'Concluído'
      ]
    ];

    $this->render('conteudo/create', $data);
  }

  /**
   * Processa a criação do conteúdo
   */
  private function processarCriacao(): void
  {
    $dados = $this->getPostData();
    $usuarioId = $this->getLoggedUserId();

    // Validação CSRF
    if (!$this->validateCsrfToken($dados['csrf_token'] ?? null)) {
      $this->setFlashMessage('error', 'Token de segurança inválido');
      $this->redirect('/conteudos/criar');
    }

    // Adiciona ID do usuário
    $dados['usuario_id'] = $usuarioId;

    // Valida dados
    $erros = $this->conteudoModel->validarDados($dados);

    if (!empty($erros)) {
      foreach ($erros as $erro) {
        $this->setFlashMessage('error', $erro);
      }
      $this->redirect('/conteudos/criar');
    }

    // Tenta criar o conteúdo
    $conteudoId = $this->conteudoModel->criar($dados);

    if ($conteudoId) {
      $this->setFlashMessage('success', 'Conteúdo criado com sucesso!');
      $this->redirect('/conteudos');
    } else {
      $this->setFlashMessage('error', 'Erro ao criar conteúdo');
      $this->redirect('/conteudos/criar');
    }
  }

  /**
   * Exibe detalhes de um conteúdo específico
   */
  public function show(): void
  {
    $this->requireLogin();

    $id = (int) ($_GET['id'] ?? 0);
    $usuarioId = $this->getLoggedUserId();

    // Busca o conteúdo
    $conteudo = $this->conteudoModel->findById($id);

    // Verifica se existe e pertence ao usuário
    if (!$conteudo || $conteudo['usuario_id'] != $usuarioId) {
      $this->setFlashMessage('error', 'Conteúdo não encontrado');
      $this->redirect('/conteudos');
    }

    // Busca dados da categoria se existir
    $categoria = null;
    if ($conteudo['categoria_id']) {
      $categoria = $this->categoriaModel->findById($conteudo['categoria_id']);
    }

    $data = [
      'titulo' => $conteudo['titulo'] . ' - ' . APP_NAME,
      'flash_messages' => $this->getFlashMessages(),
      'conteudo' => $conteudo,
      'categoria' => $categoria,
      'status_opcoes' => [
        ConteudoEstudo::STATUS_PENDENTE => 'Pendente',
        ConteudoEstudo::STATUS_EM_ANDAMENTO => 'Em Andamento',
        ConteudoEstudo::STATUS_CONCLUIDO => 'Concluído'
      ]
    ];

    $this->render('conteudo/show', $data);
  }

  /**
   * Exibe formulário para editar conteúdo
   */
  public function edit(): void
  {
    $this->requireLogin();

    $id = (int) ($_GET['id'] ?? 0);
    $usuarioId = $this->getLoggedUserId();

    // Busca o conteúdo
    $conteudo = $this->conteudoModel->findById($id);

    // Verifica se existe e pertence ao usuário
    if (!$conteudo || $conteudo['usuario_id'] != $usuarioId) {
      $this->setFlashMessage('error', 'Conteúdo não encontrado');
      $this->redirect('/conteudos');
    }

    // Se for POST, processa a edição
    if ($this->isPost()) {
      $this->processarEdicao($id);
      return;
    }

    $categorias = $this->categoriaModel->buscarPorUsuario($usuarioId);

    $data = [
      'titulo' => 'Editar Conteúdo - ' . APP_NAME,
      'flash_messages' => $this->getFlashMessages(),
      'conteudo' => $conteudo,
      'categorias' => $categorias,
      'csrf_token' => $this->generateCsrfToken(),
      'status_opcoes' => [
        ConteudoEstudo::STATUS_PENDENTE => 'Pendente',
        ConteudoEstudo::STATUS_EM_ANDAMENTO => 'Em Andamento',
        ConteudoEstudo::STATUS_CONCLUIDO => 'Concluído'
      ]
    ];

    $this->render('conteudo/edit', $data);
  }

  /**
   * Processa a edição do conteúdo
   */
  private function processarEdicao(int $id): void
  {
    $dados = $this->getPostData();
    $usuarioId = $this->getLoggedUserId();

    // Validação CSRF
    if (!$this->validateCsrfToken($dados['csrf_token'] ?? null)) {
      $this->setFlashMessage('error', 'Token de segurança inválido');
      $this->redirect('/conteudos/editar?id=' . $id);
    }

    // Adiciona ID para atualização
    $dados['id'] = $id;
    $dados['usuario_id'] = $usuarioId;

    // Valida dados
    $erros = $this->conteudoModel->validarDados($dados);

    if (!empty($erros)) {
      foreach ($erros as $erro) {
        $this->setFlashMessage('error', $erro);
      }
      $this->redirect('/conteudos/editar?id=' . $id);
    }

    // Tenta atualizar o conteúdo
    if ($this->conteudoModel->save($dados)) {
      $this->setFlashMessage('success', 'Conteúdo atualizado com sucesso!');
      $this->redirect('/conteudos/ver?id=' . $id);
    } else {
      $this->setFlashMessage('error', 'Erro ao atualizar conteúdo');
      $this->redirect('/conteudos/editar?id=' . $id);
    }
  }

  /**
   * Altera o status de um conteúdo via AJAX
   */
  public function alterarStatus(): void
  {
    $this->requireLogin();

    if (!$this->isPost()) {
      $this->jsonResponse(['erro' => 'Método não permitido'], 405);
    }

    $dados = $this->getPostData();
    $id = (int) ($dados['id'] ?? 0);
    $novoStatus = $dados['status'] ?? '';
    $usuarioId = $this->getLoggedUserId();

    // Verifica se o conteúdo pertence ao usuário
    $conteudo = $this->conteudoModel->findById($id);
    if (!$conteudo || $conteudo['usuario_id'] != $usuarioId) {
      $this->jsonResponse(['erro' => 'Conteúdo não encontrado'], 404);
    }

    // Tenta alterar o status
    if ($this->conteudoModel->alterarStatus($id, $novoStatus)) {
      $this->jsonResponse([
        'sucesso' => true,
        'mensagem' => 'Status alterado com sucesso!'
      ]);
    } else {
      $this->jsonResponse([
        'erro' => 'Erro ao alterar status'
      ], 400);
    }
  }

  /**
   * Exclui um conteúdo
   */
  public function delete(): void
  {
    $this->requireLogin();

    $id = (int) ($_GET['id'] ?? 0);
    $usuarioId = $this->getLoggedUserId();

    // Verifica se o conteúdo pertence ao usuário
    $conteudo = $this->conteudoModel->findById($id);
    if (!$conteudo || $conteudo['usuario_id'] != $usuarioId) {
      $this->setFlashMessage('error', 'Conteúdo não encontrado');
      $this->redirect('/conteudos');
    }

    // Tenta excluir
    if ($this->conteudoModel->delete($id)) {
      $this->setFlashMessage('success', 'Conteúdo excluído com sucesso!');
    } else {
      $this->setFlashMessage('error', 'Erro ao excluir conteúdo');
    }

    $this->redirect('/conteudos');
  }
}
