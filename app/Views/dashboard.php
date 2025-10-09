<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="/style.css">
</head>

<body>
  <header>
    <div class="container">
      <h1>Meu Painel de Estudos</h1>
      <nav>
        <ul>
          <li><a href="/dashboard">Início</a></li>
          <li><a href="/conteudos">Conteúdos</a></li>
          <li><a href="/categorias">Categorias</a></li>
          <li><a href="/metas">Metas</a></li>
          <li><a href="/usuario/logout">Sair</a></li>
        </ul>
      </nav>
    </div>
  </header>
  <main class="container">
    <h2>Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário'); ?>!</h2>

    <div class="card">
      <h3>Resumo dos Últimos 30 Dias</h3>
      <?php if (isset($estatisticas)): ?>
        <div class="stats-summary">
          <div>
            <strong>Total de Horas Estudadas:</strong>
            <span><?php echo htmlspecialchars($estatisticas['totalHoras']); ?>h</span>
          </div>
          <div>
            <strong>Total de Sessões:</strong>
            <span><?php echo htmlspecialchars($estatisticas['totalSessoes']); ?></span>
          </div>
          <div>
            <strong>Média Diária:</strong>
            <span><?php echo htmlspecialchars($estatisticas['mediaHorasPorDia']); ?>h</span>
          </div>
        </div>
      <?php else: ?>
        <p>Não há dados de estudo para exibir.</p>
      <?php endif; ?>
    </div>

    <p>Use o menu acima para gerenciar seus estudos.</p>
  </main>
</body>

</html>