<?php

namespace App\Models;

/**
 * Classe Categoria - Modelo para gerenciar categorias de conteúdo
 *
 * Princípios aplicados:
 * - Single Responsibility: apenas operações de categorias
 * - Encapsulation: métodos específicos para operações de categoria
 */
class Categoria extends BaseModel
{
  protected string $table = 'categorias';

  /**
   * Busca categorias de um usuário específico
   *
   * @param int $usuarioId ID do usuário
   * @return array Lista de categorias do usuário
   */
  public function buscarPorUsuario(int $usuarioId): array
  {
    $sql = "SELECT * FROM {$this->table}
                WHERE usuario_id = :usuario_id
                ORDER BY nome ASC";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Cria uma nova categoria
   *
   * @param array $dados Dados da categoria
   * @return int|false ID da categoria criada ou false se erro
   */
  public function criar(array $dados)
  {
    // Validações básicas
    if (empty($dados['nome']) || empty($dados['usuario_id'])) {
      return false;
    }

    // Verifica se já existe categoria com esse nome para o usuário
    if ($this->nomeExisteParaUsuario($dados['nome'], $dados['usuario_id'])) {
      return false;
    }

    // Prepara dados para inserção
    $dadosCategoria = [
      'nome' => trim($dados['nome']),
      'descricao' => trim($dados['descricao'] ?? ''),
      'cor' => $dados['cor'] ?? '#007bff',
      'usuario_id' => (int) $dados['usuario_id']
    ];

    return $this->save($dadosCategoria);
  }

  /**
   * Verifica se uma categoria com o nome já existe para o usuário
   *
   * @param string $nome Nome da categoria
   * @param int $usuarioId ID do usuário
   * @param int|null $excluirId ID para excluir da verificação
   * @return bool True se existe, false se não
   */
  public function nomeExisteParaUsuario(string $nome, int $usuarioId, ?int $excluirId = null): bool
  {
    $sql = "SELECT id FROM {$this->table}
                WHERE nome = :nome AND usuario_id = :usuario_id";

    if ($excluirId) {
      $sql .= " AND id != :excluir_id";
    }

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':nome', $nome);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);

    if ($excluirId) {
      $stmt->bindValue(':excluir_id', $excluirId, \PDO::PARAM_INT);
    }

    $stmt->execute();

    return $stmt->fetch() !== false;
  }

  /**
   * Conta quantos conteúdos uma categoria possui
   *
   * @param int $categoriaId ID da categoria
   * @return int Quantidade de conteúdos
   */
  public function contarConteudos(int $categoriaId): int
  {
    $sql = "SELECT COUNT(*) as total FROM conteudos_estudo
                WHERE categoria_id = :categoria_id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':categoria_id', $categoriaId, \PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetch();
    return (int) $result['total'];
  }

  /**
   * Exclui categoria (apenas se não tiver conteúdos vinculados)
   *
   * @param int $id ID da categoria
   * @return bool True se excluído com sucesso, false se erro
   */
  public function excluir(int $id): bool
  {
    // Verifica se tem conteúdos vinculados
    if ($this->contarConteudos($id) > 0) {
      return false;
    }

    return $this->delete($id);
  }
}
