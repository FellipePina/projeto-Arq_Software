<!DOCTYPE html>
<html lang="pt-BR" class="h-full scroll-smooth antialiased">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?? '' ?>">
  <title><?= $titulo ?? 'Sistema de Estudos' ?> - Auxílio Estudos</title>

  <!-- Google Fonts - Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Tailwind Config -->
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
          },
          colors: {
            primary: {
              50: '#f0f9ff',
              100: '#e0f2fe',
              200: '#bae6fd',
              300: '#7dd3fc',
              400: '#38bdf8',
              500: '#0ea5e9',
              600: '#0284c7',
              700: '#0369a1',
              800: '#075985',
              900: '#0c4a6e',
            },
            accent: {
              purple: '#8b5cf6',
              pink: '#ec4899',
              orange: '#f97316',
            }
          },
          boxShadow: {
            'soft': '0 2px 8px 0 rgb(0 0 0 / 0.05)',
            'soft-lg': '0 10px 25px -5px rgb(0 0 0 / 0.08)',
            'inner-soft': 'inset 0 2px 4px 0 rgb(0 0 0 / 0.05)',
          },
          backdropBlur: {
            xs: '2px',
          }
        }
      }
    }
  </script>

  <!-- Phosphor Icons (alternativa moderna ao Font Awesome) -->
  <script src="https://unpkg.com/@phosphor-icons/web"></script>

  <!-- Font Awesome como fallback -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Chart.js para gráficos -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <!-- FullCalendar para calendário -->
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/main.min.css' rel='stylesheet' />
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js'></script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/assets/css/components.css">

  <style>
    /* Estilo moderno e minimalista */
    * {
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    body {
      background: linear-gradient(to bottom, #ffffff 0%, #f8fafc 100%);
    }

    /* Glassmorphism */
    .glass {
      background: rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    /* Animações suaves */
    @keyframes slideInUp {
      from {
        transform: translateY(10px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    @keyframes scaleIn {
      from {
        transform: scale(0.95);
        opacity: 0;
      }

      to {
        transform: scale(1);
        opacity: 1;
      }
    }

    .animate-slide-in-up {
      animation: slideInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .animate-fade-in {
      animation: fadeIn 0.3s ease-out;
    }

    .animate-scale-in {
      animation: scaleIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Scrollbar minimalista */
    ::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }

    ::-webkit-scrollbar-track {
      background: transparent;
    }

    ::-webkit-scrollbar-thumb {
      background: rgba(0, 0, 0, 0.1);
      border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: rgba(0, 0, 0, 0.2);
    }

    /* Links de navegação modernos */
    .nav-link {
      position: relative;
      display: inline-flex;
      align-items: center;
      padding: 0.5rem 0.75rem;
      font-size: 0.875rem;
      font-weight: 500;
      color: #64748b;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      border-radius: 0.5rem;
    }

    .nav-link:hover {
      color: #0284c7;
      background: #f0f9ff;
    }

    .nav-link.active {
      color: #0284c7;
      background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
      font-weight: 600;
    }

    .nav-link.active::before {
      content: '';
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      width: 3px;
      height: 60%;
      background: #0284c7;
      border-radius: 0 2px 2px 0;
    }

    /* Cards modernos */
    .modern-card {
      background: white;
      border-radius: 16px;
      border: 1px solid rgba(0, 0, 0, 0.06);
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .modern-card:hover {
      border-color: rgba(0, 0, 0, 0.1);
      box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.08);
      transform: translateY(-2px);
    }

    /* Botões modernos */
    .btn-modern {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.625rem 1.25rem;
      font-size: 0.875rem;
      font-weight: 500;
      border-radius: 0.75rem;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      border: none;
      cursor: pointer;
    }

    .btn-modern:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-modern:active {
      transform: translateY(0);
    }

    /* Badge modernos */
    .badge-modern {
      display: inline-flex;
      align-items: center;
      padding: 0.25rem 0.75rem;
      font-size: 0.75rem;
      font-weight: 600;
      border-radius: 999px;
      letter-spacing: 0.025em;
    }

    /* Flash messages elegantes */
    .flash-message {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1rem 1.25rem;
      margin-bottom: 1rem;
      border-radius: 12px;
      font-size: 0.875rem;
      font-weight: 500;
      animation: slideInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
  </style>
</head>

<body class="h-full bg-gradient-to-b from-white to-gray-50">
  <div class="min-h-full">
    <!-- Navbar moderno e minimalista -->
    <nav class="sticky top-0 z-50 glass border-b border-gray-200/50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <!-- Logo e Menu Principal -->
          <div class="flex items-center space-x-8">
            <a href="/dashboard" class="flex items-center space-x-2 group">
              <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                <i class="ph ph-graduation-cap text-white text-lg"></i>
              </div>
              <span class="text-lg font-semibold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                Auxílio Estudos
              </span>
            </a>

            <!-- Menu Desktop -->
            <div class="hidden lg:flex lg:space-x-1">
              <a href="/dashboard" class="nav-link <?= ($active ?? '') === 'dashboard' ? 'active' : '' ?>">
                <i class="ph ph-house text-base"></i>
                <span>Dashboard</span>
              </a>
              <a href="/disciplinas" class="nav-link <?= ($active ?? '') === 'disciplinas' ? 'active' : '' ?>">
                <i class="ph ph-books text-base"></i>
                <span>Disciplinas</span>
              </a>
              <a href="/tarefas" class="nav-link <?= ($active ?? '') === 'tarefas' ? 'active' : '' ?>">
                <i class="ph ph-check-square text-base"></i>
                <span>Tarefas</span>
              </a>
              <a href="/pomodoro" class="nav-link <?= ($active ?? '') === 'pomodoro' ? 'active' : '' ?>">
                <i class="ph ph-timer text-base"></i>
                <span>Pomodoro</span>
              </a>
              <a href="/calendario" class="nav-link <?= ($active ?? '') === 'calendario' ? 'active' : '' ?>">
                <i class="ph ph-calendar text-base"></i>
                <span>Calendário</span>
              </a>
              <a href="/anotacoes" class="nav-link <?= ($active ?? '') === 'anotacoes' ? 'active' : '' ?>">
                <i class="ph ph-note text-base"></i>
                <span>Anotações</span>
              </a>
              <a href="/relatorios" class="nav-link <?= ($active ?? '') === 'relatorios' ? 'active' : '' ?>">
                <i class="ph ph-chart-bar text-base"></i>
                <span>Relatórios</span>
              </a>
            </div>
          </div>

          <!-- Menu Direito -->
          <div class="flex items-center space-x-3">
            <!-- Gamificação - Design moderno -->
            <a href="/gamificacao" class="hidden md:flex items-center space-x-3 px-3 py-2 rounded-xl hover:bg-gray-50 transition-all duration-200 group">
              <div class="flex items-center space-x-2">
                <div class="flex items-center gap-1.5 px-2.5 py-1 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg group-hover:from-yellow-100 group-hover:to-orange-100 transition-colors">
                  <i class="ph ph-trophy text-yellow-600 text-sm"></i>
                  <span class="text-sm font-semibold text-yellow-700" id="pontos-header">0</span>
                </div>
                <div class="flex items-center gap-1.5 px-2.5 py-1 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg group-hover:from-blue-100 group-hover:to-cyan-100 transition-colors">
                  <i class="ph ph-star text-blue-600 text-sm"></i>
                  <span class="text-xs font-medium text-blue-700" id="nivel-header">1</span>
                </div>
                <div class="flex items-center gap-1.5 px-2.5 py-1 bg-gradient-to-r from-orange-50 to-red-50 rounded-lg group-hover:from-orange-100 group-hover:to-red-100 transition-colors">
                  <i class="ph ph-fire text-orange-600 text-sm"></i>
                  <span class="text-xs font-medium text-orange-700" id="streak-header">0</span>
                </div>
              </div>
            </a>

            <!-- Notificações -->
            <button type="button" class="relative p-2 rounded-lg text-gray-600 hover:text-primary-600 hover:bg-gray-50 transition-all duration-200">
              <i class="ph ph-bell text-xl"></i>
              <span class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white animate-pulse"></span>
            </button>

            <!-- Menu do Usuário - Design moderno -->
            <div class="relative" x-data="{ open: false }">
              <button @click="open = !open" type="button" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-50 transition-all duration-200 group">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center ring-2 ring-offset-2 ring-transparent group-hover:ring-primary-200 transition-all">
                  <span class="text-sm font-semibold text-white">
                    <?= strtoupper(substr($_SESSION['usuario_nome'] ?? 'U', 0, 1)) ?>
                  </span>
                </div>
                <span class="hidden md:block text-sm font-medium text-gray-700"><?= $_SESSION['usuario_nome'] ?? 'Usuário' ?></span>
                <i class="ph ph-caret-down text-xs text-gray-500"></i>
              </button>

              <div x-show="open" @click.away="open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 mt-2 w-56 rounded-xl glass border border-gray-100 shadow-soft-lg z-50 overflow-hidden"
                style="display: none;">
                <div class="p-2">
                  <a href="/perfil" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="ph ph-user-circle text-lg text-gray-400"></i>
                    <span>Meu Perfil</span>
                  </a>
                  <a href="/configuracoes" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="ph ph-gear text-lg text-gray-400"></i>
                    <span>Configurações</span>
                  </a>
                  <a href="/gamificacao" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="ph ph-trophy text-lg text-gray-400"></i>
                    <span>Gamificação</span>
                  </a>
                  <div class="my-1 border-t border-gray-100"></div>
                  <a href="/logout" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm text-red-600 hover:bg-red-50 transition-colors">
                    <i class="ph ph-sign-out text-lg"></i>
                    <span>Sair</span>
                  </a>
                </div>
              </div>
            </div>

            <!-- Botão Menu Mobile -->
            <button type="button" class="lg:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors" id="mobile-menu-btn">
              <i class="ph ph-list text-xl"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Menu Mobile -->
      <div class="lg:hidden hidden border-t border-gray-100" id="mobile-menu">
        <div class="p-4 space-y-1">
          <a href="/dashboard" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="ph ph-house text-lg"></i>
            <span>Dashboard</span>
          </a>
          <a href="/disciplinas" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="ph ph-books text-lg"></i>
            <span>Disciplinas</span>
          </a>
          <a href="/tarefas" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="ph ph-check-square text-lg"></i>
            <span>Tarefas</span>
          </a>
          <a href="/pomodoro" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="ph ph-timer text-lg"></i>
            <span>Pomodoro</span>
          </a>
          <a href="/calendario" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="ph ph-calendar text-lg"></i>
            <span>Calendário</span>
          </a>
          <a href="/anotacoes" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="ph ph-note text-lg"></i>
            <span>Anotações</span>
          </a>
          <a href="/relatorios" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="ph ph-chart-bar text-lg"></i>
            <span>Relatórios</span>
          </a>
        </div>
      </div>
    </nav>

    <!-- Flash Messages - Design moderno -->
    <?php if (isset($_SESSION['flash_messages']) && !empty($_SESSION['flash_messages'])): ?>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <?php foreach ($_SESSION['flash_messages'] as $message): ?>
          <?php
          $icons = [
            'success' => 'ph-check-circle',
            'error' => 'ph-x-circle',
            'warning' => 'ph-warning-circle',
            'info' => 'ph-info'
          ];
          $colors = [
            'success' => 'from-emerald-50 to-green-50 border-emerald-200 text-emerald-800',
            'error' => 'from-red-50 to-rose-50 border-red-200 text-red-800',
            'warning' => 'from-amber-50 to-yellow-50 border-amber-200 text-amber-800',
            'info' => 'from-blue-50 to-cyan-50 border-blue-200 text-blue-800'
          ];
          $icon = $icons[$message['type']] ?? 'ph-info';
          $color = $colors[$message['type']] ?? $colors['info'];
          ?>
          <div class="flex items-center justify-between px-5 py-4 mb-3 rounded-xl bg-gradient-to-r <?= $color ?> border animate-slide-in-up shadow-soft" role="alert">
            <div class="flex items-center space-x-3">
              <i class="ph-fill <?= $icon ?> text-xl"></i>
              <span class="text-sm font-medium"><?= htmlspecialchars($message['message']) ?></span>
            </div>
            <button type="button" class="ml-4 p-1 rounded-lg hover:bg-white/50 transition-colors" onclick="this.parentElement.remove()">
              <i class="ph ph-x text-lg"></i>
            </button>
          </div>
        <?php endforeach; ?>
        <?php unset($_SESSION['flash_messages']); ?>
      </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">