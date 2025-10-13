<!-- Página de Cadastro -->
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card fade-in" style="margin-top: 3rem;">
      <div class="card-header text-center">
        <h2 class="card-title">
          <i class="fas fa-user-plus"></i>
          Criar Conta
        </h2>
        <p class="text-muted">Cadastre-se e comece a organizar seus estudos</p>
      </div>

      <form action="/register" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

        <div class="form-group">
          <label for="nome" class="form-label">
            <i class="fas fa-user"></i> Nome Completo
          </label>
          <input
            type="text"
            class="form-control"
            id="nome"
            name="nome"
            placeholder="Digite seu nome completo"
            required
            autofocus
            minlength="2"
            maxlength="255">
        </div>

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
            required>
          <small class="text-muted">
            <i class="fas fa-info-circle"></i>
            Será usado para fazer login no sistema
          </small>
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
            placeholder="Digite uma senha segura"
            required
            minlength="6">
          <small class="text-muted">
            <i class="fas fa-shield-alt"></i>
            Mínimo de 6 caracteres
          </small>
        </div>

        <div class="form-group">
          <label for="confirmar_senha" class="form-label">
            <i class="fas fa-lock"></i> Confirmar Senha
          </label>
          <input
            type="password"
            class="form-control"
            id="confirmar_senha"
            name="confirmar_senha"
            placeholder="Confirme sua senha"
            required
            minlength="6">
        </div>

        <div class="form-group">
          <div style="display: flex; align-items: center; gap: 0.5rem;">
            <input type="checkbox" id="termos" required>
            <label for="termos" class="form-label" style="margin-bottom: 0;">
              Concordo com os termos de uso
            </label>
          </div>
        </div>

        <div class="form-group text-center">
          <button type="submit" class="btn btn-success">
            <i class="fas fa-user-plus"></i>
            Criar Conta
          </button>
        </div>
      </form>

      <div class="text-center mt-3">
        <p class="text-muted">
          Já tem uma conta?
          <a href="/login" class="text-primary">
            <i class="fas fa-sign-in-alt"></i>
            Faça login aqui
          </a>
        </p>
      </div>
    </div>

    <!-- Benefícios do cadastro -->
    <div class="card mt-3 fade-in">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-star"></i>
          Por que se cadastrar?
        </h3>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="d-flex align-items-center mb-2">
            <i class="fas fa-check-circle text-success"></i>
            <span class="ml-2">Organize seus estudos</span>
          </div>
          <div class="d-flex align-items-center mb-2">
            <i class="fas fa-check-circle text-success"></i>
            <span class="ml-2">Controle de tempo</span>
          </div>
          <div class="d-flex align-items-center mb-2">
            <i class="fas fa-check-circle text-success"></i>
            <span class="ml-2">Estatísticas de progresso</span>
          </div>
        </div>

        <div class="col-md-6">
          <div class="d-flex align-items-center mb-2">
            <i class="fas fa-check-circle text-success"></i>
            <span class="ml-2">Metas personalizadas</span>
          </div>
          <div class="d-flex align-items-center mb-2">
            <i class="fas fa-check-circle text-success"></i>
            <span class="ml-2">Categorização de conteúdos</span>
          </div>
          <div class="d-flex align-items-center mb-2">
            <i class="fas fa-check-circle text-success"></i>
            <span class="ml-2">Totalmente gratuito</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Validação de confirmação de senha
  document.addEventListener('DOMContentLoaded', function() {
    const senha = document.getElementById('senha');
    const confirmarSenha = document.getElementById('confirmar_senha');

    function validarSenhas() {
      if (senha.value !== confirmarSenha.value) {
        confirmarSenha.setCustomValidity('As senhas não conferem');
      } else {
        confirmarSenha.setCustomValidity('');
      }
    }

    senha.addEventListener('change', validarSenhas);
    confirmarSenha.addEventListener('keyup', validarSenhas);
  });
</script>