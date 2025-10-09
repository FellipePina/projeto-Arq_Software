<?php

namespace App\Models;

use App\Config\Database;
use PDO;

/**
 * Classe base para todos os modelos.
 * Fornece a conexão com o banco de dados e funcionalidades básicas.
 */
abstract class BaseModel
{
  protected PDO $db;
  protected string $table;

  public function __construct()
  {
    $this->db = Database::getInstance();
  }

  /**
   * Encontra um registro pelo ID.
   *
   * @param int $id
   * @return object|false
   */
  public function find(int $id): object|false
  {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_OBJ);
  }
  /**
   * Retorna todos os registros da tabela.
   *
   * @return array
   */
  public function findAll(): array
  {
    $stmt = $this->db->query("SELECT * FROM {$this->table}");
    return $stmt->fetchAll();
  }

  /**
   * Deleta um registro pelo ID.
   *
   * @param int $id
   * @return bool
   */
  public function delete(int $id): bool
  {
    $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
    return $stmt->execute([$id]);
  }
}
