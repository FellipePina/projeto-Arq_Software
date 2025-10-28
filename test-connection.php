<?php

/**
 * Script de Teste de Conexão com Banco de Dados
 *
 * Execute este arquivo para verificar se sua configuração está correta:
 * php test-connection.php
 */

echo "\n========================================\n";
echo "   TESTE DE CONEXÃO - Sistema de Estudos\n";
echo "========================================\n\n";

// Carrega as configurações
require_once __DIR__ . '/config/config.php';

echo "✓ Arquivo de configuração carregado\n";

// Testa valores das constantes
echo "\n--- CONFIGURAÇÕES ---\n";
echo "Host: " . DB_HOST . "\n";
echo "Porta: " . DB_PORT . "\n";
echo "Banco: " . DB_NAME . "\n";
echo "Usuário: " . DB_USER . "\n";
echo "Senha: " . (empty(DB_PASS) ? "(vazia)" : "****") . "\n";

// Verifica extensões PHP necessárias
echo "\n--- EXTENSÕES PHP ---\n";

$extensions = ['mysqli', 'pdo', 'pdo_mysql', 'mbstring', 'json', 'session'];
$missing = [];

foreach ($extensions as $ext) {
  if (extension_loaded($ext)) {
    echo "✓ $ext: OK\n";
  } else {
    echo "✗ $ext: FALTANDO\n";
    $missing[] = $ext;
  }
}

if (!empty($missing)) {
  echo "\n⚠ ATENÇÃO: Extensões faltando: " . implode(', ', $missing) . "\n";
  echo "Por favor, instale as extensões necessárias no PHP\n";
  exit(1);
}

// Testa conexão com o banco
echo "\n--- TESTE DE CONEXÃO ---\n";

try {
  $dsn = sprintf(
    'mysql:host=%s;port=%d;charset=%s',
    DB_HOST,
    DB_PORT,
    DB_CHARSET
  );

  echo "Tentando conectar ao MySQL...\n";
  $pdo = new PDO($dsn, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]);
  echo "✓ Conexão com MySQL estabelecida\n";

  // Testa se o banco de dados existe
  echo "Verificando banco de dados '$DB_NAME'...\n";
  $stmt = $pdo->query("SHOW DATABASES LIKE '" . DB_NAME . "'");

  if ($stmt->rowCount() > 0) {
    echo "✓ Banco de dados '$DB_NAME' encontrado\n";

    // Conecta ao banco específico
    $pdo->exec("USE " . DB_NAME);

    // Lista tabelas
    echo "Verificando tabelas...\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (count($tables) > 0) {
      echo "✓ " . count($tables) . " tabelas encontradas:\n";
      foreach ($tables as $table) {
        echo "  - $table\n";
      }
    } else {
      echo "⚠ ATENÇÃO: Banco existe mas está vazio!\n";
      echo "Execute: mysql -u root -p " . DB_NAME . " < database_expandido.sql\n";
    }
  } else {
    echo "✗ Banco de dados '$DB_NAME' NÃO encontrado\n";
    echo "\nPara criar o banco, execute:\n";
    echo "  mysql -u root -p\n";
    echo "  CREATE DATABASE " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
    echo "  EXIT;\n";
    echo "  mysql -u root -p " . DB_NAME . " < database_expandido.sql\n";
    exit(1);
  }

  echo "\n========================================\n";
  echo "   ✓ TODOS OS TESTES PASSARAM!\n";
  echo "========================================\n";
  echo "\nVocê pode iniciar o servidor com:\n";
  echo "  php -S localhost:8000 -t public\n\n";
} catch (PDOException $e) {
  echo "✗ ERRO DE CONEXÃO: " . $e->getMessage() . "\n\n";

  echo "--- SOLUÇÕES COMUNS ---\n";

  if (strpos($e->getMessage(), 'Access denied') !== false) {
    echo "1. Verifique o usuário e senha no arquivo .env\n";
    echo "2. Para XAMPP/WAMP, a senha geralmente é vazia\n";
    echo "3. Teste manualmente: mysql -u " . DB_USER . " -p\n";
  } elseif (strpos($e->getMessage(), 'Connection refused') !== false) {
    echo "1. Certifique-se de que o MySQL está rodando\n";
    echo "2. No XAMPP, inicie o módulo MySQL\n";
    echo "3. Verifique se a porta " . DB_PORT . " está correta\n";
  } else {
    echo "1. Verifique se o MySQL está instalado e rodando\n";
    echo "2. Confirme as configurações no arquivo .env\n";
    echo "3. Teste: php -m | findstr pdo\n";
  }

  exit(1);
}
