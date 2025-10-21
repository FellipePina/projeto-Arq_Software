/**
 * Helpers AJAX - Funções auxiliares para requisições assíncronas
 */

class AjaxHelper {
  /**
   * Obtém o token CSRF da meta tag
   */
  static getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute("content") : "";
  }

  /**
   * Faz requisição GET
   */
  static async get(url) {
    try {
      const response = await fetch(url, {
        method: "GET",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      return await response.json();
    } catch (error) {
      console.error("Erro na requisição GET:", error);
      throw error;
    }
  }

  /**
   * Faz requisição POST
   */
  static async post(url, data = {}) {
    try {
      const formData = new FormData();
      formData.append("csrf_token", this.getCsrfToken());

      // Adiciona dados ao FormData
      Object.keys(data).forEach((key) => {
        if (data[key] instanceof FileList) {
          Array.from(data[key]).forEach((file) => formData.append(key, file));
        } else {
          formData.append(key, data[key]);
        }
      });

      const response = await fetch(url, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
        body: formData,
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      return await response.json();
    } catch (error) {
      console.error("Erro na requisição POST:", error);
      throw error;
    }
  }

  /**
   * Faz requisição com JSON
   */
  static async postJson(url, data = {}) {
    try {
      const response = await fetch(url, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-Token": this.getCsrfToken(),
        },
        body: JSON.stringify(data),
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      return await response.json();
    } catch (error) {
      console.error("Erro na requisição POST JSON:", error);
      throw error;
    }
  }

  /**
   * Faz requisição PUT
   */
  static async put(url, data = {}) {
    try {
      const response = await fetch(url, {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-Token": this.getCsrfToken(),
        },
        body: JSON.stringify(data),
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      return await response.json();
    } catch (error) {
      console.error("Erro na requisição PUT:", error);
      throw error;
    }
  }

  /**
   * Faz requisição DELETE
   */
  static async delete(url) {
    try {
      const response = await fetch(url, {
        method: "DELETE",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-Token": this.getCsrfToken(),
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      return await response.json();
    } catch (error) {
      console.error("Erro na requisição DELETE:", error);
      throw error;
    }
  }

  /**
   * Mostra notificação toast
   */
  static showToast(message, type = "info") {
    const toastContainer =
      document.getElementById("toast-container") || this.createToastContainer();

    const toast = document.createElement("div");
    toast.className = `toast toast-${type} animate-slide-in`;
    toast.innerHTML = `
      <div class="flex items-center">
        <i class="fas fa-${this.getToastIcon(type)} mr-3"></i>
        <span>${message}</span>
      </div>
      <button type="button" class="ml-auto" onclick="this.parentElement.remove()">
        <i class="fas fa-times"></i>
      </button>
    `;

    toastContainer.appendChild(toast);

    // Remove automaticamente após 5 segundos
    setTimeout(() => {
      toast.style.transition = "opacity 0.5s";
      toast.style.opacity = "0";
      setTimeout(() => toast.remove(), 500);
    }, 5000);
  }

  /**
   * Cria container para toasts
   */
  static createToastContainer() {
    const container = document.createElement("div");
    container.id = "toast-container";
    container.className = "fixed top-4 right-4 z-50 space-y-2";
    document.body.appendChild(container);
    return container;
  }

  /**
   * Retorna ícone baseado no tipo
   */
  static getToastIcon(type) {
    const icons = {
      success: "check-circle",
      error: "exclamation-circle",
      warning: "exclamation-triangle",
      info: "info-circle",
    };
    return icons[type] || "info-circle";
  }

  /**
   * Mostra loading spinner
   */
  static showLoading(element) {
    if (!element) return;

    element.disabled = true;
    element.dataset.originalContent = element.innerHTML;
    element.innerHTML = `
      <div class="spinner spinner-sm mr-2"></div>
      Carregando...
    `;
  }

  /**
   * Remove loading spinner
   */
  static hideLoading(element) {
    if (!element) return;

    element.disabled = false;
    if (element.dataset.originalContent) {
      element.innerHTML = element.dataset.originalContent;
      delete element.dataset.originalContent;
    }
  }

  /**
   * Confirma ação com modal
   */
  static async confirm(message, title = "Confirmar Ação") {
    return new Promise((resolve) => {
      const modal = document.createElement("div");
      modal.className = "modal-overlay";
      modal.innerHTML = `
        <div class="modal-container flex items-center justify-center">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title">${title}</h3>
              <button type="button" class="modal-close" data-action="cancel">
                <i class="fas fa-times"></i>
              </button>
            </div>
            <div class="modal-body">
              <p>${message}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline" data-action="cancel">
                Cancelar
              </button>
              <button type="button" class="btn btn-danger" data-action="confirm">
                Confirmar
              </button>
            </div>
          </div>
        </div>
      `;

      document.body.appendChild(modal);

      modal.addEventListener("click", (e) => {
        const action = e.target.closest("[data-action]")?.dataset.action;
        if (action === "confirm") {
          resolve(true);
          modal.remove();
        } else if (action === "cancel" || e.target === modal) {
          resolve(false);
          modal.remove();
        }
      });
    });
  }

  /**
   * Serializa formulário
   */
  static serializeForm(form) {
    const data = {};
    const formData = new FormData(form);

    for (const [key, value] of formData.entries()) {
      if (data[key]) {
        if (Array.isArray(data[key])) {
          data[key].push(value);
        } else {
          data[key] = [data[key], value];
        }
      } else {
        data[key] = value;
      }
    }

    return data;
  }

  /**
   * Debounce para evitar múltiplas chamadas
   */
  static debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  /**
   * Formata data para exibição
   */
  static formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString("pt-BR");
  }

  /**
   * Formata data e hora para exibição
   */
  static formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString("pt-BR");
  }

  /**
   * Formata tempo em segundos para HH:MM:SS
   */
  static formatTime(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;

    return [hours, minutes, secs]
      .map((v) => v.toString().padStart(2, "0"))
      .join(":");
  }
}

// Exporta para uso global
window.AjaxHelper = AjaxHelper;
