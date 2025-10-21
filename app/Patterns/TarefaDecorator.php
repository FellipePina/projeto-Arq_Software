<?php

namespace App\Patterns;

/**
 * Decorator Pattern - Adiciona funcionalidades a objetos dinamicamente
 *
 * Usado para adicionar comportamentos a tarefas sem modificar a classe base
 */
interface TarefaInterface
{
  public function getDescricao(): string;
  public function getPontos(): int;
}

/**
 * Tarefa base
 */
class TarefaComponent implements TarefaInterface
{
  protected string $titulo;
  protected int $pontos;

  public function __construct(string $titulo, int $pontos = 10)
  {
    $this->titulo = $titulo;
    $this->pontos = $pontos;
  }

  public function getDescricao(): string
  {
    return $this->titulo;
  }

  public function getPontos(): int
  {
    return $this->pontos;
  }
}

/**
 * Decorator base
 */
abstract class TarefaDecorator implements TarefaInterface
{
  protected TarefaInterface $tarefa;

  public function __construct(TarefaInterface $tarefa)
  {
    $this->tarefa = $tarefa;
  }

  public function getDescricao(): string
  {
    return $this->tarefa->getDescricao();
  }

  public function getPontos(): int
  {
    return $this->tarefa->getPontos();
  }
}

/**
 * Decorator para tarefas urgentes
 */
class TarefaUrgenteDecorator extends TarefaDecorator
{
  public function getDescricao(): string
  {
    return "🔥 URGENTE: " . $this->tarefa->getDescricao();
  }

  public function getPontos(): int
  {
    return $this->tarefa->getPontos() + 5; // +5 pontos bônus
  }
}

/**
 * Decorator para tarefas com prazo próximo
 */
class TarefaPrazoProximoDecorator extends TarefaDecorator
{
  private string $dataLimite;

  public function __construct(TarefaInterface $tarefa, string $dataLimite)
  {
    parent::__construct($tarefa);
    $this->dataLimite = $dataLimite;
  }

  public function getDescricao(): string
  {
    return $this->tarefa->getDescricao() . " ⏰ (até {$this->dataLimite})";
  }

  public function getPontos(): int
  {
    return $this->tarefa->getPontos() + 3; // +3 pontos bônus
  }
}

/**
 * Decorator para tarefas complexas (com subtarefas)
 */
class TarefaComplexaDecorator extends TarefaDecorator
{
  private int $numeroSubtarefas;

  public function __construct(TarefaInterface $tarefa, int $numeroSubtarefas)
  {
    parent::__construct($tarefa);
    $this->numeroSubtarefas = $numeroSubtarefas;
  }

  public function getDescricao(): string
  {
    return $this->tarefa->getDescricao() . " 📋 ({$this->numeroSubtarefas} subtarefas)";
  }

  public function getPontos(): int
  {
    return $this->tarefa->getPontos() + ($this->numeroSubtarefas * 2);
  }
}
