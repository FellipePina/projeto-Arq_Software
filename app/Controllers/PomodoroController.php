<?php

namespace App\Controllers;

use App\Models\Pomodoro;
use App\Models\Disciplina;
use App\Models\Tarefa;
use App\Models\ConfiguracaoUsuario;
use App\Models\Gamificacao;

/**
 * PomodoroController - Gerencia sessões Pomodoro
 *
 * Princípios SOLID:
 * - Single Responsibility: apenas operações de Pomodoro
 */
class PomodoroController extends BaseController
{
  private Pomodoro $pomodoroModel;
  private Disciplina $disciplinaModel;
  private Tarefa $tarefaModel;
  private ConfiguracaoUsuario $configModel;
  private Gamificacao $gamificacaoModel;

  public function __construct()
  {
    parent::__construct();
    $this->pomodoroModel = new Pomodoro();
    $this->disciplinaModel = new Disciplina();
    $this->tarefaModel = new Tarefa();
    $this->configModel = new ConfiguracaoUsuario();
    $this->gamificacaoModel = new Gamificacao();
  }

  /**
   * Página principal do Pomodoro
   */
  public function index(): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];

    // Busca sessão ativa
    $sessaoAtiva = $this->pomodoroModel->buscarSessaoAtiva($usuarioId);

    // Busca configurações
    $configuracoes = $this->configModel->buscarPorUsuario($usuarioId);

    // Busca disciplinas e tarefas para seleção
    $disciplinas = $this->disciplinaModel->buscarPorUsuario($usuarioId);
    $tarefas = $this->tarefaModel->buscarPorUsuario($usuarioId, ['concluida' => 0]);

    // Estatísticas
    $estatisticasHoje = $this->pomodoroModel->calcularEstatisticas($usuarioId, 'hoje');
    $estatisticasSemana = $this->pomodoroModel->calcularEstatisticas($usuarioId, 'semana');

    $this->render('pomodoro/index', [
      'sessao_ativa' => $sessaoAtiva,
      'configuracoes' => $configuracoes,
      'disciplinas' => $disciplinas,
      'tarefas' => $tarefas,
      'estatisticas_hoje' => $estatisticasHoje,
      'estatisticas_semana' => $estatisticasSemana,
      'titulo' => 'Pomodoro Timer'
    ]);
  }

  /**
   * Inicia uma sessão Pomodoro
   */
  public function start(): void
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

    // Verifica se já existe sessão ativa
    $sessaoAtiva = $this->pomodoroModel->buscarSessaoAtiva($usuarioId);
    if ($sessaoAtiva) {
      $this->jsonResponse([
        'success' => false,
        'message' => 'Já existe uma sessão ativa'
      ], 400);
      return;
    }

    $dados = [
      'usuario_id' => $usuarioId,
      'disciplina_id' => !empty($_POST['disciplina_id']) ? (int) $_POST['disciplina_id'] : null,
      'tarefa_id' => !empty($_POST['tarefa_id']) ? (int) $_POST['tarefa_id'] : null,
      'tipo' => $_POST['tipo'] ?? 'foco',
      'duracao_planejada' => !empty($_POST['duracao_planejada'])
        ? (int) $_POST['duracao_planejada']
        : $this->configModel->buscarDuracaoPomodoro($usuarioId)
    ];

    $id = $this->pomodoroModel->iniciarSessao($dados);

    if ($id) {
      $this->jsonResponse([
        'success' => true,
        'message' => 'Sessão iniciada!',
        'sessao_id' => $id
      ]);
    } else {
      $this->jsonResponse([
        'success' => false,
        'message' => 'Erro ao iniciar sessão'
      ], 500);
    }
  }

  /**
   * Finaliza sessão Pomodoro com sucesso
   */
  public function finish(int $id): void
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
    $sucesso = $this->pomodoroModel->finalizarSessao($id, $usuarioId);

    if ($sucesso) {
      // Adiciona pontos de gamificação
      $this->gamificacaoModel->adicionarPontos($usuarioId, 5);
      $this->gamificacaoModel->atualizarSequencia($usuarioId);
      $conquistas = $this->gamificacaoModel->verificarConquistas($usuarioId);

      $this->jsonResponse([
        'success' => true,
        'message' => 'Pomodoro concluído! +5 pontos',
        'conquistas' => $conquistas
      ]);
    } else {
      $this->jsonResponse([
        'success' => false,
        'message' => 'Erro ao finalizar sessão'
      ], 500);
    }
  }

  /**
   * Interrompe sessão Pomodoro
   */
  public function interrupt(int $id): void
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
    $sucesso = $this->pomodoroModel->interromperSessao($id, $usuarioId);

    if ($sucesso) {
      $this->jsonResponse([
        'success' => true,
        'message' => 'Sessão interrompida'
      ]);
    } else {
      $this->jsonResponse([
        'success' => false,
        'message' => 'Erro ao interromper sessão'
      ], 500);
    }
  }

  /**
   * Exibe histórico de sessões
   */
  public function history(): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];

    $filtros = [
      'disciplina_id' => $_GET['disciplina'] ?? null,
      'tipo' => $_GET['tipo'] ?? null,
      'data_inicio' => $_GET['data_inicio'] ?? null,
      'data_fim' => $_GET['data_fim'] ?? null
    ];

    $filtros = array_filter($filtros, fn($v) => $v !== null);

    $historico = $this->pomodoroModel->buscarHistorico($usuarioId, $filtros, 100);
    $disciplinas = $this->disciplinaModel->buscarPorUsuario($usuarioId);

    $this->render('pomodoro/history', [
      'historico' => $historico,
      'disciplinas' => $disciplinas,
      'filtros' => $filtros,
      'titulo' => 'Histórico Pomodoro'
    ]);
  }

  /**
   * Busca sessão ativa (AJAX)
   */
  public function activeSession(): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $sessao = $this->pomodoroModel->buscarSessaoAtiva($usuarioId);

    $this->jsonResponse([
      'success' => true,
      'sessao' => $sessao
    ]);
  }
}
