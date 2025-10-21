<?php

namespace App\Models;

/**
 * Classe Pomodoro - Modelo para gerenciar sessões Pomodoro
 *
 * Princípios SOLID:
 * - Single Responsibility: gerencia apenas sessões de tempo
 * - Open/Closed: extensível para novos tipos de sessão
 */
class Pomodoro extends BaseModel
{
  protected string $table = 'sessoes_pomodoro';

  /**
   * Inicia uma sessão Pomodoro
   *
   * @param array $dados Dados da sessão
   * @return int|false ID da sessão ou false
   */
  public function iniciarSessao(array $dados)
  {
    if (empty($dados['usuario_id']) || empty($dados['tipo'])) {
      return false;
    }

    $sessao = [
      'usuario_id' => (int) $dados['usuario_id'],
      'disciplina_id' => !empty($dados['disciplina_id']) ? (int) $dados['disciplina_id'] : null,
      'tarefa_id' => !empty($dados['tarefa_id']) ? (int) $dados['tarefa_id'] : null,
      'tipo' => $dados['tipo'], // foco, pausa_curta, pausa_longa
      'duracao_planejada' => (int) ($dados['duracao_planejada'] ?? 25),
      'data_inicio' => date('Y-m-d H:i:s')
    ];

    return $this->save($sessao);
  }

  /**
   * Finaliza uma sessão Pomodoro com sucesso
   *
   * @param int $id ID da sessão
   * @param int $usuarioId ID do usuário
   * @return bool Sucesso
   */
  public function finalizarSessao(int $id, int $usuarioId): bool
  {
    $sessao = $this->buscarPorIdEUsuario($id, $usuarioId);
    if (!$sessao || $sessao['concluida']) {
      return false;
    }

    $dataInicio = new \DateTime($sessao['data_inicio']);
    $dataFim = new \DateTime();
    $duracaoReal = $dataFim->getTimestamp() - $dataInicio->getTimestamp();
    $duracaoMinutos = round($duracaoReal / 60);

    $sql = "UPDATE {$this->table}
            SET concluida = 1,
                data_fim = :data_fim,
                duracao_real = :duracao_real,
                interrompida = 0
            WHERE id = :id AND usuario_id = :usuario_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':data_fim', $dataFim->format('Y-m-d H:i:s'));
    $stmt->bindValue(':duracao_real', $duracaoMinutos, \PDO::PARAM_INT);
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Interrompe uma sessão Pomodoro
   *
   * @param int $id ID da sessão
   * @param int $usuarioId ID do usuário
   * @return bool Sucesso
   */
  public function interromperSessao(int $id, int $usuarioId): bool
  {
    $sessao = $this->buscarPorIdEUsuario($id, $usuarioId);
    if (!$sessao || $sessao['concluida']) {
      return false;
    }

    $dataInicio = new \DateTime($sessao['data_inicio']);
    $dataFim = new \DateTime();
    $duracaoReal = $dataFim->getTimestamp() - $dataInicio->getTimestamp();
    $duracaoMinutos = round($duracaoReal / 60);

    $sql = "UPDATE {$this->table}
            SET concluida = 1,
                data_fim = :data_fim,
                duracao_real = :duracao_real,
                interrompida = 1
            WHERE id = :id AND usuario_id = :usuario_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':data_fim', $dataFim->format('Y-m-d H:i:s'));
    $stmt->bindValue(':duracao_real', $duracaoMinutos, \PDO::PARAM_INT);
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Busca sessão por ID com validação de usuário
   *
   * @param int $id ID da sessão
   * @param int $usuarioId ID do usuário
   * @return array|false Dados da sessão
   */
  public function buscarPorIdEUsuario(int $id, int $usuarioId)
  {
    $sql = "SELECT p.*,
              d.nome as disciplina_nome,
              d.cor as disciplina_cor,
              t.titulo as tarefa_titulo
            FROM {$this->table} p
            LEFT JOIN disciplinas d ON d.id = p.disciplina_id
            LEFT JOIN tarefas t ON t.id = p.tarefa_id
            WHERE p.id = :id AND p.usuario_id = :usuario_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch();
  }

  /**
   * Busca sessão ativa (em andamento)
   *
   * @param int $usuarioId ID do usuário
   * @return array|false Sessão ativa
   */
  public function buscarSessaoAtiva(int $usuarioId)
  {
    $sql = "SELECT p.*,
              d.nome as disciplina_nome,
              d.cor as disciplina_cor,
              t.titulo as tarefa_titulo
            FROM {$this->table} p
            LEFT JOIN disciplinas d ON d.id = p.disciplina_id
            LEFT JOIN tarefas t ON t.id = p.tarefa_id
            WHERE p.usuario_id = :usuario_id
            AND p.concluida = 0
            ORDER BY p.data_inicio DESC
            LIMIT 1";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch();
  }

  /**
   * Busca histórico de sessões
   *
   * @param int $usuarioId ID do usuário
   * @param array $filtros Filtros opcionais
   * @param int $limite Limite de resultados
   * @return array Lista de sessões
   */
  public function buscarHistorico(int $usuarioId, array $filtros = [], int $limite = 50): array
  {
    $sql = "SELECT p.*,
              d.nome as disciplina_nome,
              d.cor as disciplina_cor,
              t.titulo as tarefa_titulo
            FROM {$this->table} p
            LEFT JOIN disciplinas d ON d.id = p.disciplina_id
            LEFT JOIN tarefas t ON t.id = p.tarefa_id
            WHERE p.usuario_id = :usuario_id";

    $params = [':usuario_id' => $usuarioId];

    // Filtro por disciplina
    if (!empty($filtros['disciplina_id'])) {
      $sql .= " AND p.disciplina_id = :disciplina_id";
      $params[':disciplina_id'] = $filtros['disciplina_id'];
    }

    // Filtro por tipo
    if (!empty($filtros['tipo'])) {
      $sql .= " AND p.tipo = :tipo";
      $params[':tipo'] = $filtros['tipo'];
    }

    // Filtro por período
    if (!empty($filtros['data_inicio'])) {
      $sql .= " AND DATE(p.data_inicio) >= :data_inicio";
      $params[':data_inicio'] = $filtros['data_inicio'];
    }

    if (!empty($filtros['data_fim'])) {
      $sql .= " AND DATE(p.data_inicio) <= :data_fim";
      $params[':data_fim'] = $filtros['data_fim'];
    }

    $sql .= " ORDER BY p.data_inicio DESC LIMIT :limite";

    $stmt = $this->db->prepare($sql);
    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Calcula estatísticas de Pomodoro
   *
   * @param int $usuarioId ID do usuário
   * @param string|null $periodo Período (hoje, semana, mes)
   * @return array Estatísticas
   */
  public function calcularEstatisticas(int $usuarioId, ?string $periodo = null): array
  {
    $sql = "SELECT
              COUNT(*) as total_sessoes,
              SUM(CASE WHEN tipo = 'foco' AND concluida = 1 AND interrompida = 0 THEN 1 ELSE 0 END) as sessoes_completas,
              SUM(CASE WHEN interrompida = 1 THEN 1 ELSE 0 END) as sessoes_interrompidas,
              SUM(CASE WHEN tipo = 'foco' THEN duracao_real ELSE 0 END) as tempo_foco_total,
              AVG(CASE WHEN tipo = 'foco' AND concluida = 1 THEN duracao_real END) as tempo_foco_medio
            FROM {$this->table}
            WHERE usuario_id = :usuario_id";

    $params = [':usuario_id' => $usuarioId];

    // Filtro de período
    if ($periodo === 'hoje') {
      $sql .= " AND DATE(data_inicio) = CURDATE()";
    } elseif ($periodo === 'semana') {
      $sql .= " AND YEARWEEK(data_inicio, 1) = YEARWEEK(CURDATE(), 1)";
    } elseif ($periodo === 'mes') {
      $sql .= " AND YEAR(data_inicio) = YEAR(CURDATE())
                AND MONTH(data_inicio) = MONTH(CURDATE())";
    }

    $stmt = $this->db->prepare($sql);
    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value);
    }
    $stmt->execute();

    $resultado = $stmt->fetch();

    return [
      'total_sessoes' => (int) $resultado['total_sessoes'],
      'sessoes_completas' => (int) $resultado['sessoes_completas'],
      'sessoes_interrompidas' => (int) $resultado['sessoes_interrompidas'],
      'tempo_foco_total' => (int) $resultado['tempo_foco_total'],
      'tempo_foco_medio' => round((float) $resultado['tempo_foco_medio'], 1),
      'taxa_conclusao' => $resultado['total_sessoes'] > 0
        ? round(($resultado['sessoes_completas'] / $resultado['total_sessoes']) * 100, 1)
        : 0
    ];
  }

  /**
   * Busca tempo por disciplina
   *
   * @param int $usuarioId ID do usuário
   * @param string|null $periodo Período
   * @return array Tempo por disciplina
   */
  public function buscarTempoPorDisciplina(int $usuarioId, ?string $periodo = null): array
  {
    $sql = "SELECT
              d.id,
              d.nome,
              d.cor,
              SUM(p.duracao_real) as tempo_total,
              COUNT(*) as total_sessoes
            FROM {$this->table} p
            INNER JOIN disciplinas d ON d.id = p.disciplina_id
            WHERE p.usuario_id = :usuario_id
            AND p.tipo = 'foco'
            AND p.concluida = 1";

    $params = [':usuario_id' => $usuarioId];

    // Filtro de período
    if ($periodo === 'hoje') {
      $sql .= " AND DATE(p.data_inicio) = CURDATE()";
    } elseif ($periodo === 'semana') {
      $sql .= " AND YEARWEEK(p.data_inicio, 1) = YEARWEEK(CURDATE(), 1)";
    } elseif ($periodo === 'mes') {
      $sql .= " AND YEAR(p.data_inicio) = YEAR(CURDATE())
                AND MONTH(p.data_inicio) = MONTH(CURDATE())";
    }

    $sql .= " GROUP BY d.id, d.nome, d.cor
              ORDER BY tempo_total DESC";

    $stmt = $this->db->prepare($sql);
    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value);
    }
    $stmt->execute();

    return $stmt->fetchAll();
  }
}
