<?php

namespace App\Config;

/**
 * Carrega as variáveis de ambiente do arquivo .env
 * Uso: EnvLoader::load();
 */
class EnvLoader
{
  /**
   * Carrega o arquivo .env e define as variáveis de ambiente
   *
   * @param string|null $path Caminho para o arquivo .env
   * @return void
   */
  public static function load(?string $path = null): void
  {
    if ($path === null) {
      $path = dirname(__DIR__, 2) . '/.env';
    }

    if (!file_exists($path)) {
      return; // Se não existir .env, usa valores padrão
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
      // Ignora comentários
      if (strpos(trim($line), '#') === 0) {
        continue;
      }

      // Parse linha KEY=VALUE
      if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        // Remove aspas se existirem
        $value = trim($value, '"\'');

        // Define a variável de ambiente
        if (!array_key_exists($key, $_ENV)) {
          $_ENV[$key] = $value;
          putenv("$key=$value");
        }
      }
    }
  }

  /**
   * Obtém uma variável de ambiente
   *
   * @param string $key Nome da variável
   * @param mixed $default Valor padrão se não existir
   * @return mixed
   */
  public static function get(string $key, $default = null)
  {
    return $_ENV[$key] ?? getenv($key) ?: $default;
  }
}
