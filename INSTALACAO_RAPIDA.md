# ⚡ Guia de Instalação Rápida

## 🎯 Para Usuários com Pressa

### 1. Pré-requisitos (5 minutos)

Baixe e instale:

- **XAMPP** (inclui PHP + MySQL): https://www.apachefriends.org/

OU

- **PHP 8+**: https://windows.php.net/download/
- **MySQL**: https://dev.mysql.com/downloads/installer/

### 2. Instalação (10 minutos)

#### Passo 1: Baixar o projeto

- Extraia o arquivo ZIP em: `C:\xampp\htdocs\sistema-estudos`
- Ou em qualquer pasta de sua preferência

#### Passo 2: Configurar banco de dados

**Se usar XAMPP:**

1. Abra o painel do XAMPP
2. Inicie **Apache** e **MySQL**
3. Clique em **Admin** no MySQL (abre phpMyAdmin)
4. Clique em **Novo** para criar banco de dados
5. Nome: `sistema_estudos`
6. Cotejamento: `utf8mb4_unicode_ci`
7. Clique em **Importar**
8. Escolha o arquivo `database_expandido.sql`
9. Clique em **Executar**

**Se usar MySQL via terminal:**

```bash
mysql -u root -p
CREATE DATABASE sistema_estudos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
mysql -u root -p sistema_estudos < database_expandido.sql
```

#### Passo 3: Configurar .env

**Windows (PowerShell):**

```powershell
cd C:\caminho\para\projeto-quintafeira
Copy-Item .env.example .env
notepad .env
```

**Edite o arquivo .env:**

```env
DB_HOST=localhost
DB_NAME=sistema_estudos
DB_USER=root
DB_PASSWORD=        # Deixe vazio se usa XAMPP/WAMP
```

#### Passo 4: Iniciar o servidor

**Windows (PowerShell/CMD):**

```bash
cd C:\caminho\para\projeto-quintafeira
php -S localhost:8000 -t public
```

**Linux/Mac:**

```bash
cd /caminho/para/projeto-quintafeira
php -S localhost:8000 -t public
```

#### Passo 5: Acessar

Abra o navegador e acesse:

```
http://localhost:8000
```

## ✅ Checklist Rápido

- [ ] PHP 8+ instalado (`php -v`)
- [ ] MySQL instalado e rodando
- [ ] Banco `sistema_estudos` criado
- [ ] Arquivo SQL importado
- [ ] Arquivo `.env` configurado
- [ ] Servidor PHP rodando (`php -S localhost:8000 -t public`)
- [ ] Navegador aberto em `http://localhost:8000`

## 🚨 Problemas Comuns (1 minuto cada)

### Erro: "Connection refused"

**Solução:** Inicie o MySQL no XAMPP

### Erro: "Access denied"

**Solução:** Verifique senha no `.env` (deixe vazio se XAMPP)

### Erro: "Table doesn't exist"

**Solução:** Reimporte o `database_expandido.sql`

### Sistema lento/travando

**Solução:** Edite `.env` e coloque:

```env
PERFORMANCE_MODE=true
```

## 📖 Documentação Completa

Para mais detalhes, veja o arquivo `README.md`

## 🎉 Pronto!

Agora é só criar sua conta e começar a usar!

**Primeira vez:**

1. Clique em "Criar Conta"
2. Preencha seus dados
3. Faça login
4. Explore o Dashboard

**Dica:** Use o timer Pomodoro para aumentar sua produtividade! ⏰
