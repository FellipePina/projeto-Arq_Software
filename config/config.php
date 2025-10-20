<?php

/**
 * Configurações do Sistema de Gerenciamento de Estudos
 *
 * Este arquivo contém todas as configurações principais do sistema.
 * Seguindo o princípio DRY (Don't Repeat Yourself), centralizamos
 * todas as configurações em um local único.
 */

// Configurações do banco de dados
define('DB_HOST', 'localhost');
// Porta do banco de dados (altere para 3307 se mudar no XAMPP)
define('DB_PORT', 3306);
define('DB_NAME', 'auxilio_estudos');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configurações da aplicação
define('APP_NAME', 'Auxílio Estudos');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost');

// Configurações de sessão
define('SESSION_NAME', 'auxilio_estudos_session');
define('SESSION_LIFETIME', 3600); // 1 hora em segundos

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
