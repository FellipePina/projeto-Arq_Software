<?php

/**
 * Autoloader simples seguindo PSR-4
 *
 * Este arquivo é responsável por carregar automaticamente as classes
 * do projeto, evitando a necessidade de usar require/include manual.
 *
 * Princípios aplicados:
 * - Single Responsibility: apenas carrega classes
 * - Clean Code: código limpo e fácil de entender
 */

spl_autoload_register(function ($className) {
  // Remove o namespace base se existir
  $className = str_replace('App\\', '', $className);

  // Converte namespace separators para directory separators
  $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);

  // Constrói o caminho completo para o arquivo
  $file = __DIR__ . '/../app/' . $className . '.php';

  // Verifica se o arquivo existe e o inclui
  if (file_exists($file)) {
    require_once $file;
    return true;
  }

  return false;
});
