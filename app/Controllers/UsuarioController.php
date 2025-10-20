<?php

namespace App\Controllers;

use App\Models\Usuario;

/**
 * Classe UsuarioController - Controlador para operações de usuário
 *
 * PADRÃO GOF: FACADE (ESTRUTURAL)
 *
 * Este controller atua como uma FACHADA (Facade) que simplifica e unifica
 * a interface para o complexo subsistema de autenticação de usuários.
 *
 * Complexidades escondidas pela Facade:
 * - Validação de dados (CSRF, campos obrigatórios, formato de email)
 * - Consulta ao modelo Usuario
 * - Verificação de senhas com hash
 * - Gerenciamento de sessões PHP ($_SESSION)
 * - Regeneração de IDs de sessão
 * - Controle de tokens CSRF
 * - Mensagens flash
 * - Redirecionamentos
 *
 * Benefícios do padrão Facade:
 * - Interface simplificada: O roteador chama apenas processarLogin()
 * - Subsistema desacoplado: As rotas não precisam conhecer a complexidade
 * - Manutenibilidade: Mudanças internas não afetam quem usa a fachada
 * - Organização: Toda lógica de autenticação centralizada
 *
 * Exemplo de uso (no roteador):
 *   $controller = new UsuarioController();
 *   $controller->login(); // Interface simples, complexidade escondida
 *
 * Princípios SOLID aplicados:
 * - Single Responsibility: apenas operações de usuário
 * - Dependency Inversion: depende de abstrações (modelos)
 * - Open/Closed: aberto para extensão, fechado para modificação
 */
class UsuarioController extends BaseController
{
  private Usuario $usuarioModel;

  /**
   * Construtor - inicializa o modelo de usuário
   */
  public function __construct()
  {
    parent::__construct();
    $this->usuarioModel = new Usuario();
  }

  /**
   * Exibe o formulário de login
   *
   * MÉTODO FACADE: Interface simplificada para autenticação
   * Este método esconde toda a complexidade do processo de login
   */
  public function login(): void
  {
    // Se já está logado, redireciona para dashboard
    if ($this->isLoggedIn()) {
      $this->redirect('/dashboard');
    }

    $data = [
      'titulo' => 'Login - ' . APP_NAME,
      'csrf_token' => $this->generateCsrfToken(),
      'flash_messages' => $this->getFlashMessages()
    ];

    // Se for POST, processa o login
    if ($this->isPost()) {
      $this->processarLogin();
      return;
    }

    $this->render('usuario/login', $data);
  }

  /**
   * Processa o login do usuário
   *
   * SUBSISTEMA COMPLEXO escondido pela Facade:
   * Este método coordena múltiplas operações complexas:
   * 1. Validação de token CSRF
   * 2. Validação de campos obrigatórios
   * 3. Consulta ao banco de dados
   * 4. Verificação de senha com hash
   * 5. Criação de sessão segura
   * 6. Regeneração de tokens
   * 7. Mensagens de feedback
   * 8. Redirecionamentos apropriados
   */
  private function processarLogin(): void
  {
    $dados = $this->getPostData();

    // Validação CSRF
    if (!$this->validateCsrfToken($dados['csrf_token'] ?? null)) {
      $this->setFlashMessage('error', 'Token de segurança inválido');
      $this->redirect('/login');
    }    // Validações básicas
    if (empty($dados['email']) || empty($dados['senha'])) {
      $this->setFlashMessage('error', 'Email e senha são obrigatórios');
      $this->redirect('/login');
    }

    // Tenta fazer login
    $usuario = $this->usuarioModel->validarLogin($dados['email'], $dados['senha']);

    if (!$usuario) {
      $this->setFlashMessage('error', 'Email ou senha incorretos');
      $this->redirect('/login');
    }

    // Login bem-sucedido - cria sessão
    $this->criarSessaoUsuario($usuario);

    // Regenera token CSRF após login bem-sucedido
    $this->regenerateCsrfToken();

    $this->setFlashMessage('success', 'Login realizado com sucesso!');
    $this->redirect('/dashboard');
  }

  /**
   * Exibe o formulário de cadastro
   */
  public function register(): void
  {
    // Se já está logado, redireciona para dashboard
    if ($this->isLoggedIn()) {
      $this->redirect('/dashboard');
    }

    $data = [
      'titulo' => 'Cadastro - ' . APP_NAME,
      'csrf_token' => $this->generateCsrfToken(),
      'flash_messages' => $this->getFlashMessages()
    ];

    // Se for POST, processa o cadastro
    if ($this->isPost()) {
      $this->processarCadastro();
      return;
    }

    $this->render('usuario/register', $data);
  }

  /**
   * Processa o cadastro do usuário
   */
  private function processarCadastro(): void
  {
    $dados = $this->getPostData();

    // Validação CSRF
    if (!$this->validateCsrfToken($dados['csrf_token'] ?? null)) {
      $this->setFlashMessage('error', 'Token de segurança inválido');
      $this->redirect('/register');
    }

    // Validações
    $erros = $this->validarDadosCadastro($dados);

    if (!empty($erros)) {
      foreach ($erros as $erro) {
        $this->setFlashMessage('error', $erro);
      }
      $this->redirect('/register');
    }

    // Tenta criar o usuário
    $usuarioId = $this->usuarioModel->criar($dados);

    if (!$usuarioId) {
      $this->setFlashMessage('error', 'Erro ao criar usuário. Email pode já estar em uso.');
      $this->redirect('/register');
    }

    // Busca o usuário criado para fazer login automático
    $usuario = $this->usuarioModel->findById($usuarioId);
    $this->criarSessaoUsuario($usuario);

    // Regenera token CSRF após registro bem-sucedido
    $this->regenerateCsrfToken();

    $this->setFlashMessage('success', 'Cadastro realizado com sucesso! Bem-vindo!');
    $this->redirect('/dashboard');
  }

  /**
   * Valida dados do cadastro
   *
   * @param array $dados Dados a validar
   * @return array Lista de erros
   */
  private function validarDadosCadastro(array $dados): array
  {
    $erros = [];

    // Nome obrigatório
    if (empty($dados['nome'])) {
      $erros[] = 'Nome é obrigatório';
    } elseif (strlen($dados['nome']) < 2) {
      $erros[] = 'Nome deve ter pelo menos 2 caracteres';
    }

    // Email obrigatório e válido
    if (empty($dados['email'])) {
      $erros[] = 'Email é obrigatório';
    } elseif (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
      $erros[] = 'Email inválido';
    }

    // Senha obrigatória e com critérios mínimos
    if (empty($dados['senha'])) {
      $erros[] = 'Senha é obrigatória';
    } elseif (strlen($dados['senha']) < 6) {
      $erros[] = 'Senha deve ter pelo menos 6 caracteres';
    }

    // Confirmação de senha
    if (empty($dados['confirmar_senha'])) {
      $erros[] = 'Confirmação de senha é obrigatória';
    } elseif ($dados['senha'] !== $dados['confirmar_senha']) {
      $erros[] = 'Senha e confirmação não conferem';
    }

    return $erros;
  }

  /**
   * Cria sessão do usuário após login bem-sucedido
   *
   * @param array $usuario Dados do usuário
   */
  private function criarSessaoUsuario(array $usuario): void
  {
    // Regenera ID da sessão por segurança
    session_regenerate_id(true);

    // Armazena dados na sessão
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['login_timestamp'] = time();
  }

  /**
   * Faz logout do usuário
   */
  public function logout(): void
  {
    // Limpa dados da sessão
    $_SESSION = [];

    // Destrói o cookie da sessão
    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
      );
    }

    // Destrói a sessão
    session_destroy();

    $this->setFlashMessage('success', 'Logout realizado com sucesso!');
    $this->redirect('/login');
  }

  /**
   * Exibe perfil do usuário
   */
  public function perfil(): void
  {
    $this->requireLogin();

    $usuario = $this->usuarioModel->findById($this->getLoggedUserId());

    $data = [
      'titulo' => 'Meu Perfil - ' . APP_NAME,
      'usuario' => $usuario,
      'flash_messages' => $this->getFlashMessages()
    ];

    $this->render('usuario/perfil', $data);
  }

  /**
   * Atualiza dados do perfil
   */
  public function atualizarPerfil(): void
  {
    $this->requireLogin();

    if (!$this->isPost()) {
      $this->redirect('/perfil');
    }

    $dados = $this->getPostData();
    $usuarioId = $this->getLoggedUserId();

    // Validação CSRF
    if (!$this->validateCsrfToken($dados['csrf_token'] ?? null)) {
      $this->setFlashMessage('error', 'Token de segurança inválido');
      $this->redirect('/perfil');
    }

    // Validações básicas
    $erros = [];

    if (empty($dados['nome'])) {
      $erros[] = 'Nome é obrigatório';
    }

    if (empty($dados['email'])) {
      $erros[] = 'Email é obrigatório';
    } elseif (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
      $erros[] = 'Email inválido';
    } elseif ($this->usuarioModel->emailExiste($dados['email'], $usuarioId)) {
      $erros[] = 'Este email já está em uso por outro usuário';
    }

    if (!empty($erros)) {
      foreach ($erros as $erro) {
        $this->setFlashMessage('error', $erro);
      }
      $this->redirect('/perfil');
    }

    // Prepara dados para atualização
    $dadosAtualizacao = [
      'id' => $usuarioId,
      'nome' => $dados['nome'],
      'email' => $dados['email']
    ];

    // Se informou nova senha, atualiza também
    if (!empty($dados['nova_senha'])) {
      if (strlen($dados['nova_senha']) < 6) {
        $this->setFlashMessage('error', 'Nova senha deve ter pelo menos 6 caracteres');
        $this->redirect('/perfil');
      }

      if ($dados['nova_senha'] !== $dados['confirmar_nova_senha']) {
        $this->setFlashMessage('error', 'Nova senha e confirmação não conferem');
        $this->redirect('/perfil');
      }

      $dadosAtualizacao['senha'] = $this->usuarioModel->criptografarSenha($dados['nova_senha']);
    }

    // Atualiza no banco
    if ($this->usuarioModel->save($dadosAtualizacao)) {
      // Atualiza dados da sessão
      $_SESSION['usuario_nome'] = $dados['nome'];
      $_SESSION['usuario_email'] = $dados['email'];

      $this->setFlashMessage('success', 'Perfil atualizado com sucesso!');
    } else {
      $this->setFlashMessage('error', 'Erro ao atualizar perfil');
    }

    $this->redirect('/perfil');
  }
}
