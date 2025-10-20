<?php

namespace App\Models;

use App\Interfaces\ObserverInterface;
use App\Interfaces\SubjectInterface;

/**
 * Classe MetaObserver - Wrapper para Meta implementar o padrão Observer
 *
 * PADRÃO GOF: OBSERVER (COMPORTAMENTAL) + ADAPTER
 *
 * Esta classe resolve o conflito de nomes entre:
 * - ObserverInterface::update() (padrão Observer)
 * - BaseModel::update() (operação de banco de dados)
 *
 * Atua como um adaptador que permite Meta participar do padrão Observer
 * sem conflitos com métodos existentes.
 *
 * Funcionamento:
 * 1. MetaObserver é registrado como observador do ConteudoEstudo
 * 2. Quando ConteudoEstudo notifica mudanças
 * 3. MetaObserver recebe a notificação no método update()
 * 4. MetaObserver delega o trabalho para a Meta correspondente
 */
class MetaObserver implements ObserverInterface
{
  /**
   * Instância da Meta que será notificada
   */
  private Meta $meta;

  /**
   * ID da meta para identificação
   */
  private int $metaId;

  /**
   * Construtor
   *
   * @param int $metaId ID da meta que será observadora
   */
  public function __construct(int $metaId)
  {
    $this->metaId = $metaId;
    $this->meta = new Meta();
  }

  /**
   * Método chamado quando um ConteudoEstudo muda (padrão Observer)
   *
   * @param SubjectInterface $subject O ConteudoEstudo que mudou
   * @param mixed $data Dados sobre a mudança
   * @return void
   */
  public function update(SubjectInterface $subject, $data = null): void
  {
    // Extrai informações da notificação
    $conteudoId = $data['conteudo_id'] ?? null;
    $evento = $data['evento'] ?? null;

    // Verifica se o evento é a conclusão de um conteúdo
    if ($evento !== 'conteudo_concluido' || !$conteudoId) {
      return;
    }

    // Verifica se este conteúdo pertence a esta meta
    if (!$this->conteudoPertenceAMeta($conteudoId)) {
      return;
    }

    // Marca o conteúdo como concluído na meta
    $this->meta->marcarConteudoConcluido($this->metaId, $conteudoId);

    // Log da atualização
    error_log(sprintf(
      "[OBSERVER] Meta #%d notificada: Conteúdo #%d concluído. Progresso recalculado.",
      $this->metaId,
      $conteudoId
    ));
  }

  /**
   * Verifica se um conteúdo pertence a esta meta
   *
   * @param int $conteudoId ID do conteúdo
   * @return bool True se o conteúdo pertence à meta
   */
  private function conteudoPertenceAMeta(int $conteudoId): bool
  {
    $db = Database::getInstance()->getConnection();

    $sql = "SELECT id FROM metas_conteudos
                WHERE meta_id = :meta_id
                AND conteudo_id = :conteudo_id
                AND concluido = 0";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':meta_id', $this->metaId, \PDO::PARAM_INT);
    $stmt->bindValue(':conteudo_id', $conteudoId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch() !== false;
  }

  /**
   * Obtém o ID da meta
   *
   * @return int
   */
  public function getMetaId(): int
  {
    return $this->metaId;
  }
}
