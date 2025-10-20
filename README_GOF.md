# Padrões GOF Implementados no Sistema

Este documento descreve os três padrões de projeto GOF (Gang of Four) implementados no sistema de gerenciamento de estudos.

## 📋 Índice

1. [Singleton (Criacional)](#1-singleton-criacional)
2. [Facade (Estrutural)](#2-facade-estrutural)
3. [Observer (Comportamental)](#3-observer-comportamental)

---

## 1. Singleton (Criacional)

### 📍 Localização

**Arquivo:** `app/Models/Database.php`

### 🎯 Objetivo

Garantir que exista apenas uma única instância de conexão com o banco de dados em toda a aplicação, evitando desperdício de recursos e garantindo consistência.

### 💡 Justificativa

O sistema precisa de um ponto de acesso único e controlado à conexão com o banco de dados. Conforme o material de aula, este padrão é ideal para o gerenciamento de "conexão com banco de dados", garantindo eficiência e consistência.

### 🔧 Implementação

#### Características do Singleton implementadas:

- **Construtor privado:** Impede a criação de instâncias fora da classe
- **Método `__clone()` privado:** Impede a clonagem da instância
- **Método `__wakeup()` público:** Impede a desserialização
- **Método estático `getInstance()`:** Único ponto de acesso à instância
- **Propriedade estática `$instance`:** Armazena a única instância

#### Código Principal:

```php
class Database
{
  private static ?Database $instance = null;
  private ?PDO $connection = null;

  // Construtor privado - impede instanciação direta
  private function __construct() {}

  // Previne a clonagem
  private function __clone() {}

  // Previne a desserialização
  public function __wakeup() {
    throw new \Exception("Não é possível desserializar um Singleton");
  }

  // Único ponto de acesso à instância
  public static function getInstance(): Database {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function getConnection(): PDO {
    if ($this->connection !== null) {
      return $this->connection;
    }
    // Cria conexão (Lazy Loading)
    // ...
    return $this->connection;
  }
}
```

### 📖 Como Usar

```php
// ❌ ERRADO - Não funciona (construtor privado)
$db = new Database();

// ✅ CORRETO - Obtém a instância única
$database = Database::getInstance();
$connection = $database->getConnection();

// Sempre retorna a mesma instância
$db1 = Database::getInstance();
$db2 = Database::getInstance();
// $db1 === $db2 → true
```

### 🎁 Benefícios

- ✅ Economia de recursos (uma única conexão compartilhada)
- ✅ Controle centralizado de acesso ao banco
- ✅ Consistência em toda a aplicação
- ✅ Implementa Lazy Loading (conexão criada sob demanda)

---

## 2. Facade (Estrutural)

### 📍 Localização

**Arquivo:** `app/Controllers/UsuarioController.php`

### 🎯 Objetivo

Fornecer uma interface simplificada para o complexo subsistema de autenticação de usuários, escondendo a complexidade interna.

### 💡 Justificativa

O processo de login é complexo, envolvendo múltiplas etapas:

- Recepção e validação de dados
- Validação de token CSRF
- Consulta ao `UsuarioModel`
- Verificação de senhas com hash
- Gerenciamento de sessões (`$_SESSION`)
- Regeneração de IDs de sessão
- Controle de mensagens flash
- Redirecionamentos

O `UsuarioController` atua como uma **fachada**, escondendo toda essa complexidade. O roteador simplesmente chama `login()`, e o controller coordena todas as etapas internas.

### 🔧 Implementação

#### Subsistema Complexo (escondido pela Facade):

```php
class UsuarioController extends BaseController
{
  // INTERFACE SIMPLES (Facade)
  public function login(): void {
    if ($this->isPost()) {
      $this->processarLogin(); // Esconde toda a complexidade
      return;
    }
    $this->render('usuario/login', $data);
  }

  // SUBSISTEMA COMPLEXO (escondido)
  private function processarLogin(): void {
    // 1. Validação CSRF
    if (!$this->validateCsrfToken($dados['csrf_token'])) {
      // ...
    }

    // 2. Validação de campos
    if (empty($dados['email']) || empty($dados['senha'])) {
      // ...
    }

    // 3. Validação no banco
    $usuario = $this->usuarioModel->validarLogin($email, $senha);

    // 4. Verificação de senha hash
    // (realizada no modelo)

    // 5. Criação de sessão segura
    $this->criarSessaoUsuario($usuario);

    // 6. Regeneração de token CSRF
    $this->regenerateCsrfToken();

    // 7. Mensagem de sucesso
    $this->setFlashMessage('success', 'Login realizado com sucesso!');

    // 8. Redirecionamento
    $this->redirect('/dashboard');
  }

  // Outros métodos complexos escondidos...
  private function criarSessaoUsuario($usuario) { /* ... */ }
  private function validarDadosCadastro($dados) { /* ... */ }
}
```

### 📖 Como Usar

```php
// No roteador (index.php ou routes.php)

// ✅ Interface SIMPLES - Toda complexidade escondida
$controller = new UsuarioController();
$controller->login();

// O controller coordena internamente:
// - Validações
// - Banco de dados
// - Sessões
// - Segurança
// - Redirecionamentos
```

### 🎁 Benefícios

- ✅ **Interface simplificada:** Fácil de usar pelo roteador
- ✅ **Subsistema desacoplado:** Rotas não conhecem a complexidade
- ✅ **Manutenibilidade:** Mudanças internas não afetam quem usa
- ✅ **Organização:** Toda lógica de autenticação centralizada
- ✅ **Reutilização:** Métodos podem ser usados por outras ações

---

## 3. Observer (Comportamental)

### 📍 Localização

**Arquivos:**

- `app/Interfaces/SubjectInterface.php` - Interface do Sujeito
- `app/Interfaces/ObserverInterface.php` - Interface do Observador
- `app/Models/ConteudoEstudo.php` - Sujeito (Subject)
- `app/Models/Meta.php` - Observado (recebe notificações)
- `app/Models/MetaObserver.php` - Observador (Observer)

### 🎯 Objetivo

Permitir que objetos `Meta` sejam notificados automaticamente quando o status de um `ConteudoEstudo` muda para 'CONCLUÍDO', sem que haja acoplamento direto entre as classes.

### 💡 Justificativa

Quando o status de um `ConteudoEstudo` muda para 'CONCLUÍDO', as `Metas` que incluem esse conteúdo precisam recalcular seu progresso. O padrão Observer permite que o `ConteudoEstudo` notifique as `Metas` sem conhecê-las diretamente, criando um sistema desacoplado e flexível.

**Analogia:** Como um canal do YouTube notificando todos os inscritos sobre um novo vídeo.

### 🔧 Implementação

#### 1. Interfaces

```php
// SubjectInterface - Quem é observado
interface SubjectInterface {
  public function attach(ObserverInterface $observer): void;
  public function detach(ObserverInterface $observer): void;
  public function notify($data = null): void;
}

// ObserverInterface - Quem observa
interface ObserverInterface {
  public function update(SubjectInterface $subject, $data = null): void;
}
```

#### 2. ConteudoEstudo (Subject)

```php
class ConteudoEstudo extends BaseModel implements SubjectInterface
{
  private array $observers = [];

  // Adiciona observador
  public function attach(ObserverInterface $observer): void {
    if (!in_array($observer, $this->observers, true)) {
      $this->observers[] = $observer;
    }
  }

  // Remove observador
  public function detach(ObserverInterface $observer): void {
    $key = array_search($observer, $this->observers, true);
    if ($key !== false) {
      unset($this->observers[$key]);
    }
  }

  // Notifica todos os observadores
  public function notify($data = null): void {
    foreach ($this->observers as $observer) {
      $observer->update($this, $data);
    }
  }

  // Altera status e notifica observadores
  public function alterarStatus(int $id, string $novoStatus): bool {
    // ... atualiza no banco ...

    // Se mudou para CONCLUÍDO, notifica observadores
    if ($resultado && $novoStatus === self::STATUS_CONCLUIDO) {
      $this->carregarObservadores($id);
      $this->notify([
        'conteudo_id' => $id,
        'novo_status' => $novoStatus,
        'evento' => 'conteudo_concluido'
      ]);
    }

    return $resultado;
  }
}
```

#### 3. MetaObserver (Observer)

```php
class MetaObserver implements ObserverInterface
{
  private Meta $meta;
  private int $metaId;

  public function __construct(int $metaId) {
    $this->metaId = $metaId;
    $this->meta = new Meta();
  }

  // Recebe notificação do ConteudoEstudo
  public function update(SubjectInterface $subject, $data = null): void {
    $conteudoId = $data['conteudo_id'] ?? null;
    $evento = $data['evento'] ?? null;

    if ($evento === 'conteudo_concluido' && $conteudoId) {
      // Verifica se conteúdo pertence a esta meta
      if ($this->conteudoPertenceAMeta($conteudoId)) {
        // Marca conteúdo como concluído e recalcula progresso
        $this->meta->marcarConteudoConcluido($this->metaId, $conteudoId);
      }
    }
  }
}
```

### 📖 Fluxo de Execução

```
1. Usuário marca ConteudoEstudo como concluído
   ↓
2. ConteudoEstudo::alterarStatus() é chamado
   ↓
3. Status atualizado no banco de dados
   ↓
4. ConteudoEstudo::carregarObservadores() carrega Metas relacionadas
   ↓
5. ConteudoEstudo::notify() notifica todos os MetaObserver registrados
   ↓
6. MetaObserver::update() é chamado para cada observador
   ↓
7. MetaObserver verifica se conteúdo pertence à Meta
   ↓
8. Meta::marcarConteudoConcluido() marca conteúdo e recalcula progresso
   ↓
9. Meta::calcularProgresso() atualiza percentual da meta
   ↓
10. Se meta atingiu 100%, é marcada como concluída automaticamente
```

### 📖 Como Usar

```php
// Cenário: Usuário conclui um conteúdo de estudo

// 1. Carregar o conteúdo
$conteudoModel = new ConteudoEstudo();
$conteudoId = 5;

// 2. Alterar status para CONCLUÍDO
// O padrão Observer entra em ação automaticamente!
$conteudoModel->alterarStatus($conteudoId, ConteudoEstudo::STATUS_CONCLUIDO);

// 3. Internamente, o sistema:
//    - Carrega todas as Metas que incluem este conteúdo
//    - Registra MetaObserver para cada Meta
//    - Notifica todos os observadores
//    - Cada Meta recalcula seu progresso automaticamente

// 4. Resultado: Metas atualizadas automaticamente! 🎉
```

### 🎁 Benefícios

- ✅ **Desacoplamento:** ConteudoEstudo não conhece Meta diretamente
- ✅ **Flexibilidade:** Múltiplas Metas podem observar o mesmo conteúdo
- ✅ **Extensibilidade:** Novos observadores podem ser adicionados facilmente
- ✅ **Atualização automática:** Metas não precisam verificar mudanças manualmente
- ✅ **Reatividade:** Mudanças propagadas instantaneamente
- ✅ **Open/Closed:** Aberto para novos observadores, fechado para modificação

### 🔍 Por que MetaObserver?

Usamos a classe `MetaObserver` como **adaptador** para resolver um conflito:

- `ObserverInterface::update()` - método do padrão Observer
- `BaseModel::update()` - método para atualizar dados no banco

Sem o adaptador, haveria conflito de nomes na classe `Meta`.

---

## 📚 Resumo

| Padrão        | Tipo           | Classe                           | Benefício Principal                 |
| ------------- | -------------- | -------------------------------- | ----------------------------------- |
| **Singleton** | Criacional     | `Database`                       | Uma única conexão com BD            |
| **Facade**    | Estrutural     | `UsuarioController`              | Interface simples para autenticação |
| **Observer**  | Comportamental | `ConteudoEstudo`, `MetaObserver` | Notificação automática de mudanças  |

---

## 🎓 Referências

- Gamma, E., Helm, R., Johnson, R., & Vlissides, J. (1994). **Design Patterns: Elements of Reusable Object-Oriented Software**. Addison-Wesley.
- Material de aula sobre Padrões de Projeto GOF
- Documentação PHP: [Design Patterns in PHP](https://www.php.net/manual/en/language.oop5.patterns.php)

---

## 📝 Notas de Implementação

### Singleton

- ✅ Construtor privado implementado
- ✅ Clone e desserialização bloqueados
- ✅ Lazy Loading na conexão
- ✅ Compatibilidade com código legado mantida

### Facade

- ✅ Complexidade escondida nos métodos privados
- ✅ Interface pública simples e intuitiva
- ✅ Coordenação de múltiplos subsistemas
- ✅ Mensagens e redirecionamentos centralizados

### Observer

- ✅ Interfaces claras e bem documentadas
- ✅ Desacoplamento total entre Subject e Observer
- ✅ Adaptador para resolver conflito de nomes
- ✅ Sistema escalável para múltiplos observadores
- ✅ Logs para rastreamento de notificações

---

**Desenvolvido com 💙 aplicando os melhores padrões de projeto**
