/**
 * Chart Handler - Gerenciamento de gráficos com Chart.js
 */

class ChartHandler {
  constructor() {
    this.charts = {};
    this.defaultColors = [
      "#3b82f6", // blue
      "#10b981", // green
      "#f59e0b", // yellow
      "#ef4444", // red
      "#8b5cf6", // purple
      "#ec4899", // pink
      "#14b8a6", // teal
      "#f97316", // orange
    ];
  }

  /**
   * Cria gráfico de linha
   */
  createLineChart(canvasId, data, options = {}) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;

    const defaultOptions = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: "top",
        },
        tooltip: {
          mode: "index",
          intersect: false,
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0,
          },
        },
      },
    };

    // Destroy existing chart
    if (this.charts[canvasId]) {
      this.charts[canvasId].destroy();
    }

    this.charts[canvasId] = new Chart(ctx, {
      type: "line",
      data: this.prepareChartData(data),
      options: { ...defaultOptions, ...options },
    });

    return this.charts[canvasId];
  }

  /**
   * Cria gráfico de barras
   */
  createBarChart(canvasId, data, options = {}) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;

    const defaultOptions = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0,
          },
        },
      },
    };

    if (this.charts[canvasId]) {
      this.charts[canvasId].destroy();
    }

    this.charts[canvasId] = new Chart(ctx, {
      type: "bar",
      data: this.prepareChartData(data),
      options: { ...defaultOptions, ...options },
    });

    return this.charts[canvasId];
  }

  /**
   * Cria gráfico de pizza
   */
  createPieChart(canvasId, data, options = {}) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;

    const defaultOptions = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: "right",
        },
      },
    };

    if (this.charts[canvasId]) {
      this.charts[canvasId].destroy();
    }

    this.charts[canvasId] = new Chart(ctx, {
      type: "pie",
      data: this.prepareChartData(data),
      options: { ...defaultOptions, ...options },
    });

    return this.charts[canvasId];
  }

  /**
   * Cria gráfico de donut
   */
  createDoughnutChart(canvasId, data, options = {}) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;

    const defaultOptions = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: "right",
        },
      },
    };

    if (this.charts[canvasId]) {
      this.charts[canvasId].destroy();
    }

    this.charts[canvasId] = new Chart(ctx, {
      type: "doughnut",
      data: this.prepareChartData(data),
      options: { ...defaultOptions, ...options },
    });

    return this.charts[canvasId];
  }

  /**
   * Prepara dados para o Chart.js
   */
  prepareChartData(data) {
    if (!data.datasets) {
      // Dados simples: { labels: [], data: [] }
      return {
        labels: data.labels || [],
        datasets: [
          {
            data: data.data || [],
            backgroundColor: this.defaultColors,
            borderColor: this.defaultColors,
            borderWidth: 2,
          },
        ],
      };
    }

    // Dados complexos com múltiplos datasets
    const datasets = data.datasets.map((dataset, index) => ({
      label: dataset.label,
      data: dataset.data,
      backgroundColor: dataset.backgroundColor || this.defaultColors[index],
      borderColor: dataset.borderColor || this.defaultColors[index],
      borderWidth: dataset.borderWidth || 2,
      fill: dataset.fill !== undefined ? dataset.fill : false,
    }));

    return {
      labels: data.labels || [],
      datasets: datasets,
    };
  }

  /**
   * Atualiza dados do gráfico
   */
  updateChart(canvasId, data) {
    const chart = this.charts[canvasId];
    if (!chart) return;

    chart.data = this.prepareChartData(data);
    chart.update();
  }

  /**
   * Destrói gráfico
   */
  destroyChart(canvasId) {
    if (this.charts[canvasId]) {
      this.charts[canvasId].destroy();
      delete this.charts[canvasId];
    }
  }

  /**
   * Destrói todos os gráficos
   */
  destroyAll() {
    Object.keys(this.charts).forEach((canvasId) => {
      this.destroyChart(canvasId);
    });
  }

  /**
   * Carrega gráfico de Pomodoro diário
   */
  async loadPomodoroDiarioChart() {
    try {
      const response = await AjaxHelper.get("/relatorios/pomodoro-diario");

      if (response.success && response.dados) {
        this.createLineChart("chart-pomodoro-diario", {
          labels: response.dados.map((d) => d.data),
          datasets: [
            {
              label: "Sessões Concluídas",
              data: response.dados.map((d) => d.sessoes),
              borderColor: "#3b82f6",
              backgroundColor: "rgba(59, 130, 246, 0.1)",
              fill: true,
            },
          ],
        });
      }
    } catch (error) {
      console.error("Erro ao carregar gráfico de pomodoro:", error);
    }
  }

  /**
   * Carrega gráfico de disciplinas
   */
  async loadDisciplinasChart() {
    try {
      const response = await AjaxHelper.get("/relatorios/disciplinas");

      if (response.success && response.dados) {
        this.createDoughnutChart("chart-disciplinas", {
          labels: response.dados.map((d) => d.nome),
          data: response.dados.map((d) => d.tempo_total),
        });
      }
    } catch (error) {
      console.error("Erro ao carregar gráfico de disciplinas:", error);
    }
  }

  /**
   * Carrega gráfico de tarefas
   */
  async loadTarefasChart() {
    try {
      const response = await AjaxHelper.get("/relatorios/tarefas");

      if (response.success && response.dados) {
        this.createBarChart("chart-tarefas", {
          labels: ["Pendentes", "Em Andamento", "Concluídas"],
          datasets: [
            {
              label: "Tarefas",
              data: [
                response.dados.pendentes,
                response.dados.em_andamento,
                response.dados.concluidas,
              ],
              backgroundColor: ["#f59e0b", "#3b82f6", "#10b981"],
            },
          ],
        });
      }
    } catch (error) {
      console.error("Erro ao carregar gráfico de tarefas:", error);
    }
  }

  /**
   * Carrega gráfico de progresso semanal
   */
  async loadProgressoSemanalChart() {
    try {
      const response = await AjaxHelper.get("/relatorios/progresso-semanal");

      if (response.success && response.dados) {
        this.createLineChart("chart-progresso-semanal", {
          labels: response.dados.map((d) => d.dia),
          datasets: [
            {
              label: "Horas de Estudo",
              data: response.dados.map((d) => d.horas),
              borderColor: "#10b981",
              backgroundColor: "rgba(16, 185, 129, 0.1)",
              fill: true,
            },
            {
              label: "Meta Diária",
              data: response.dados.map((d) => d.meta),
              borderColor: "#f59e0b",
              borderDash: [5, 5],
              fill: false,
            },
          ],
        });
      }
    } catch (error) {
      console.error("Erro ao carregar gráfico de progresso:", error);
    }
  }

  /**
   * Exporta gráfico como imagem
   */
  exportChart(canvasId, filename = "chart.png") {
    const chart = this.charts[canvasId];
    if (!chart) return;

    const url = chart.toBase64Image();
    const link = document.createElement("a");
    link.download = filename;
    link.href = url;
    link.click();
  }

  /**
   * Redimensiona todos os gráficos
   */
  resizeAll() {
    Object.values(this.charts).forEach((chart) => {
      chart.resize();
    });
  }
}

// Instância global
window.chartHandler = new ChartHandler();

// Auto-load charts on page load
document.addEventListener("DOMContentLoaded", () => {
  // Página de relatórios
  if (document.getElementById("chart-pomodoro-diario")) {
    chartHandler.loadPomodoroDiarioChart();
  }

  if (document.getElementById("chart-disciplinas")) {
    chartHandler.loadDisciplinasChart();
  }

  if (document.getElementById("chart-tarefas")) {
    chartHandler.loadTarefasChart();
  }

  if (document.getElementById("chart-progresso-semanal")) {
    chartHandler.loadProgressoSemanalChart();
  }

  // Redimensiona ao mudar tamanho da janela
  window.addEventListener("resize", () => {
    chartHandler.resizeAll();
  });
});
