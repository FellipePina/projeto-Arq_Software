/**
 * Pomodoro Timer - Gerenciamento do temporizador Pomodoro
 */

class PomodoroTimer {
  constructor() {
    this.timer = null;
    this.timeRemaining = 0;
    this.isRunning = false;
    this.currentSession = null;
    this.sessionType = "foco"; // foco, pausa_curta, pausa_longa
    this.config = {
      foco: 25 * 60, // 25 minutos em segundos
      pausa_curta: 5 * 60, // 5 minutos
      pausa_longa: 15 * 60, // 15 minutos
    };
    this.pomodorosCompleted = 0;
    this.audioEnabled = true;

    this.init();
  }

  /**
   * Inicializa o timer
   */
  init() {
    this.loadConfig();
    this.setupEventListeners();
    this.updateDisplay();
  }

  /**
   * Carrega configurações do usuário
   */
  async loadConfig() {
    try {
      const response = await AjaxHelper.get("/configuracoes/dados");
      if (response.success && response.configuracoes) {
        this.config.foco = response.configuracoes.tempo_foco * 60;
        this.config.pausa_curta = response.configuracoes.tempo_pausa_curta * 60;
        this.config.pausa_longa = response.configuracoes.tempo_pausa_longa * 60;
      }
    } catch (error) {
      console.error("Erro ao carregar configurações:", error);
    }
  }

  /**
   * Configura event listeners
   */
  setupEventListeners() {
    // Botão iniciar/pausar
    const startBtn = document.getElementById("pomodoro-start");
    if (startBtn) {
      startBtn.addEventListener("click", () => this.toggle());
    }

    // Botão parar
    const stopBtn = document.getElementById("pomodoro-stop");
    if (stopBtn) {
      stopBtn.addEventListener("click", () => this.stop());
    }

    // Botões de tipo de sessão
    document.querySelectorAll("[data-session-type]").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const type = e.currentTarget.dataset.sessionType;
        this.changeSessionType(type);
      });
    });

    // Select de disciplina
    const disciplinaSelect = document.getElementById("disciplina-select");
    if (disciplinaSelect) {
      disciplinaSelect.addEventListener("change", (e) => {
        this.selectedDisciplina = e.target.value;
      });
    }
  }

  /**
   * Inicia ou pausa o timer
   */
  async toggle() {
    if (this.isRunning) {
      this.pause();
    } else {
      await this.start();
    }
  }

  /**
   * Inicia o timer
   */
  async start() {
    if (!this.selectedDisciplina) {
      AjaxHelper.showToast(
        "Selecione uma disciplina antes de iniciar",
        "warning"
      );
      return;
    }

    // Se não tem tempo restante, inicia nova sessão
    if (this.timeRemaining === 0) {
      this.timeRemaining = this.config[this.sessionType];

      // Cria sessão no servidor
      try {
        const response = await AjaxHelper.post("/pomodoro/iniciar", {
          disciplina_id: this.selectedDisciplina,
          tipo_sessao: this.sessionType,
        });

        if (response.success) {
          this.currentSession = response.sessao;
        } else {
          AjaxHelper.showToast(
            response.message || "Erro ao iniciar sessão",
            "error"
          );
          return;
        }
      } catch (error) {
        AjaxHelper.showToast("Erro ao iniciar sessão", "error");
        return;
      }
    }

    this.isRunning = true;
    this.updateButtons();

    this.timer = setInterval(() => {
      this.timeRemaining--;

      if (this.timeRemaining <= 0) {
        this.complete();
      }

      this.updateDisplay();
    }, 1000);
  }

  /**
   * Pausa o timer
   */
  pause() {
    this.isRunning = false;
    clearInterval(this.timer);
    this.updateButtons();
  }

  /**
   * Para o timer completamente
   */
  async stop() {
    if (!this.currentSession) return;

    const confirmed = await AjaxHelper.confirm(
      "Deseja realmente interromper esta sessão? O tempo não será contabilizado.",
      "Interromper Sessão"
    );

    if (!confirmed) return;

    this.pause();

    // Interrompe no servidor
    try {
      await AjaxHelper.post(`/pomodoro/${this.currentSession.id}/interromper`);
      AjaxHelper.showToast("Sessão interrompida", "info");
    } catch (error) {
      console.error("Erro ao interromper sessão:", error);
    }

    this.reset();
  }

  /**
   * Completa a sessão
   */
  async complete() {
    this.pause();

    // Finaliza no servidor
    if (this.currentSession) {
      try {
        const response = await AjaxHelper.post(
          `/pomodoro/${this.currentSession.id}/finalizar`
        );

        if (response.success) {
          AjaxHelper.showToast("Sessão concluída! +5 pontos 🎉", "success");

          // Atualiza estatísticas na página
          this.updateStatistics();

          // Atualiza gamificação no header
          if (window.updateGamificationHeader) {
            window.updateGamificationHeader();
          }
        }
      } catch (error) {
        console.error("Erro ao finalizar sessão:", error);
      }
    }

    // Toca som
    if (this.audioEnabled) {
      this.playNotificationSound();
    }

    // Notificação do navegador
    this.showNotification();

    // Incrementa contador de pomodoros
    if (this.sessionType === "foco") {
      this.pomodorosCompleted++;
    }

    // Sugere próxima sessão
    this.suggestNextSession();

    this.reset();
  }

  /**
   * Reseta o timer
   */
  reset() {
    this.timeRemaining = 0;
    this.currentSession = null;
    this.isRunning = false;
    clearInterval(this.timer);
    this.updateDisplay();
    this.updateButtons();
  }

  /**
   * Muda o tipo de sessão
   */
  changeSessionType(type) {
    if (this.isRunning) {
      AjaxHelper.showToast(
        "Pause o timer antes de mudar o tipo de sessão",
        "warning"
      );
      return;
    }

    this.sessionType = type;
    this.timeRemaining = this.config[type];
    this.updateDisplay();
    this.updateButtons();

    // Atualiza botões ativos
    document.querySelectorAll("[data-session-type]").forEach((btn) => {
      btn.classList.toggle("active", btn.dataset.sessionType === type);
    });
  }

  /**
   * Sugere próxima sessão baseado na regra Pomodoro
   */
  suggestNextSession() {
    let nextType;
    let message;

    if (this.sessionType === "foco") {
      // Após foco, sugere pausa
      if (this.pomodorosCompleted % 4 === 0) {
        nextType = "pausa_longa";
        message = "Hora de uma pausa longa! ☕";
      } else {
        nextType = "pausa_curta";
        message = "Hora de uma pausa curta! ☕";
      }
    } else {
      // Após pausa, sugere foco
      nextType = "foco";
      message = "Vamos focar novamente! 🎯";
    }

    AjaxHelper.showToast(message, "info");
    this.changeSessionType(nextType);
  }

  /**
   * Atualiza o display do timer
   */
  updateDisplay() {
    const minutes = Math.floor(this.timeRemaining / 60);
    const seconds = this.timeRemaining % 60;

    const display = document.getElementById("pomodoro-display");
    if (display) {
      display.textContent = `${minutes.toString().padStart(2, "0")}:${seconds
        .toString()
        .padStart(2, "0")}`;
    }

    // Atualiza progress circle
    const totalTime = this.config[this.sessionType];
    const progress = ((totalTime - this.timeRemaining) / totalTime) * 100;
    this.updateProgressCircle(progress);

    // Atualiza título da página
    if (this.isRunning) {
      document.title = `${minutes}:${seconds
        .toString()
        .padStart(2, "0")} - Pomodoro`;
    }
  }

  /**
   * Atualiza círculo de progresso
   */
  updateProgressCircle(progress) {
    const circle = document.getElementById("progress-circle");
    if (circle) {
      const circumference = 2 * Math.PI * 120; // raio do círculo
      const offset = circumference - (progress / 100) * circumference;
      circle.style.strokeDashoffset = offset;
    }
  }

  /**
   * Atualiza botões
   */
  updateButtons() {
    const startBtn = document.getElementById("pomodoro-start");
    const stopBtn = document.getElementById("pomodoro-stop");

    if (startBtn) {
      if (this.isRunning) {
        startBtn.innerHTML = '<i class="fas fa-pause mr-2"></i> Pausar';
        startBtn.classList.remove("btn-success");
        startBtn.classList.add("btn-warning");
      } else {
        startBtn.innerHTML = '<i class="fas fa-play mr-2"></i> Iniciar';
        startBtn.classList.remove("btn-warning");
        startBtn.classList.add("btn-success");
      }
    }

    if (stopBtn) {
      stopBtn.disabled = !this.currentSession;
    }
  }

  /**
   * Toca som de notificação
   */
  playNotificationSound() {
    const audio = new Audio("/assets/sounds/notification.mp3");
    audio.play().catch((e) => console.log("Erro ao tocar som:", e));
  }

  /**
   * Mostra notificação do navegador
   */
  async showNotification() {
    if (!("Notification" in window)) return;

    if (Notification.permission === "granted") {
      new Notification("Pomodoro Concluído! 🎉", {
        body: "Sua sessão foi finalizada com sucesso.",
        icon: "/favicon.ico",
      });
    } else if (Notification.permission !== "denied") {
      const permission = await Notification.requestPermission();
      if (permission === "granted") {
        this.showNotification();
      }
    }
  }

  /**
   * Atualiza estatísticas na página
   */
  async updateStatistics() {
    try {
      const response = await AjaxHelper.get("/pomodoro/estatisticas");
      if (response.success && response.estatisticas) {
        // Atualiza cards de estatísticas
        const stats = response.estatisticas;

        const totalElement = document.getElementById("total-sessoes");
        if (totalElement) {
          totalElement.textContent = stats.total_sessoes || 0;
        }

        const hojeElement = document.getElementById("sessoes-hoje");
        if (hojeElement) {
          hojeElement.textContent = stats.sessoes_hoje || 0;
        }

        const tempoElement = document.getElementById("tempo-total");
        if (tempoElement) {
          tempoElement.textContent = AjaxHelper.formatTime(
            stats.tempo_total || 0
          );
        }
      }
    } catch (error) {
      console.error("Erro ao atualizar estatísticas:", error);
    }
  }
}

// Inicializa quando o DOM estiver pronto
document.addEventListener("DOMContentLoaded", () => {
  if (document.getElementById("pomodoro-timer")) {
    window.pomodoroTimer = new PomodoroTimer();
  }
});
