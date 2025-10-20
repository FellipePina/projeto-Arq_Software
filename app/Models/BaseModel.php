<?php

namespace App\Models;

use PDO;

/**
 * Classe BaseModel - Classe abstrata para operações comuns dos models
 *
 * Princípios aplicados:
 * - DRY (Don't Repeat Yourself): evita repetição de código
 * - Template Method Pattern: define estrutura comum para models
 * - Single Responsibility: apenas operações básicas de CRUD
 */
abstract class BaseModel
{
  protected PDO $db;
  protected string $table;
  protected string $primaryKey = 'id';

  /**
   * Construtor - inicializa conexão com banco
   * Utiliza o padrão Singleton do Database
   */
  public function __construct()
  {
    $this->db = Database::getInstance()->getConnection();
  }

  /**
   * Busca todos os registros da tabela
   *
   * @return array Lista de registros
   */
  public function findAll(): array
  {
    $sql = "SELECT * FROM {$this->table} ORDER BY {$this->primaryKey} DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Busca um registro por ID
   *
   * @param int $id ID do registro
   * @return array|false Dados do registro ou false se não encontrado
   */
  public function findById(int $id)
  {
    $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch();
  }

  /**
   * Salva um registro (INSERT ou UPDATE)
   *
   * @param array $data Dados para salvar
   * @return int|false ID do registro salvo ou false se erro
   */
  public function save(array $data)
  {
    // Se tem ID, é uma atualização
    if (isset($data[$this->primaryKey]) && $data[$this->primaryKey] > 0) {
      return $this->update($data);
    }

    // Caso contrário, é uma inserção
    return $this->insert($data);
  }

  /**
   * Insere um novo registro
   *
   * @param array $data Dados para inserir
   * @return int|false ID do registro inserido ou false se erro
   */
  protected function insert(array $data)
  {
    // Remove o ID se existir (será auto incrementado)
    unset($data[$this->primaryKey]);

    // Prepara os campos e valores
    $fields = array_keys($data);
    $placeholders = ':' . implode(', :', $fields);
    $fieldsList = implode(', ', $fields);

    $sql = "INSERT INTO {$this->table} ({$fieldsList}) VALUES ({$placeholders})";
    $stmt = $this->db->prepare($sql);

    // Executa a query
    if ($stmt->execute($data)) {
      return (int) $this->db->lastInsertId();
    }

    return false;
  }

  /**
   * Atualiza um registro existente
   *
   * @param array $data Dados para atualizar
   * @return bool True se sucesso, false se erro
   */
  protected function update(array $data)
  {
    $id = $data[$this->primaryKey];
    unset($data[$this->primaryKey]);

    // Prepara os campos para SET
    $setClause = [];
    foreach (array_keys($data) as $field) {
      $setClause[] = "{$field} = :{$field}";
    }
    $setString = implode(', ', $setClause);

    $sql = "UPDATE {$this->table} SET {$setString} WHERE {$this->primaryKey} = :id";
    $stmt = $this->db->prepare($sql);

    // Adiciona o ID aos dados
    $data['id'] = $id;

    return $stmt->execute($data);
  }

  /**
   * Exclui um registro
   *
   * @param int $id ID do registro para excluir
   * @return bool True se sucesso, false se erro
   */
  public function delete(int $id): bool
  {
    $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Conta o total de registros na tabela
   *
   * @return int Total de registros
   */
  public function count(): int
  {
    $sql = "SELECT COUNT(*) as total FROM {$this->table}";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetch();
    return (int) $result['total'];
  }
}
