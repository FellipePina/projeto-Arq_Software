/**
 * PERFORMANCE JAVASCRIPT
 * Sistema de Estudos - Otimizações para Hardware Fraco
 *
 * Principais recursos:
 * - Debounce e Throttle
 * - Lazy Loading com Intersection Observer
 * - Detecção de hardware fraco
 * - FPS Monitor
 * - Virtualização de listas
 * - Cache de DOM queries
 */

(function () {
  "use strict";

  // ============================================
  // CONFIGURAÇÕES GLOBAIS
  // ============================================
  const PerformanceConfig = {
    // Limiares de FPS
    FPS_THRESHOLD_LOW: 30,
    FPS_THRESHOLD_MEDIUM: 45,
    FPS_THRESHOLD_HIGH: 55,

    // Configurações de lazy load
    LAZY_LOAD_MARGIN: "50px",
    LAZY_LOAD_THRESHOLD: 0.01,

    // Debounce/Throttle delays
    DEBOUNCE_DELAY: 300,
    THROTTLE_DELAY: 16, // ~60fps

    // Detecção de hardware
    AUTO_PERFORMANCE_MODE: true,
    SAVE_PREFERENCES: true,
  };

  // ============================================
  // UTILITÁRIOS DE PERFORMANCE
  // ============================================

  /**
   * Debounce - Executa função após delay sem novas chamadas
   */
  function debounce(func, delay = PerformanceConfig.DEBOUNCE_DELAY) {
    let timeoutId;
    return function (...args) {
      clearTimeout(timeoutId);
      timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
  }

  /**
   * Throttle - Limita execução de função a um intervalo
   */
  function throttle(func, delay = PerformanceConfig.THROTTLE_DELAY) {
    let lastCall = 0;
    return function (...args) {
      const now = Date.now();
      if (now - lastCall >= delay) {
        lastCall = now;
        return func.apply(this, args);
      }
    };
  }

  /**
   * RequestAnimationFrame wrapper para animações suaves
   */
  function rafThrottle(func) {
    let rafId = null;
    return function (...args) {
      if (rafId === null) {
        rafId = requestAnimationFrame(() => {
          func.apply(this, args);
          rafId = null;
        });
      }
    };
  }

  // ============================================
  // DETECÇÃO DE HARDWARE E PERFORMANCE
  // ============================================

  class PerformanceDetector {
    constructor() {
      this.fps = 60;
      this.fpsHistory = [];
      this.isLowEnd = false;
      this.performanceMode = false;

      this.init();
    }

    init() {
      this.detectHardware();
      this.detectUserPreferences();
      this.startFPSMonitoring();

      // Verifica FPS após 3 segundos
      setTimeout(() => {
        this.analyzePerformance();
      }, 3000);
    }

    /**
     * Detecta características do hardware
     */
    detectHardware() {
      // Número de cores
      const cores = navigator.hardwareConcurrency || 2;

      // Memória (se disponível)
      const memory = navigator.deviceMemory || 4;

      // Tipo de conexão
      const connection =
        navigator.connection ||
        navigator.mozConnection ||
        navigator.webkitConnection;
      const slowConnection =
        connection &&
        (connection.effectiveType === "slow-2g" ||
          connection.effectiveType === "2g" ||
          connection.effectiveType === "3g");

      // Considera hardware fraco se:
      // - Menos de 4 cores
      // - Menos de 4GB RAM
      // - Conexão lenta
      this.isLowEnd = cores < 4 || memory < 4 || slowConnection;

      if (this.isLowEnd) {
        console.log("🐌 Hardware fraco detectado - Ativando modo performance");
        this.enablePerformanceMode(true);
      }
    }

    /**
     * Detecta preferências do usuário
     */
    detectUserPreferences() {
      // Respeita prefers-reduced-motion
      const prefersReducedMotion = window.matchMedia(
        "(prefers-reduced-motion: reduce)"
      ).matches;
      if (prefersReducedMotion) {
        this.enablePerformanceMode(true);
      }

      // Verifica preferência salva
      const savedPreference = localStorage.getItem("performanceMode");
      if (savedPreference === "true") {
        this.enablePerformanceMode(true);
      }
    }

    /**
     * Monitora FPS em tempo real
     */
    startFPSMonitoring() {
      let lastTime = performance.now();
      let frames = 0;

      const measureFPS = () => {
        frames++;
        const currentTime = performance.now();

        if (currentTime >= lastTime + 1000) {
          this.fps = Math.round((frames * 1000) / (currentTime - lastTime));
          this.fpsHistory.push(this.fps);

          // Mantém apenas últimos 10 valores
          if (this.fpsHistory.length > 10) {
            this.fpsHistory.shift();
          }

          frames = 0;
          lastTime = currentTime;

          // Atualiza display se existir
          this.updateFPSDisplay();
        }

        requestAnimationFrame(measureFPS);
      };

      requestAnimationFrame(measureFPS);
    }

    /**
     * Analisa performance e ativa modo se necessário
     */
    analyzePerformance() {
      if (this.fpsHistory.length === 0) return;

      const avgFPS =
        this.fpsHistory.reduce((a, b) => a + b, 0) / this.fpsHistory.length;

      if (avgFPS < PerformanceConfig.FPS_THRESHOLD_LOW) {
        console.log(
          `⚠️ FPS baixo detectado (${avgFPS.toFixed(
            1
          )}fps) - Ativando modo performance`
        );
        this.enablePerformanceMode(true);
      }
    }

    /**
     * Ativa/desativa modo performance
     */
    enablePerformanceMode(enabled) {
      this.performanceMode = enabled;

      if (enabled) {
        document.body.classList.add("performance-mode");

        // Desabilita animações complexas
        this.disableHeavyAnimations();

        // Salva preferência
        if (PerformanceConfig.SAVE_PREFERENCES) {
          localStorage.setItem("performanceMode", "true");
        }
      } else {
        document.body.classList.remove("performance-mode");
        localStorage.setItem("performanceMode", "false");
      }

      // Dispara evento custom
      window.dispatchEvent(
        new CustomEvent("performanceModeChanged", {
          detail: { enabled },
        })
      );
    }

    /**
     * Desabilita animações pesadas
     */
    disableHeavyAnimations() {
      // Remove backdrop-filter
      const glassElements = document.querySelectorAll(
        '[class*="glass"], [class*="backdrop"]'
      );
      glassElements.forEach((el) => {
        el.style.backdropFilter = "none";
        el.style.webkitBackdropFilter = "none";
      });

      // Simplifica sombras
      const shadowElements = document.querySelectorAll('[class*="shadow"]');
      shadowElements.forEach((el) => {
        const currentShadow = window.getComputedStyle(el).boxShadow;
        if (currentShadow !== "none") {
          el.style.boxShadow = "0 2px 8px rgba(0, 0, 0, 0.08)";
        }
      });
    }

    /**
     * Atualiza display de FPS (se existir)
     */
    updateFPSDisplay() {
      const fpsDisplay = document.querySelector(".fps-monitor");
      if (fpsDisplay) {
        fpsDisplay.textContent = `${this.fps} FPS`;

        // Cor baseada em performance
        if (this.fps < PerformanceConfig.FPS_THRESHOLD_LOW) {
          fpsDisplay.style.color = "#f00";
        } else if (this.fps < PerformanceConfig.FPS_THRESHOLD_MEDIUM) {
          fpsDisplay.style.color = "#ff0";
        } else {
          fpsDisplay.style.color = "#0f0";
        }
      }
    }

    /**
     * Retorna FPS atual
     */
    getCurrentFPS() {
      return this.fps;
    }

    /**
     * Retorna se está em modo performance
     */
    isPerformanceMode() {
      return this.performanceMode;
    }
  }

  // ============================================
  // LAZY LOADING COM INTERSECTION OBSERVER
  // ============================================

  class LazyLoader {
    constructor(selector = "[data-lazy]", options = {}) {
      this.selector = selector;
      this.options = {
        rootMargin: options.rootMargin || PerformanceConfig.LAZY_LOAD_MARGIN,
        threshold: options.threshold || PerformanceConfig.LAZY_LOAD_THRESHOLD,
      };

      this.observer = null;
      this.init();
    }

    init() {
      if (!("IntersectionObserver" in window)) {
        // Fallback para browsers antigos
        this.loadAll();
        return;
      }

      this.observer = new IntersectionObserver(
        this.handleIntersection.bind(this),
        this.options
      );

      this.observe();
    }

    observe() {
      const elements = document.querySelectorAll(this.selector);
      elements.forEach((el) => this.observer.observe(el));
    }

    handleIntersection(entries) {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          this.loadElement(entry.target);
          this.observer.unobserve(entry.target);
        }
      });
    }

    loadElement(element) {
      // Carrega imagem
      if (element.dataset.lazySrc) {
        element.src = element.dataset.lazySrc;
        element.removeAttribute("data-lazy-src");
      }

      // Carrega background
      if (element.dataset.lazyBg) {
        element.style.backgroundImage = `url(${element.dataset.lazyBg})`;
        element.removeAttribute("data-lazy-bg");
      }

      // Carrega conteúdo
      if (element.dataset.lazyContent) {
        element.innerHTML = element.dataset.lazyContent;
        element.removeAttribute("data-lazy-content");
      }

      element.classList.add("lazy-loaded");
    }

    loadAll() {
      const elements = document.querySelectorAll(this.selector);
      elements.forEach((el) => this.loadElement(el));
    }

    refresh() {
      this.observe();
    }
  }

  // ============================================
  // VIRTUALIZAÇÃO DE LISTAS
  // ============================================

  class VirtualList {
    constructor(container, items, renderItem, itemHeight = 60) {
      this.container = container;
      this.items = items;
      this.renderItem = renderItem;
      this.itemHeight = itemHeight;

      this.visibleStart = 0;
      this.visibleEnd = 0;
      this.scrollTop = 0;

      this.init();
    }

    init() {
      // Cria container de scroll
      this.scrollContainer = document.createElement("div");
      this.scrollContainer.style.height = `${
        this.items.length * this.itemHeight
      }px`;
      this.scrollContainer.style.position = "relative";

      // Cria container de itens visíveis
      this.itemsContainer = document.createElement("div");
      this.itemsContainer.style.position = "absolute";
      this.itemsContainer.style.top = "0";
      this.itemsContainer.style.left = "0";
      this.itemsContainer.style.right = "0";

      this.scrollContainer.appendChild(this.itemsContainer);
      this.container.appendChild(this.scrollContainer);

      // Event listener para scroll
      this.container.addEventListener(
        "scroll",
        throttle(() => {
          this.handleScroll();
        }, 16)
      );

      // Renderização inicial
      this.handleScroll();
    }

    handleScroll() {
      this.scrollTop = this.container.scrollTop;

      const containerHeight = this.container.clientHeight;
      const buffer = 3; // Renderiza 3 itens extras acima e abaixo

      this.visibleStart = Math.max(
        0,
        Math.floor(this.scrollTop / this.itemHeight) - buffer
      );
      this.visibleEnd = Math.min(
        this.items.length,
        Math.ceil((this.scrollTop + containerHeight) / this.itemHeight) + buffer
      );

      this.render();
    }

    render() {
      const fragment = document.createDocumentFragment();

      for (let i = this.visibleStart; i < this.visibleEnd; i++) {
        const item = this.items[i];
        const element = this.renderItem(item, i);
        element.style.position = "absolute";
        element.style.top = `${i * this.itemHeight}px`;
        element.style.height = `${this.itemHeight}px`;
        fragment.appendChild(element);
      }

      this.itemsContainer.innerHTML = "";
      this.itemsContainer.appendChild(fragment);
    }

    updateItems(newItems) {
      this.items = newItems;
      this.scrollContainer.style.height = `${
        this.items.length * this.itemHeight
      }px`;
      this.handleScroll();
    }
  }

  // ============================================
  // CACHE DE DOM QUERIES
  // ============================================

  class DOMCache {
    constructor() {
      this.cache = new Map();
    }

    query(selector) {
      if (!this.cache.has(selector)) {
        this.cache.set(selector, document.querySelector(selector));
      }
      return this.cache.get(selector);
    }

    queryAll(selector) {
      if (!this.cache.has(selector)) {
        this.cache.set(selector, document.querySelectorAll(selector));
      }
      return this.cache.get(selector);
    }

    clear(selector = null) {
      if (selector) {
        this.cache.delete(selector);
      } else {
        this.cache.clear();
      }
    }
  }

  // ============================================
  // INICIALIZAÇÃO
  // ============================================

  // Espera DOM estar pronto
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initPerformance);
  } else {
    initPerformance();
  }

  function initPerformance() {
    // Inicializa detector de performance
    window.performanceDetector = new PerformanceDetector();

    // Inicializa lazy loading
    window.lazyLoader = new LazyLoader();

    // Inicializa cache DOM
    window.domCache = new DOMCache();

    // Expõe utilitários globalmente
    window.PerformanceUtils = {
      debounce,
      throttle,
      rafThrottle,
      VirtualList,
      LazyLoader,
      DOMCache,
    };

    console.log("✅ Sistema de Performance inicializado");

    // Mostra FPS monitor se em desenvolvimento
    if (
      window.location.hostname === "localhost" ||
      window.location.hostname === "127.0.0.1"
    ) {
      showFPSMonitor();
    }
  }

  /**
   * Mostra monitor de FPS
   */
  function showFPSMonitor() {
    const monitor = document.createElement("div");
    monitor.className = "fps-monitor";
    monitor.textContent = "-- FPS";
    document.body.appendChild(monitor);
  }

  // ============================================
  // TOGGLE MANUAL DE MODO PERFORMANCE
  // ============================================

  /**
   * Função global para toggle manual
   */
  window.togglePerformanceMode = function () {
    if (window.performanceDetector) {
      const currentMode = window.performanceDetector.isPerformanceMode();
      window.performanceDetector.enablePerformanceMode(!currentMode);
      return !currentMode;
    }
    return false;
  };

  // ============================================
  // OTIMIZAÇÕES ESPECÍFICAS
  // ============================================

  /**
   * Otimiza event listeners de scroll
   */
  window.addEventListener(
    "scroll",
    throttle(function () {
      // Adiciona classe quando scrolled
      if (window.scrollY > 50) {
        document.body.classList.add("scrolled");
      } else {
        document.body.classList.remove("scrolled");
      }
    }, 100),
    { passive: true }
  );

  /**
   * Otimiza event listeners de resize
   */
  window.addEventListener(
    "resize",
    debounce(function () {
      // Atualiza variáveis CSS se necessário
      document.documentElement.style.setProperty(
        "--vh",
        `${window.innerHeight * 0.01}px`
      );
    }, 150)
  );

  /**
   * Previne memory leaks com event listeners
   */
  window.addEventListener("beforeunload", function () {
    // Limpa cache
    if (window.domCache) {
      window.domCache.clear();
    }

    // Para observer de lazy loading
    if (window.lazyLoader && window.lazyLoader.observer) {
      window.lazyLoader.observer.disconnect();
    }
  });
})();
