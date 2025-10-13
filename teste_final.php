<?php
echo "=== TESTE FINAL DO CSRF ===\n";

session_start();

// Simular geração do token
function generateCsrfToken()
{
  if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf_token'];
}

// Limpar sessão para teste
session_destroy();
session_start();

echo "1. Primeira chamada - gerar token:\n";
$token1 = generateCsrfToken();
echo "Token 1: " . $token1 . "\n";
echo "Session: " . json_encode($_SESSION) . "\n";

echo "\n2. Segunda chamada - deve retornar o mesmo token:\n";
$token2 = generateCsrfToken();
echo "Token 2: " . $token2 . "\n";
echo "São iguais? " . ($token1 === $token2 ? "SIM" : "NÃO") . "\n";

echo "\n3. Validação do token:\n";
function validarCsrfToken($token)
{
  return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

$valido = validarCsrfToken($token1);
echo "Token válido? " . ($valido ? "SIM" : "NÃO") . "\n";

echo "\n=== TESTE COMPLETO ===\n";
