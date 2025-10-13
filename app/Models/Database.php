<?php

namespace App\Models;

use PDO;
use PDOException;

/**
 * Classe Database - Responsável pela conexão com o banco de dados
 *
 * Princípios aplicados:
 * - Single Responsibility: apenas gerencia conexão com BD
 * - Singleton Pattern: uma única instância de conexão
 * - Dependency Injection: facilita testes e manutenção
 */
class Database
{
  private static ?PDO $connection = null;

  /**
   * Obtém a conexão com o banco de dados
   *
   * @return PDO Instância da conexão PDO
   * @throws PDOException Se não conseguir conectar
   */
  public static function getConnection(): PDO
  {
    // Se já existe conexão, retorna ela (Singleton)
    if (self::$connection !== null) {
      return self::$connection;
    }

    try {
      // Constrói a DSN (Data Source Name)
      $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_NAME,
        DB_CHARSET
      );

      // Opções do PDO para melhor segurança e performance
      $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
      ];

      // Cria a conexão
      self::$connection = new PDO($dsn, DB_USER, DB_PASS, $options);

      return self::$connection;
    } catch (PDOException $e) {
      // Log do erro (em produção, usar sistema de log adequado)
      error_log("Erro na conexão com banco de dados: " . $e->getMessage());

      // Retorna erro amigável ao usuário
      throw new PDOException("Erro ao conectar com o banco de dados");
    }
  }

  /**
   * Fecha a conexão com o banco
   */
  public static function closeConnection(): void
  {
    self::$connection = null;
  }
}
