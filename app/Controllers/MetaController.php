<?php

namespace App\Controllers;

use App\Models\Meta;
use App\Models\ConteudoEstudo;

/**
 * Classe MetaController - Controlador para operações de metas de estudo
 *
 * Princípios aplicados:
 * - Single Responsibility: apenas operações de metas
 * - Composition: usa outros modelos para operações relacionadas
 * - Clean Code: métodos bem definidos e com propósito claro
 */
class MetaController extends BaseController
{
  private Meta $metaModel;
  private ConteudoEstudo $conteudoModel;

  /**
   * Construtor - inicializa os modelos necessários
   */
  public function __construct()
  {
    $this->metaModel = new Meta();
    $this->conteudoModel = new ConteudoEstudo();
  }

  /**
   * Lista todas as metas do usuário
   */
  public function index(): void
  {
    $this->requireLogin();

    $usuarioId = $this->getLoggedUserId();

    // Busca metas do usuário
    $metas = $this->metaModel->buscarPorUsuario($usuarioId);

    // Adiciona progresso atual para cada meta
    foreach ($metas as &$meta) {
      $meta['progresso_atual'] = $this->metaModel->calcularProgresso($meta['id']);
      $meta['conteudos'] = $this->metaModel->buscarConteudos($meta['id']);
      $meta['dias_restantes'] = $this->calcularDiasRestantes($meta['data_alvo']);
    }

    // Estatísticas
    $estatisticas = [
      'total_metas' => count($metas),
      'metas_ativas' => count(array_filter($metas, fn($m) => $m['status'] === Meta::STATUS_ATIVA)),
      'metas_concluidas' => count(array_filter($metas, fn($m) => $m['status'] === Meta::STATUS_CONCLUIDA)),
      'progresso_medio' => $this->calcularProgressoMedio($metas)
    ];

    $data = [
      'titulo' => 'Minhas Metas - ' . APP_NAME,
      'flash_messages' => $this->getFlashMessages(),
      'metas' => $metas,
      'estatisticas' => $estatisticas,
      'status_opcoes' => [
        Meta::STATUS_ATIVA => 'Ativa',
        Meta::STATUS_CONCLUIDA => 'Concluída',
        Meta::STATUS_CANCELADA => 'Cancelada'
      ]
    ];

    $this->render('meta/index', $data);
  }

  /**
   * Exibe formulário para criar nova meta
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

    // Busca conteúdos para vincular à meta
    $conteudos = $this->conteudoModel->buscarPorUsuario($usuarioId);

    $data = [
      'titulo' => 'Nova Meta - ' . APP_NAME,
      'flash_messages' => $this->getFlashMessages(),
      'conteudos' => $conteudos,
      'csrf_token' => $this->generateCsrfToken()
    ];

    $this->render('meta/create', $data);
  }

  /**
   * Processa a criação da meta
   */
  private function processarCriacao(): void
  {
    $dados = $this->getPostData();
    $usuarioId = $this->getLoggedUserId();

    // Validação CSRF
    if (!$this->validateCsrfToken($dados['csrf_token'] ?? null)) {
      $this->setFlashMessage('error', 'Token de segurança inválido');
      $this->redirect('/metas/criar');
    }

    // Validações
    $erros = $this->validarDadosMeta($dados);

    if (!empty($erros)) {
      foreach ($erros as $erro) {
        $this->setFlashMessage('error', $erro);
      }
      $this->redirect('/metas/criar');
    }

    // Adiciona ID do usuário
    $dados['usuario_id'] = $usuarioId;

    // Cria a meta
    $metaId = $this->metaModel->criar($dados);

    if (!$metaId) {
      $this->setFlashMessage('error', 'Erro ao criar meta');
      $this->redirect('/metas/criar');
    }

    // Vincula conteúdos selecionados
    $conteudosSelecionados = $dados['conteudos'] ?? [];
    foreach ($conteudosSelecionados as $conteudoId) {
      $this->metaModel->adicionarConteudo($metaId, (int) $conteudoId);
    }

    $this->setFlashMessage('success', 'Meta criada com sucesso!');
    $this->redirect('/metas/ver?id=' . $metaId);
  }

  /**
   * Exibe detalhes de uma meta específica
   */
  public function show(): void
  {
    $this->requireLogin();

    $id = (int) ($_GET['id'] ?? 0);
    $usuarioId = $this->getLoggedUserId();

    // Busca a meta
    $meta = $this->metaModel->findById($id);

    // Verifica se existe e pertence ao usuário
    if (!$meta || $meta['usuario_id'] != $usuarioId) {
      $this->setFlashMessage('error', 'Meta não encontrada');
      $this->redirect('/metas');
    }

    // Busca conteúdos da meta
    $conteudos = $this->metaModel->buscarConteudos($id);

    // Calcula progresso atual
    $progressoAtual = $this->metaModel->calcularProgresso($id);

    $data = [
      'titulo' => $meta['titulo'] . ' - ' . APP_NAME,
      'flash_messages' => $this->getFlashMessages(),
      'meta' => $meta,
      'conteudos' => $conteudos,
      'progresso_atual' => $progressoAtual,
      'dias_restantes' => $this->calcularDiasRestantes($meta['data_alvo']),
      'status_opcoes' => [
        Meta::STATUS_ATIVA => 'Ativa',
        Meta::STATUS_CONCLUIDA => 'Concluída',
        Meta::STATUS_CANCELADA => 'Cancelada'
      ]
    ];

    $this->render('meta/show', $data);
  }

  /**
   * Marca um conteúdo da meta como concluído
   */
  public function marcarConteudoConcluido(): void
  {
    $this->requireLogin();

    if (!$this->isPost()) {
      $this->jsonResponse(['erro' => 'Método não permitido'], 405);
    }

    $dados = $this->getPostData();
    $metaId = (int) ($dados['meta_id'] ?? 0);
    $conteudoId = (int) ($dados['conteudo_id'] ?? 0);
    $usuarioId = $this->getLoggedUserId();

    // Verifica se a meta pertence ao usuário
    $meta = $this->metaModel->findById($metaId);
    if (!$meta || $meta['usuario_id'] != $usuarioId) {
      $this->jsonResponse(['erro' => 'Meta não encontrada'], 404);
    }

    // Marca conteúdo como concluído na meta
    if ($this->metaModel->marcarConteudoConcluido($metaId, $conteudoId)) {
      // Também altera o status do conteúdo
      $this->conteudoModel->alterarStatus($conteudoId, ConteudoEstudo::STATUS_CONCLUIDO);

      $this->jsonResponse([
        'sucesso' => true,
        'mensagem' => 'Conteúdo marcado como concluído!',
        'progresso_atual' => $this->metaModel->calcularProgresso($metaId)
      ]);
    } else {
      $this->jsonResponse(['erro' => 'Erro ao marcar conteúdo'], 400);
    }
  }

  /**
   * Adiciona conteúdo a uma meta existente
   */
  public function adicionarConteudo(): void
  {
    $this->requireLogin();

    $metaId = (int) ($_GET['meta'] ?? 0);
    $usuarioId = $this->getLoggedUserId();

    // Verifica se a meta pertence ao usuário
    $meta = $this->metaModel->findById($metaId);
    if (!$meta || $meta['usuario_id'] != $usuarioId) {
      $this->setFlashMessage('error', 'Meta não encontrada');
      $this->redirect('/metas');
    }

    // Se for POST, processa a adição
    if ($this->isPost()) {
      $this->processarAdicaoConteudo($metaId);
      return;
    }

    // Busca conteúdos disponíveis (que não estão na meta)
    $conteudosDisponiveis = $this->buscarConteudosDisponiveis($metaId, $usuarioId);

    $data = [
      'titulo' => 'Adicionar Conteúdo - ' . APP_NAME,
      'flash_messages' => $this->getFlashMessages(),
      'meta' => $meta,
      'conteudos_disponiveis' => $conteudosDisponiveis,
      'csrf_token' => $this->generateCsrfToken()
    ];

    $this->render('meta/adicionar_conteudo', $data);
  }

  /**
   * Processa a adição de conteúdo à meta
   */
  private function processarAdicaoConteudo(int $metaId): void
  {
    $dados = $this->getPostData();

    // Validação CSRF
    if (!$this->validateCsrfToken($dados['csrf_token'] ?? null)) {
      $this->setFlashMessage('error', 'Token de segurança inválido');
      $this->redirect('/metas/adicionar-conteudo?meta=' . $metaId);
    }

    $conteudosSelecionados = $dados['conteudos'] ?? [];

    if (empty($conteudosSelecionados)) {
      $this->setFlashMessage('error', 'Selecione pelo menos um conteúdo');
      $this->redirect('/metas/adicionar-conteudo?meta=' . $metaId);
    }

    $adicionados = 0;
    foreach ($conteudosSelecionados as $conteudoId) {
      if ($this->metaModel->adicionarConteudo($metaId, (int) $conteudoId)) {
        $adicionados++;
      }
    }

    if ($adicionados > 0) {
      $this->setFlashMessage('success', "Conteúdo(s) adicionado(s) com sucesso!");
    } else {
      $this->setFlashMessage('error', 'Erro ao adicionar conteúdos');
    }

    $this->redirect('/metas/ver?id=' . $metaId);
  }

  /**
   * Valida dados da meta
   *
   * @param array $dados Dados a validar
   * @return array Lista de erros
   */
  private function validarDadosMeta(array $dados): array
  {
    $erros = [];

    // Título obrigatório
    if (empty($dados['titulo'])) {
      $erros[] = 'Título é obrigatório';
    }

    // Data alvo obrigatória e no futuro
    if (empty($dados['data_alvo'])) {
      $erros[] = 'Data alvo é obrigatória';
    } elseif (strtotime($dados['data_alvo']) < strtotime(date('Y-m-d'))) {
      $erros[] = 'Data alvo deve ser no futuro';
    }

    return $erros;
  }

  /**
   * Calcula dias restantes para a data alvo
   *
   * @param string $dataAlvo Data alvo
   * @return int Dias restantes (negativo se passou)
   */
  private function calcularDiasRestantes(string $dataAlvo): int
  {
    $hoje = new \DateTime();
    $alvo = new \DateTime($dataAlvo);

    return (int) $hoje->diff($alvo)->format('%r%a');
  }

  /**
   * Calcula progresso médio das metas
   *
   * @param array $metas Lista de metas
   * @return float Progresso médio
   */
  private function calcularProgressoMedio(array $metas): float
  {
    if (empty($metas)) {
      return 0;
    }

    $somaProgressos = 0;
    foreach ($metas as $meta) {
      $somaProgressos += (float) $meta['percentual_progresso'];
    }

    return round($somaProgressos / count($metas), 1);
  }

  /**
   * Busca conteúdos disponíveis para adicionar à meta
   *
   * @param int $metaId ID da meta
   * @param int $usuarioId ID do usuário
   * @return array Lista de conteúdos disponíveis
   */
  private function buscarConteudosDisponiveis(int $metaId, int $usuarioId): array
  {
    $todosConteudos = $this->conteudoModel->buscarPorUsuario($usuarioId);
    $conteudosDaMeta = $this->metaModel->buscarConteudos($metaId);

    // Cria array com IDs dos conteúdos já vinculados
    $idsVinculados = array_column($conteudosDaMeta, 'id');

    // Filtra conteúdos disponíveis
    return array_filter($todosConteudos, function ($conteudo) use ($idsVinculados) {
      return !in_array($conteudo['id'], $idsVinculados);
    });
  }
}
