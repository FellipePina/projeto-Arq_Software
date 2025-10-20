<?php
// Teste de conexão com o banco de dados

echo "=== Teste de Conexão com Banco de Dados ===" . PHP_EOL . PHP_EOL;

// Teste 1: Conexão com o servidor MySQL
echo "1. Testando conexão com MySQL..." . PHP_EOL;
try {
  $pdo = new PDO('mysql:host=localhost;port=3306', 'root', '');
  echo "   ✓ Conexão com MySQL OK!" . PHP_EOL . PHP_EOL;
} catch (PDOException $e) {
  echo "   ✗ ERRO na conexão: " . $e->getMessage() . PHP_EOL;
  exit(1);
}

// Teste 2: Listar bancos de dados
echo "2. Listando bancos de dados disponíveis:" . PHP_EOL;
try {
  $dbs = $pdo->query('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN);
  foreach ($dbs as $db) {
    echo "   - " . $db . PHP_EOL;
  }
  echo PHP_EOL;
} catch (PDOException $e) {
  echo "   ✗ ERRO ao listar bancos: " . $e->getMessage() . PHP_EOL;
}

// Teste 3: Verificar se o banco auxilio_estudos existe
echo "3. Verificando banco 'auxilio_estudos'..." . PHP_EOL;
if (in_array('auxilio_estudos', $dbs)) {
  echo "   ✓ Banco 'auxilio_estudos' encontrado!" . PHP_EOL . PHP_EOL;
} else {
  echo "   ✗ Banco 'auxilio_estudos' NÃO encontrado!" . PHP_EOL;
  echo "   Criando banco de dados..." . PHP_EOL;
  try {
    $pdo->exec("CREATE DATABASE auxilio_estudos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "   ✓ Banco criado com sucesso!" . PHP_EOL . PHP_EOL;
  } catch (PDOException $e) {
    echo "   ✗ ERRO ao criar banco: " . $e->getMessage() . PHP_EOL;
    exit(1);
  }
}

// Teste 4: Conectar ao banco auxilio_estudos
echo "4. Conectando ao banco 'auxilio_estudos'..." . PHP_EOL;
try {
  $pdo = new PDO('mysql:host=localhost;port=3306;dbname=auxilio_estudos;charset=utf8mb4', 'root', '');
  echo "   ✓ Conexão com banco 'auxilio_estudos' OK!" . PHP_EOL . PHP_EOL;
} catch (PDOException $e) {
  echo "   ✗ ERRO: " . $e->getMessage() . PHP_EOL;
  exit(1);
}

// Teste 5: Verificar tabelas
echo "5. Verificando tabelas existentes:" . PHP_EOL;
try {
  $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
  if (count($tables) > 0) {
    foreach ($tables as $table) {
      echo "   - " . $table . PHP_EOL;
    }
  } else {
    echo "   ⚠ Nenhuma tabela encontrada! Execute o arquivo database.sql" . PHP_EOL;
  }
  echo PHP_EOL;
} catch (PDOException $e) {
  echo "   ✗ ERRO: " . $e->getMessage() . PHP_EOL;
}

echo "=== Fim do Teste ===" . PHP_EOL;
