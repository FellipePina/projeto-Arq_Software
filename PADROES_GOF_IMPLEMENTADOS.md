# Padrões GOF Implementados no Sistema

## 📚 Visão Geral

Este documento descreve os padrões de projeto GOF (Gang of Four) implementados no sistema de gerenciamento de estudos, explicando onde e como cada padrão é usado.

---

## 1️⃣ **Singleton Pattern** (Criacional)

### 📍 Localização

- `app/Models/Database.php`

### 🎯 Propósito

Garante que apenas uma instância da conexão com banco de dados exista em toda a aplicação.

### 💡 Benefícios

- Economia de recursos (uma única conexão)
- Ponto de acesso global
- Controle sobre instanciação

### 📝 Exemplo de Uso

```php
$db = Database::getInstance()->getConnection();
```

---

## 2️⃣ **Observer Pattern** (Comportamental)

### 📍 Localização

- `app/Interfaces/ObserverInterface.php`
- `app/Interfaces/SubjectInterface.php`
- `app/Models/MetaObserver.php`

### 🎯 Propósito

Notifica observadores quando o estado de metas muda, permitindo atualizações automáticas.

### 💡 Benefícios

- Baixo acoplamento entre objetos
- Reação automática a mudanças
- Fácil adição de novos observadores

### 📝 Exemplo de Uso

```php
$meta = new Meta();
$observer = new MetaObserver();
$meta->attach($observer);
$meta->atualizar($id, $dados); // Observer é notificado automaticamente
```

---

## 3️⃣ **Factory Pattern** (Criacional)

### 📍 Localização

- `app/Patterns/ModelFactory.php`

### 🎯 Propósito

Centraliza criação de objetos Model, encapsulando lógica de instanciação.

### 💡 Benefícios

- Código mais limpo nos controllers
- Facilita testes (pode retornar mocks)
- Reutilização de instâncias

### 📝 Exemplo de Uso

```php
$tarefaModel = ModelFactory::createTarefa();
$disciplinaModel = ModelFactory::createDisciplina();
```

---

## 4️⃣ **Strategy Pattern** (Comportamental)

### 📍 Localização

- `app/Patterns/NotificationStrategy.php`

### 🎯 Propósito

Permite diferentes estratégias de notificação (sessão, email, push) sem alterar código.

### 💡 Benefícios

- Fácil troca de algoritmos
- Código aberto para extensão
- Evita condicionais complexos

### 📝 Exemplo de Uso

```php
// Notificação por sessão
$notifier = new NotificationContext(new SessionNotificationStrategy());
$notifier->notify($userId, 'Título', 'Mensagem');

// Trocar para email
$notifier->setStrategy(new EmailNotificationStrategy());
$notifier->notify($userId, 'Título', 'Mensagem');
```

---

## 5️⃣ **Chain of Responsibility** (Comportamental)

### 📍 Localização

- `app/Patterns/ValidationChain.php`

### 🎯 Propósito

Valida dados em cadeia, permitindo adicionar/remover validadores dinamicamente.

### 💡 Benefícios

- Validação modular e reutilizável
- Fácil adição de novas regras
- Separação de responsabilidades

### 📝 Exemplo de Uso

```php
$validator = new RequiredFieldsValidator(['titulo', 'descricao']);
$validator->setNext(new StringLengthValidator([
    'titulo' => ['min' => 3, 'max' => 100]
]))->setNext(new DateValidator(['data_entrega']));

if ($validator->handle($dados)) {
    // Dados válidos
} else {
    $errors = $validator->getErrors();
}
```

---

## 6️⃣ **Decorator Pattern** (Estrutural)

### 📍 Localização

- `app/Patterns/TarefaDecorator.php`

### 🎯 Propósito

Adiciona comportamentos a tarefas dinamicamente (urgente, prazo próximo, complexa).

### 💡 Benefícios

- Adiciona funcionalidades sem modificar classe base
- Composição ao invés de herança
- Combinação flexível de comportamentos

### 📝 Exemplo de Uso

```php
$tarefa = new TarefaComponent('Estudar PHP', 10);

// Adiciona decorators
$tarefa = new TarefaUrgenteDecorator($tarefa); // +5 pontos
$tarefa = new TarefaPrazoProximoDecorator($tarefa, '2025-10-25'); // +3 pontos

echo $tarefa->getDescricao(); // "🔥 URGENTE: Estudar PHP ⏰ (até 2025-10-25)"
echo $tarefa->getPontos(); // 18 pontos
```

---

## 7️⃣ **Command Pattern** (Comportamental)

### 📍 Localização

- `app/Patterns/CommandPattern.php`

### 🎯 Propósito

Encapsula operações como objetos, permitindo desfazer/refazer e histórico.

### 💡 Benefícios

- Suporta undo/redo
- Histórico de operações
- Desacopla invocador de receptor

### 📝 Exemplo de Uso

```php
$invoker = new CommandInvoker();

// Criar tarefa
$command = new CreateTarefaCommand($tarefaModel, $dados);
$invoker->execute($command);

// Desfazer
if ($invoker->canUndo()) {
    $invoker->undo(); // Tarefa é excluída
}

// Refazer
if ($invoker->canRedo()) {
    $invoker->redo(); // Tarefa é recriada
}
```

---

## 8️⃣ **Template Method** (Comportamental)

### 📍 Localização

- `app/Patterns/RelatorioTemplate.php`

### 🎯 Propósito

Define esqueleto de algoritmo de exportação, permitindo subclasses personalizarem passos.

### 💡 Benefícios

- Evita duplicação de código
- Garante consistência no fluxo
- Flexibilidade para customização

### 📝 Exemplo de Uso

```php
// Exportar como CSV
$exporter = new CsvRelatorioExporter($dados, 'Relatório de Tarefas');
$csv = $exporter->export();
header('Content-Type: ' . $exporter->getContentType());
echo $csv;

// Exportar como JSON
$exporter = new JsonRelatorioExporter($dados, 'Relatório de Tarefas');
$json = $exporter->export();
```

---

## 📊 Mapa de Padrões por Categoria

### Criacionais

- ✅ **Singleton**: Database
- ✅ **Factory**: ModelFactory

### Estruturais

- ✅ **Decorator**: TarefaDecorator

### Comportamentais

- ✅ **Observer**: MetaObserver
- ✅ **Strategy**: NotificationStrategy
- ✅ **Chain of Responsibility**: ValidationChain
- ✅ **Command**: CommandPattern
- ✅ **Template Method**: RelatorioTemplate

---

## 🎓 Como Usar no Sistema

### 1. Validação de Dados

Use **Chain of Responsibility** nos controllers:

```php
$validator = new RequiredFieldsValidator(['titulo']);
$validator->setNext(new StringLengthValidator(['titulo' => ['min' => 3]]));

if (!$validator->handle($_POST)) {
    foreach ($validator->getErrors() as $error) {
        $this->setFlashMessage('error', $error);
    }
    return;
}
```

### 2. Notificações

Use **Strategy** para enviar notificações:

```php
$strategy = $user->preferEmail()
    ? new EmailNotificationStrategy()
    : new SessionNotificationStrategy();

$notifier = new NotificationContext($strategy);
$notifier->notify($userId, 'Lembrete', 'Você tem tarefas pendentes');
```

### 3. Pontuação Dinâmica

Use **Decorator** para calcular pontos:

```php
$tarefa = new TarefaComponent($dados['titulo'], 10);

if ($dados['prioridade'] === 'urgente') {
    $tarefa = new TarefaUrgenteDecorator($tarefa);
}

if ($subtarefas > 0) {
    $tarefa = new TarefaComplexaDecorator($tarefa, $subtarefas);
}

$pontos = $tarefa->getPontos(); // Pontos calculados dinamicamente
```

### 4. Exportação de Relatórios

Use **Template Method**:

```php
$formato = $_GET['formato'] ?? 'csv';

$exporter = match($formato) {
    'json' => new JsonRelatorioExporter($dados, $titulo),
    'html' => new HtmlRelatorioExporter($dados, $titulo),
    default => new CsvRelatorioExporter($dados, $titulo)
};

header('Content-Type: ' . $exporter->getContentType());
echo $exporter->export();
```

---

## 🔗 Princípios SOLID Aplicados

Todos os padrões seguem SOLID:

- **S**ingle Responsibility: Cada classe tem uma única responsabilidade
- **O**pen/Closed: Aberto para extensão, fechado para modificação
- **L**iskov Substitution: Subclasses podem substituir classes base
- **I**nterface Segregation: Interfaces específicas e focadas
- **D**ependency Inversion: Dependência de abstrações, não implementações

---

## 📚 Referências

- Design Patterns: Elements of Reusable Object-Oriented Software (GoF)
- PHP The Right Way
- SOLID Principles
- Clean Code by Robert C. Martin
