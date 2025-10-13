<?php

namespace App\Models;

/**
 * Classe Usuario - Modelo para gerenciar usuários do sistema
 *
 * Princípios aplicados:
 * - Single Responsibility: apenas operações relacionadas a usuários
 * - Open/Closed: aberta para extensão, fechada para modificação
 * - Liskov Substitution: pode ser usada onde BaseModel é esperada
 */
class Usuario extends BaseModel
{
  protected string $table = 'usuarios';

  /**
   * Busca usuário por email
   *
   * @param string $email Email do usuário
   * @return array|false Dados do usuário ou false se não encontrado
   */
  public function buscarPorEmail(string $email)
  {
    $sql = "SELECT * FROM {$this->table} WHERE email = :email";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();

    return $stmt->fetch();
  }

  /**
   * Valida login do usuário
   *
   * @param string $email Email do usuário
   * @param string $senha Senha em texto plano
   * @return array|false Dados do usuário se válido, false se inválido
   */
  public function validarLogin(string $email, string $senha)
  {
    $usuario = $this->buscarPorEmail($email);

    // Se usuário não existe ou senha não confere
    if (!$usuario || !password_verify($senha, $usuario['senha'])) {
      return false;
    }

    // Se usuário está inativo
    if (!$usuario['ativo']) {
      return false;
    }

    return $usuario;
  }

  /**
   * Criptografa senha usando bcrypt
   *
   * @param string $senha Senha em texto plano
   * @return string Senha criptografada
   */
  public function criptografarSenha(string $senha): string
  {
    return password_hash($senha, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
  }

  /**
   * Valida se email já existe no sistema
   *
   * @param string $email Email para validar
   * @param int|null $excluirId ID para excluir da validação (útil em updates)
   * @return bool True se email já existe, false se disponível
   */
  public function emailExiste(string $email, ?int $excluirId = null): bool
  {
    $sql = "SELECT id FROM {$this->table} WHERE email = :email";

    // Se estamos atualizando, excluir o próprio usuário da verificação
    if ($excluirId) {
      $sql .= " AND id != :excluir_id";
    }

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':email', $email);

    if ($excluirId) {
      $stmt->bindValue(':excluir_id', $excluirId, \PDO::PARAM_INT);
    }

    $stmt->execute();

    return $stmt->fetch() !== false;
  }

  /**
   * Cria um novo usuário
   *
   * @param array $dados Dados do usuário
   * @return int|false ID do usuário criado ou false se erro
   */
  public function criar(array $dados)
  {
    // Validações básicas
    if (empty($dados['nome']) || empty($dados['email']) || empty($dados['senha'])) {
      return false;
    }

    // Verifica se email já existe
    if ($this->emailExiste($dados['email'])) {
      return false;
    }

    // Prepara dados para inserção
    $dadosUsuario = [
      'nome' => trim($dados['nome']),
      'email' => trim(strtolower($dados['email'])),
      'senha' => $this->criptografarSenha($dados['senha']),
      'ativo' => 1
    ];

    return $this->save($dadosUsuario);
  }

  /**
   * Busca usuários ativos
   *
   * @return array Lista de usuários ativos
   */
  public function buscarAtivos(): array
  {
    $sql = "SELECT id, nome, email, data_criacao FROM {$this->table}
                WHERE ativo = 1
                ORDER BY nome ASC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
  }
}
