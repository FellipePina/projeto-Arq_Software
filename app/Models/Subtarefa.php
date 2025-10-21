<?php

namespace App\Models;

/**
 * Classe Subtarefa - Modelo para gerenciar subtarefas (checklist)
 *
 * Princípios SOLID:
 * - Single Responsibility: gerencia apenas subtarefas
 */
class Subtarefa extends BaseModel
{
  protected string $table = 'subtarefas';

  /**
   * Busca subtarefas de uma tarefa
   *
   * @param int $tarefaId ID da tarefa
   * @return array Lista de subtarefas
   */
  public function buscarPorTarefa(int $tarefaId): array
  {
    $sql = "SELECT * FROM {$this->table}
            WHERE tarefa_id = :tarefa_id
            ORDER BY ordem ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':tarefa_id', $tarefaId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Cria uma nova subtarefa
   *
   * @param array $dados Dados da subtarefa
   * @return int|false ID da subtarefa ou false
   */
  public function criar(array $dados)
  {
    if (empty($dados['titulo']) || empty($dados['tarefa_id'])) {
      return false;
    }

    // Busca próxima ordem
    $ordem = $this->buscarProximaOrdem($dados['tarefa_id']);

    $subtarefa = [
      'tarefa_id' => (int) $dados['tarefa_id'],
      'titulo' => trim($dados['titulo']),
      'ordem' => $ordem
    ];

    return $this->save($subtarefa);
  }

  /**
   * Busca próxima ordem para subtarefa
   *
   * @param int $tarefaId ID da tarefa
   * @return int Próxima ordem
   */
  private function buscarProximaOrdem(int $tarefaId): int
  {
    $sql = "SELECT MAX(ordem) as max_ordem FROM {$this->table}
            WHERE tarefa_id = :tarefa_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':tarefa_id', $tarefaId, \PDO::PARAM_INT);
    $stmt->execute();

    $resultado = $stmt->fetch();
    return ($resultado['max_ordem'] ?? 0) + 1;
  }

  /**
   * Marca subtarefa como concluída
   *
   * @param int $id ID da subtarefa
   * @return bool Sucesso
   */
  public function marcarConcluida(int $id): bool
  {
    $sql = "UPDATE {$this->table}
            SET concluida = 1
            WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Marca subtarefa como não concluída
   *
   * @param int $id ID da subtarefa
   * @return bool Sucesso
   */
  public function marcarPendente(int $id): bool
  {
    $sql = "UPDATE {$this->table}
            SET concluida = 0
            WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Reordena subtarefas
   *
   * @param array $ordens Array associativo [id => ordem]
   * @return bool Sucesso
   */
  public function reordenar(array $ordens): bool
  {
    $this->db->beginTransaction();

    try {
      $sql = "UPDATE {$this->table} SET ordem = :ordem WHERE id = :id";
      $stmt = $this->db->prepare($sql);

      foreach ($ordens as $id => $ordem) {
        $stmt->bindValue(':id', (int) $id, \PDO::PARAM_INT);
        $stmt->bindValue(':ordem', (int) $ordem, \PDO::PARAM_INT);
        $stmt->execute();
      }

      $this->db->commit();
      return true;
    } catch (\PDOException $e) {
      $this->db->rollBack();
      return false;
    }
  }

  /**
   * Exclui subtarefa
   *
   * @param int $id ID da subtarefa
   * @return bool Sucesso
   */
  public function excluir(int $id): bool
  {
    return $this->delete($id);
  }

  /**
   * Calcula progresso das subtarefas de uma tarefa
   *
   * @param int $tarefaId ID da tarefa
   * @return array Progresso [total, concluidas, percentual]
   */
  public function calcularProgresso(int $tarefaId): array
  {
    $sql = "SELECT
              COUNT(*) as total,
              SUM(concluida) as concluidas
            FROM {$this->table}
            WHERE tarefa_id = :tarefa_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':tarefa_id', $tarefaId, \PDO::PARAM_INT);
    $stmt->execute();

    $resultado = $stmt->fetch();
    $total = (int) $resultado['total'];
    $concluidas = (int) $resultado['concluidas'];

    return [
      'total' => $total,
      'concluidas' => $concluidas,
      'percentual' => $total > 0 ? round(($concluidas / $total) * 100, 1) : 0
    ];
  }
}
