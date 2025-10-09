<?php

namespace App\Models;

use App\Config\Database;
use PDO;

/**
 * Classe para gerar estatísticas de estudo.
 * Não é um modelo de tabela, mas uma classe de serviço.
 */
class Estatisticas
{
  protected PDO $db;

  public function __construct()
  {
    $this->db = Database::getInstance();
  }

  /**
   * Gera um relatório de estatísticas para um usuário em um dado período.
   *
   * @param int $usuarioId
   * @param string $dataInicio
   * @param string $dataFim
   * @return array
   */
  public function gerarRelatorio(int $usuarioId, string $dataInicio, string $dataFim): array
  {
    $sessaoModel = new SessaoEstudo();
    $sessoes = $sessaoModel->buscarPorPeriodo($usuarioId, $dataInicio, $dataFim);

    return $this->calcularTotais($sessoes);
  }

  /**
   * Calcula os totais com base nas sessões de estudo.
   *
   * @param array $sessoes
   * @return array
   */
  private function calcularTotais(array $sessoes): array
  {
    $totalHoras = 0;
    $totalSessoes = count($sessoes);
    $dadosPorDia = [];

    foreach ($sessoes as $sessao) {
      $totalHoras += $sessao['duracao_minutos'];
      $dia = date('Y-m-d', strtotime($sessao['data_inicio']));
      if (!isset($dadosPorDia[$dia])) {
        $dadosPorDia[$dia] = 0;
      }
      $dadosPorDia[$dia] += $sessao['duracao_minutos'];
    }

    $totalHoras = round($totalHoras / 60, 2);
    $numDias = count($dadosPorDia);
    $mediaHorasPorDia = ($numDias > 0) ? round($totalHoras / $numDias, 2) : 0;

    return [
      'totalHoras' => $totalHoras,
      'totalSessoes' => $totalSessoes,
      'mediaHorasPorDia' => $mediaHorasPorDia,
      'dadosPorDia' => $dadosPorDia,
    ];
  }
}
