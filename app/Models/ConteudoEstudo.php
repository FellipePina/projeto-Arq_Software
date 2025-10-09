<?php

namespace App\Models;

/**
 * Modelo para a tabela de conteudos_estudo.
 */
class ConteudoEstudo extends BaseModel
{
  protected string $table = 'conteudos_estudo';

  /**
   * Busca todos os conteúdos de um usuário.
   *
   * @param int $usuario_id
   * @return array
   */
  public function buscarPorUsuario(int $usuario_id): array
  {
    $sql = "SELECT c.*, cat.nome as categoria_nome
                FROM {$this->table} c
                LEFT JOIN categorias cat ON c.categoria_id = cat.id
                WHERE c.usuario_id = ?
                ORDER BY c.data_atualizacao DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$usuario_id]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Salva ou atualiza um conteúdo de estudo.
   */
  public function salvar(array $dados): bool
  {
    if (!$this->validarDados($dados)) {
      return false;
    }

    if (isset($dados['id']) && $dados['id']) {
      // Atualizar
      $stmt = $this->db->prepare(
        "UPDATE {$this->table} SET titulo = ?, descricao = ?, status = ?, categoria_id = ? WHERE id = ? AND usuario_id = ?"
      );
      return $stmt->execute([
        $dados['titulo'],
        $dados['descricao'],
        $dados['status'],
        $dados['categoria_id'],
        $dados['id'],
        $dados['usuario_id']
      ]);
    }

    // Inserir
    $stmt = $this->db->prepare(
      "INSERT INTO {$this->table} (titulo, descricao, status, categoria_id, usuario_id) VALUES (?, ?, ?, ?, ?)"
    );
    return $stmt->execute([
      $dados['titulo'],
      $dados['descricao'],
      $dados['status'],
      $dados['categoria_id'],
      $dados['usuario_id']
    ]);
  }

  /**
   * Altera o status de um conteúdo.
   *
   * @param int $id
   * @param string $novoStatus
   * @return bool
   */
  public function alterarStatus(int $id, string $novoStatus): bool
  {
    $stmt = $this->db->prepare("UPDATE {$this->table} SET status = ? WHERE id = ?");
    return $stmt->execute([$novoStatus, $id]);
  }

  /**
   * Valida os dados antes de salvar.
   *
   * @param array $dados
   * @return bool
   */
  public function validarDados(array $dados): bool
  {
    return !empty($dados['titulo']) && !empty($dados['status']);
  }
}
