<?php

namespace App\Patterns;

/**
 * Strategy Pattern - Interface para estratégias de notificação
 *
 * Permite diferentes tipos de notificações sem alterar código existente
 */
interface NotificationStrategyInterface
{
  /**
   * Envia notificação
   *
   * @param int $usuarioId ID do usuário
   * @param string $titulo Título da notificação
   * @param string $mensagem Mensagem
   * @return bool Sucesso
   */
  public function send(int $usuarioId, string $titulo, string $mensagem): bool;
}

/**
 * Estratégia de notificação por sessão (Flash Message)
 */
class SessionNotificationStrategy implements NotificationStrategyInterface
{
  public function send(int $usuarioId, string $titulo, string $mensagem): bool
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    $_SESSION['notifications'][] = [
      'titulo' => $titulo,
      'mensagem' => $mensagem,
      'tipo' => 'info',
      'data' => date('Y-m-d H:i:s')
    ];

    return true;
  }
}

/**
 * Estratégia de notificação por email (placeholder)
 */
class EmailNotificationStrategy implements NotificationStrategyInterface
{
  public function send(int $usuarioId, string $titulo, string $mensagem): bool
  {
    // TODO: Implementar envio de email real
    // Por enquanto, apenas simula o envio
    error_log("Email para usuário {$usuarioId}: {$titulo} - {$mensagem}");
    return true;
  }
}

/**
 * Estratégia de notificação push (placeholder)
 */
class PushNotificationStrategy implements NotificationStrategyInterface
{
  public function send(int $usuarioId, string $titulo, string $mensagem): bool
  {
    // TODO: Implementar notificação push real
    error_log("Push para usuário {$usuarioId}: {$titulo} - {$mensagem}");
    return true;
  }
}

/**
 * Context para gerenciar estratégias de notificação
 */
class NotificationContext
{
  private NotificationStrategyInterface $strategy;

  public function __construct(NotificationStrategyInterface $strategy)
  {
    $this->strategy = $strategy;
  }

  /**
   * Define nova estratégia
   */
  public function setStrategy(NotificationStrategyInterface $strategy): void
  {
    $this->strategy = $strategy;
  }

  /**
   * Executa envio com estratégia atual
   */
  public function notify(int $usuarioId, string $titulo, string $mensagem): bool
  {
    return $this->strategy->send($usuarioId, $titulo, $mensagem);
  }
}
