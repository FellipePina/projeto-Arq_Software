<?php

namespace App\Controllers;

/**
 * Classe BaseController - Controlador base com funcionalidades comuns
 *
 * Princípios aplicados:
 * - Single Responsibility: funcionalidades comuns a todos controllers
 * - DRY (Don't Repeat Yourself): evita repetição de código
 * - Template Method Pattern: estrutura comum para controllers
 */
abstract class BaseController
{
  /**
   * Construtor - inicializa a sessão se necessário
   */
  public function __construct()
  {
    // Garante que a sessão está ativa
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }

  /**
   * Verifica se o usuário está logado
   *
   * @return bool True se logado, false se não
   */
  protected function isLoggedIn(): bool
  {
    return isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] > 0;
  }

  /**
   * Obtém o ID do usuário logado
   *
   * @return int|null ID do usuário ou null se não logado
   */
  protected function getLoggedUserId(): ?int
  {
    return $this->isLoggedIn() ? (int) $_SESSION['usuario_id'] : null;
  }

  /**
   * Obtém dados do usuário logado da sessão
   *
   * @return array|null Dados do usuário ou null se não logado
   */
  protected function getLoggedUser(): ?array
  {
    if (!$this->isLoggedIn()) {
      return null;
    }

    return [
      'id' => $_SESSION['usuario_id'],
      'nome' => $_SESSION['usuario_nome'] ?? '',
      'email' => $_SESSION['usuario_email'] ?? ''
    ];
  }

  /**
   * Redireciona para uma URL específica
   *
   * @param string $url URL para redirecionamento
   */
  protected function redirect(string $url): void
  {
    header("Location: $url");
    exit;
  }

  /**
   * Redireciona para login se usuário não estiver logado
   */
  protected function requireLogin(): void
  {
    if (!$this->isLoggedIn()) {
      $this->redirect('/login');
    }
  }

  /**
   * Renderiza uma view com dados
   *
   * @param string $view Nome da view (sem extensão)
   * @param array $data Dados para passar à view
   */
  protected function render(string $view, array $data = []): void
  {
    // Adiciona dados do usuário logado automaticamente
    if ($this->isLoggedIn()) {
      $data['usuario_logado'] = $this->getLoggedUser();
    }

    // Extrai as variáveis para a view
    extract($data);

    // Inclui o header
    $this->includeView('layouts/header', $data);

    // Inclui a view principal
    $this->includeView($view, $data);

    // Inclui o footer
    $this->includeView('layouts/footer', $data);
  }

  /**
   * Inclui um arquivo de view
   *
   * @param string $view Nome da view
   * @param array $data Dados para a view
   */
  private function includeView(string $view, array $data = []): void
  {
    $viewFile = __DIR__ . "/../Views/{$view}.php";

    if (file_exists($viewFile)) {
      // Extrai dados para uso na view
      extract($data);
      include $viewFile;
    } else {
      throw new \Exception("View não encontrada: $view");
    }
  }

  /**
   * Renderiza apenas uma view (sem layout)
   *
   * @param string $view Nome da view
   * @param array $data Dados para a view
   */
  protected function renderPartial(string $view, array $data = []): void
  {
    $this->includeView($view, $data);
  }

  /**
   * Define uma mensagem flash para a próxima requisição
   *
   * @param string $type Tipo da mensagem (success, error, warning, info)
   * @param string $message Mensagem
   */
  protected function setFlashMessage(string $type, string $message): void
  {
    $_SESSION['flash_messages'][] = [
      'type' => $type,
      'message' => $message
    ];
  }

  /**
   * Obtém e limpa as mensagens flash
   *
   * @return array Mensagens flash
   */
  protected function getFlashMessages(): array
  {
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
  }

  /**
   * Valida token CSRF
   *
   * @param string|null $token Token recebido
   * @return bool True se válido
   */
  protected function validateCsrfToken(?string $token): bool
  {
    $sessionToken = $_SESSION['csrf_token'] ?? null;
    return $token && $sessionToken && hash_equals($sessionToken, $token);
  }

  /**
   * Gera e armazena token CSRF na sessão
   *
   * @return string Token CSRF gerado
   */
  protected function generateCsrfToken(): string
  {
    // Reutiliza token existente se houver
    if (!empty($_SESSION['csrf_token'])) {
      return $_SESSION['csrf_token'];
    }

    // Gera novo token apenas se não existir
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
  }

  /**
   * Regenera token CSRF (após uso bem-sucedido)
   *
   * @return string Novo token gerado
   */
  protected function regenerateCsrfToken(): string
  {
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
  }

  /**
   * Sanitiza dados de entrada
   *
   * @param array $data Dados para sanitizar
   * @return array Dados sanitizados
   */
  protected function sanitizeInput(array $data): array
  {
    $sanitized = [];

    foreach ($data as $key => $value) {
      if (is_string($value)) {
        // Remove tags HTML e espaços extras
        $sanitized[$key] = trim(strip_tags($value));
      } elseif (is_array($value)) {
        $sanitized[$key] = $this->sanitizeInput($value);
      } else {
        $sanitized[$key] = $value;
      }
    }

    return $sanitized;
  }

  /**
   * Valida se a requisição é POST
   *
   * @return bool True se for POST
   */
  protected function isPost(): bool
  {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
  }

  /**
   * Obtém dados POST sanitizados
   *
   * @return array Dados POST sanitizados
   */
  protected function getPostData(): array
  {
    return $this->sanitizeInput($_POST);
  }

  /**
   * Retorna resposta JSON
   *
   * @param array $data Dados para retornar
   * @param int $httpCode Código HTTP
   */
  protected function jsonResponse(array $data, int $httpCode = 200): void
  {
    http_response_code($httpCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
  }
}
