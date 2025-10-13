<!-- Página de Login -->
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card fade-in" style="margin-top: 5rem;">
      <div class="card-header text-center">
        <h2 class="card-title">
          <i class="fas fa-sign-in-alt"></i>
          Entrar no Sistema
        </h2>
        <p class="text-muted">Faça login para acessar seus estudos</p>
      </div>

      <form action="/login" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

        <div class="form-group">
          <label for="email" class="form-label">
            <i class="fas fa-envelope"></i> Email
          </label>
          <input
            type="email"
            class="form-control"
            id="email"
            name="email"
            placeholder="Digite seu email"
            required
            autofocus>
        </div>

        <div class="form-group">
          <label for="senha" class="form-label">
            <i class="fas fa-lock"></i> Senha
          </label>
          <input
            type="password"
            class="form-control"
            id="senha"
            name="senha"
            placeholder="Digite sua senha"
            required>
        </div>

        <div class="form-group text-center">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-sign-in-alt"></i>
            Entrar
          </button>
        </div>
      </form>

      <div class="text-center mt-3">
        <p class="text-muted">
          Ainda não tem uma conta?
          <a href="/register" class="text-primary">
            <i class="fas fa-user-plus"></i>
            Cadastre-se aqui
          </a>
        </p>
      </div>
    </div>

    <!-- Informações sobre o sistema -->
    <div class="card mt-3 fade-in">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-info-circle"></i>
          Sobre o Sistema
        </h3>
      </div>

      <div class="row">
        <div class="col-md-4 text-center">
          <i class="fas fa-book fa-3x text-primary mb-2"></i>
          <h4>Organize Conteúdos</h4>
          <p class="text-muted">Gerencie seus materiais de estudo de forma organizada</p>
        </div>

        <div class="col-md-4 text-center">
          <i class="fas fa-clock fa-3x text-success mb-2"></i>
          <h4>Controle o Tempo</h4>
          <p class="text-muted">Registre suas sessões de estudo e acompanhe seu progresso</p>
        </div>

        <div class="col-md-4 text-center">
          <i class="fas fa-target fa-3x text-warning mb-2"></i>
          <h4>Defina Metas</h4>
          <p class="text-muted">Estabeleça objetivos e acompanhe seu cumprimento</p>
        </div>
      </div>
    </div>

    <!-- Dados de demonstração -->
    <div class="card mt-3">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-flask"></i>
          Teste o Sistema
        </h3>
      </div>
      <p><strong>Email:</strong> teste@exemplo.com</p>
      <p><strong>Senha:</strong> 123456</p>
      <p class="text-muted">
        <small>
          <i class="fas fa-info-circle"></i>
          Use estas credenciais para testar o sistema
        </small>
      </p>
    </div>
  </div>
</div>