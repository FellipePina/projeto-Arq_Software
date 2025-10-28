# 📚 Sistema de Gerenciamento de Estudos

Sistema completo para gerenciar estudos, disciplinas, tarefas, anotações e sessões de estudo com técnica Pomodoro.

## 🚀 Funcionalidades

- ✅ **Gestão de Disciplinas**: Organize suas matérias e conteúdos
- 📝 **Anotações**: Crie e gerencie anotações vinculadas a disciplinas
- ⏰ **Timer Pomodoro**: Técnica de produtividade com sessões cronometradas
- 📊 **Dashboard**: Visualize seu progresso e estatísticas
- 📅 **Calendário**: Organize eventos e prazos
- 🎯 **Metas**: Defina e acompanhe objetivos de estudo
- 📈 **Relatórios**: Análise detalhada do seu desempenho
- 🎮 **Gamificação**: Sistema de conquistas e níveis
- 🌙 **Tema Escuro**: Interface moderna e confortável

## 📋 Requisitos do Sistema

Antes de começar, certifique-se de ter instalado em seu computador:

- **PHP 8.0 ou superior** (recomendado: PHP 8.4)
- **MySQL 5.7 ou superior** (ou MariaDB 10.3+)
- **Extensões PHP necessárias**:
  - `mysqli` (banco de dados)
  - `pdo` e `pdo_mysql` (alternativa ao mysqli)
  - `mbstring` (manipulação de strings)
  - `json` (manipulação JSON)
  - `session` (gerenciamento de sessões)

### Verificar se o PHP está instalado:

```bash
php -v
```

Você deve ver algo como: `PHP 8.4.12 (cli) ...`

### Verificar extensões PHP:

```bash
php -m
```

Procure por: `mysqli`, `pdo_mysql`, `mbstring`, `json`, `session`

## 🔧 Instalação Passo a Passo

### 1️⃣ Baixar o Projeto

Faça o download ou clone este repositório para seu computador:

```bash
git clone <url-do-repositorio>
cd projeto-quintafeira
```

Ou simplesmente extraia o arquivo ZIP em uma pasta de sua preferência.

### 2️⃣ Configurar o Banco de Dados

#### A) Criar o Banco de Dados

1. Abra o **phpMyAdmin**, **MySQL Workbench** ou acesse o MySQL via terminal:

```bash
mysql -u root -p
```

2. Crie o banco de dados:

```sql
CREATE DATABASE sistema_estudos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

#### B) Importar a Estrutura

No phpMyAdmin:

1. Selecione o banco `sistema_estudos`
2. Clique em "Importar"
3. Escolha o arquivo `database_expandido.sql` (recomendado) ou `database.sql`
4. Clique em "Executar"

Via terminal:

```bash
mysql -u root -p sistema_estudos < database_expandido.sql
```

### 3️⃣ Configurar Variáveis de Ambiente

1. Copie o arquivo de exemplo:

**Windows (PowerShell):**

```powershell
Copy-Item .env.example .env
```

**Windows (CMD):**

```cmd
copy .env.example .env
```

**Linux/Mac:**

```bash
cp .env.example .env
```

2. Abra o arquivo `.env` em um editor de texto e configure suas credenciais:

```env
DB_HOST=localhost
DB_NAME=sistema_estudos
DB_USER=root
DB_PASSWORD=sua_senha_aqui
```

**⚠️ IMPORTANTE**: Se você estiver usando **XAMPP** ou **WAMP**, a senha padrão geralmente é vazia. Deixe assim:

```env
DB_PASSWORD=
```

### 4️⃣ Atualizar Configuração do Banco (Opcional)

Se preferir não usar o arquivo `.env`, você pode editar diretamente o arquivo de configuração:

Abra `app/Models/Database.php` e ajuste as linhas:

```php
private $host = 'localhost';
private $db_name = 'sistema_estudos';
private $username = 'root';
private $password = ''; // Sua senha do MySQL
```

### 5️⃣ Iniciar o Servidor

Abra o terminal na pasta do projeto e execute:

**Windows (PowerShell ou CMD):**

```bash
cd c:\Users\Luiz\Documents\projeto-quintafeira
php -S localhost:8000 -t public
```

**Linux/Mac:**

```bash
cd /caminho/para/projeto-quintafeira
php -S localhost:8000 -t public
```

Você verá a mensagem:

```
PHP 8.4.12 Development Server (http://localhost:8000) started
```

### 6️⃣ Acessar o Sistema

Abra seu navegador e acesse:

```
http://localhost:8000
```

## 👤 Primeiro Acesso

### Criar sua Conta

1. Na tela de login, clique em **"Criar Conta"**
2. Preencha os dados:
   - Nome completo
   - Email
   - Senha (mínimo 6 caracteres)
3. Clique em **"Cadastrar"**
4. Faça login com suas credenciais

### Dados de Teste (opcional)

Se você importou o `database_expandido.sql`, pode ter usuários de exemplo. Caso contrário, crie sua própria conta conforme acima.

## 🎯 Guia de Uso Rápido

### Dashboard

- Acesse o **Dashboard** para ver estatísticas gerais
- Veja gráficos de tempo de estudo por disciplina
- Acompanhe metas e conquistas

### Disciplinas

1. Vá em **Disciplinas** no menu
2. Clique em **"Nova Disciplina"**
3. Preencha nome, descrição e escolha uma cor
4. Adicione conteúdos à disciplina

### Timer Pomodoro

1. Acesse **Pomodoro** no menu
2. Configure o tempo de foco (padrão: 25 min)
3. Clique em **Iniciar**
4. Trabalhe até o alarme tocar
5. Faça uma pausa de 5 minutos
6. Repita o ciclo

### Anotações

1. Vá em **Anotações**
2. Clique em **"Nova Anotação"**
3. Escreva seu conteúdo
4. Vincule a uma disciplina (opcional)
5. Use tags para organizar

### Tarefas

1. Acesse **Tarefas**
2. Crie uma nova tarefa com título e descrição
3. Defina prioridade (Baixa, Média, Alta)
4. Adicione prazo
5. Marque como concluída quando terminar

### Relatórios

- Acesse **Relatórios** para ver análises detalhadas
- Filtre por período (semana, mês, ano)
- Exporte relatórios em PDF (se configurado)

## ⚡ Modo de Performance (PCs Fracos)

Se o sistema estiver lento ou travando em seu computador:

### Opção 1: Ativar via .env

Edite o arquivo `.env`:

```env
PERFORMANCE_MODE=true
```

### Opção 2: Usar Templates Otimizados

Em cada view, substitua:

```php
<?php include __DIR__ . '/../layouts/header.php'; ?>
// por:
<?php include __DIR__ . '/../layouts/header-optimized.php'; ?>
```

E no final:

```php
<?php include __DIR__ . '/../layouts/footer.php'; ?>
// por:
<?php include __DIR__ . '/../layouts/footer-optimized.php'; ?>
```

### O que o Modo de Performance faz?

- ✅ Remove efeitos de vidro/blur (backdrop-filter)
- ✅ Reduz animações pesadas
- ✅ Ativa lazy loading de imagens
- ✅ Otimiza renderização de listas grandes
- ✅ Comprime HTML automaticamente
- ✅ Melhora o FPS em notebooks fracos

Veja mais detalhes em: `OTIMIZACOES_PERFORMANCE.md`

## 🛠️ Solução de Problemas

### ❌ Erro: "Connection refused" ou "Can't connect to MySQL"

**Causa**: MySQL não está rodando.

**Solução**:

- **XAMPP**: Abra o painel e inicie o MySQL
- **WAMP**: Inicie todos os serviços
- **Standalone MySQL**:

  ```bash
  # Windows (como Administrador)
  net start MySQL80

  # Linux/Mac
  sudo systemctl start mysql
  ```

### ❌ Erro: "Access denied for user"

**Causa**: Credenciais do banco incorretas.

**Solução**:

1. Verifique usuário e senha no arquivo `.env`
2. Teste a conexão manualmente:
   ```bash
   mysql -u root -p
   ```
3. Se necessário, redefina a senha do MySQL

### ❌ Erro: "Table 'sistema_estudos.usuarios' doesn't exist"

**Causa**: Banco de dados não foi importado corretamente.

**Solução**:

1. Reimporte o arquivo SQL:
   ```bash
   mysql -u root -p sistema_estudos < database_expandido.sql
   ```
2. Verifique se todas as tabelas foram criadas:
   ```sql
   USE sistema_estudos;
   SHOW TABLES;
   ```

### ❌ Erro: "Class not found" ou "Fatal error"

**Causa**: Autoloader não está funcionando.

**Solução**:

1. Verifique se o arquivo `config/autoloader.php` existe
2. Certifique-se de que está executando o servidor na pasta correta:
   ```bash
   php -S localhost:8000 -t public
   ```
   (Dentro da pasta `projeto-quintafeira`)

### ❌ Página em branco ou erro 500

**Causa**: Erro de sintaxe PHP ou configuração.

**Solução**:

1. Ative exibição de erros editando `public/index.php`:
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```
2. Verifique o log de erros do PHP:
   ```bash
   php -S localhost:8000 -t public 2>&1 | findstr "error"
   ```
3. Olhe o console do navegador (F12) para erros JavaScript

### 🐌 Sistema está lento/travando

**Solução**:

1. Ative o **Modo de Performance** (veja seção acima)
2. Feche abas desnecessárias do navegador
3. Use navegadores mais leves (Edge, Chrome)
4. Desative extensões do navegador
5. Aumente memória do PHP editando `php.ini`:
   ```ini
   memory_limit = 256M
   ```

### 🔍 CSS não está carregando

**Causa**: Caminho de arquivos incorreto.

**Solução**:

1. Verifique se o servidor está rodando na pasta `public`:
   ```bash
   php -S localhost:8000 -t public
   ```
2. Acesse diretamente: `http://localhost:8000/css/style.css`
3. Se retornar 404, verifique se o arquivo existe em `public/css/`

## 📁 Estrutura de Pastas

```
projeto-quintafeira/
├── app/
│   ├── Controllers/      # Lógica de controle (rotas)
│   ├── Models/           # Modelos de dados (banco)
│   ├── Views/            # Templates HTML/PHP
│   ├── Helpers/          # Funções auxiliares
│   ├── Patterns/         # Padrões de projeto (GOF)
│   └── Interfaces/       # Interfaces Observer
├── config/
│   ├── autoloader.php    # Carregamento automático de classes
│   └── config.php        # Configurações gerais
├── public/
│   ├── index.php         # Front controller (ponto de entrada)
│   ├── css/              # Estilos CSS
│   ├── js/               # Scripts JavaScript
│   └── assets/           # Imagens, fontes, etc.
├── database.sql          # Schema básico do banco
├── database_expandido.sql # Schema com dados de exemplo
├── .env.example          # Exemplo de configuração
├── .env                  # Suas configurações (não commitar!)
└── README.md             # Este arquivo
```

## 🔐 Segurança

- **Senhas**: Armazenadas com hash bcrypt
- **Sessões**: Gerenciadas via PHP Session
- **SQL Injection**: Prevenida com prepared statements
- **XSS**: Escape de dados com `htmlspecialchars()`

**⚠️ AVISO**: Este sistema é para fins educacionais. Para produção:

- Use HTTPS
- Configure CORS
- Implemente rate limiting
- Use variáveis de ambiente seguras
- Configure firewall

## 🎓 Padrões de Projeto Implementados

Este sistema utiliza diversos padrões de design (Design Patterns):

- **MVC**: Separação Model-View-Controller
- **Singleton**: Conexão única com banco (Database.php)
- **Factory**: Criação de objetos (ModelFactory.php)
- **Observer**: Notificações de metas (MetaObserver.php)
- **Strategy**: Diferentes tipos de notificação
- **Template Method**: Geração de relatórios
- **Decorator**: Extensão de tarefas
- **Command**: Execução de ações
- **Chain of Responsibility**: Validação em cadeia

Veja mais em: `PADROES_GOF_IMPLEMENTADOS.md`

## 📊 Otimizações de Performance

Documentação completa das otimizações implementadas:

- CSS: `OTIMIZACOES_PERFORMANCE.md`
- Helper PHP: `app/Helpers/PerformanceHelper.php`
- JavaScript: `public/js/performance.js`
- Estilos otimizados: `public/css/performance.css`

## 🤝 Contribuindo

Contribuições são bem-vindas! Para contribuir:

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/NovaFuncionalidade`)
3. Commit suas mudanças (`git commit -m 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/NovaFuncionalidade`)
5. Abra um Pull Request

## 📝 Licença

Este projeto é livre para uso educacional e pessoal.

## 📧 Suporte

Se tiver dúvidas ou problemas:

1. Consulte a seção **Solução de Problemas** acima
2. Verifique o arquivo `OTIMIZACOES_PERFORMANCE.md`
3. Abra uma issue no repositório
4. Entre em contato com o desenvolvedor

## 🎉 Pronto!

Agora você tem um sistema completo de gerenciamento de estudos rodando em sua máquina!

**Dica**: Explore todas as funcionalidades, configure suas disciplinas, use o timer Pomodoro e acompanhe seu progresso no dashboard.

Bons estudos! 📚✨
