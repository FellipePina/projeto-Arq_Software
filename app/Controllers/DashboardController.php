<?php

namespace App\Controllers;

use App\Models\Usuario;
use App\Models\ConteudoEstudo;
use App\Models\SessaoEstudo;
use App\Models\Meta;
use App\Models\Categoria;

/**
 * Classe DashboardController - Controlador do painel principal
 *
 * Princípios aplicados:
 * - Single Responsibility: apenas operações do dashboard
 * - Composition: usa múltiplos modelos para compor dados
 * - Interface Segregation: métodos específicos para cada necessidade
 */
class DashboardController extends BaseController
{
  private ConteudoEstudo $conteudoModel;
  private SessaoEstudo $sessaoModel;
  private Meta $metaModel;
  private Categoria $categoriaModel;

  /**
   * Construtor - inicializa os modelos necessários
   */
  public function __construct()
  {
    parent::__construct();
    $this->conteudoModel = new ConteudoEstudo();
    $this->sessaoModel = new SessaoEstudo();
    $this->metaModel = new Meta();
    $this->categoriaModel = new Categoria();
  }

  /**
   * Página principal do dashboard
   */
  public function index(): void
  {
    $this->requireLogin();

    $usuarioId = $this->getLoggedUserId();

    // Busca dados para o dashboard
    $data = [
      'titulo' => 'Dashboard - ' . APP_NAME,
      'flash_messages' => $this->getFlashMessages(),
      'estatisticas' => $this->gerarEstatisticas($usuarioId),
      'conteudos_recentes' => $this->buscarConteudosRecentes($usuarioId),
      'sessoes_recentes' => $this->buscarSessoesRecentes($usuarioId),
      'metas_ativas' => $this->buscarMetasAtivas($usuarioId),
      'progresso_semanal' => $this->calcularProgressoSemanal($usuarioId)
    ];

    $this->render('dashboard/index', $data);
  }

  /**
   * Gera estatísticas gerais do usuário
   *
   * @param int $usuarioId ID do usuário
   * @return array Estatísticas compiladas
   */
  private function gerarEstatisticas(int $usuarioId): array
  {
    // Contadores por status de conteúdo
    $contadoresConteudo = $this->conteudoModel->contarPorStatus($usuarioId);

    // Total de horas estudadas
    $totalHorasHoje = $this->sessaoModel->calcularTotalHoras(
      $usuarioId,
      date('Y-m-d'),
      date('Y-m-d')
    );

    $totalHorasSemana = $this->sessaoModel->calcularTotalHoras(
      $usuarioId,
      date('Y-m-d', strtotime('-6 days')),
      date('Y-m-d')
    );

    $totalHorasMes = $this->sessaoModel->calcularTotalHoras(
      $usuarioId,
      date('Y-m-01'),
      date('Y-m-d')
    );

    // Contadores de metas
    $metasAtivas = count($this->metaModel->buscarAtivasPorUsuario($usuarioId));

    return [
      'total_conteudos' => array_sum($contadoresConteudo),
      'conteudos_concluidos' => $contadoresConteudo[ConteudoEstudo::STATUS_CONCLUIDO] ?? 0,
      'conteudos_em_andamento' => $contadoresConteudo[ConteudoEstudo::STATUS_EM_ANDAMENTO] ?? 0,
      'conteudos_pendentes' => $contadoresConteudo[ConteudoEstudo::STATUS_PENDENTE] ?? 0,
      'horas_hoje' => $totalHorasHoje,
      'horas_semana' => $totalHorasSemana,
      'horas_mes' => $totalHorasMes,
      'metas_ativas' => $metasAtivas,
      'media_horas_dia' => $totalHorasSemana > 0 ? round($totalHorasSemana / 7, 1) : 0
    ];
  }

  /**
   * Busca conteúdos mais recentes do usuário
   *
   * @param int $usuarioId ID do usuário
   * @return array Lista de conteúdos recentes
   */
  private function buscarConteudosRecentes(int $usuarioId): array
  {
    $conteudos = $this->conteudoModel->buscarPorUsuario($usuarioId);

    // Retorna apenas os 5 mais recentes
    return array_slice($conteudos, 0, 5);
  }

  /**
   * Busca sessões de estudo mais recentes
   *
   * @param int $usuarioId ID do usuário
   * @return array Lista de sessões recentes
   */
  private function buscarSessoesRecentes(int $usuarioId): array
  {
    return $this->sessaoModel->buscarPorUsuario($usuarioId, 5);
  }

  /**
   * Busca metas ativas do usuário
   *
   * @param int $usuarioId ID do usuário
   * @return array Lista de metas ativas
   */
  private function buscarMetasAtivas(int $usuarioId): array
  {
    $metas = $this->metaModel->buscarAtivasPorUsuario($usuarioId);

    // Adiciona dados de progresso para cada meta
    foreach ($metas as &$meta) {
      $meta['progresso_atual'] = $this->metaModel->calcularProgresso($meta['id']);
    }

    return $metas;
  }

  /**
   * Calcula progresso de estudo da última semana
   *
   * @param int $usuarioId ID do usuário
   * @return array Dados de progresso por dia da semana
   */
  private function calcularProgressoSemanal(int $usuarioId): array
  {
    $progresso = [];

    // Gera dados para os últimos 7 dias
    for ($i = 6; $i >= 0; $i--) {
      $data = date('Y-m-d', strtotime("-{$i} days"));
      $dataFormatada = date('d/m', strtotime($data));
      $diaSemana = date('D', strtotime($data));

      // Traduz dias da semana
      $diasSemana = [
        'Sun' => 'Dom',
        'Mon' => 'Seg',
        'Tue' => 'Ter',
        'Wed' => 'Qua',
        'Thu' => 'Qui',
        'Fri' => 'Sex',
        'Sat' => 'Sáb'
      ];

      $horasEstudadas = $this->sessaoModel->calcularTotalHoras($usuarioId, $data, $data);

      $progresso[] = [
        'data' => $data,
        'data_formatada' => $dataFormatada,
        'dia_semana' => $diasSemana[$diaSemana] ?? $diaSemana,
        'horas' => $horasEstudadas,
        'minutos_totais' => $horasEstudadas * 60
      ];
    }

    return $progresso;
  }

  /**
   * Retorna dados para gráficos em formato JSON
   */
  public function graficos(): void
  {
    $this->requireLogin();

    $usuarioId = $this->getLoggedUserId();
    $tipo = $_GET['tipo'] ?? 'progresso_semanal';

    switch ($tipo) {
      case 'progresso_semanal':
        $dados = $this->calcularProgressoSemanal($usuarioId);
        break;

      case 'conteudos_por_categoria':
        $dados = $this->gerarDadosConteudosPorCategoria($usuarioId);
        break;

      case 'horas_por_mes':
        $dados = $this->calcularHorasPorMes($usuarioId);
        break;

      default:
        $dados = [];
    }

    $this->jsonResponse(['dados' => $dados]);
  }

  /**
   * Gera dados de conteúdos por categoria para gráfico
   *
   * @param int $usuarioId ID do usuário
   * @return array Dados por categoria
   */
  private function gerarDadosConteudosPorCategoria(int $usuarioId): array
  {
    $categorias = $this->categoriaModel->buscarPorUsuario($usuarioId);
    $dados = [];

    foreach ($categorias as $categoria) {
      $conteudos = $this->conteudoModel->buscarPorCategoria($categoria['id']);

      if (count($conteudos) > 0) {
        $dados[] = [
          'nome' => $categoria['nome'],
          'total' => count($conteudos),
          'cor' => $categoria['cor']
        ];
      }
    }

    // Adiciona conteúdos sem categoria
    $semCategoria = $this->conteudoModel->contarSemCategoria($usuarioId);

    if ($semCategoria > 0) {
      $dados[] = [
        'nome' => 'Sem Categoria',
        'total' => (int) $semCategoria,
        'cor' => '#6c757d'
      ];
    }

    return $dados;
  }

  /**
   * Calcula horas estudadas por mês (últimos 6 meses)
   *
   * @param int $usuarioId ID do usuário
   * @return array Dados por mês
   */
  private function calcularHorasPorMes(int $usuarioId): array
  {
    $dados = [];

    for ($i = 5; $i >= 0; $i--) {
      $data = date('Y-m-01', strtotime("-{$i} months"));
      $ultimoDiaMes = date('Y-m-t', strtotime($data));
      $mesAno = date('M/Y', strtotime($data));

      // Traduz mês
      $meses = [
        'Jan' => 'Jan',
        'Feb' => 'Fev',
        'Mar' => 'Mar',
        'Apr' => 'Abr',
        'May' => 'Mai',
        'Jun' => 'Jun',
        'Jul' => 'Jul',
        'Aug' => 'Ago',
        'Sep' => 'Set',
        'Oct' => 'Out',
        'Nov' => 'Nov',
        'Dec' => 'Dez'
      ];

      $mesFormatado = str_replace(array_keys($meses), array_values($meses), $mesAno);

      $horas = $this->sessaoModel->calcularTotalHoras($usuarioId, $data, $ultimoDiaMes);

      $dados[] = [
        'mes' => $mesFormatado,
        'horas' => $horas
      ];
    }

    return $dados;
  }
}
