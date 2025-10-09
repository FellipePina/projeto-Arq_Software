-- Arquivo SQL para criação do banco de dados do Sistema de Gerenciamento de Estudos
-- Versão 2.0 - Alinhado com o Diagrama de Classes

-- Tabela de Usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Categorias de Estudo
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    cor VARCHAR(7) DEFAULT '#FFFFFF', -- Cor em hexadecimal, ex: #RRGGBB
    usuario_id INT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de Conteúdo de Estudo
CREATE TABLE conteudos_estudo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    status ENUM('pendente', 'em_andamento', 'concluido') NOT NULL DEFAULT 'pendente',
    usuario_id INT,
    categoria_id INT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de Metas de Estudo
CREATE TABLE metas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    data_alvo DATE NOT NULL,
    status ENUM('ativa', 'concluida', 'cancelada') NOT NULL DEFAULT 'ativa',
    usuario_id INT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    percentual_progresso FLOAT DEFAULT 0,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de Associação entre Metas e Conteúdos (N-M)
CREATE TABLE metas_conteudos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    meta_id INT,
    conteudo_id INT,
    concluido BOOLEAN DEFAULT FALSE,
    data_conclusao DATETIME,
    FOREIGN KEY (meta_id) REFERENCES metas(id) ON DELETE CASCADE,
    FOREIGN KEY (conteudo_id) REFERENCES conteudos_estudo(id) ON DELETE CASCADE
);

-- Tabela de Sessões de Estudo
CREATE TABLE sessoes_estudo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conteudo_id INT,
    usuario_id INT,
    data_inicio DATETIME NOT NULL,
    data_fim DATETIME,
    duracao_minutos INT DEFAULT 0,
    observacoes TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (conteudo_id) REFERENCES conteudos_estudo(id) ON DELETE SET NULL
);

-- Tabela de Lembretes
CREATE TABLE lembretes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conteudo_id INT,
    usuario_id INT,
    data_hora DATETIME NOT NULL,
    mensagem TEXT NOT NULL,
    status ENUM('pendente', 'enviado') NOT NULL DEFAULT 'pendente',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (conteudo_id) REFERENCES conteudos_estudo(id) ON DELETE SET NULL
);
