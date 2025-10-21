<?php

namespace App\Models;

/**
 * Classe Anotacao - Modelo para gerenciar anotações
 *
 * Princípios SOLID:
 * - Single Responsibility: gerencia apenas anotações
 */
class Anotacao extends BaseModel
{
  protected string $table = 'anotacoes';

  /**
   * Busca anotações do usuário
   *
   * @param int $usuarioId ID do usuário
   * @param array $filtros Filtros opcionais
   * @return array Lista de anotações
   */
  public function buscarPorUsuario(int $usuarioId, array $filtros = []): array
  {
    $sql = "SELECT a.*, d.nome as disciplina_nome, d.cor as disciplina_cor
            FROM {$this->table} a
            LEFT JOIN disciplinas d ON d.id = a.disciplina_id
            WHERE a.usuario_id = :usuario_id";

    $params = [':usuario_id' => $usuarioId];

    // Filtro por disciplina
    if (!empty($filtros['disciplina_id'])) {
      $sql .= " AND a.disciplina_id = :disciplina_id";
      $params[':disciplina_id'] = $filtros['disciplina_id'];
    }

    // Filtro por fixadas
    if (isset($filtros['fixada'])) {
      $sql .= " AND a.fixada = :fixada";
      $params[':fixada'] = (int) $filtros['fixada'];
    }

    // Busca por texto
    if (!empty($filtros['busca'])) {
      $sql .= " AND (a.titulo LIKE :busca OR a.conteudo LIKE :busca)";
      $params[':busca'] = '%' . $filtros['busca'] . '%';
    }

    $sql .= " ORDER BY a.fixada DESC, a.data_atualizacao DESC";

    $stmt = $this->db->prepare($sql);
    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value);
    }
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Busca anotação por ID com validação de usuário
   *
   * @param int $id ID da anotação
   * @param int $usuarioId ID do usuário
   * @return array|false Dados da anotação
   */
  public function buscarPorIdEUsuario(int $id, int $usuarioId)
  {
    $sql = "SELECT a.*, d.nome as disciplina_nome, d.cor as disciplina_cor
            FROM {$this->table} a
            LEFT JOIN disciplinas d ON d.id = a.disciplina_id
            WHERE a.id = :id AND a.usuario_id = :usuario_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch();
  }

  /**
   * Cria uma nova anotação
   *
   * @param array $dados Dados da anotação
   * @return int|false ID da anotação ou false
   */
  public function criar(array $dados)
  {
    if (empty($dados['titulo']) || empty($dados['usuario_id'])) {
      return false;
    }

    $anotacao = [
      'titulo' => trim($dados['titulo']),
      'conteudo' => trim($dados['conteudo'] ?? ''),
      'usuario_id' => (int) $dados['usuario_id'],
      'disciplina_id' => !empty($dados['disciplina_id']) ? (int) $dados['disciplina_id'] : null
    ];

    return $this->save($anotacao);
  }

  /**
   * Atualiza uma anotação
   *
   * @param int $id ID da anotação
   * @param array $dados Novos dados
   * @param int $usuarioId ID do usuário
   * @return bool Sucesso
   */
  public function atualizar(int $id, array $dados, int $usuarioId): bool
  {
    $anotacao = $this->buscarPorIdEUsuario($id, $usuarioId);
    if (!$anotacao) {
      return false;
    }

    $dadosAtualizados = [
      'id' => $id,
      'titulo' => trim($dados['titulo']),
      'conteudo' => trim($dados['conteudo'] ?? ''),
      'disciplina_id' => !empty($dados['disciplina_id']) ? (int) $dados['disciplina_id'] : null,
      'data_atualizacao' => date('Y-m-d H:i:s')
    ];

    return $this->save($dadosAtualizados) !== false;
  }

  /**
   * Marca/desmarca anotação como fixada
   *
   * @param int $id ID da anotação
   * @param int $usuarioId ID do usuário
   * @param bool $fixada Se deve marcar ou desmarcar
   * @return bool Sucesso
   */
  public function alternarFixada(int $id, int $usuarioId, bool $fixada = true): bool
  {
    $sql = "UPDATE {$this->table}
            SET fixada = :fixada
            WHERE id = :id AND usuario_id = :usuario_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':fixada', (int) $fixada, \PDO::PARAM_INT);
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Exclui anotação
   *
   * @param int $id ID da anotação
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
   * Busca anotações fixadas
   *
   * @param int $usuarioId ID do usuário
   * @return array Anotações fixadas
   */
  public function buscarFixadas(int $usuarioId): array
  {
    return $this->buscarPorUsuario($usuarioId, ['fixada' => 1]);
  }
}
