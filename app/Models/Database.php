<?php

namespace App\Models;

use PDO;
use PDOException;

/**
 * Classe Database - Responsável pela conexão com o banco de dados
 *
 * PADRÃO GOF: SINGLETON (CRIACIONAL)
 *
 * Implementação do padrão Singleton para garantir que exista apenas
 * uma única instância de conexão com o banco de dados em toda a aplicação.
 *
 * Características do Singleton implementadas:
 * - Construtor privado: impede a criação de instâncias fora da classe
 * - Clone privado: impede a clonagem da instância
 * - Método getInstance(): ponto único de acesso à instância
 * - Propriedade estática $instance: armazena a única instância
 *
 * Benefícios:
 * - Economia de recursos: uma única conexão é compartilhada
 * - Controle de acesso: ponto centralizado para gerenciar a conexão
 * - Consistência: todos os componentes usam a mesma conexão
 *
 * Princípios SOLID aplicados:
 * - Single Responsibility: apenas gerencia conexão com BD
 */
class Database
{
  /**
   * Instância única da classe (Singleton)
   */
  private static ?Database $instance = null;

  /**
   * Conexão PDO com o banco de dados
   */
  private ?PDO $connection = null;

  /**
   * Construtor privado - impede instanciação direta
   * Parte essencial do padrão Singleton
   */
  private function __construct()
  {
    // Construtor vazio - a conexão é criada sob demanda
  }

  /**
   * Previne a clonagem da instância
   * Parte essencial do padrão Singleton
   */
  private function __clone()
  {
    // Clonagem não permitida
  }

  /**
   * Previne a desserialização da instância
   * Parte essencial do padrão Singleton
   */
  public function __wakeup()
  {
    throw new \Exception("Não é possível desserializar um Singleton");
  }

  /**
   * Obtém a instância única da classe Database (Singleton)
   *
   * Este é o único ponto de acesso à classe Database.
   * Cria a instância na primeira chamada e retorna a mesma
   * instância em todas as chamadas subsequentes.
   *
   * @return Database Instância única da classe
   */
  public static function getInstance(): Database
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  /**
   * Obtém a conexão com o banco de dados
   *
   * Lazy Loading: A conexão só é criada quando realmente necessária
   *
   * @return PDO Instância da conexão PDO
   * @throws PDOException Se não conseguir conectar
   */
  public function getConnection(): PDO
  {
    // Se já existe conexão, retorna ela (Lazy Loading)
    if ($this->connection !== null) {
      return $this->connection;
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
      $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);

      return $this->connection;
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
  public function closeConnection(): void
  {
    $this->connection = null;
  }

  /**
   * Método estático para manter compatibilidade com código legado
   *
   * @return PDO
   * @deprecated Use getInstance()->getConnection() no lugar
   */
  public static function getConnectionStatic(): PDO
  {
    return self::getInstance()->getConnection();
  }
}
