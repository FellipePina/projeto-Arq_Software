<?php

namespace App\Models;

use App\Interfaces\SubjectInterface;
use App\Interfaces\ObserverInterface;

/**
 * Classe ConteudoEstudo - Modelo para gerenciar conteúdos de estudo
 *
 * PADRÃO GOF: OBSERVER (COMPORTAMENTAL) - SUBJECT
 *
 * Esta classe atua como SUJEITO (Subject) no padrão Observer.
 * Quando o status de um conteúdo muda para 'CONCLUÍDO', ela notifica
 * todos os observadores registrados (geralmente Metas).
 *
 * Funcionamento:
 * 1. Metas se registram como observadoras deste conteúdo
 * 2. Quando alterarStatus() muda o status para CONCLUÍDO
 * 3. O método notify() é chamado automaticamente
 * 4. Todas as Metas observadoras são notificadas
 * 5. Cada Meta recalcula seu progresso
 *
 * Benefícios do Observer:
 * - Desacoplamento: ConteudoEstudo não precisa conhecer Meta diretamente
 * - Flexibilidade: Múltiplas Metas podem observar o mesmo conteúdo
 * - Extensibilidade: Outros observadores podem ser adicionados facilmente
 *
 * Analogia: Canal do YouTube notificando inscritos sobre novo vídeo
 *
 * Princípios aplicados:
 * - Single Responsibility: apenas operações de conteúdo de estudo
 * - Composition: usa Categoria para operações relacionadas
 * - Open/Closed: aberto para novos observadores sem modificação
 */
class ConteudoEstudo extends BaseModel implements SubjectInterface
{
  protected string $table = 'conteudos_estudo';

  // Status possíveis para conteúdo
  public const STATUS_PENDENTE = 'pendente';
  public const STATUS_EM_ANDAMENTO = 'em_andamento';
  public const STATUS_CONCLUIDO = 'concluido';

  /**
   * Lista de observadores registrados (padrão Observer)
   * @var ObserverInterface[]
   */
  private array $observers = [];

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
   * PADRÃO OBSERVER EM AÇÃO:
   * Quando o status muda para CONCLUÍDO, notifica todos os observadores
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

    // Busca o status anterior
    $conteudoAtual = $this->findById($id);
    $statusAnterior = $conteudoAtual['status'] ?? null;

    $sql = "UPDATE {$this->table}
                SET status = :status, data_atualizacao = :data_atualizacao
                WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':status', $novoStatus);
    $stmt->bindValue(':data_atualizacao', date('Y-m-d H:i:s'));
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

    $resultado = $stmt->execute();

    // PADRÃO OBSERVER: Notifica observadores quando status muda para CONCLUÍDO
    if ($resultado && $novoStatus === self::STATUS_CONCLUIDO && $statusAnterior !== self::STATUS_CONCLUIDO) {
      // Carrega os observadores (Metas que incluem este conteúdo)
      $this->carregarObservadores($id);

      // Notifica todos os observadores sobre a conclusão
      $this->notify([
        'conteudo_id' => $id,
        'novo_status' => $novoStatus,
        'status_anterior' => $statusAnterior,
        'evento' => 'conteudo_concluido'
      ]);
    }

    return $resultado;
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

  // ========================================================================
  // IMPLEMENTAÇÃO DO PADRÃO OBSERVER - MÉTODOS DE SUBJECT
  // ========================================================================

  /**
   * Adiciona um observador à lista (padrão Observer)
   *
   * Permite que objetos (como Meta) se registrem para serem
   * notificados quando este conteúdo mudar de status.
   *
   * @param ObserverInterface $observer Observador a ser registrado
   * @return void
   */
  public function attach(ObserverInterface $observer): void
  {
    // Evita adicionar o mesmo observador múltiplas vezes
    if (!in_array($observer, $this->observers, true)) {
      $this->observers[] = $observer;
    }
  }

  /**
   * Remove um observador da lista (padrão Observer)
   *
   * @param ObserverInterface $observer Observador a ser removido
   * @return void
   */
  public function detach(ObserverInterface $observer): void
  {
    $key = array_search($observer, $this->observers, true);
    if ($key !== false) {
      unset($this->observers[$key]);
    }
  }

  /**
   * Notifica todos os observadores sobre uma mudança (padrão Observer)
   *
   * Este método é chamado quando o status do conteúdo muda.
   * Ele informa a todos os observadores registrados (Metas) sobre a mudança.
   *
   * @param mixed $data Dados sobre a mudança (ex: novo status)
   * @return void
   */
  public function notify($data = null): void
  {
    foreach ($this->observers as $observer) {
      $observer->update($this, $data);
    }
  }

  /**
   * Carrega os observadores deste conteúdo
   *
   * Este método busca todas as Metas que incluem este conteúdo
   * e as registra como observadoras através do MetaObserver.
   *
   * @param int $conteudoId ID do conteúdo
   * @return void
   */
  public function carregarObservadores(int $conteudoId): void
  {
    // Busca todas as metas ativas que incluem este conteúdo
    // e ainda não marcaram este conteúdo como concluído
    $sql = "SELECT DISTINCT m.id
                FROM metas m
                INNER JOIN metas_conteudos mc ON m.id = mc.meta_id
                WHERE mc.conteudo_id = :conteudo_id
                AND m.status = 'ativa'
                AND mc.concluido = 0";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':conteudo_id', $conteudoId, \PDO::PARAM_INT);
    $stmt->execute();

    $metasIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

    // Registra cada Meta como observadora usando MetaObserver
    foreach ($metasIds as $metaId) {
      $observer = new MetaObserver((int) $metaId);
      $this->attach($observer);
    }
  }
}
