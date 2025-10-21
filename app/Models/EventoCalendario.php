<?php

namespace App\Models;

/**
 * Classe EventoCalendario - Modelo para gerenciar eventos do calendário
 *
 * Princípios SOLID:
 * - Single Responsibility: gerencia apenas eventos
 */
class EventoCalendario extends BaseModel
{
  protected string $table = 'eventos_calendario';

  /**
   * Busca eventos por período
   *
   * @param int $usuarioId ID do usuário
   * @param string $dataInicio Data de início
   * @param string $dataFim Data de fim
   * @return array Lista de eventos
   */
  public function buscarPorPeriodo(int $usuarioId, string $dataInicio, string $dataFim): array
  {
    $sql = "SELECT e.*, d.nome as disciplina_nome, d.cor as disciplina_cor
            FROM {$this->table} e
            LEFT JOIN disciplinas d ON d.id = e.disciplina_id
            WHERE e.usuario_id = :usuario_id
            AND (
              (e.data_inicio BETWEEN :data_inicio AND :data_fim)
              OR (e.data_fim BETWEEN :data_inicio AND :data_fim)
              OR (e.data_inicio <= :data_inicio AND e.data_fim >= :data_fim)
            )
            ORDER BY e.data_inicio ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->bindValue(':data_inicio', $dataInicio);
    $stmt->bindValue(':data_fim', $dataFim);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Busca próximos eventos
   *
   * @param int $usuarioId ID do usuário
   * @param int $limite Número de eventos
   * @return array Próximos eventos
   */
  public function buscarProximos(int $usuarioId, int $limite = 5): array
  {
    $sql = "SELECT e.*, d.nome as disciplina_nome, d.cor as disciplina_cor
            FROM {$this->table} e
            LEFT JOIN disciplinas d ON d.id = e.disciplina_id
            WHERE e.usuario_id = :usuario_id
            AND e.data_inicio >= NOW()
            ORDER BY e.data_inicio ASC
            LIMIT :limite";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Cria um novo evento
   *
   * @param array $dados Dados do evento
   * @return int|false ID do evento ou false
   */
  public function criar(array $dados)
  {
    if (
      empty($dados['titulo']) || empty($dados['usuario_id'])
      || empty($dados['data_inicio'])
    ) {
      return false;
    }

    $evento = [
      'titulo' => trim($dados['titulo']),
      'descricao' => trim($dados['descricao'] ?? ''),
      'usuario_id' => (int) $dados['usuario_id'],
      'disciplina_id' => !empty($dados['disciplina_id']) ? (int) $dados['disciplina_id'] : null,
      'data_inicio' => $dados['data_inicio'],
      'data_fim' => $dados['data_fim'] ?? null,
      'tipo' => $dados['tipo'] ?? 'evento',
      'lembrete_minutos' => !empty($dados['lembrete_minutos']) ? (int) $dados['lembrete_minutos'] : null,
      'cor' => $dados['cor'] ?? null
    ];

    return $this->save($evento);
  }

  /**
   * Atualiza um evento
   *
   * @param int $id ID do evento
   * @param array $dados Novos dados
   * @param int $usuarioId ID do usuário
   * @return bool Sucesso
   */
  public function atualizar(int $id, array $dados, int $usuarioId): bool
  {
    $evento = $this->buscarPorIdEUsuario($id, $usuarioId);
    if (!$evento) {
      return false;
    }

    $dadosAtualizados = [
      'id' => $id,
      'titulo' => trim($dados['titulo']),
      'descricao' => trim($dados['descricao'] ?? ''),
      'disciplina_id' => !empty($dados['disciplina_id']) ? (int) $dados['disciplina_id'] : null,
      'data_inicio' => $dados['data_inicio'],
      'data_fim' => $dados['data_fim'] ?? null,
      'tipo' => $dados['tipo'] ?? $evento['tipo'],
      'lembrete_minutos' => !empty($dados['lembrete_minutos']) ? (int) $dados['lembrete_minutos'] : null,
      'cor' => $dados['cor'] ?? $evento['cor']
    ];

    return $this->save($dadosAtualizados) !== false;
  }

  /**
   * Busca evento por ID com validação de usuário
   *
   * @param int $id ID do evento
   * @param int $usuarioId ID do usuário
   * @return array|false Dados do evento
   */
  public function buscarPorIdEUsuario(int $id, int $usuarioId)
  {
    $sql = "SELECT e.*, d.nome as disciplina_nome, d.cor as disciplina_cor
            FROM {$this->table} e
            LEFT JOIN disciplinas d ON d.id = e.disciplina_id
            WHERE e.id = :id AND e.usuario_id = :usuario_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch();
  }

  /**
   * Exclui evento
   *
   * @param int $id ID do evento
   * @param int $usuarioId ID do usuário
   * @return bool Sucesso
   */
  public function excluir(int $id, int $usuarioId): bool
  {
    $sql = "DELETE FROM {$this->table}
            WHERE id = :id AND usuario_id = :usuario_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Busca eventos com lembrete próximo
   *
   * @param int $usuarioId ID do usuário
   * @return array Eventos com lembrete
   */
  public function buscarComLembreteProximo(int $usuarioId): array
  {
    $sql = "SELECT e.*, d.nome as disciplina_nome, d.cor as disciplina_cor
            FROM {$this->table} e
            LEFT JOIN disciplinas d ON d.id = e.disciplina_id
            WHERE e.usuario_id = :usuario_id
            AND e.lembrete_minutos IS NOT NULL
            AND TIMESTAMPDIFF(MINUTE, NOW(), e.data_inicio) <= e.lembrete_minutos
            AND TIMESTAMPDIFF(MINUTE, NOW(), e.data_inicio) > 0
            ORDER BY e.data_inicio ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
  }
}
