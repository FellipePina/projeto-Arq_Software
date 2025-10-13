<?php

namespace App\Models;

/**
 * Classe Meta - Modelo para gerenciar metas de estudo
 *
 * Princípios aplicados:
 * - Single Responsibility: apenas operações de metas
 * - Open/Closed: aberta para extensão de novos tipos de metas
 */
class Meta extends BaseModel
{
  protected string $table = 'metas';

  // Status possíveis para metas
  public const STATUS_ATIVA = 'ativa';
  public const STATUS_CONCLUIDA = 'concluida';
  public const STATUS_CANCELADA = 'cancelada';

  /**
   * Cria uma nova meta
   *
   * @param array $dados Dados da meta
   * @return int|false ID da meta criada ou false se erro
   */
  public function criar(array $dados)
  {
    // Validações básicas
    if (empty($dados['titulo']) || empty($dados['usuario_id']) || empty($dados['data_alvo'])) {
      return false;
    }

    // Prepara dados para inserção
    $dadosMeta = [
      'titulo' => trim($dados['titulo']),
      'data_alvo' => $dados['data_alvo'],
      'status' => $dados['status'] ?? self::STATUS_ATIVA,
      'usuario_id' => (int) $dados['usuario_id'],
      'data_criacao' => date('Y-m-d H:i:s'),
      'percentual_progresso' => 0.0
    ];

    return $this->save($dadosMeta);
  }

  /**
   * Busca metas de um usuário específico
   *
   * @param int $usuarioId ID do usuário
   * @return array Lista de metas do usuário
   */
  public function buscarPorUsuario(int $usuarioId): array
  {
    $sql = "SELECT * FROM {$this->table}
                WHERE usuario_id = :usuario_id
                ORDER BY data_alvo ASC";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Busca metas ativas de um usuário
   *
   * @param int $usuarioId ID do usuário
   * @return array Lista de metas ativas
   */
  public function buscarAtivasPorUsuario(int $usuarioId): array
  {
    $sql = "SELECT * FROM {$this->table}
                WHERE usuario_id = :usuario_id AND status = :status
                ORDER BY data_alvo ASC";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->bindValue(':status', self::STATUS_ATIVA);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Adiciona um conteúdo a uma meta
   *
   * @param int $metaId ID da meta
   * @param int $conteudoId ID do conteúdo
   * @return bool True se adicionado com sucesso
   */
  public function adicionarConteudo(int $metaId, int $conteudoId): bool
  {
    // Verifica se o relacionamento já existe
    if ($this->conteudoJaVinculado($metaId, $conteudoId)) {
      return false;
    }

    $sql = "INSERT INTO metas_conteudos (meta_id, conteudo_id, concluido, data_conclusao)
                VALUES (:meta_id, :conteudo_id, 0, NULL)";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':meta_id', $metaId, \PDO::PARAM_INT);
    $stmt->bindValue(':conteudo_id', $conteudoId, \PDO::PARAM_INT);

    $resultado = $stmt->execute();

    // Recalcula o progresso da meta após adicionar conteúdo
    if ($resultado) {
      $this->calcularProgresso($metaId);
    }

    return $resultado;
  }

  /**
   * Verifica se um conteúdo já está vinculado a uma meta
   *
   * @param int $metaId ID da meta
   * @param int $conteudoId ID do conteúdo
   * @return bool True se já vinculado
   */
  private function conteudoJaVinculado(int $metaId, int $conteudoId): bool
  {
    $sql = "SELECT id FROM metas_conteudos
                WHERE meta_id = :meta_id AND conteudo_id = :conteudo_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':meta_id', $metaId, \PDO::PARAM_INT);
    $stmt->bindValue(':conteudo_id', $conteudoId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch() !== false;
  }

  /**
   * Calcula e atualiza o progresso de uma meta
   *
   * @param int $metaId ID da meta
   * @return float Percentual de progresso (0-100)
   */
  public function calcularProgresso(int $metaId): float
  {
    // Busca total de conteúdos e conteúdos concluídos
    $sql = "SELECT
                    COUNT(*) as total_conteudos,
                    SUM(CASE WHEN concluido = 1 THEN 1 ELSE 0 END) as conteudos_concluidos
                FROM metas_conteudos
                WHERE meta_id = :meta_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':meta_id', $metaId, \PDO::PARAM_INT);
    $stmt->execute();

    $resultado = $stmt->fetch();

    $totalConteudos = (int) $resultado['total_conteudos'];
    $conteudosConcluidos = (int) $resultado['conteudos_concluidos'];

    // Calcula percentual
    $percentual = $totalConteudos > 0 ? ($conteudosConcluidos / $totalConteudos) * 100 : 0;

    // Atualiza a meta com o novo percentual
    $this->atualizarProgresso($metaId, $percentual);

    // Se chegou a 100%, marca como concluída
    if ($percentual >= 100) {
      $this->marcarComoConcluida($metaId);
    }

    return round($percentual, 2);
  }

  /**
   * Atualiza o percentual de progresso da meta
   *
   * @param int $metaId ID da meta
   * @param float $percentual Novo percentual
   * @return bool True se atualizado com sucesso
   */
  private function atualizarProgresso(int $metaId, float $percentual): bool
  {
    $sql = "UPDATE {$this->table}
                SET percentual_progresso = :percentual
                WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':percentual', $percentual);
    $stmt->bindValue(':id', $metaId, \PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Marca uma meta como concluída
   *
   * @param int $metaId ID da meta
   * @return bool True se marcada com sucesso
   */
  public function marcarComoConcluida(int $metaId): bool
  {
    $sql = "UPDATE {$this->table}
                SET status = :status
                WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':status', self::STATUS_CONCLUIDA);
    $stmt->bindValue(':id', $metaId, \PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Busca conteúdos vinculados a uma meta
   *
   * @param int $metaId ID da meta
   * @return array Lista de conteúdos da meta
   */
  public function buscarConteudos(int $metaId): array
  {
    $sql = "SELECT ce.*, mc.concluido, mc.data_conclusao, c.nome as categoria_nome
                FROM metas_conteudos mc
                INNER JOIN conteudos_estudo ce ON mc.conteudo_id = ce.id
                LEFT JOIN categorias c ON ce.categoria_id = c.id
                WHERE mc.meta_id = :meta_id
                ORDER BY mc.concluido ASC, ce.titulo ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':meta_id', $metaId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Marca um conteúdo da meta como concluído
   *
   * @param int $metaId ID da meta
   * @param int $conteudoId ID do conteúdo
   * @return bool True se marcado com sucesso
   */
  public function marcarConteudoConcluido(int $metaId, int $conteudoId): bool
  {
    $sql = "UPDATE metas_conteudos
                SET concluido = 1, data_conclusao = :data_conclusao
                WHERE meta_id = :meta_id AND conteudo_id = :conteudo_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':data_conclusao', date('Y-m-d H:i:s'));
    $stmt->bindValue(':meta_id', $metaId, \PDO::PARAM_INT);
    $stmt->bindValue(':conteudo_id', $conteudoId, \PDO::PARAM_INT);

    $resultado = $stmt->execute();

    // Recalcula progresso após marcar conteúdo como concluído
    if ($resultado) {
      $this->calcularProgresso($metaId);
    }

    return $resultado;
  }
}
