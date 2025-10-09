<?php

namespace App\Controllers;

use App\Models\Estatisticas;

class DashboardController
{
  public function index()
  {
    session_start();
    if (!isset($_SESSION['usuario_id'])) {
      header('Location: /usuario/login');
      exit;
    }

    // Gerar estatísticas básicas para o dashboard
    $estatisticasModel = new Estatisticas();
    $dataInicio = date('Y-m-d', strtotime('-30 days'));
    $dataFim = date('Y-m-d');
    $estatisticas = $estatisticasModel->gerarRelatorio($_SESSION['usuario_id'], $dataInicio, $dataFim);

    // Carrega a view do dashboard
    require_once '../app/Views/dashboard.php';
  }
}
