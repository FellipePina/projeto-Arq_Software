-- ===================================
-- Sistema de Auxílio para Estudos - VERSÃO EXPANDIDA
-- Seguindo princípios de normalização e boas práticas
-- ===================================

-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS auxilio_estudos
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE auxilio_estudos;

-- ===================================
-- TABELA: usuarios
-- ===================================
CREATE TABLE IF NOT EXISTS usuarios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL COMMENT 'Nome completo do usuário',
    email VARCHAR(255) NOT NULL UNIQUE COMMENT 'Email único do usuário',
    senha VARCHAR(255) NOT NULL COMMENT 'Senha criptografada',
    data_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,

    INDEX idx_email (email),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB COMMENT='Usuários do sistema';

-- ===================================
-- TABELA: disciplinas (Matérias/Disciplinas)
-- ===================================
CREATE TABLE IF NOT EXISTS disciplinas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL COMMENT 'Nome da disciplina',
    codigo VARCHAR(50) COMMENT 'Código da disciplina (ex: MAT101)',
    cor VARCHAR(7) DEFAULT '#007bff' COMMENT 'Cor para identificação visual',
    descricao TEXT COMMENT 'Descrição da disciplina',
    usuario_id BIGINT UNSIGNED NOT NULL,
    ativa BOOLEAN DEFAULT TRUE COMMENT 'Disciplina ainda está em andamento',
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_ativa (ativa)
) ENGINE=InnoDB COMMENT='Disciplinas/Matérias do estudante';

-- ===================================
-- TABELA: tarefas
-- ===================================
CREATE TABLE IF NOT EXISTS tarefas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL COMMENT 'Título da tarefa',
    descricao TEXT COMMENT 'Descrição detalhada',
    disciplina_id BIGINT UNSIGNED NULL COMMENT 'Disciplina associada',
    usuario_id BIGINT UNSIGNED NOT NULL,
    data_entrega DATE COMMENT 'Data limite para conclusão',
    prioridade ENUM('baixa', 'media', 'alta', 'urgente') DEFAULT 'media',
    status ENUM('pendente', 'em_andamento', 'concluida', 'cancelada') DEFAULT 'pendente',
    concluida BOOLEAN DEFAULT FALSE,
    data_conclusao DATETIME NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (disciplina_id) REFERENCES disciplinas(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_disciplina (disciplina_id),
    INDEX idx_status (status),
    INDEX idx_prioridade (prioridade),
    INDEX idx_data_entrega (data_entrega)
) ENGINE=InnoDB COMMENT='Tarefas do estudante';

-- ===================================
-- TABELA: subtarefas (Checklists)
-- ===================================
CREATE TABLE IF NOT EXISTS subtarefas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tarefa_id BIGINT UNSIGNED NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    concluida BOOLEAN DEFAULT FALSE,
    ordem INT DEFAULT 0 COMMENT 'Ordem de exibição',
    data_conclusao DATETIME NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (tarefa_id) REFERENCES tarefas(id) ON DELETE CASCADE,
    INDEX idx_tarefa (tarefa_id),
    INDEX idx_ordem (ordem)
) ENGINE=InnoDB COMMENT='Sub-tarefas (checklist) de tarefas';

-- ===================================
-- TABELA: sessoes_pomodoro
-- ===================================
CREATE TABLE IF NOT EXISTS sessoes_pomodoro (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    tarefa_id BIGINT UNSIGNED NULL COMMENT 'Tarefa associada',
    disciplina_id BIGINT UNSIGNED NULL COMMENT 'Disciplina associada',
    data_inicio DATETIME NOT NULL,
    data_fim DATETIME NULL,
    duracao_planejada INT NOT NULL COMMENT 'Duração planejada em minutos',
    duracao_real INT NULL COMMENT 'Duração real em minutos',
    tipo ENUM('foco', 'pausa_curta', 'pausa_longa') DEFAULT 'foco',
    concluida BOOLEAN DEFAULT FALSE,
    interrompida BOOLEAN DEFAULT FALSE,
    observacoes TEXT,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (tarefa_id) REFERENCES tarefas(id) ON DELETE SET NULL,
    FOREIGN KEY (disciplina_id) REFERENCES disciplinas(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_tarefa (tarefa_id),
    INDEX idx_disciplina (disciplina_id),
    INDEX idx_data_inicio (data_inicio),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB COMMENT='Sessões Pomodoro do estudante';

-- ===================================
-- TABELA: eventos_calendario
-- ===================================
CREATE TABLE IF NOT EXISTS eventos_calendario (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    disciplina_id BIGINT UNSIGNED NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    data_inicio DATETIME NOT NULL,
    data_fim DATETIME NOT NULL,
    dia_inteiro BOOLEAN DEFAULT FALSE,
    tipo ENUM('evento', 'prova', 'aula', 'estudo', 'outro') DEFAULT 'evento',
    cor VARCHAR(7) NULL COMMENT 'Cor personalizada',
    lembrete_minutos INT NULL COMMENT 'Minutos antes para lembrete',
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (disciplina_id) REFERENCES disciplinas(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_disciplina (disciplina_id),
    INDEX idx_data_inicio (data_inicio),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB COMMENT='Eventos do calendário';

-- ===================================
-- TABELA: anotacoes
-- ===================================
CREATE TABLE IF NOT EXISTS anotacoes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    disciplina_id BIGINT UNSIGNED NULL,
    titulo VARCHAR(255) NOT NULL,
    conteudo TEXT NOT NULL,
    tags VARCHAR(500) COMMENT 'Tags separadas por vírgula',
    cor VARCHAR(7) DEFAULT '#ffffff',
    fixada BOOLEAN DEFAULT FALSE,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (disciplina_id) REFERENCES disciplinas(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_disciplina (disciplina_id),
    INDEX idx_fixada (fixada)
) ENGINE=InnoDB COMMENT='Anotações do estudante';

-- ===================================
-- TABELA: metas_tempo
-- ===================================
CREATE TABLE IF NOT EXISTS metas_tempo (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    disciplina_id BIGINT UNSIGNED NULL,
    tipo ENUM('diaria', 'semanal', 'mensal') NOT NULL,
    meta_minutos INT NOT NULL COMMENT 'Meta em minutos',
    periodo_inicio DATE NOT NULL,
    periodo_fim DATE NOT NULL,
    ativa BOOLEAN DEFAULT TRUE,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (disciplina_id) REFERENCES disciplinas(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_disciplina (disciplina_id),
    INDEX idx_periodo (periodo_inicio, periodo_fim),
    INDEX idx_ativa (ativa)
) ENGINE=InnoDB COMMENT='Metas de tempo de estudo';

-- ===================================
-- TABELA: configuracoes_usuario
-- ===================================
CREATE TABLE IF NOT EXISTS configuracoes_usuario (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL UNIQUE,

    -- Configurações Pomodoro
    pomodoro_foco_minutos INT DEFAULT 25,
    pomodoro_pausa_curta_minutos INT DEFAULT 5,
    pomodoro_pausa_longa_minutos INT DEFAULT 15,
    pomodoro_ciclos_ate_pausa_longa INT DEFAULT 4,
    pomodoro_som_ativo BOOLEAN DEFAULT TRUE,
    pomodoro_notificacao_ativa BOOLEAN DEFAULT TRUE,

    -- Configurações de Interface
    tema ENUM('claro', 'escuro', 'auto') DEFAULT 'claro',
    idioma VARCHAR(5) DEFAULT 'pt-BR',

    -- Configurações de Notificação
    notificacao_tarefas BOOLEAN DEFAULT TRUE,
    notificacao_eventos BOOLEAN DEFAULT TRUE,
    notificacao_metas BOOLEAN DEFAULT TRUE,

    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Configurações personalizadas do usuário';

-- ===================================
-- TABELA: gamificacao (Sistema de Pontos e Conquistas)
-- ===================================
CREATE TABLE IF NOT EXISTS gamificacao (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL UNIQUE,
    pontos_total INT DEFAULT 0,
    nivel INT DEFAULT 1,
    streak_dias INT DEFAULT 0 COMMENT 'Dias consecutivos de estudo',
    melhor_streak INT DEFAULT 0,
    ultimo_acesso DATE,
    pomodoros_concluidos INT DEFAULT 0,
    tarefas_concluidas INT DEFAULT 0,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Sistema de gamificação';

-- ===================================
-- TABELA: conquistas
-- ===================================
CREATE TABLE IF NOT EXISTS conquistas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT,
    icone VARCHAR(100),
    pontos INT DEFAULT 10,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB COMMENT='Conquistas disponíveis';

-- ===================================
-- TABELA: usuario_conquistas
-- ===================================
CREATE TABLE IF NOT EXISTS usuario_conquistas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    conquista_id BIGINT UNSIGNED NOT NULL,
    data_obtencao DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (conquista_id) REFERENCES conquistas(id) ON DELETE CASCADE,
    UNIQUE KEY uk_usuario_conquista (usuario_id, conquista_id),
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB COMMENT='Conquistas obtidas pelos usuários';

-- ===================================
-- INSERIR CONQUISTAS PADRÃO
-- ===================================
INSERT INTO conquistas (codigo, nome, descricao, icone, pontos) VALUES
('primeiro_pomodoro', 'Primeiro Foco', 'Complete seu primeiro Pomodoro', '🍅', 10),
('10_pomodoros', 'Focado', 'Complete 10 Pomodoros', '🎯', 25),
('50_pomodoros', 'Mestre do Foco', 'Complete 50 Pomodoros', '🏆', 50),
('primeira_tarefa', 'Começando', 'Complete sua primeira tarefa', '✅', 10),
('10_tarefas', 'Produtivo', 'Complete 10 tarefas', '📝', 25),
('streak_7_dias', 'Uma Semana', 'Mantenha 7 dias de streak', '🔥', 30),
('streak_30_dias', 'Um Mês', 'Mantenha 30 dias de streak', '⭐', 100),
('nivel_5', 'Nível 5', 'Alcance o nível 5', '🎓', 50);

-- ===================================
-- VIEWS PARA RELATÓRIOS
-- ===================================

-- View: Tempo total por disciplina
CREATE OR REPLACE VIEW vw_tempo_por_disciplina AS
SELECT
    u.id as usuario_id,
    d.id as disciplina_id,
    d.nome as disciplina_nome,
    d.cor as disciplina_cor,
    COUNT(DISTINCT sp.id) as total_pomodoros,
    SUM(CASE WHEN sp.tipo = 'foco' AND sp.concluida = TRUE THEN sp.duracao_real ELSE 0 END) as minutos_foco,
    SUM(CASE WHEN sp.tipo = 'foco' AND sp.concluida = TRUE THEN sp.duracao_real ELSE 0 END) / 60.0 as horas_foco
FROM usuarios u
LEFT JOIN disciplinas d ON d.usuario_id = u.id
LEFT JOIN sessoes_pomodoro sp ON sp.disciplina_id = d.id
WHERE d.ativa = TRUE
GROUP BY u.id, d.id, d.nome, d.cor;

-- View: Estatísticas do usuário
CREATE OR REPLACE VIEW vw_estatisticas_usuario AS
SELECT
    u.id as usuario_id,
    u.nome as usuario_nome,
    COUNT(DISTINCT d.id) as total_disciplinas,
    COUNT(DISTINCT t.id) as total_tarefas,
    COUNT(DISTINCT CASE WHEN t.concluida = TRUE THEN t.id END) as tarefas_concluidas,
    COUNT(DISTINCT sp.id) as total_pomodoros,
    COUNT(DISTINCT CASE WHEN sp.concluida = TRUE THEN sp.id END) as pomodoros_concluidos,
    SUM(CASE WHEN sp.tipo = 'foco' AND sp.concluida = TRUE THEN sp.duracao_real ELSE 0 END) as minutos_totais,
    COALESCE(g.pontos_total, 0) as pontos,
    COALESCE(g.nivel, 1) as nivel,
    COALESCE(g.streak_dias, 0) as streak
FROM usuarios u
LEFT JOIN disciplinas d ON d.usuario_id = u.id
LEFT JOIN tarefas t ON t.usuario_id = u.id
LEFT JOIN sessoes_pomodoro sp ON sp.usuario_id = u.id
LEFT JOIN gamificacao g ON g.usuario_id = u.id
WHERE u.ativo = TRUE
GROUP BY u.id, u.nome, g.pontos_total, g.nivel, g.streak_dias;

-- ===================================
-- DADOS INICIAIS PARA TESTE
-- ===================================

-- Configurações padrão para usuário de teste (ID 1)
INSERT INTO configuracoes_usuario (usuario_id) VALUES (1)
ON DUPLICATE KEY UPDATE usuario_id = usuario_id;

-- Gamificação para usuário de teste
INSERT INTO gamificacao (usuario_id) VALUES (1)
ON DUPLICATE KEY UPDATE usuario_id = usuario_id;

-- Disciplinas de exemplo
INSERT INTO disciplinas (nome, codigo, cor, descricao, usuario_id) VALUES
('Cálculo I', 'MAT101', '#e74c3c', 'Cálculo Diferencial e Integral', 1),
('Programação Web', 'INF202', '#3498db', 'Desenvolvimento de aplicações web', 1),
('Banco de Dados', 'INF301', '#2ecc71', 'Modelagem e implementação de bancos de dados', 1);

-- Tarefas de exemplo
INSERT INTO tarefas (titulo, descricao, disciplina_id, usuario_id, data_entrega, prioridade, status) VALUES
('Estudar Derivadas', 'Revisar conceitos de derivadas parciais', 1, 1, DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'alta', 'pendente'),
('Trabalho de PHP', 'Desenvolver sistema CRUD em PHP', 2, 1, DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'alta', 'em_andamento'),
('Lista de Exercícios SQL', 'Resolver exercícios de normalização', 3, 1, DATE_ADD(CURDATE(), INTERVAL 5 DAY), 'media', 'pendente');

-- Evento de exemplo
INSERT INTO eventos_calendario (usuario_id, disciplina_id, titulo, descricao, data_inicio, data_fim, tipo) VALUES
(1, 1, 'Prova de Cálculo I', 'Avaliação sobre derivadas e integrais',
 DATE_ADD(NOW(), INTERVAL 14 DAY), DATE_ADD(NOW(), INTERVAL 14 DAY) + INTERVAL 2 HOUR, 'prova');
