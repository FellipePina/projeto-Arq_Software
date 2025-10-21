<?php

namespace App\Patterns;

/**
 * Command Pattern - Encapsula operações como objetos
 *
 * Útil para desfazer/refazer ações e histórico
 */
interface CommandInterface
{
  public function execute(): bool;
  public function undo(): bool;
  public function getDescription(): string;
}

/**
 * Command para criar tarefa
 */
class CreateTarefaCommand implements CommandInterface
{
  private $tarefaModel;
  private array $dados;
  private ?int $tarefaId = null;

  public function __construct($tarefaModel, array $dados)
  {
    $this->tarefaModel = $tarefaModel;
    $this->dados = $dados;
  }

  public function execute(): bool
  {
    $this->tarefaId = $this->tarefaModel->criar($this->dados);
    return $this->tarefaId !== false;
  }

  public function undo(): bool
  {
    if ($this->tarefaId) {
      return $this->tarefaModel->delete($this->tarefaId);
    }
    return false;
  }

  public function getDescription(): string
  {
    return "Criar tarefa: {$this->dados['titulo']}";
  }
}

/**
 * Command para completar tarefa
 */
class CompleteTarefaCommand implements CommandInterface
{
  private $tarefaModel;
  private int $tarefaId;
  private int $usuarioId;

  public function __construct($tarefaModel, int $tarefaId, int $usuarioId)
  {
    $this->tarefaModel = $tarefaModel;
    $this->tarefaId = $tarefaId;
    $this->usuarioId = $usuarioId;
  }

  public function execute(): bool
  {
    return $this->tarefaModel->marcarConcluida($this->tarefaId, $this->usuarioId);
  }

  public function undo(): bool
  {
    return $this->tarefaModel->marcarPendente($this->tarefaId, $this->usuarioId);
  }

  public function getDescription(): string
  {
    return "Completar tarefa #{$this->tarefaId}";
  }
}

/**
 * Invoker - Gerencia comandos e histórico
 */
class CommandInvoker
{
  private array $history = [];
  private int $currentPosition = -1;

  /**
   * Executa comando e adiciona ao histórico
   */
  public function execute(CommandInterface $command): bool
  {
    $result = $command->execute();

    if ($result) {
      // Remove comandos após a posição atual (se houver redo)
      array_splice($this->history, $this->currentPosition + 1);

      // Adiciona novo comando
      $this->history[] = $command;
      $this->currentPosition++;
    }

    return $result;
  }

  /**
   * Desfaz último comando
   */
  public function undo(): bool
  {
    if ($this->currentPosition < 0) {
      return false;
    }

    $command = $this->history[$this->currentPosition];
    $result = $command->undo();

    if ($result) {
      $this->currentPosition--;
    }

    return $result;
  }

  /**
   * Refaz comando desfeito
   */
  public function redo(): bool
  {
    if ($this->currentPosition >= count($this->history) - 1) {
      return false;
    }

    $this->currentPosition++;
    $command = $this->history[$this->currentPosition];

    return $command->execute();
  }

  /**
   * Verifica se pode desfazer
   */
  public function canUndo(): bool
  {
    return $this->currentPosition >= 0;
  }

  /**
   * Verifica se pode refazer
   */
  public function canRedo(): bool
  {
    return $this->currentPosition < count($this->history) - 1;
  }

  /**
   * Retorna histórico
   */
  public function getHistory(): array
  {
    return array_map(fn($cmd) => $cmd->getDescription(), $this->history);
  }
}
