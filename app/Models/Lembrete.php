<?php

namespace App\Models;

/**
 * Modelo para a tabela de lembretes.
 */
class Lembrete extends BaseModel
{
  protected string $table = 'lembretes';

  /**
   * Busca todos os lembretes de um usuário.
   *
   * @param int $usuario_id
   * @return array
   */
  public function buscarPorUsuario(int $usuario_id): array
  {
    $sql = "SELECT l.*, c.titulo as conteudo_titulo
                FROM {$this->table} l
                LEFT JOIN conteudos_estudo c ON l.conteudo_id = c.id
                WHERE l.usuario_id = ? AND l.status = 'pendente'
                ORDER BY l.data_hora ASC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$usuario_id]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Salva ou atualiza um lembrete.
   */
  public function salvar(array $dados): bool
  {
    if (isset($dados['id']) && $dados['id']) {
      // Atualizar
      $stmt = $this->db->prepare(
        "UPDATE {$this->table} SET mensagem = ?, data_hora = ?, status = ?, conteudo_id = ? WHERE id = ? AND usuario_id = ?"
      );
      return $stmt->execute([
        $dados['mensagem'],
        $dados['data_hora'],
        $dados['status'],
        $dados['conteudo_id'],
        $dados['id'],
        $dados['usuario_id']
      ]);
    }

    // Inserir
    $stmt = $this->db->prepare(
      "INSERT INTO {$this->table} (mensagem, data_hora, status, conteudo_id, usuario_id) VALUES (?, ?, ?, ?, ?)"
    );
    return $stmt->execute([
      $dados['mensagem'],
      $dados['data_hora'],
      'pendente',
      $dados['conteudo_id'],
      $dados['usuario_id']
    ]);
  }

  /**
   * Marca um lembrete como enviado.
   *
   * @param int $id
   * @return bool
   */
  public function marcarComoEnviado(int $id): bool
  {
    $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'enviado' WHERE id = ?");
    return $stmt->execute([$id]);
  }
}
