<?php

// Autoloader simples para carregar as classes automaticamente
spl_autoload_register(function ($class_name) {
  $file = '../' . str_replace('\\', '/', $class_name) . '.php';
  if (file_exists($file)) {
    require $file;
  }
});

// Roteador simples
$request_uri = $_SERVER['REQUEST_URI'];
$request_path = parse_url($request_uri, PHP_URL_PATH);

// Remove o nome do diretório base se o projeto não estiver na raiz do servidor
$base_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
if (strpos($request_path, $base_dir) === 0) {
  $request_path = substr($request_path, strlen($base_dir));
}

$path = trim($request_path, '/');
$path = empty($path) ? 'dashboard' : $path; // Rota padrão

$parts = explode('/', $path);
$controller_name = "App\\Controllers\\" . ucfirst($parts[0] ?? 'Dashboard') . 'Controller';
$method_name = $parts[1] ?? 'index';
$param = $parts[2] ?? null;

if (class_exists($controller_name)) {
  $controller = new $controller_name();
  if (method_exists($controller, $method_name)) {
    $controller->$method_name($param);
  } else {
    http_response_code(404);
    echo "<h1>404 - Método não encontrado</h1>";
  }
} else {
  http_response_code(404);
  echo "<h1>404 - Controlador não encontrado</h1>";
}
