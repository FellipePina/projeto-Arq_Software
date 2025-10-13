# Sistema de Auxílio para Gerenciamento de Estudos

## 📚 Sobre o Projeto

Este é um sistema web desenvolvido em PHP seguindo a arquitetura **MVC (Model-View-Controller)** e aplicando os **princípios SOLID** e **Clean Code**. O objetivo é fornecer uma ferramenta simples e eficiente para gerenciar estudos, permitindo organizar conteúdos, controlar tempo de estudo e definir metas.

### 🎯 Funcionalidades Principais

- **Gerenciamento de Usuários**: Cadastro, login, perfil
- **Conteúdos de Estudo**: Organização por categorias e status
- **Sessões de Estudo**: Cronômetro e registro de tempo
- **Metas**: Definição de objetivos com acompanhamento de progresso
- **Dashboard**: Visão geral com estatísticas e gráficos
- **Relatórios**: Progresso semanal e mensal

## 🏗️ Arquitetura

### Estrutura de Pastas

```
auxilo-estudos/
├── app/
│   ├── Controllers/          # Controladores (lógica de negócio)
│   │   ├── BaseController.php
│   │   ├── UsuarioController.php
│   │   ├── ConteudoController.php
│   │   ├── SessaoController.php
│   │   ├── MetaController.php
│   │   └── DashboardController.php
│   ├── Models/              # Modelos (acesso a dados)
│   │   ├── Database.php
│   │   ├── BaseModel.php
│   │   ├── Usuario.php
│   │   ├── ConteudoEstudo.php
│   │   ├── SessaoEstudo.php
│   │   ├── Meta.php
│   │   └── Categoria.php
│   └── Views/               # Views (apresentação)
│       ├── layouts/
│       ├── usuario/
│       ├── dashboard/
│       ├── conteudo/
│       ├── sessao/
│       ├── meta/
│       └── errors/
├── config/                  # Configurações
│   ├── config.php
│   └── autoloader.php
├── public/                  # Arquivos públicos
│   ├── index.php           # Front Controller
│   ├── css/
│   └── js/
└── database.sql            # Script de criação do banco
```

### Princípios Aplicados

#### SOLID

- **S** - Single Responsibility: Cada classe tem uma única responsabilidade
- **O** - Open/Closed: Abertas para extensão, fechadas para modificação
- **L** - Liskov Substitution: Subtipos são substituíveis pelos tipos base
- **I** - Interface Segregation: Interfaces específicas e focadas
- **D** - Dependency Inversion: Dependência de abstrações, não de implementações

#### Clean Code

- Nomes descritivos para variáveis, funções e classes
- Funções pequenas e com propósito único
- Comentários explicativos quando necessário
- Código organizado e fácil de ler

## 🚀 Instalação

### Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx) ou PHP built-in server
- Extensões PHP: PDO, PDO_MySQL

### Passos para Instalação

1. **Clone ou baixe o projeto**

   ```bash
   git clone [url-do-repositorio] auxilo-estudos
   cd auxilo-estudos
   ```

2. **Configure o banco de dados**

   - Crie um banco MySQL chamado `auxilio_estudos`
   - Execute o script `database.sql` para criar as tabelas

   ```sql
   mysql -u root -p auxilio_estudos < database.sql
   ```

3. **Configure as credenciais do banco**

   - Edite o arquivo `config/config.php`
   - Ajuste as constantes do banco de dados:

   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'auxilio_estudos');
   define('DB_USER', 'seu_usuario');
   define('DB_PASS', 'sua_senha');
   ```

4. **Configure o servidor web**

   **Opção A: Servidor PHP Built-in (Desenvolvimento)**

   ```bash
   cd public
   php -S localhost:8000
   ```

   Acesse: `http://localhost:8000`

   **Opção B: Apache/Nginx**

   - Configure o DocumentRoot para a pasta `public/`
   - Configure rewrite rules se necessário

### Dados de Teste

O sistema já inclui um usuário de demonstração:

- **Email**: `teste@exemplo.com`
- **Senha**: `123456`

## 📱 Como Usar

### 1. Primeiro Acesso

- Acesse o sistema pela URL configurada
- Faça login com as credenciais de teste ou crie uma nova conta
- Explore o dashboard inicial

### 2. Gerenciar Conteúdos

- Vá em "Conteúdos" no menu
- Clique em "Novo Conteúdo"
- Preencha título, descrição e categoria (opcional)
- Defina o status: Não Iniciado, Em Andamento, Pausado ou Concluído

### 3. Sessões de Estudo

- Na página de conteúdos, clique em "Iniciar Sessão"
- Use o cronômetro para controlar o tempo
- Finalize a sessão e adicione observações

### 4. Definir Metas

- Acesse "Metas" no menu
- Crie uma nova meta definindo título e data alvo
- Vincule conteúdos à meta
- Acompanhe o progresso no dashboard

### 5. Acompanhar Progresso

- O dashboard mostra estatísticas gerais
- Gráfico de progresso semanal
- Conteúdos e sessões recentes
- Metas ativas com progresso

## 🔧 Funcionalidades Técnicas

### Segurança Implementada

- **CSRF Protection**: Tokens para formulários
- **SQL Injection**: Prepared statements (PDO)
- **XSS Protection**: Sanitização de entradas
- **Password Hashing**: bcrypt para senhas
- **Session Security**: Configurações seguras de sessão

### Validações

- Validação no frontend (JavaScript)
- Validação no backend (PHP)
- Mensagens de erro amigáveis
- Feedback visual para usuários

### Responsividade

- CSS responsivo para mobile e desktop
- Interface adaptável para diferentes telas
- Componentes flexíveis

## 🎨 Personalização

### Modificar Estilos

- Edite `public/css/style.css`
- O CSS usa variáveis para cores principais
- Classes utilitárias disponíveis

### Adicionar Funcionalidades

1. Crie o modelo em `app/Models/`
2. Implemente o controller em `app/Controllers/`
3. Crie as views em `app/Views/`
4. Adicione rotas em `public/index.php`

### Exemplo: Nova Funcionalidade

```php
// 1. Modelo (app/Models/NovaFuncionalidade.php)
class NovaFuncionalidade extends BaseModel {
    protected string $table = 'nova_tabela';

    public function metodoEspecifico() {
        // Implementação
    }
}

// 2. Controller (app/Controllers/NovaFuncionalidadeController.php)
class NovaFuncionalidadeController extends BaseController {
    public function index() {
        // Lógica do controller
    }
}

// 3. Rota (public/index.php)
case '/nova-funcionalidade':
    $controller = new App\Controllers\NovaFuncionalidadeController();
    $controller->index();
    break;
```

## 🐛 Solução de Problemas

### Erro de Conexão com Banco

- Verifique as credenciais em `config/config.php`
- Teste conexão MySQL manualmente
- Confirme se o banco `auxilio_estudos` existe

### Erro 500 (Internal Server Error)

- Ative display_errors no PHP para ver detalhes
- Verifique logs do servidor web
- Confirme permissões de arquivos

### CSS/JS não carregam

- Verifique se o caminho está correto
- Confirme se os arquivos existem em `public/`
- Teste acesso direto aos arquivos

### Problemas de Sessão

- Verifique se a sessão está iniciada
- Confirme configurações de sessão no PHP
- Limpe cookies do navegador

## 📈 Melhorias Futuras

### Funcionalidades

- [ ] Sistema de lembretes/notificações
- [ ] Exportar relatórios em PDF
- [ ] Integração com calendário
- [ ] Sistema de gamificação
- [ ] API RESTful
- [ ] PWA (Progressive Web App)

### Técnicas

- [ ] Cache de consultas
- [ ] Testes automatizados (PHPUnit)
- [ ] CI/CD pipeline
- [ ] Docker containerization
- [ ] Logs estruturados

## 🤝 Contribuições

Este projeto foi desenvolvido seguindo boas práticas e está aberto para contribuições:

1. Fork o projeto
2. Crie uma branch para sua funcionalidade
3. Implemente seguindo os padrões do código
4. Teste sua implementação
5. Faça um pull request

### Padrões de Código

- PSR-4 para autoloading
- PSR-12 para estilo de código
- Comentários em português
- Nomes de variáveis em português
- Princípios SOLID e Clean Code

## 📝 Licença

Este projeto é de uso educacional e demonstrativo.

## 👨‍💻 Desenvolvimento

**Versão**: 1.0.0
**Linguagem**: PHP 7.4+
**Banco de Dados**: MySQL 5.7+
**Frontend**: HTML5, CSS3, JavaScript (Vanilla)
**Arquitetura**: MVC
**Princípios**: SOLID, Clean Code

---

**Desenvolvido com ❤️ para ajudar estudantes a se organizarem melhor!**
