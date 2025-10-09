<?php

namespace App\Models;

/**
 * Modelo para a tabela de usuários.
 */
class Usuario extends BaseModel
{
  protected string $table = 'usuarios';

  /**
   * Cria um novo usuário ou atualiza um existente.
   *
   * @param array $dados
   * @return bool
   */
  public function salvar(array $dados): bool
  {
    $senhaHash = $this->criptografarSenha($dados['senha']);

    if (isset($dados['id']) && $dados['id']) {
      // Atualizar
      $stmt = $this->db->prepare(
        "UPDATE {$this->table} SET nome = ?, email = ?, senha = ?, ativo = ? WHERE id = ?"
      );
      return $stmt->execute([$dados['nome'], $dados['email'], $senhaHash, $dados['ativo'], $dados['id']]);
    }

    // Inserir
    $stmt = $this->db->prepare(
      "INSERT INTO {$this->table} (nome, email, senha) VALUES (?, ?, ?)"
    );
    return $stmt->execute([$dados['nome'], $dados['email'], $senhaHash]);
  }

  /**
   * Busca um usuário pelo email.
   *
   * @param string $email
   * @return mixed
   */
  public function buscarPorEmail(string $email): mixed
  {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ? AND ativo = TRUE");
    $stmt->execute([$email]);
    return $stmt->fetch(\PDO::FETCH_OBJ);
  }

  /**
   * Valida as credenciais de login.
   *
   * @param string $email
   * @param string $senha
   * @return object|false
   */
  public function validarLogin(string $email, string $senha): object|false
  {
    $usuario = $this->buscarPorEmail($email);
    if ($usuario && password_verify($senha, $usuario->senha)) {
      return $usuario;
    }
    return false;
  }

  /**
   * Criptografa a senha para armazenamento seguro.
   *
   * @param string $senha
   * @return string
   */
  private function criptografarSenha(string $senha): string
  {
    // Se a senha já for um hash, não faz o hash novamente
    if (password_get_info($senha)['algo']) {
      return $senha;
    }
    return password_hash($senha, PASSWORD_BCRYPT);
  }
}
