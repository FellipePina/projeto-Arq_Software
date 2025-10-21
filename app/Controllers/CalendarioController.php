<?php

namespace App\Controllers;

use App\Models\EventoCalendario;
use App\Models\Disciplina;

/**
 * CalendarioController - Gerencia eventos do calendário
 *
 * Princípios SOLID:
 * - Single Responsibility: apenas operações de calendário
 */
class CalendarioController extends BaseController
{
  private EventoCalendario $eventoModel;
  private Disciplina $disciplinaModel;

  public function __construct()
  {
    parent::__construct();
    $this->eventoModel = new EventoCalendario();
    $this->disciplinaModel = new Disciplina();
  }

  /**
   * Página principal do calendário
   */
  public function index(): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $disciplinas = $this->disciplinaModel->buscarPorUsuario($usuarioId);

    $this->render('calendario/index', [
      'disciplinas' => $disciplinas,
      'titulo' => 'Calendário'
    ]);
  }

  /**
   * Busca eventos por período (AJAX)
   */
  public function events(): void
  {
    $this->requireLogin();

    $usuarioId = $_SESSION['usuario_id'];
    $dataInicio = $_GET['start'] ?? date('Y-m-01');
    $dataFim = $_GET['end'] ?? date('Y-m-t');

    $eventos = $this->eventoModel->buscarPorPeriodo($usuarioId, $dataInicio, $dataFim);

    // Formata para o formato do FullCalendar
    $eventosFormatados = array_map(function ($evento) {
      return [
        'id' => $evento['id'],
        'title' => $evento['titulo'],
        'start' => $evento['data_inicio'],
        'end' => $evento['data_fim'],
        'backgroundColor' => $evento['cor'] ?? $evento['disciplina_cor'] ?? '#3B82F6',
        'borderColor' => $evento['cor'] ?? $evento['disciplina_cor'] ?? '#3B82F6',
        'extendedProps' => [
          'descricao' => $evento['descricao'],
          'disciplina' => $evento['disciplina_nome'],
          'tipo' => $evento['tipo']
        ]
      ];
    }, $eventos);

    $this->jsonResponse($eventosFormatados);
  }

  /**
   * Cria novo evento
   */
  public function store(): void
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

    $dados = [
      'titulo' => $this->sanitizeInput($_POST['titulo'] ?? ''),
      'descricao' => $this->sanitizeInput($_POST['descricao'] ?? ''),
      'usuario_id' => $_SESSION['usuario_id'],
      'disciplina_id' => !empty($_POST['disciplina_id']) ? (int) $_POST['disciplina_id'] : null,
      'data_inicio' => $_POST['data_inicio'] ?? date('Y-m-d H:i:s'),
      'data_fim' => $_POST['data_fim'] ?? null,
      'tipo' => $_POST['tipo'] ?? 'evento',
      'lembrete_minutos' => !empty($_POST['lembrete_minutos']) ? (int) $_POST['lembrete_minutos'] : null,
      'cor' => $_POST['cor'] ?? null
    ];

    if (empty($dados['titulo'])) {
      $this->jsonResponse(['success' => false, 'message' => 'Título obrigatório'], 400);
      return;
    }

    $id = $this->eventoModel->criar($dados);

    if ($id) {
      $evento = $this->eventoModel->buscarPorIdEUsuario($id, $_SESSION['usuario_id']);
      $this->jsonResponse([
        'success' => true,
        'message' => 'Evento criado!',
        'evento' => $evento
      ]);
    } else {
      $this->jsonResponse(['success' => false, 'message' => 'Erro ao criar evento'], 500);
    }
  }

  /**
   * Atualiza evento
   */
  public function update(int $id): void
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
    $dados = [
      'titulo' => $this->sanitizeInput($_POST['titulo'] ?? ''),
      'descricao' => $this->sanitizeInput($_POST['descricao'] ?? ''),
      'disciplina_id' => !empty($_POST['disciplina_id']) ? (int) $_POST['disciplina_id'] : null,
      'data_inicio' => $_POST['data_inicio'],
      'data_fim' => $_POST['data_fim'] ?? null,
      'tipo' => $_POST['tipo'] ?? 'evento',
      'lembrete_minutos' => !empty($_POST['lembrete_minutos']) ? (int) $_POST['lembrete_minutos'] : null,
      'cor' => $_POST['cor'] ?? null
    ];

    if (empty($dados['titulo'])) {
      $this->jsonResponse(['success' => false, 'message' => 'Título obrigatório'], 400);
      return;
    }

    $sucesso = $this->eventoModel->atualizar($id, $dados, $usuarioId);

    if ($sucesso) {
      $this->jsonResponse(['success' => true, 'message' => 'Evento atualizado!']);
    } else {
      $this->jsonResponse(['success' => false, 'message' => 'Erro ao atualizar evento'], 500);
    }
  }

  /**
   * Exclui evento
   */
  public function delete(int $id): void
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
    $sucesso = $this->eventoModel->excluir($id, $usuarioId);

    if ($sucesso) {
      $this->jsonResponse(['success' => true, 'message' => 'Evento excluído!']);
    } else {
      $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir evento'], 500);
    }
  }

  /**
   * Atualiza data do evento (drag and drop)
   */
  public function updateDate(int $id): void
  {
    $this->requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->jsonResponse(['success' => false, 'message' => 'Método inválido'], 400);
      return;
    }

    $usuarioId = $_SESSION['usuario_id'];
    $evento = $this->eventoModel->buscarPorIdEUsuario($id, $usuarioId);

    if (!$evento) {
      $this->jsonResponse(['success' => false, 'message' => 'Evento não encontrado'], 404);
      return;
    }

    $dados = [
      'titulo' => $evento['titulo'],
      'descricao' => $evento['descricao'],
      'disciplina_id' => $evento['disciplina_id'],
      'data_inicio' => $_POST['data_inicio'],
      'data_fim' => $_POST['data_fim'] ?? $evento['data_fim'],
      'tipo' => $evento['tipo'],
      'lembrete_minutos' => $evento['lembrete_minutos'],
      'cor' => $evento['cor']
    ];

    $sucesso = $this->eventoModel->atualizar($id, $dados, $usuarioId);

    if ($sucesso) {
      $this->jsonResponse(['success' => true, 'message' => 'Data atualizada!']);
    } else {
      $this->jsonResponse(['success' => false, 'message' => 'Erro ao atualizar'], 500);
    }
  }
}
