/**
 * JavaScript Principal do Sistema de Auxílio para Estudos
 *
 * Seguindo princípios de Clean Code:
 * - Funções bem nomeadas e com propósito específico
 * - Comentários explicativos
 * - Código organizado e legível
 */

// Inicialização quando DOM estiver pronto
document.addEventListener("DOMContentLoaded", function () {
  // Inicializa componentes
  initFlashMessages();
  initTooltips();
  initConfirmations();
  initAjaxForms();
});

/**
 * Gerencia mensagens flash (auto-hide)
 */
function initFlashMessages() {
  const alerts = document.querySelectorAll(".alert");

  alerts.forEach(function (alert) {
    // Auto-hide após 5 segundos
    setTimeout(function () {
      fadeOut(alert);
    }, 5000);

    // Permitir fechar manualmente
    alert.style.cursor = "pointer";
    alert.addEventListener("click", function () {
      fadeOut(alert);
    });
  });
}

/**
 * Fade out animado para elementos
 */
function fadeOut(element) {
  element.style.transition = "opacity 0.5s";
  element.style.opacity = "0";

  setTimeout(function () {
    element.style.display = "none";
  }, 500);
}

/**
 * Inicializa tooltips para elementos com title
 */
function initTooltips() {
  const elementsWithTitle = document.querySelectorAll("[title]");

  elementsWithTitle.forEach(function (element) {
    element.style.cursor = "help";
  });
}

/**
 * Inicializa confirmações para ações perigosas
 */
function initConfirmations() {
  // Links de exclusão
  const deleteLinks = document.querySelectorAll(
    'a[href*="delete"], a[href*="excluir"]'
  );

  deleteLinks.forEach(function (link) {
    link.addEventListener("click", function (e) {
      if (
        !confirm(
          "Tem certeza que deseja excluir? Esta ação não pode ser desfeita."
        )
      ) {
        e.preventDefault();
      }
    });
  });
}

/**
 * Inicializa formulários AJAX
 */
function initAjaxForms() {
  const ajaxForms = document.querySelectorAll(".ajax-form");

  ajaxForms.forEach(function (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      submitAjaxForm(form);
    });
  });
}

/**
 * Submete formulário via AJAX
 */
function submitAjaxForm(form) {
  const formData = new FormData(form);
  const submitButton = form.querySelector('button[type="submit"]');

  // Desabilita botão durante envio
  if (submitButton) {
    submitButton.disabled = true;
    submitButton.innerHTML =
      '<i class="fas fa-spinner fa-spin"></i> Enviando...';
  }

  fetch(form.action, {
    method: form.method,
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.sucesso) {
        showAlert("success", data.mensagem);

        // Redireciona se especificado
        if (data.redirect) {
          setTimeout(() => {
            window.location.href = data.redirect;
          }, 1500);
        }

        // Recarrega página se especificado
        if (data.reload) {
          setTimeout(() => {
            location.reload();
          }, 1500);
        }
      } else {
        showAlert("error", data.erro || "Erro ao processar solicitação");
      }
    })
    .catch((error) => {
      console.error("Erro:", error);
      showAlert("error", "Erro de conexão");
    })
    .finally(() => {
      // Reabilita botão
      if (submitButton) {
        submitButton.disabled = false;
        submitButton.innerHTML = submitButton.dataset.originalText || "Enviar";
      }
    });
}

/**
 * Exibe alerta dinâmico
 */
function showAlert(type, message) {
  const alertContainer = document.createElement("div");
  alertContainer.className = `alert alert-${type} fade-in`;
  alertContainer.innerHTML = `
        <i class="fas ${getAlertIcon(type)}"></i>
        ${message}
    `;

  // Adiciona no topo da página
  const main = document.querySelector("main");
  main.insertBefore(alertContainer, main.firstChild);

  // Remove após 5 segundos
  setTimeout(() => {
    fadeOut(alertContainer);
  }, 5000);
}

/**
 * Retorna ícone apropriado para tipo de alerta
 */
function getAlertIcon(type) {
  const icons = {
    success: "fa-check-circle",
    error: "fa-exclamation-circle",
    warning: "fa-exclamation-triangle",
    info: "fa-info-circle",
  };

  return icons[type] || "fa-info-circle";
}

/**
 * Formatar tempo em formato legível
 */
function formatarTempo(segundos) {
  const horas = Math.floor(segundos / 3600);
  const minutos = Math.floor((segundos % 3600) / 60);
  const segs = segundos % 60;

  if (horas > 0) {
    return `${horas.toString().padStart(2, "0")}:${minutos
      .toString()
      .padStart(2, "0")}:${segs.toString().padStart(2, "0")}`;
  } else {
    return `${minutos.toString().padStart(2, "0")}:${segs
      .toString()
      .padStart(2, "0")}`;
  }
}

/**
 * Cronômetro para sessões de estudo
 */
class Cronometro {
  constructor(elementoId, dataInicio = null) {
    this.elemento = document.getElementById(elementoId);
    this.segundos = 0;
    this.intervalo = null;
    this.rodando = false;

    // Se há data de início, calcula tempo decorrido
    if (dataInicio) {
      const inicio = new Date(dataInicio);
      const agora = new Date();
      this.segundos = Math.floor((agora - inicio) / 1000);
    }

    this.atualizar();
  }

  iniciar() {
    if (!this.rodando) {
      this.rodando = true;
      this.intervalo = setInterval(() => {
        this.segundos++;
        this.atualizar();
      }, 1000);
    }
  }

  parar() {
    if (this.rodando) {
      this.rodando = false;
      clearInterval(this.intervalo);
    }
  }

  resetar() {
    this.parar();
    this.segundos = 0;
    this.atualizar();
  }

  atualizar() {
    if (this.elemento) {
      this.elemento.textContent = formatarTempo(this.segundos);
    }
  }

  getSegundos() {
    return this.segundos;
  }

  getMinutos() {
    return Math.floor(this.segundos / 60);
  }
}

/**
 * Função para alterar status de conteúdo via AJAX
 */
function alterarStatusConteudo(conteudoId, novoStatus) {
  const formData = new FormData();
  formData.append("id", conteudoId);
  formData.append("status", novoStatus);

  fetch("/conteudos/alterar-status", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.sucesso) {
        showAlert("success", data.mensagem);
        setTimeout(() => location.reload(), 1500);
      } else {
        showAlert("error", data.erro);
      }
    })
    .catch((error) => {
      console.error("Erro:", error);
      showAlert("error", "Erro ao alterar status");
    });
}

/**
 * Função para marcar conteúdo de meta como concluído
 */
function marcarConteudoConcluido(metaId, conteudoId) {
  const formData = new FormData();
  formData.append("meta_id", metaId);
  formData.append("conteudo_id", conteudoId);

  fetch("/metas/marcar-conteudo-concluido", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.sucesso) {
        showAlert("success", data.mensagem);
        setTimeout(() => location.reload(), 1500);
      } else {
        showAlert("error", data.erro);
      }
    })
    .catch((error) => {
      console.error("Erro:", error);
      showAlert("error", "Erro ao marcar conteúdo");
    });
}

/**
 * Validação de formulários em tempo real
 */
function validarFormulario(form) {
  const campos = form.querySelectorAll(
    "input[required], select[required], textarea[required]"
  );
  let valido = true;

  campos.forEach((campo) => {
    if (!campo.value.trim()) {
      campo.style.borderColor = "#dc3545";
      valido = false;
    } else {
      campo.style.borderColor = "#28a745";
    }
  });

  return valido;
}
