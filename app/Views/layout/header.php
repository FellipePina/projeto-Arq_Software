<header>
  <div class="container">
    <h1><a href="/dashboard">Sistema de Estudos</a></h1>
    <nav>
      <ul>
        <?php if (isset($_SESSION['usuario_id'])): ?>
          <li><a href="/dashboard">Painel</a></li>
          <li><a href="/conteudos">Conteúdos</a></li>
          <li><a href="/categorias">Categorias</a></li>
          <li><a href="/metas">Metas</a></li>
          <li><a href="/usuario/logout">Sair</a></li>
        <?php else: ?>
          <li><a href="/usuario/login">Login</a></li>
          <li><a href="/usuario/cadastro">Cadastro</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>