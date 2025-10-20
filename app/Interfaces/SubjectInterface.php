<?php

namespace App\Interfaces;

/**
 * Interface SubjectInterface
 *
 * PADRÃO GOF: OBSERVER (COMPORTAMENTAL)
 *
 * Esta interface define o contrato para objetos SUJEITOS
 * que podem ser observados por múltiplos OBSERVADORES.
 *
 * No padrão Observer:
 * - O Subject mantém uma lista de Observers interessados
 * - O Subject notifica todos os Observers quando algo importante acontece
 * - Permite adicionar/remover Observers dinamicamente
 *
 * Analogia: Como um canal do YouTube
 * - O canal (Subject) mantém uma lista de inscritos (Observers)
 * - Quando o canal posta um vídeo, notifica todos os inscritos
 * - Inscritos podem se inscrever ou cancelar inscrição a qualquer momento
 *
 * Exemplo no sistema:
 * - ConteudoEstudo implementa Subject
 * - Metas se inscrevem como Observers do ConteudoEstudo
 * - Quando ConteudoEstudo muda para 'CONCLUÍDO', notifica todas as Metas
 * - Cada Meta recalcula seu progresso automaticamente
 */
interface SubjectInterface
{
  /**
   * Adiciona um observador à lista de observadores
   *
   * @param ObserverInterface $observer Observador a ser adicionado
   * @return void
   */
  public function attach(ObserverInterface $observer): void;

  /**
   * Remove um observador da lista de observadores
   *
   * @param ObserverInterface $observer Observador a ser removido
   * @return void
   */
  public function detach(ObserverInterface $observer): void;

  /**
   * Notifica todos os observadores sobre uma mudança
   *
   * @param mixed $data Dados sobre a mudança ocorrida
   * @return void
   */
  public function notify($data = null): void;
}
