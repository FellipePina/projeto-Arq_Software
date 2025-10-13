<?php

namespace App\Models;

/**
 * Classe ConteudoEstudo - Modelo para gerenciar conteúdos de estudo
 *
 * Princípios aplicados:
 * - Single Responsibility: apenas operações de conteúdo de estudo
 * - Composition: usa Categoria para operações relacionadas
 */
class ConteudoEstudo extends BaseModel
{
  protected string $table = 'conteudos_estudo';

  // Status possíveis para conteúdo
  public const STATUS_PENDENTE = 'pendente';
  public const STATUS_EM_ANDAMENTO = 'em_andamento';
  public const STATUS_CONCLUIDO = 'concluido';

  /**
   * Busca conteúdos de um usuário específico
   *
   * @param int $usuarioId ID do usuário
   * @return array Lista de conteúdos com dados da categoria
   */
  public function buscarPorUsuario(int $usuarioId): array
  {
    $sql = "SELECT ce.*, c.nome as categoria_nome, c.cor as categoria_cor
                FROM {$this->table} ce
                LEFT JOIN categorias c ON ce.categoria_id = c.id
                WHERE ce.usuario_id = :usuario_id
                ORDER BY ce.data_atualizacao DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Busca conteúdos por status
   *
   * @param int $usuarioId ID do usuário
   * @param string $status Status para filtrar
   * @return array Lista de conteúdos
   */
  public function buscarPorStatus(int $usuarioId, string $status): array
  {
    $sql = "SELECT ce.*, c.nome as categoria_nome, c.cor as categoria_cor
                FROM {$this->table} ce
                LEFT JOIN categorias c ON ce.categoria_id = c.id
                WHERE ce.usuario_id = :usuario_id AND ce.status = :status
                ORDER BY ce.data_atualizacao DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->bindValue(':status', $status);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Cria um novo conteúdo de estudo
   *
   * @param array $dados Dados do conteúdo
   * @return int|false ID do conteúdo criado ou false se erro
   */
  public function criar(array $dados)
  {
    // Validações básicas
    if (empty($dados['titulo']) || empty($dados['usuario_id'])) {
      return false;
    }

    // Prepara dados para inserção
    $dadosConteudo = [
      'titulo' => trim($dados['titulo']),
      'descricao' => trim($dados['descricao'] ?? ''),
      'status' => $dados['status'] ?? self::STATUS_PENDENTE,
      'usuario_id' => (int) $dados['usuario_id'],
      'categoria_id' => !empty($dados['categoria_id']) ? (int) $dados['categoria_id'] : null,
      'data_criacao' => date('Y-m-d H:i:s'),
      'data_atualizacao' => date('Y-m-d H:i:s')
    ];

    return $this->save($dadosConteudo);
  }

  /**
   * Altera o status de um conteúdo
   *
   * @param int $id ID do conteúdo
   * @param string $novoStatus Novo status
   * @return bool True se alterado com sucesso
   */
  public function alterarStatus(int $id, string $novoStatus): bool
  {
    // Valida se o status é válido
    $statusValidos = [
      self::STATUS_PENDENTE,
      self::STATUS_EM_ANDAMENTO,
      self::STATUS_CONCLUIDO
    ];

    if (!in_array($novoStatus, $statusValidos)) {
      return false;
    }

    $sql = "UPDATE {$this->table}
                SET status = :status, data_atualizacao = :data_atualizacao
                WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':status', $novoStatus);
    $stmt->bindValue(':data_atualizacao', date('Y-m-d H:i:s'));
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Busca conteúdos por categoria
   *
   * @param int $categoriaId ID da categoria
   * @return array Lista de conteúdos
   */
  public function buscarPorCategoria(int $categoriaId): array
  {
    $sql = "SELECT * FROM {$this->table}
                WHERE categoria_id = :categoria_id
                ORDER BY data_atualizacao DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':categoria_id', $categoriaId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Valida os dados do conteúdo
   *
   * @param array $dados Dados para validar
   * @return array Array com erros encontrados (vazio se tudo ok)
   */
  public function validarDados(array $dados): array
  {
    $erros = [];

    // Título é obrigatório
    if (empty($dados['titulo'])) {
      $erros[] = 'Título é obrigatório';
    } elseif (strlen($dados['titulo']) > 255) {
      $erros[] = 'Título deve ter no máximo 255 caracteres';
    }

    // Validar categoria se informada
    if (!empty($dados['categoria_id'])) {
      $categoria = new Categoria();
      if (!$categoria->findById((int) $dados['categoria_id'])) {
        $erros[] = 'Categoria não encontrada';
      }
    }

    return $erros;
  }

  /**
   * Conta conteúdos por status do usuário
   *
   * @param int $usuarioId ID do usuário
   * @return array Array associativo com contagem por status
   */
  public function contarPorStatus(int $usuarioId): array
  {
    $sql = "SELECT status, COUNT(*) as total
                FROM {$this->table}
                WHERE usuario_id = :usuario_id
                GROUP BY status";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    $resultados = $stmt->fetchAll();
    $contadores = [];

    foreach ($resultados as $resultado) {
      $contadores[$resultado['status']] = (int) $resultado['total'];
    }

    return $contadores;
  }

  /**
   * Conta conteúdos sem categoria de um usuário
   *
   * @param int $usuarioId ID do usuário
   * @return int Quantidade de conteúdos sem categoria
   */
  public function contarSemCategoria(int $usuarioId): int
  {
    $sql = "SELECT COUNT(*) as total FROM {$this->table}
                WHERE usuario_id = :usuario_id AND categoria_id IS NULL";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetch();
    return (int) $result['total'];
  }
}
