<?php

namespace App\Models;

/**
 * Modelo para a tabela de metas.
 */
class Meta extends BaseModel
{
  protected string $table = 'metas';

  /**
   * Busca todas as metas de um usuário.
   *
   * @param int $usuario_id
   * @return array
   */
  public function buscarPorUsuario(int $usuario_id): array
  {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE usuario_id = ? ORDER BY data_alvo DESC");
    $stmt->execute([$usuario_id]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Salva ou atualiza uma meta.
   */
  public function salvar(array $dados): bool
  {
    if (isset($dados['id']) && $dados['id']) {
      // Atualizar
      $stmt = $this->db->prepare(
        "UPDATE {$this->table} SET titulo = ?, data_alvo = ?, status = ? WHERE id = ? AND usuario_id = ?"
      );
      return $stmt->execute([
        $dados['titulo'],
        $dados['data_alvo'],
        $dados['status'],
        $dados['id'],
        $dados['usuario_id']
      ]);
    }

    // Inserir
    $stmt = $this->db->prepare(
      "INSERT INTO {$this->table} (titulo, data_alvo, status, usuario_id) VALUES (?, ?, ?, ?)"
    );
    return $stmt->execute([
      $dados['titulo'],
      $dados['data_alvo'],
      $dados['status'],
      $dados['usuario_id']
    ]);
  }

  /**
   * Recalcula o progresso de uma meta.
   *
   * @param int $metaId
   * @return float
   */
  public function calcularProgresso(int $metaId): float
  {
    $metaConteudoModel = new MetaConteudo();
    $conteudos = $metaConteudoModel->buscarPorMeta($metaId);

    if (empty($conteudos)) {
      return 0.0;
    }

    $concluidos = 0;
    foreach ($conteudos as $item) {
      if ($item['concluido']) {
        $concluidos++;
      }
    }

    $progresso = ($concluidos / count($conteudos)) * 100;

    // Atualiza o progresso no banco
    $stmt = $this->db->prepare("UPDATE {$this->table} SET percentual_progresso = ? WHERE id = ?");
    $stmt->execute([$progresso, $metaId]);

    return $progresso;
  }
}
