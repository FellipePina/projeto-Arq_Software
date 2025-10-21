<?php

namespace App\Models;

/**
 * Classe Disciplina - Modelo para gerenciar disciplinas/matérias
 *
 * Princípios SOLID aplicados:
 * - Single Responsibility: apenas operações de disciplinas
 * - Open/Closed: extensível sem modificar BaseModel
 */
class Disciplina extends BaseModel
{
  protected string $table = 'disciplinas';

  /**
   * Busca todas as disciplinas ativas de um usuário
   *
   * @param int $usuarioId ID do usuário
   * @return array Lista de disciplinas
   */
  public function buscarPorUsuario(int $usuarioId): array
  {
    $sql = "SELECT * FROM {$this->table}
            WHERE usuario_id = :usuario_id AND ativa = 1
            ORDER BY nome ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Busca disciplina por ID e valida se pertence ao usuário
   *
   * @param int $id ID da disciplina
   * @param int $usuarioId ID do usuário
   * @return array|false Dados da disciplina ou false
   */
  public function buscarPorIdEUsuario(int $id, int $usuarioId)
  {
    $sql = "SELECT * FROM {$this->table}
            WHERE id = :id AND usuario_id = :usuario_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch();
  }

  /**
   * Cria uma nova disciplina
   *
   * @param array $dados Dados da disciplina
   * @return int|false ID da disciplina criada ou false
   */
  public function criar(array $dados)
  {
    // Validações
    if (empty($dados['nome']) || empty($dados['usuario_id'])) {
      return false;
    }

    $disciplina = [
      'nome' => trim($dados['nome']),
      'codigo' => trim($dados['codigo'] ?? ''),
      'cor' => $dados['cor'] ?? '#007bff',
      'descricao' => trim($dados['descricao'] ?? ''),
      'usuario_id' => (int) $dados['usuario_id'],
      'ativa' => true
    ];

    return $this->save($disciplina);
  }

  /**
   * Atualiza uma disciplina
   *
   * @param int $id ID da disciplina
   * @param array $dados Novos dados
   * @param int $usuarioId ID do usuário (validação)
   * @return bool Sucesso da operação
   */
  public function atualizar(int $id, array $dados, int $usuarioId): bool
  {
    // Verifica se a disciplina pertence ao usuário
    $disciplina = $this->buscarPorIdEUsuario($id, $usuarioId);
    if (!$disciplina) {
      return false;
    }

    $dadosAtualizados = [
      'id' => $id,
      'nome' => trim($dados['nome']),
      'codigo' => trim($dados['codigo'] ?? ''),
      'cor' => $dados['cor'] ?? $disciplina['cor'],
      'descricao' => trim($dados['descricao'] ?? '')
    ];

    return $this->save($dadosAtualizados) !== false;
  }

  /**
   * Arquiva (desativa) uma disciplina
   *
   * @param int $id ID da disciplina
   * @param int $usuarioId ID do usuário
   * @return bool Sucesso da operação
   */
  public function arquivar(int $id, int $usuarioId): bool
  {
    $sql = "UPDATE {$this->table} SET ativa = 0
            WHERE id = :id AND usuario_id = :usuario_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Conta tarefas pendentes por disciplina
   *
   * @param int $disciplinaId ID da disciplina
   * @return int Número de tarefas pendentes
   */
  public function contarTarefasPendentes(int $disciplinaId): int
  {
    $sql = "SELECT COUNT(*) FROM tarefas
            WHERE disciplina_id = :disciplina_id AND concluida = 0";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':disciplina_id', $disciplinaId, \PDO::PARAM_INT);
    $stmt->execute();

    return (int) $stmt->fetchColumn();
  }

  /**
   * Busca estatísticas de uma disciplina
   *
   * @param int $disciplinaId ID da disciplina
   * @return array Estatísticas
   */
  public function buscarEstatisticas(int $disciplinaId): array
  {
    $sql = "SELECT
              COUNT(DISTINCT t.id) as total_tarefas,
              COUNT(DISTINCT CASE WHEN t.concluida = 1 THEN t.id END) as tarefas_concluidas,
              COUNT(DISTINCT sp.id) as total_pomodoros,
              SUM(CASE WHEN sp.tipo = 'foco' AND sp.concluida = 1 THEN sp.duracao_real ELSE 0 END) as minutos_foco
            FROM disciplinas d
            LEFT JOIN tarefas t ON t.disciplina_id = d.id
            LEFT JOIN sessoes_pomodoro sp ON sp.disciplina_id = d.id
            WHERE d.id = :disciplina_id
            GROUP BY d.id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':disciplina_id', $disciplinaId, \PDO::PARAM_INT);
    $stmt->execute();

    $stats = $stmt->fetch();
    return $stats ?: [
      'total_tarefas' => 0,
      'tarefas_concluidas' => 0,
      'total_pomodoros' => 0,
      'minutos_foco' => 0
    ];
  }
}
