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

// Obtém a URL requisitada e o caminho base de forma dinâmica
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Detecta automaticamente o caminho base (pasta onde o index.php está)
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

// Se estiver rodando em um subdiretório (ex.: /projeto-quintafeira/public),
// remove esse prefixo do path para que as rotas funcionem corretamente
if ($basePath !== '' && $basePath !== '/' && str_starts_with($path, $basePath)) {
  $path = substr($path, strlen($basePath));
}

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

  // === DISCIPLINAS ===
  case '/disciplinas':
    $controller = new App\Controllers\DisciplinaController();
    $controller->index();
    break;

  case '/disciplinas/criar':
    $controller = new App\Controllers\DisciplinaController();
    $controller->create();
    break;

  case '/disciplinas/salvar':
    $controller = new App\Controllers\DisciplinaController();
    $controller->store();
    break;

  case (preg_match('/^\/disciplinas\/(\d+)\/editar$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\DisciplinaController();
    $controller->edit((int)$matches[1]);
    break;

  case (preg_match('/^\/disciplinas\/(\d+)\/atualizar$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\DisciplinaController();
    $controller->update((int)$matches[1]);
    break;

  case (preg_match('/^\/disciplinas\/(\d+)\/arquivar$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\DisciplinaController();
    $controller->archive((int)$matches[1]);
    break;

  case (preg_match('/^\/disciplinas\/(\d+)$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\DisciplinaController();
    $controller->show((int)$matches[1]);
    break;

  // === TAREFAS ===
  case '/tarefas':
    $controller = new App\Controllers\TarefaController();
    $controller->index();
    break;

  case '/tarefas/criar':
    $controller = new App\Controllers\TarefaController();
    $controller->create();
    break;

  case '/tarefas/salvar':
    $controller = new App\Controllers\TarefaController();
    $controller->store();
    break;

  case (preg_match('/^\/tarefas\/(\d+)$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\TarefaController();
    $controller->show((int)$matches[1]);
    break;

  case (preg_match('/^\/tarefas\/(\d+)\/editar$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\TarefaController();
    $controller->edit((int)$matches[1]);
    break;

  case (preg_match('/^\/tarefas\/(\d+)\/atualizar$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\TarefaController();
    $controller->update((int)$matches[1]);
    break;

  case (preg_match('/^\/tarefas\/(\d+)\/completar$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\TarefaController();
    $controller->complete((int)$matches[1]);
    break;

  case (preg_match('/^\/tarefas\/(\d+)\/desmarcar$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\TarefaController();
    $controller->uncomplete((int)$matches[1]);
    break;

  case (preg_match('/^\/tarefas\/(\d+)\/subtarefa$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\TarefaController();
    $controller->addSubtask((int)$matches[1]);
    break;

  case (preg_match('/^\/tarefas\/(\d+)\/subtarefa\/(\d+)\/toggle$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\TarefaController();
    $controller->toggleSubtask((int)$matches[1], (int)$matches[2]);
    break;

  // === POMODORO ===
  case '/pomodoro':
    $controller = new App\Controllers\PomodoroController();
    $controller->index();
    break;

  case '/pomodoro/iniciar':
    $controller = new App\Controllers\PomodoroController();
    $controller->start();
    break;

  case (preg_match('/^\/pomodoro\/(\d+)\/finalizar$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\PomodoroController();
    $controller->finish((int)$matches[1]);
    break;

  case (preg_match('/^\/pomodoro\/(\d+)\/interromper$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\PomodoroController();
    $controller->interrupt((int)$matches[1]);
    break;

  case '/pomodoro/historico':
    $controller = new App\Controllers\PomodoroController();
    $controller->history();
    break;

  case '/pomodoro/sessao-ativa':
    $controller = new App\Controllers\PomodoroController();
    $controller->activeSession();
    break;

  // === CALENDÁRIO ===
  case '/calendario':
    $controller = new App\Controllers\CalendarioController();
    $controller->index();
    break;

  case '/calendario/eventos':
    $controller = new App\Controllers\CalendarioController();
    $controller->events();
    break;

  case '/calendario/criar':
    $controller = new App\Controllers\CalendarioController();
    $controller->store();
    break;

  case (preg_match('/^\/calendario\/(\d+)\/atualizar$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\CalendarioController();
    $controller->update((int)$matches[1]);
    break;

  case (preg_match('/^\/calendario\/(\d+)\/excluir$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\CalendarioController();
    $controller->delete((int)$matches[1]);
    break;

  case (preg_match('/^\/calendario\/(\d+)\/mover$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\CalendarioController();
    $controller->updateDate((int)$matches[1]);
    break;

  // === ANOTAÇÕES ===
  case '/anotacoes':
    $controller = new App\Controllers\AnotacaoController();
    $controller->index();
    break;

  case '/anotacoes/criar':
    $controller = new App\Controllers\AnotacaoController();
    $controller->create();
    break;

  case '/anotacoes/salvar':
    $controller = new App\Controllers\AnotacaoController();
    $controller->store();
    break;

  case (preg_match('/^\/anotacoes\/(\d+)$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\AnotacaoController();
    $controller->show((int)$matches[1]);
    break;

  case (preg_match('/^\/anotacoes\/(\d+)\/editar$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\AnotacaoController();
    $controller->edit((int)$matches[1]);
    break;

  case (preg_match('/^\/anotacoes\/(\d+)\/atualizar$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\AnotacaoController();
    $controller->update((int)$matches[1]);
    break;

  case (preg_match('/^\/anotacoes\/(\d+)\/excluir$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\AnotacaoController();
    $controller->delete((int)$matches[1]);
    break;

  case (preg_match('/^\/anotacoes\/(\d+)\/favorita$/', $path, $matches) ? true : false):
    $controller = new App\Controllers\AnotacaoController();
    $controller->toggleFavorite((int)$matches[1]);
    break;

  // === RELATÓRIOS ===
  case '/relatorios':
    $controller = new App\Controllers\RelatorioController();
    $controller->index();
    break;

  case '/relatorios/pomodoro-diario':
    $controller = new App\Controllers\RelatorioController();
    $controller->chartPomodoroDaily();
    break;

  case '/relatorios/disciplinas':
    $controller = new App\Controllers\RelatorioController();
    $controller->chartDisciplinas();
    break;

  case '/relatorios/tarefas':
    $controller = new App\Controllers\RelatorioController();
    $controller->chartTarefas();
    break;

  case '/relatorios/exportar':
    $controller = new App\Controllers\RelatorioController();
    $controller->exportCsv();
    break;

  // === CONFIGURAÇÕES ===
  case '/configuracoes':
    $controller = new App\Controllers\ConfiguracaoController();
    $controller->index();
    break;

  case '/configuracoes/atualizar':
    $controller = new App\Controllers\ConfiguracaoController();
    $controller->update();
    break;

  case '/configuracoes/tema':
    $controller = new App\Controllers\ConfiguracaoController();
    $controller->toggleTheme();
    break;

  case '/gamificacao':
    $controller = new App\Controllers\ConfiguracaoController();
    $controller->gamificacao();
    break;

  case '/gamificacao/verificar':
    $controller = new App\Controllers\ConfiguracaoController();
    $controller->checkAchievements();
    break;

  case '/gamificacao/dados':
    $controller = new App\Controllers\ConfiguracaoController();
    $controller->gamificationData();
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
