<?php

/**
 * Configurações do Sistema de Gerenciamento de Estudos
 *
 * Este arquivo contém todas as configurações principais do sistema.
 * Seguindo o princípio DRY (Don't Repeat Yourself), centralizamos
 * todas as configurações em um local único.
 */

// Carrega as variáveis de ambiente do arquivo .env
require_once __DIR__ . '/EnvLoader.php';

use App\Config\EnvLoader;

EnvLoader::load();

// Configurações do banco de dados (usa .env ou valores padrão)
define('DB_HOST', EnvLoader::get('DB_HOST', 'localhost'));
define('DB_PORT', (int) EnvLoader::get('DB_PORT', 3306));
define('DB_NAME', EnvLoader::get('DB_NAME', 'sistema_estudos'));
define('DB_USER', EnvLoader::get('DB_USER', 'root'));
define('DB_PASS', EnvLoader::get('DB_PASSWORD', ''));
define('DB_CHARSET', 'utf8mb4');

// Configurações da aplicação
define('APP_NAME', 'Sistema de Gerenciamento de Estudos');
define('APP_VERSION', '2.0.0');
define('APP_URL', EnvLoader::get('APP_URL', 'http://localhost:8000'));
define('APP_ENV', EnvLoader::get('APP_ENV', 'development'));
define('APP_DEBUG', EnvLoader::get('APP_DEBUG', 'true') === 'true');

// Configurações de sessão
define('SESSION_NAME', EnvLoader::get('SESSION_NAME', 'sistema_estudos_session'));
define('SESSION_LIFETIME', (int) EnvLoader::get('SESSION_LIFETIME', 7200)); // 2 horas padrão

// Configurações de segurança
define('BCRYPT_COST', 12); // Custo do hash de senha

// Configurações de timezone
date_default_timezone_set('America/Sao_Paulo');

// Configuração de erro (para desenvolvimento)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicia a sessão
if (session_status() === PHP_SESSION_NONE) {
  session_name(SESSION_NAME);
  session_start();
}
