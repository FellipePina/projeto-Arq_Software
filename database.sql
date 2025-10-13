-- ===================================
-- Script SQL para Sistema de Auxílio para Estudos
-- Seguindo princípios de normalização e boas práticas
-- ===================================

-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS auxilio_estudos
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE auxilio_estudos;

-- ===================================
-- TABELA: usuarios
-- Armazena informações dos usuários do sistema
-- ===================================
CREATE TABLE usuarios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL COMMENT 'Nome completo do usuário',
    email VARCHAR(255) NOT NULL UNIQUE COMMENT 'Email único do usuário',
    senha VARCHAR(255) NOT NULL COMMENT 'Senha criptografada',
    data_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de cadastro',
    ativo BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'Se o usuário está ativo',

    -- Índices para performance
    INDEX idx_email (email),
    INDEX idx_ativo (ativo),
    INDEX idx_data_cadastro (data_cadastro)
) ENGINE=InnoDB COMMENT='Tabela de usuários do sistema';

-- ===================================
-- TABELA: categorias
-- Armazena categorias de conteúdo por usuário
-- ===================================
CREATE TABLE categorias (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL COMMENT 'Nome da categoria',
    descricao TEXT COMMENT 'Descrição da categoria',
    cor VARCHAR(7) DEFAULT '#007bff' COMMENT 'Cor em hexadecimal',
    usuario_id BIGINT UNSIGNED NOT NULL COMMENT 'ID do usuário proprietário',

    -- Chaves estrangeiras
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    -- Índices
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_nome_usuario (nome, usuario_id),

    -- Constraints
    UNIQUE KEY uk_nome_usuario (nome, usuario_id)
) ENGINE=InnoDB COMMENT='Categorias de conteúdo de estudo';

-- ===================================
-- TABELA: conteudo_estudo
-- Armazena conteúdos de estudo dos usuários
-- ===================================
CREATE TABLE conteudo_estudo (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL COMMENT 'Título do conteúdo',
    descricao TEXT COMMENT 'Descrição detalhada',
    status ENUM('nao_iniciado', 'em_andamento', 'pausado', 'concluido')
        NOT NULL DEFAULT 'nao_iniciado' COMMENT 'Status do conteúdo',
    usuario_id BIGINT UNSIGNED NOT NULL COMMENT 'ID do usuário',
    categoria_id BIGINT UNSIGNED NULL COMMENT 'ID da categoria (opcional)',
    data_criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de criação',
    data_atualizacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data da última atualização',

    -- Chaves estrangeiras
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
        ON DELETE SET NULL ON UPDATE CASCADE,

    -- Índices
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_categoria_id (categoria_id),
    INDEX idx_status (status),
    INDEX idx_data_atualizacao (data_atualizacao),
    INDEX idx_usuario_status (usuario_id, status)
) ENGINE=InnoDB COMMENT='Conteúdos de estudo';

-- ===================================
-- TABELA: sessao_estudo
-- Registra sessões de estudo dos conteúdos
-- ===================================
CREATE TABLE sessao_estudo (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conteudo_id BIGINT UNSIGNED NOT NULL COMMENT 'ID do conteúdo estudado',
    usuario_id BIGINT UNSIGNED NOT NULL COMMENT 'ID do usuário',
    data_inicio DATETIME NOT NULL COMMENT 'Data/hora de início da sessão',
    data_fim DATETIME NULL COMMENT 'Data/hora de fim da sessão',
    duracao_minutos INT UNSIGNED NULL COMMENT 'Duração em minutos',
    observacoes TEXT COMMENT 'Observações sobre a sessão',

    -- Chaves estrangeiras
    FOREIGN KEY (conteudo_id) REFERENCES conteudo_estudo(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    -- Índices
    INDEX idx_conteudo_id (conteudo_id),
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_data_inicio (data_inicio),
    INDEX idx_usuario_data (usuario_id, data_inicio),

    -- Constraints
    CHECK (data_fim IS NULL OR data_fim >= data_inicio),
    CHECK (duracao_minutos IS NULL OR duracao_minutos >= 0)
) ENGINE=InnoDB COMMENT='Sessões de estudo';

-- ===================================
-- TABELA: metas
-- Define metas de estudo dos usuários
-- ===================================
CREATE TABLE metas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL COMMENT 'Título da meta',
    data_alvo DATE NOT NULL COMMENT 'Data alvo para conclusão',
    status ENUM('ativa', 'concluida', 'cancelada')
        NOT NULL DEFAULT 'ativa' COMMENT 'Status da meta',
    usuario_id BIGINT UNSIGNED NOT NULL COMMENT 'ID do usuário',
    data_criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de criação',
    percentual_progresso DECIMAL(5,2) NOT NULL DEFAULT 0.00
        COMMENT 'Percentual de progresso (0.00 a 100.00)',

    -- Chaves estrangeiras
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    -- Índices
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_status (status),
    INDEX idx_data_alvo (data_alvo),
    INDEX idx_usuario_status (usuario_id, status),

    -- Constraints
    CHECK (percentual_progresso >= 0.00 AND percentual_progresso <= 100.00)
) ENGINE=InnoDB COMMENT='Metas de estudo';

-- ===================================
-- TABELA: meta_conteudo
-- Relaciona metas com conteúdos (N:N)
-- ===================================
CREATE TABLE meta_conteudo (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    meta_id BIGINT UNSIGNED NOT NULL COMMENT 'ID da meta',
    conteudo_id BIGINT UNSIGNED NOT NULL COMMENT 'ID do conteúdo',
    concluido BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Se foi concluído',
    data_conclusao DATETIME NULL COMMENT 'Data da conclusão',

    -- Chaves estrangeiras
    FOREIGN KEY (meta_id) REFERENCES metas(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (conteudo_id) REFERENCES conteudo_estudo(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    -- Índices
    INDEX idx_meta_id (meta_id),
    INDEX idx_conteudo_id (conteudo_id),
    INDEX idx_concluido (concluido),

    -- Constraint única
    UNIQUE KEY uk_meta_conteudo (meta_id, conteudo_id)
) ENGINE=InnoDB COMMENT='Relacionamento entre metas e conteúdos';

-- ===================================
-- TABELA: lembretes
-- Sistema de lembretes para os usuários
-- ===================================
CREATE TABLE lembretes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conteudo_id BIGINT UNSIGNED NULL COMMENT 'ID do conteúdo (opcional)',
    usuario_id BIGINT UNSIGNED NOT NULL COMMENT 'ID do usuário',
    data_hora DATETIME NOT NULL COMMENT 'Data/hora do lembrete',
    mensagem TEXT NOT NULL COMMENT 'Mensagem do lembrete',
    status ENUM('pendente', 'enviado', 'cancelado')
        NOT NULL DEFAULT 'pendente' COMMENT 'Status do lembrete',

    -- Chaves estrangeiras
    FOREIGN KEY (conteudo_id) REFERENCES conteudo_estudo(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    -- Índices
    INDEX idx_conteudo_id (conteudo_id),
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_data_hora (data_hora),
    INDEX idx_status (status),
    INDEX idx_usuario_data (usuario_id, data_hora)
) ENGINE=InnoDB COMMENT='Sistema de lembretes';

-- ===================================
-- DADOS INICIAIS PARA TESTE
-- ===================================

-- Usuário de teste (senha: 123456)
INSERT INTO usuarios (nome, email, senha) VALUES
('Usuário Teste', 'teste@exemplo.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Categorias de exemplo
INSERT INTO categorias (nome, descricao, cor, usuario_id) VALUES
('Programação', 'Conteúdos relacionados a programação', '#28a745', 1),
('Matemática', 'Conteúdos de matemática e cálculo', '#007bff', 1),
('Idiomas', 'Estudos de idiomas estrangeiros', '#ffc107', 1);

-- ===================================
-- VIEWS PARA RELATÓRIOS
-- ===================================

-- View com estatísticas por usuário
CREATE VIEW vw_estatisticas_usuario AS
SELECT
    u.id as usuario_id,
    u.nome as usuario_nome,
    COUNT(DISTINCT ce.id) as total_conteudos,
    COUNT(DISTINCT CASE WHEN ce.status = 'concluido' THEN ce.id END) as conteudos_concluidos,
    COUNT(DISTINCT se.id) as total_sessoes,
    COALESCE(SUM(se.duracao_minutos), 0) as total_minutos_estudados,
    ROUND(COALESCE(SUM(se.duracao_minutos), 0) / 60, 2) as total_horas_estudadas,
    COUNT(DISTINCT m.id) as total_metas,
    COUNT(DISTINCT CASE WHEN m.status = 'concluida' THEN m.id END) as metas_concluidas
FROM usuarios u
LEFT JOIN conteudo_estudo ce ON u.id = ce.usuario_id
LEFT JOIN sessao_estudo se ON u.id = se.usuario_id
LEFT JOIN metas m ON u.id = m.usuario_id
WHERE u.ativo = 1
GROUP BY u.id, u.nome;

-- ===================================
-- TRIGGERS PARA MANTER CONSISTÊNCIA
-- ===================================

-- Trigger para atualizar data_atualizacao em conteudo_estudo
DELIMITER //
CREATE TRIGGER tr_conteudo_update_timestamp
    BEFORE UPDATE ON conteudo_estudo
    FOR EACH ROW
BEGIN
    SET NEW.data_atualizacao = CURRENT_TIMESTAMP;
END//
DELIMITER ;
-- Versão 2.0 - Alinhado com o Diagrama de Classes

-- Tabela de Usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de Categorias de Estudo
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    cor VARCHAR(7) DEFAULT '#FFFFFF',
    usuario_id INT NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de Conteúdo de Estudo
CREATE TABLE IF NOT EXISTS conteudos_estudo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    status ENUM('pendente', 'em_andamento', 'concluido') NOT NULL DEFAULT 'pendente',
    usuario_id INT NOT NULL,
    categoria_id INT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de Metas de Estudo
CREATE TABLE IF NOT EXISTS metas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    data_alvo DATE NOT NULL,
    status ENUM('ativa', 'concluida', 'cancelada') NOT NULL DEFAULT 'ativa',
    usuario_id INT NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    percentual_progresso FLOAT DEFAULT 0,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de Associação entre Metas e Conteúdos (N-M)
CREATE TABLE IF NOT EXISTS metas_conteudos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    meta_id INT NOT NULL,
    conteudo_id INT NOT NULL,
    concluido BOOLEAN DEFAULT FALSE,
    data_conclusao DATETIME NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (meta_id) REFERENCES metas(id) ON DELETE CASCADE,
    FOREIGN KEY (conteudo_id) REFERENCES conteudos_estudo(id) ON DELETE CASCADE,
    UNIQUE KEY unique_meta_conteudo (meta_id, conteudo_id)
);

-- Tabela de Sessões de Estudo
CREATE TABLE IF NOT EXISTS sessoes_estudo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conteudo_id INT,
    usuario_id INT NOT NULL,
    data_inicio DATETIME NOT NULL,
    data_fim DATETIME NULL,
    duracao_minutos INT DEFAULT 0,
    observacoes TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (conteudo_id) REFERENCES conteudos_estudo(id) ON DELETE SET NULL
);

-- Tabela de Lembretes
CREATE TABLE IF NOT EXISTS lembretes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conteudo_id INT,
    usuario_id INT NOT NULL,
    data_hora DATETIME NOT NULL,
    mensagem TEXT NOT NULL,
    status ENUM('pendente', 'enviado') NOT NULL DEFAULT 'pendente',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (conteudo_id) REFERENCES conteudos_estudo(id) ON DELETE SET NULL
);

-- Índices para melhorar performance
CREATE INDEX idx_usuarios_email ON usuarios(email);
CREATE INDEX idx_categorias_usuario ON categorias(usuario_id);
CREATE INDEX idx_conteudos_usuario ON conteudos_estudo(usuario_id);
CREATE INDEX idx_conteudos_categoria ON conteudos_estudo(categoria_id);
CREATE INDEX idx_conteudos_status ON conteudos_estudo(status);
CREATE INDEX idx_metas_usuario ON metas(usuario_id);
CREATE INDEX idx_metas_status ON metas(status);
CREATE INDEX idx_sessoes_usuario ON sessoes_estudo(usuario_id);
CREATE INDEX idx_sessoes_conteudo ON sessoes_estudo(conteudo_id);
CREATE INDEX idx_sessoes_data ON sessoes_estudo(data_inicio);
CREATE INDEX idx_lembretes_usuario ON lembretes(usuario_id);
CREATE INDEX idx_lembretes_data ON lembretes(data_hora);
CREATE INDEX idx_lembretes_status ON lembretes(status);