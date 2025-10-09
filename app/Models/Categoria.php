<?php

namespace App\Models;

/**
 * Modelo para a tabela de categorias.
 */
class Categoria extends BaseModel
{
  protected string $table = 'categorias';

  /**
   * Busca todas as categorias de um usuário específico.
   *
   * @param int $usuario_id
   * @return array
   */
  public function buscarPorUsuario(int $usuario_id): array
  {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE usuario_id = ? ORDER BY nome");
    $stmt->execute([$usuario_id]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Salva ou atualiza uma categoria.
   *
   * @param array $dados
   * @return bool
   */
  public function salvar(array $dados): bool
  {
    if (isset($dados['id']) && $dados['id']) {
      // Atualizar
      $stmt = $this->db->prepare(
        "UPDATE {$this->table} SET nome = ?, descricao = ?, cor = ? WHERE id = ? AND usuario_id = ?"
      );
      return $stmt->execute([$dados['nome'], $dados['descricao'], $dados['cor'], $dados['id'], $dados['usuario_id']]);
    }

    // Inserir
    $stmt = $this->db->prepare(
      "INSERT INTO {$this->table} (nome, descricao, cor, usuario_id) VALUES (?, ?, ?, ?)"
    );
    return $stmt->execute([$dados['nome'], $dados['descricao'], $dados['cor'], $dados['usuario_id']]);
  }

  /**
   * Exclui uma categoria.
   *
   * @param int $id
   * @return bool
   */
  public function excluir(int $id): bool
  {
    return $this->delete($id);
  }
}
