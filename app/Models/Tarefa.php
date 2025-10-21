<?php

namespace App\Models;

/**
 * Classe Tarefa - Modelo para gerenciar tarefas (To-Do)
 *
 * Princípios SOLID:
 * - Single Responsibility: gerencia apenas tarefas
 * - Interface Segregation: métodos específicos e focados
 */
class Tarefa extends BaseModel
{
  protected string $table = 'tarefas';

  /**
   * Busca tarefas de um usuário com filtros
   *
   * @param int $usuarioId ID do usuário
   * @param array $filtros Filtros opcionais
   * @return array Lista de tarefas
   */
  public function buscarPorUsuario(int $usuarioId, array $filtros = []): array
  {
    $sql = "SELECT t.*, d.nome as disciplina_nome, d.cor as disciplina_cor
            FROM {$this->table} t
            LEFT JOIN disciplinas d ON d.id = t.disciplina_id
            WHERE t.usuario_id = :usuario_id";

    $params = [':usuario_id' => $usuarioId];

    // Filtro por disciplina
    if (!empty($filtros['disciplina_id'])) {
      $sql .= " AND t.disciplina_id = :disciplina_id";
      $params[':disciplina_id'] = $filtros['disciplina_id'];
    }

    // Filtro por status
    if (!empty($filtros['status'])) {
      $sql .= " AND t.status = :status";
      $params[':status'] = $filtros['status'];
    }

    // Filtro por prioridade
    if (!empty($filtros['prioridade'])) {
      $sql .= " AND t.prioridade = :prioridade";
      $params[':prioridade'] = $filtros['prioridade'];
    }

    // Filtro de concluída
    if (isset($filtros['concluida'])) {
      $sql .= " AND t.concluida = :concluida";
      $params[':concluida'] = (int) $filtros['concluida'];
    }

    $sql .= " ORDER BY
              CASE t.prioridade
                WHEN 'urgente' THEN 1
                WHEN 'alta' THEN 2
                WHEN 'media' THEN 3
                WHEN 'baixa' THEN 4
              END,
              t.data_entrega ASC,
              t.data_criacao DESC";

    $stmt = $this->db->prepare($sql);
    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value);
    }
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Busca tarefa por ID com validação de usuário
   *
   * @param int $id ID da tarefa
   * @param int $usuarioId ID do usuário
   * @return array|false Dados da tarefa
   */
  public function buscarPorIdEUsuario(int $id, int $usuarioId)
  {
    $sql = "SELECT t.*, d.nome as disciplina_nome, d.cor as disciplina_cor
            FROM {$this->table} t
            LEFT JOIN disciplinas d ON d.id = t.disciplina_id
            WHERE t.id = :id AND t.usuario_id = :usuario_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch();
  }

  /**
   * Cria uma nova tarefa
   *
   * @param array $dados Dados da tarefa
   * @return int|false ID da tarefa ou false
   */
  public function criar(array $dados)
  {
    if (empty($dados['titulo']) || empty($dados['usuario_id'])) {
      return false;
    }

    $tarefa = [
      'titulo' => trim($dados['titulo']),
      'descricao' => trim($dados['descricao'] ?? ''),
      'disciplina_id' => !empty($dados['disciplina_id']) ? (int) $dados['disciplina_id'] : null,
      'usuario_id' => (int) $dados['usuario_id'],
      'data_entrega' => !empty($dados['data_entrega']) ? $dados['data_entrega'] : null,
      'prioridade' => $dados['prioridade'] ?? 'media',
      'status' => 'pendente'
    ];

    return $this->save($tarefa);
  }

  /**
   * Atualiza uma tarefa
   *
   * @param int $id ID da tarefa
   * @param array $dados Novos dados
   * @param int $usuarioId ID do usuário
   * @return bool Sucesso
   */
  public function atualizar(int $id, array $dados, int $usuarioId): bool
  {
    $tarefa = $this->buscarPorIdEUsuario($id, $usuarioId);
    if (!$tarefa) {
      return false;
    }

    $dadosAtualizados = [
      'id' => $id,
      'titulo' => trim($dados['titulo']),
      'descricao' => trim($dados['descricao'] ?? ''),
      'disciplina_id' => !empty($dados['disciplina_id']) ? (int) $dados['disciplina_id'] : null,
      'data_entrega' => $dados['data_entrega'] ?? null,
      'prioridade' => $dados['prioridade'] ?? $tarefa['prioridade'],
      'status' => $dados['status'] ?? $tarefa['status']
    ];

    return $this->save($dadosAtualizados) !== false;
  }

  /**
   * Marca tarefa como concluída
   *
   * @param int $id ID da tarefa
   * @param int $usuarioId ID do usuário
   * @return bool Sucesso
   */
  public function marcarConcluida(int $id, int $usuarioId): bool
  {
    $sql = "UPDATE {$this->table}
            SET concluida = 1, status = 'concluida', data_conclusao = NOW()
            WHERE id = :id AND usuario_id = :usuario_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Marca tarefa como não concluída
   *
   * @param int $id ID da tarefa
   * @param int $usuarioId ID do usuário
   * @return bool Sucesso
   */
  public function marcarPendente(int $id, int $usuarioId): bool
  {
    $sql = "UPDATE {$this->table}
            SET concluida = 0, status = 'pendente', data_conclusao = NULL
            WHERE id = :id AND usuario_id = :usuario_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Busca tarefas com prazo próximo (próximos 7 dias)
   *
   * @param int $usuarioId ID do usuário
   * @return array Tarefas próximas do prazo
   */
  public function buscarProximasDoPrazo(int $usuarioId): array
  {
    $sql = "SELECT t.*, d.nome as disciplina_nome, d.cor as disciplina_cor
            FROM {$this->table} t
            LEFT JOIN disciplinas d ON d.id = t.disciplina_id
            WHERE t.usuario_id = :usuario_id
            AND t.concluida = 0
            AND t.data_entrega IS NOT NULL
            AND t.data_entrega BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
            ORDER BY t.data_entrega ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Busca tarefas atrasadas
   *
   * @param int $usuarioId ID do usuário
   * @return array Tarefas atrasadas
   */
  public function buscarAtrasadas(int $usuarioId): array
  {
    $sql = "SELECT t.*, d.nome as disciplina_nome, d.cor as disciplina_cor
            FROM {$this->table} t
            LEFT JOIN disciplinas d ON d.id = t.disciplina_id
            WHERE t.usuario_id = :usuario_id
            AND t.concluida = 0
            AND t.data_entrega < CURDATE()
            ORDER BY t.data_entrega ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Conta tarefas por status
   *
   * @param int $usuarioId ID do usuário
   * @return array Contagem por status
   */
  public function contarPorStatus(int $usuarioId): array
  {
    $sql = "SELECT
              status,
              COUNT(*) as total
            FROM {$this->table}
            WHERE usuario_id = :usuario_id
            GROUP BY status";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    $resultado = [];
    while ($row = $stmt->fetch()) {
      $resultado[$row['status']] = (int) $row['total'];
    }

    return $resultado;
  }
}
