<?php

namespace App\Controllers;

use App\Models\ConfiguracaoUsuario;
use App\Models\Gamificacao;

/**
 * ConfiguracaoController - Gerencia configurações e gamificação
 *
 * Princípios SOLID:
 * - Single Responsibility: apenas operações de configuração
 */
class ConfiguracaoController extends BaseController
{
  private ConfiguracaoUsuario $configModel;
  private Gamificacao $gamificacaoModel;

  public function __construct()
  {
    parent::__construct();
    $this->configModel = new ConfiguracaoUsuario();
    $this->gamificacaoModel = new Gamificacao();
  }

  /**
   * Página de configurações
   */
  public function index(): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $configuracoes = $this->configModel->buscarPorUsuario($usuarioId);

    $this->render('configuracao/index', [
      'configuracoes' => $configuracoes,
      'titulo' => 'Configurações'
    ]);
  }

  /**
   * Atualiza configurações
   */
  public function update(): void
  {
    $this->requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('/configuracoes');
      return;
    }

    if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
      $this->setFlashMessage('error', 'Token de segurança inválido');
      $this->redirect('/configuracoes');
      return;
    }

    $usuarioId = $_SESSION['usuario_id'];
    $dados = [
      'duracao_pomodoro' => !empty($_POST['duracao_pomodoro']) ? (int) $_POST['duracao_pomodoro'] : 25,
      'duracao_pausa_curta' => !empty($_POST['duracao_pausa_curta']) ? (int) $_POST['duracao_pausa_curta'] : 5,
      'duracao_pausa_longa' => !empty($_POST['duracao_pausa_longa']) ? (int) $_POST['duracao_pausa_longa'] : 15,
      'pomodoros_ate_pausa_longa' => !empty($_POST['pomodoros_ate_pausa_longa']) ? (int) $_POST['pomodoros_ate_pausa_longa'] : 4,
      'notificacoes_ativadas' => isset($_POST['notificacoes_ativadas']) ? 1 : 0,
      'tema' => $_POST['tema'] ?? 'claro'
    ];

    $sucesso = $this->configModel->atualizar($usuarioId, $dados);

    if ($sucesso) {
      $this->setFlashMessage('success', 'Configurações atualizadas!');
    } else {
      $this->setFlashMessage('error', 'Erro ao atualizar configurações');
    }

    $this->redirect('/configuracoes');
  }

  /**
   * Alterna tema (AJAX)
   */
  public function toggleTheme(): void
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
    $sucesso = $this->configModel->alternarTema($usuarioId);

    if ($sucesso) {
      $config = $this->configModel->buscarPorUsuario($usuarioId);
      $this->jsonResponse([
        'success' => true,
        'tema' => $config['tema']
      ]);
    } else {
      $this->jsonResponse(['success' => false, 'message' => 'Erro ao alternar tema'], 500);
    }
  }

  /**
   * Página de gamificação
   */
  public function gamificacao(): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $gamificacao = $this->gamificacaoModel->buscarPorUsuario($usuarioId);
    $conquistas = $this->gamificacaoModel->buscarConquistas($usuarioId);
    $ranking = $this->gamificacaoModel->buscarRanking(10);

    // Calcula progresso para próximo nível
    $pontosProximoNivel = $gamificacao['nivel'] * 100;
    $pontosAtual = $gamificacao['pontos_total'] % 100;
    $progressoNivel = ($pontosAtual / 100) * 100;

    $this->render('configuracao/gamificacao', [
      'gamificacao' => $gamificacao,
      'conquistas' => $conquistas,
      'ranking' => $ranking,
      'pontos_proximo_nivel' => $pontosProximoNivel,
      'pontos_atual' => $pontosAtual,
      'progresso_nivel' => $progressoNivel,
      'titulo' => 'Gamificação'
    ]);
  }

  /**
   * Verifica novas conquistas (AJAX)
   */
  public function checkAchievements(): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $novasConquistas = $this->gamificacaoModel->verificarConquistas($usuarioId);

    $this->jsonResponse([
      'success' => true,
      'conquistas' => $novasConquistas
    ]);
  }

  /**
   * Busca dados de gamificação (AJAX)
   */
  public function gamificationData(): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $gamificacao = $this->gamificacaoModel->buscarPorUsuario($usuarioId);

    $pontosAtual = $gamificacao['pontos_total'] % 100;
    $progressoNivel = ($pontosAtual / 100) * 100;

    $this->jsonResponse([
      'success' => true,
      'pontos_totais' => $gamificacao['pontos_total'],
      'nivel' => $gamificacao['nivel'],
      'sequencia_dias' => $gamificacao['streak_dias'],
      'maior_sequencia' => $gamificacao['melhor_streak'],
      'pontos_atual' => $pontosAtual,
      'progresso_nivel' => round($progressoNivel, 1)
    ]);
  }
}
