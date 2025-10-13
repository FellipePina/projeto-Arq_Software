<?php

/**
 * Ponto de entrada da aplicação
 *
 * Este é o arquivo principal que recebe todas as requisições
 * e direciona para os controllers apropriados.
 *
 * Princípios aplicados:
 * - Front Controller Pattern: ponto único de entrada
 * - Separation of Concerns: separa roteamento da lógica de negócio
 */

// Carrega as configurações e autoloader
require_once '../config/config.php';
require_once '../config/autoloader.php';

// Obtém a URL requisitada
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Remove o diretório base se necessário
$path = str_replace('/auxilo-estudos/public', '', $path);

// Define rota padrão
if ($path === '' || $path === '/') {
  $path = '/dashboard';
}

// Router simples - seguindo princípio Single Responsibility
switch ($path) {
  // === DASHBOARD ===
  case '/dashboard':
    $controller = new App\Controllers\DashboardController();
    $controller->index();
    break;

  // === USUÁRIOS ===
  case '/login':
    $controller = new App\Controllers\UsuarioController();
    $controller->login();
    break;

  case '/register':
    $controller = new App\Controllers\UsuarioController();
    $controller->register();
    break;

  case '/logout':
    $controller = new App\Controllers\UsuarioController();
    $controller->logout();
    break;

  case '/perfil':
    $controller = new App\Controllers\UsuarioController();
    $controller->perfil();
    break;

  case '/perfil/atualizar':
    $controller = new App\Controllers\UsuarioController();
    $controller->atualizarPerfil();
    break;

  // === CONTEÚDOS ===
  case '/conteudos':
    $controller = new App\Controllers\ConteudoController();
    $controller->index();
    break;

  case '/conteudos/criar':
    $controller = new App\Controllers\ConteudoController();
    $controller->create();
    break;

  case '/conteudos/ver':
    $controller = new App\Controllers\ConteudoController();
    $controller->show();
    break;

  case '/conteudos/editar':
    $controller = new App\Controllers\ConteudoController();
    $controller->edit();
    break;

  case '/conteudos/excluir':
    $controller = new App\Controllers\ConteudoController();
    $controller->delete();
    break;

  case '/conteudos/alterar-status':
    $controller = new App\Controllers\ConteudoController();
    $controller->alterarStatus();
    break;

  // === SESSÕES ===
  case '/sessoes':
    $controller = new App\Controllers\SessaoController();
    $controller->index();
    break;

  case '/sessoes/iniciar':
    $controller = new App\Controllers\SessaoController();
    $controller->iniciar();
    break;

  case '/sessoes/cronometro':
    $controller = new App\Controllers\SessaoController();
    $controller->cronometro();
    break;

  case '/sessoes/finalizar':
    $controller = new App\Controllers\SessaoController();
    $controller->finalizar();
    break;

  case '/sessoes/ver':
    $controller = new App\Controllers\SessaoController();
    $controller->show();
    break;

  case '/sessoes/excluir':
    $controller = new App\Controllers\SessaoController();
    $controller->delete();
    break;

  // === METAS ===
  case '/metas':
    $controller = new App\Controllers\MetaController();
    $controller->index();
    break;

  case '/metas/criar':
    $controller = new App\Controllers\MetaController();
    $controller->create();
    break;

  case '/metas/ver':
    $controller = new App\Controllers\MetaController();
    $controller->show();
    break;

  case '/metas/adicionar-conteudo':
    $controller = new App\Controllers\MetaController();
    $controller->adicionarConteudo();
    break;

  case '/metas/marcar-conteudo-concluido':
    $controller = new App\Controllers\MetaController();
    $controller->marcarConteudoConcluido();
    break;

  // === AJAX/API ===
  case '/dashboard/graficos':
    $controller = new App\Controllers\DashboardController();
    $controller->graficos();
    break;

  default:
    // Página não encontrada - seguindo princípio de responsabilidade única
    http_response_code(404);
    include __DIR__ . '/../app/Views/errors/404.php';
    break;
}
