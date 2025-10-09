<?php

namespace App\Models;

/**
 * Modelo para a tabela de associação metas_conteudos.
 */
class MetaConteudo extends BaseModel
{
  protected string $table = 'metas_conteudos';

  /**
   * Adiciona um conteúdo a uma meta.
   *
   * @param int $metaId
   * @param int $conteudoId
   * @return bool
   */
  public function salvar(int $metaId, int $conteudoId): bool
  {
    $stmt = $this->db->prepare(
      "INSERT INTO {$this->table} (meta_id, conteudo_id) VALUES (?, ?)"
    );
    return $stmt->execute([$metaId, $conteudoId]);
  }

  /**
   * Marca um conteúdo de uma meta como concluído.
   *
   * @param int $metaConteudoId
   * @return bool
   */
  public function marcarConcluido(int $metaConteudoId): bool
  {
    $stmt = $this->db->prepare(
      "UPDATE {$this->table} SET concluido = TRUE, data_conclusao = NOW() WHERE id = ?"
    );
    return $stmt->execute([$metaConteudoId]);
  }

  /**
   * Busca todos os conteúdos associados a uma meta.
   *
   * @param int $metaId
   * @return array
   */
  public function buscarPorMeta(int $metaId): array
  {
    $sql = "SELECT mc.*, ce.titulo as conteudo_titulo
                FROM {$this->table} mc
                JOIN conteudos_estudo ce ON mc.conteudo_id = ce.id
                WHERE mc.meta_id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$metaId]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }
}
