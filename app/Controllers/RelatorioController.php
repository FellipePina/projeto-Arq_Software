<?php

namespace App\Controllers;

use App\Models\Pomodoro;
use App\Models\Tarefa;
use App\Models\Disciplina;
use App\Models\Gamificacao;

/**
 * RelatorioController - Gera relatórios e estatísticas
 *
 * Princípios SOLID:
 * - Single Responsibility: apenas geração de relatórios
 */
class RelatorioController extends BaseController
{
  private Pomodoro $pomodoroModel;
  private Tarefa $tarefaModel;
  private Disciplina $disciplinaModel;
  private Gamificacao $gamificacaoModel;

  public function __construct()
  {
    parent::__construct();
    $this->pomodoroModel = new Pomodoro();
    $this->tarefaModel = new Tarefa();
    $this->disciplinaModel = new Disciplina();
    $this->gamificacaoModel = new Gamificacao();
  }

  /**
   * Página principal de relatórios
   */
  public function index(): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $periodo = $_GET['periodo'] ?? 'mes';

    // Estatísticas Pomodoro
    $estatisticasPomodoro = $this->pomodoroModel->calcularEstatisticas($usuarioId, $periodo);
    $tempoPorDisciplina = $this->pomodoroModel->buscarTempoPorDisciplina($usuarioId, $periodo);

    // Estatísticas de Tarefas
    $estatisticasTarefas = $this->tarefaModel->contarPorStatus($usuarioId);
    $tarefasAtrasadas = count($this->tarefaModel->buscarAtrasadas($usuarioId));
    $tarefasProximas = count($this->tarefaModel->buscarProximasDoPrazo($usuarioId));

    // Gamificação
    $gamificacao = $this->gamificacaoModel->buscarPorUsuario($usuarioId);

    // Disciplinas
    $disciplinas = $this->disciplinaModel->buscarPorUsuario($usuarioId);
    $estatisticasDisciplinas = [];
    foreach ($disciplinas as $disciplina) {
      $estatisticasDisciplinas[] = array_merge(
        $disciplina,
        $this->disciplinaModel->buscarEstatisticas($disciplina['id'])
      );
    }

    $this->render('relatorio/index', [
      'periodo' => $periodo,
      'pomodoro' => $estatisticasPomodoro,
      'tempo_disciplinas' => $tempoPorDisciplina,
      'tarefas' => $estatisticasTarefas,
      'tarefas_atrasadas' => $tarefasAtrasadas,
      'tarefas_proximas' => $tarefasProximas,
      'gamificacao' => $gamificacao,
      'disciplinas' => $estatisticasDisciplinas,
      'titulo' => 'Relatórios'
    ]);
  }

  /**
   * Dados para gráfico de Pomodoro por dia (AJAX)
   */
  public function chartPomodoroDaily(): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $dias = (int) ($_GET['dias'] ?? 7);

    $dataInicio = date('Y-m-d', strtotime("-{$dias} days"));
    $dataFim = date('Y-m-d');

    $historico = $this->pomodoroModel->buscarHistorico($usuarioId, [
      'data_inicio' => $dataInicio,
      'data_fim' => $dataFim
    ], 1000);

    // Agrupa por dia
    $dadosPorDia = [];
    foreach ($historico as $sessao) {
      if ($sessao['tipo'] === 'foco' && $sessao['finalizada']) {
        $dia = date('Y-m-d', strtotime($sessao['data_inicio']));
        if (!isset($dadosPorDia[$dia])) {
          $dadosPorDia[$dia] = 0;
        }
        $dadosPorDia[$dia] += (int) $sessao['duracao_real'];
      }
    }

    // Preenche dias sem dados
    $labels = [];
    $valores = [];
    for ($i = $dias - 1; $i >= 0; $i--) {
      $dia = date('Y-m-d', strtotime("-{$i} days"));
      $labels[] = date('d/m', strtotime($dia));
      $valores[] = $dadosPorDia[$dia] ?? 0;
    }

    $this->jsonResponse([
      'labels' => $labels,
      'data' => $valores
    ]);
  }

  /**
   * Dados para gráfico de tempo por disciplina (AJAX)
   */
  public function chartDisciplinas(): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $periodo = $_GET['periodo'] ?? 'mes';

    $tempoPorDisciplina = $this->pomodoroModel->buscarTempoPorDisciplina($usuarioId, $periodo);

    $labels = array_map(fn($d) => $d['nome'], $tempoPorDisciplina);
    $valores = array_map(fn($d) => (int) $d['tempo_total'], $tempoPorDisciplina);
    $cores = array_map(fn($d) => $d['cor'], $tempoPorDisciplina);

    $this->jsonResponse([
      'labels' => $labels,
      'data' => $valores,
      'colors' => $cores
    ]);
  }

  /**
   * Dados para gráfico de tarefas por status (AJAX)
   */
  public function chartTarefas(): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $estatisticas = $this->tarefaModel->contarPorStatus($usuarioId);

    $this->jsonResponse([
      'labels' => array_keys($estatisticas),
      'data' => array_values($estatisticas)
    ]);
  }

  /**
   * Exporta relatório em CSV
   */
  public function exportCsv(): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $tipo = $_GET['tipo'] ?? 'pomodoro';
    $periodo = $_GET['periodo'] ?? 'mes';

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="relatorio_' . $tipo . '_' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

    if ($tipo === 'pomodoro') {
      fputcsv($output, ['Data', 'Disciplina', 'Tarefa', 'Tipo', 'Duração (min)', 'Interrompida']);

      $historico = $this->pomodoroModel->buscarHistorico($usuarioId, [], 1000);
      foreach ($historico as $sessao) {
        fputcsv($output, [
          date('d/m/Y H:i', strtotime($sessao['data_inicio'])),
          $sessao['disciplina_nome'] ?? '-',
          $sessao['tarefa_titulo'] ?? '-',
          ucfirst($sessao['tipo']),
          $sessao['duracao_real'],
          $sessao['interrompida'] ? 'Sim' : 'Não'
        ]);
      }
    } elseif ($tipo === 'tarefas') {
      fputcsv($output, ['Título', 'Disciplina', 'Prioridade', 'Status', 'Data Entrega', 'Concluída']);

      $tarefas = $this->tarefaModel->buscarPorUsuario($usuarioId);
      foreach ($tarefas as $tarefa) {
        fputcsv($output, [
          $tarefa['titulo'],
          $tarefa['disciplina_nome'] ?? '-',
          ucfirst($tarefa['prioridade']),
          ucfirst($tarefa['status']),
          $tarefa['data_entrega'] ? date('d/m/Y', strtotime($tarefa['data_entrega'])) : '-',
          $tarefa['concluida'] ? 'Sim' : 'Não'
        ]);
      }
    }

    fclose($output);
    exit;
  }
}
