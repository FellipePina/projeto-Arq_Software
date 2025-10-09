<?php

namespace App\Controllers;

use App\Models\Meta;

class MetaController
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
    $metaModel = new Meta();
    $metas = $metaModel->buscarPorUsuario($_SESSION['usuario_id']);

    require_once '../app/Views/meta/index.php';
  }

  public function novo()
  {
    $this->verificarAutenticacao();
    require_once '../app/Views/meta/novo.php';
  }

  public function salvar()
  {
    $this->verificarAutenticacao();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $dados = [
        'id' => $_POST['id'] ?? null,
        'titulo' => filter_input(INPUT_POST, 'titulo'),
        'data_alvo' => filter_input(INPUT_POST, 'data_alvo'),
        'status' => filter_input(INPUT_POST, 'status'),
        'usuario_id' => $_SESSION['usuario_id']
      ];

      $metaModel = new Meta();
      $metaModel->salvar($dados);

      header('Location: /meta/index');
      exit;
    }
  }

  public function editar($id)
  {
    $this->verificarAutenticacao();
    $metaModel = new Meta();
    $meta = $metaModel->find($id);

    if (!$meta || $meta['usuario_id'] != $_SESSION['usuario_id']) {
      echo "Meta não encontrada ou não pertence ao usuário.";
      return;
    }

    require_once '../app/Views/meta/editar.php';
  }

  public function deletar($id)
  {
    $this->verificarAutenticacao();
    $metaModel = new Meta();
    $meta = $metaModel->find($id);

    if ($meta && $meta['usuario_id'] == $_SESSION['usuario_id']) {
      $metaModel->delete($id);
    }

    header('Location: /meta/index');
    exit;
  }
}
