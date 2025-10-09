<?php

namespace App\Controllers;

use App\Models\ConteudoEstudo;
use App\Models\Categoria;

class ConteudoController
{
  private function verificarAutenticacao()
  {
    session_start();
    if (!isset($_SESSION['usuario_id'])) {
      header('Location: /usuario/login');
      exit;
    }
  }

  public function index()
  {
    $this->verificarAutenticacao();
    $conteudoModel = new ConteudoEstudo();
    $conteudos = $conteudoModel->buscarPorUsuario($_SESSION['usuario_id']);
    require_once '../app/Views/conteudo/index.php';
  }

  public function novo()
  {
    $this->verificarAutenticacao();
    $categoriaModel = new Categoria();
    $categorias = $categoriaModel->buscarPorUsuario($_SESSION['usuario_id']);
    require_once '../app/Views/conteudo/novo.php';
  }
  public function salvar()
  {
    $this->verificarAutenticacao();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $dados = [
        'id' => $_POST['id'] ?? null,
        'titulo' => filter_input(INPUT_POST, 'titulo'),
        'descricao' => filter_input(INPUT_POST, 'descricao'),
        'status' => filter_input(INPUT_POST, 'status'),
        'categoria_id' => filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT),
        'usuario_id' => $_SESSION['usuario_id']
      ];

      $conteudoModel = new ConteudoEstudo();
      $conteudoModel->salvar($dados);

      header('Location: /conteudo/index');
      exit;
    }
  }

  public function editar($id)
  {
    $this->verificarAutenticacao();
    $conteudoModel = new ConteudoEstudo();
    $conteudo = $conteudoModel->find($id);

    if (!$conteudo || $conteudo['usuario_id'] != $_SESSION['usuario_id']) {
      echo "Conteúdo não encontrado ou não pertence ao usuário.";
      return;
    }

    $categoriaModel = new Categoria();
    $categorias = $categoriaModel->buscarPorUsuario($_SESSION['usuario_id']);

    require_once '../app/Views/conteudo/editar.php';
  }

  public function deletar($id)
  {
    $this->verificarAutenticacao();
    $conteudoModel = new ConteudoEstudo();
    $conteudo = $conteudoModel->find($id);

    if ($conteudo && $conteudo['usuario_id'] == $_SESSION['usuario_id']) {
      $conteudoModel->delete($id);
    }

    header('Location: /conteudo/index');
    exit;
  }
}
