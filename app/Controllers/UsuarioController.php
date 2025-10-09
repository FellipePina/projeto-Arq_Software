<?php

namespace App\Controllers;

use App\Models\Usuario;

/**
 * Controlador para gerenciar usuários.
 */
class UsuarioController
{
  /**
   * Exibe o formulário de cadastro de usuário.
   */
  public function cadastro()
  {
    // Carrega a view de cadastro
    require_once '../app/Views/usuario/cadastro.php';
  }

  /**
   * Processa o formulário de cadastro.
   */
  public function salvar()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $nome = filter_input(INPUT_POST, 'nome');
      $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
      $senha = $_POST['senha'];

      if (!$nome || !$email || !$senha) {
        echo "Todos os campos são obrigatórios.";
        return;
      }

      $usuarioModel = new Usuario();
      if ($usuarioModel->buscarPorEmail($email)) {
        echo "Este e-mail já está cadastrado.";
        return;
      }

      $dados = ['nome' => $nome, 'email' => $email, 'senha' => $senha];
      if ($usuarioModel->salvar($dados)) {
        header('Location: /usuario/login');
        exit;
      }

      echo "Ocorreu um erro ao tentar cadastrar.";
    } else {
      require_once '../app/Views/usuario/cadastro.php';
    }
  }

  /**
   * Exibe o formulário de login.
   */
  public function login()
  {
    // Carrega a view de login
    require_once '../app/Views/usuario/login.php';
  }

  /**
   * Processa o login do usuário.
   */
  public function autenticar()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
      $senha = $_POST['senha'];

      if (!$email || !$senha) {
        // Idealmente, redirecionar com mensagem de erro
        echo "E-mail ou senha inválidos.";
        return;
      }

      $usuarioModel = new Usuario();
      $usuario = $usuarioModel->validarLogin($email, $senha);

      if ($usuario) {
        // Iniciar sessão
        session_start();
        $_SESSION['usuario_id'] = $usuario->id;
        $_SESSION['usuario_nome'] = $usuario->nome;
        header('Location: /dashboard'); // Redireciona para o painel
        exit;
      }

      echo "E-mail ou senha incorretos.";
    }
  }
  /**
   * Faz o logout do usuário.
   */
  public function logout()
  {
    session_start();
    session_unset();
    session_destroy();
    header('Location: /usuario/login');
    exit;
  }
}
