<?php

namespace App\Interfaces;

/**
 * Interface ObserverInterface
 *
 * PADRÃO GOF: OBSERVER (COMPORTAMENTAL)
 *
 * Esta interface define o contrato para objetos OBSERVADORES
 * que desejam ser notificados sobre mudanças em um SUJEITO.
 *
 * No padrão Observer:
 * - O Observer é notificado quando algo importante acontece no Subject
 * - O Observer implementa o método update() para reagir às notificações
 * - Permite comunicação desacoplada entre objetos
 *
 * Analogia: Como um inscrito em um canal do YouTube
 * - O canal (Subject) notifica todos os inscritos (Observers)
 * - Cada inscrito (Observer) recebe a notificação e decide o que fazer
 *
 * Exemplo no sistema:
 * - Meta implementa Observer
 * - Meta é notificada quando um ConteudoEstudo muda de status
 * - Meta recalcula seu progresso automaticamente
 */
interface ObserverInterface
{
  /**
   * Método chamado quando o sujeito observado sofre uma mudança
   *
   * @param SubjectInterface $subject O sujeito que disparou a notificação
   * @param mixed $data Dados adicionais sobre a mudança ocorrida
   * @return void
   */
  public function update(SubjectInterface $subject, $data = null): void;
}
