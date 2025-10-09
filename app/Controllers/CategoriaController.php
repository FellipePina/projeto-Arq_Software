<?php

namespace App\Controllers;

use App\Models\Categoria;

class CategoriaController
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
    $categoriaModel = new Categoria();
    $categorias = $categoriaModel->buscarPorUsuario($_SESSION['usuario_id']);

    // Carregar a view e passar as categorias
    require_once '../app/Views/categoria/index.php';
  }

  public function salvar()
  {
    $this->verificarAutenticacao();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = $_POST['id'] ?? null;
      $nome = filter_input(INPUT_POST, 'nome');
      $descricao = filter_input(INPUT_POST, 'descricao');
      $cor = filter_input(INPUT_POST, 'cor');

      $dados = [
        'id' => $id,
        'nome' => $nome,
        'descricao' => $descricao,
        'cor' => $cor,
        'usuario_id' => $_SESSION['usuario_id']
      ];

      $categoriaModel = new Categoria();
      $categoriaModel->salvar($dados);

      header('Location: /categoria/index');
      exit;
    }
  }

  public function editar($id)
  {
    $this->verificarAutenticacao();
    $categoriaModel = new Categoria();
    $categoria = $categoriaModel->find($id);

    if (!$categoria || $categoria['usuario_id'] != $_SESSION['usuario_id']) {
      echo "Categoria não encontrada ou não pertence ao usuário.";
      return;
    }

    require_once '../app/Views/categoria/editar.php';
  }

  public function deletar($id)
  {
    $this->verificarAutenticacao();
    $categoriaModel = new Categoria();
    $categoria = $categoriaModel->find($id);

    if ($categoria && $categoria['usuario_id'] == $_SESSION['usuario_id']) {
      $categoriaModel->delete($id);
    }

    header('Location: /categoria/index');
    exit;
  }
}
