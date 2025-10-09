<?php

namespace App\Config;

use PDO;
use PDOException;

/**
 * Classe de conexão com o banco de dados utilizando o padrão Singleton.
 * Garante que apenas uma instância da conexão seja criada.
 */
class Database
{
  private static ?PDO $instance = null;
  private string $host = 'localhost';
  private string $db_name = 'sistema_estudos'; // Nome do seu banco de dados
  private string $username = 'root'; // Seu usuário do banco de dados
  private string $password = ''; // Sua senha do banco de dados

  /**
   * O construtor é privado para prevenir a criação de instâncias diretas.
   */
  private function __construct() {}

  /**
   * Previne a clonagem da instância.
   */
  private function __clone() {}

  /**
   * Previne a desserialização da instância.
   */
  public function __wakeup() {}

  /**
   * Método estático que retorna a instância única da conexão PDO.
   *
   * @return PDO|null
   */
  public static function getInstance(): ?PDO
  {
    if (self::$instance === null) {
      try {
        $dsn = 'mysql:host=' . (new self())->host . ';dbname=' . (new self())->db_name . ';charset=utf8';
        self::$instance = new PDO($dsn, (new self())->username, (new self())->password);
        self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      } catch (PDOException $e) {
        // Em um ambiente de produção, você poderia logar o erro em vez de exibi-lo.
        die('Erro de conexão com o banco de dados: ' . $e->getMessage());
      }
    }

    return self::$instance;
  }
}
