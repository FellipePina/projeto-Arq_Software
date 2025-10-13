<?php

namespace App\Controllers;

use App\Models\SessaoEstudo;
use App\Models\ConteudoEstudo;

/**
 * Classe SessaoController - Controlador para operações de sessões de estudo
 *
 * Princípios aplicados:
 * - Single Responsibility: apenas operações de sessões
 * - Composition: usa modelos para operações específicas
 * - Interface Segregation: métodos específicos para cada funcionalidade
 */
class SessaoController extends BaseController
{
  private SessaoEstudo $sessaoModel;
  private ConteudoEstudo $conteudoModel;

  /**
   * Construtor - inicializa os modelos necessários
   */
  public function __construct()
  {
    $this->sessaoModel = new SessaoEstudo();
    $this->conteudoModel = new ConteudoEstudo();
  }

  /**
   * Lista sessões de estudo do usuário
   */
  public function index(): void
  {
    $this->requireLogin();

    $usuarioId = $this->getLoggedUserId();

    // Filtros
    $conteudoId = $_GET['conteudo'] ?? null;
    $dataInicio = $_GET['data_inicio'] ?? null;
    $dataFim = $_GET['data_fim'] ?? null;

    // Busca sessões com filtros
    if ($conteudoId) {
      $sessoes = $this->sessaoModel->buscarPorConteudo((int) $conteudoId);
    } elseif ($dataInicio && $dataFim) {
      $sessoes = $this->sessaoModel->buscarPorPeriodo($usuarioId, $dataInicio, $dataFim);
    } else {
      $sessoes = $this->sessaoModel->buscarPorUsuario($usuarioId);
    }

    // Busca conteúdos para filtro
    $conteudos = $this->conteudoModel->buscarPorUsuario($usuarioId);

    // Estatísticas
    $estatisticas = [
      'total_sessoes' => count($sessoes),
      'total_horas_periodo' => $this->calcularTotalHorasSessoes($sessoes),
      'sessoes_hoje' => $this->contarSessoesHoje($usuarioId),
      'horas_hoje' => $this->sessaoModel->calcularTotalHoras($usuarioId, date('Y-m-d'), date('Y-m-d'))
    ];

    $data = [
      'titulo' => 'Minhas Sessões - ' . APP_NAME,
      'flash_messages' => $this->getFlashMessages(),
      'sessoes' => $sessoes,
      'conteudos' => $conteudos,
      'estatisticas' => $estatisticas,
      'filtros' => [
        'conteudo' => $conteudoId,
        'data_inicio' => $dataInicio,
        'data_fim' => $dataFim
      ]
    ];

    $this->render('sessao/index', $data);
  }

  /**
   * Inicia uma nova sessão de estudo
   */
  public function iniciar(): void
  {
    $this->requireLogin();

    $usuarioId = $this->getLoggedUserId();
    $conteudoId = (int) ($_GET['conteudo'] ?? 0);

    // Verifica se o conteúdo existe e pertence ao usuário
    $conteudo = $this->conteudoModel->findById($conteudoId);
    if (!$conteudo || $conteudo['usuario_id'] != $usuarioId) {
      $this->setFlashMessage('error', 'Conteúdo não encontrado');
      $this->redirect('/conteudos');
    }

    // Se for POST, cria a sessão
    if ($this->isPost()) {
      $this->processarInicio($conteudoId);
      return;
    }

    $data = [
      'titulo' => 'Iniciar Sessão - ' . APP_NAME,
      'flash_messages' => $this->getFlashMessages(),
      'conteudo' => $conteudo,
      'csrf_token' => $this->generateCsrfToken()
    ];

    $this->render('sessao/iniciar', $data);
  }

  /**
   * Processa o início de uma sessão
   */
  private function processarInicio(int $conteudoId): void
  {
    $dados = $this->getPostData();
    $usuarioId = $this->getLoggedUserId();

    // Validação CSRF
    if (!$this->validateCsrfToken($dados['csrf_token'] ?? null)) {
      $this->setFlashMessage('error', 'Token de segurança inválido');
      $this->redirect('/sessoes/iniciar?conteudo=' . $conteudoId);
    }

    // Dados da sessão
    $dadosSessao = [
      'conteudo_id' => $conteudoId,
      'usuario_id' => $usuarioId,
      'data_inicio' => date('Y-m-d H:i:s'),
      'observacoes' => $dados['observacoes'] ?? ''
    ];

    // Cria a sessão
    $sessaoId = $this->sessaoModel->criar($dadosSessao);

    if ($sessaoId) {
      // Altera status do conteúdo para "em andamento"
      $this->conteudoModel->alterarStatus($conteudoId, ConteudoEstudo::STATUS_EM_ANDAMENTO);

      $this->setFlashMessage('success', 'Sessão iniciada! Boa sorte nos estudos!');
      $this->redirect('/sessoes/cronometro?id=' . $sessaoId);
    } else {
      $this->setFlashMessage('error', 'Erro ao iniciar sessão');
      $this->redirect('/conteudos');
    }
  }

  /**
   * Exibe cronômetro da sessão em andamento
   */
  public function cronometro(): void
  {
    $this->requireLogin();

    $id = (int) ($_GET['id'] ?? 0);
    $usuarioId = $this->getLoggedUserId();

    // Busca a sessão
    $sessao = $this->sessaoModel->findById($id);

    // Verifica se existe, pertence ao usuário e não foi finalizada
    if (!$sessao || $sessao['usuario_id'] != $usuarioId || $sessao['data_fim']) {
      $this->setFlashMessage('error', 'Sessão não encontrada ou já finalizada');
      $this->redirect('/sessoes');
    }

    // Busca dados do conteúdo
    $conteudo = $this->conteudoModel->findById($sessao['conteudo_id']);

    $data = [
      'titulo' => 'Cronômetro - ' . APP_NAME,
      'flash_messages' => $this->getFlashMessages(),
      'sessao' => $sessao,
      'conteudo' => $conteudo
    ];

    $this->render('sessao/cronometro', $data);
  }

  /**
   * Finaliza uma sessão de estudo
   */
  public function finalizar(): void
  {
    $this->requireLogin();

    if (!$this->isPost()) {
      $this->jsonResponse(['erro' => 'Método não permitido'], 405);
    }

    $dados = $this->getPostData();
    $id = (int) ($dados['id'] ?? 0);
    $usuarioId = $this->getLoggedUserId();

    // Busca a sessão
    $sessao = $this->sessaoModel->findById($id);

    // Verifica se existe, pertence ao usuário e não foi finalizada
    if (!$sessao || $sessao['usuario_id'] != $usuarioId || $sessao['data_fim']) {
      $this->jsonResponse(['erro' => 'Sessão não encontrada ou já finalizada'], 404);
    }

    // Finaliza a sessão
    $observacoes = $dados['observacoes'] ?? '';

    if ($this->sessaoModel->finalizar($id, null, $observacoes)) {
      $this->jsonResponse([
        'sucesso' => true,
        'mensagem' => 'Sessão finalizada com sucesso!',
        'redirect' => '/sessoes'
      ]);
    } else {
      $this->jsonResponse(['erro' => 'Erro ao finalizar sessão'], 400);
    }
  }

  /**
   * Exibe detalhes de uma sessão
   */
  public function show(): void
  {
    $this->requireLogin();

    $id = (int) ($_GET['id'] ?? 0);
    $usuarioId = $this->getLoggedUserId();

    // Busca a sessão
    $sessao = $this->sessaoModel->findById($id);

    // Verifica se existe e pertence ao usuário
    if (!$sessao || $sessao['usuario_id'] != $usuarioId) {
      $this->setFlashMessage('error', 'Sessão não encontrada');
      $this->redirect('/sessoes');
    }

    // Busca dados do conteúdo
    $conteudo = $this->conteudoModel->findById($sessao['conteudo_id']);

    $data = [
      'titulo' => 'Detalhes da Sessão - ' . APP_NAME,
      'flash_messages' => $this->getFlashMessages(),
      'sessao' => $sessao,
      'conteudo' => $conteudo,
      'duracao_formatada' => $this->formatarDuracao($sessao['duracao_minutos'])
    ];

    $this->render('sessao/show', $data);
  }

  /**
   * Exclui uma sessão
   */
  public function delete(): void
  {
    $this->requireLogin();

    $id = (int) ($_GET['id'] ?? 0);
    $usuarioId = $this->getLoggedUserId();

    // Busca a sessão
    $sessao = $this->sessaoModel->findById($id);

    // Verifica se existe e pertence ao usuário
    if (!$sessao || $sessao['usuario_id'] != $usuarioId) {
      $this->setFlashMessage('error', 'Sessão não encontrada');
      $this->redirect('/sessoes');
    }

    // Tenta excluir
    if ($this->sessaoModel->delete($id)) {
      $this->setFlashMessage('success', 'Sessão excluída com sucesso!');
    } else {
      $this->setFlashMessage('error', 'Erro ao excluir sessão');
    }

    $this->redirect('/sessoes');
  }

  /**
   * Calcula total de horas de uma lista de sessões
   *
   * @param array $sessoes Lista de sessões
   * @return float Total de horas
   */
  private function calcularTotalHorasSessoes(array $sessoes): float
  {
    $totalMinutos = 0;

    foreach ($sessoes as $sessao) {
      if ($sessao['duracao_minutos']) {
        $totalMinutos += (int) $sessao['duracao_minutos'];
      }
    }

    return round($totalMinutos / 60, 2);
  }

  /**
   * Conta sessões de hoje do usuário
   *
   * @param int $usuarioId ID do usuário
   * @return int Quantidade de sessões hoje
   */
  private function contarSessoesHoje(int $usuarioId): int
  {
    $sessoes = $this->sessaoModel->buscarPorPeriodo(
      $usuarioId,
      date('Y-m-d'),
      date('Y-m-d')
    );

    return count($sessoes);
  }

  /**
   * Formata duração em minutos para formato legível
   *
   * @param int|null $minutos Duração em minutos
   * @return string Duração formatada
   */
  private function formatarDuracao(?int $minutos): string
  {
    if (!$minutos) {
      return 'N/A';
    }

    $horas = floor($minutos / 60);
    $mins = $minutos % 60;

    if ($horas > 0) {
      return sprintf('%dh %02dm', $horas, $mins);
    }

    return sprintf('%dm', $mins);
  }
}
