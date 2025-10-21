<?php

namespace App\Patterns;

use App\Models\Disciplina;
use App\Models\Tarefa;
use App\Models\Pomodoro;
use App\Models\EventoCalendario;
use App\Models\Anotacao;

/**
 * Factory Pattern - Criação centralizada de Models
 *
 * Benefícios:
 * - Encapsula lógica de criação de objetos
 * - Facilita manutenção e testes
 * - Permite adicionar lógica de inicialização
 */
class ModelFactory
{
  private static array $instances = [];

  /**
   * Cria ou retorna instância de Disciplina
   */
  public static function createDisciplina(): Disciplina
  {
    if (!isset(self::$instances['disciplina'])) {
      self::$instances['disciplina'] = new Disciplina();
    }
    return self::$instances['disciplina'];
  }

  /**
   * Cria ou retorna instância de Tarefa
   */
  public static function createTarefa(): Tarefa
  {
    if (!isset(self::$instances['tarefa'])) {
      self::$instances['tarefa'] = new Tarefa();
    }
    return self::$instances['tarefa'];
  }

  /**
   * Cria ou retorna instância de Pomodoro
   */
  public static function createPomodoro(): Pomodoro
  {
    if (!isset(self::$instances['pomodoro'])) {
      self::$instances['pomodoro'] = new Pomodoro();
    }
    return self::$instances['pomodoro'];
  }

  /**
   * Cria ou retorna instância de EventoCalendario
   */
  public static function createEvento(): EventoCalendario
  {
    if (!isset(self::$instances['evento'])) {
      self::$instances['evento'] = new EventoCalendario();
    }
    return self::$instances['evento'];
  }

  /**
   * Cria ou retorna instância de Anotacao
   */
  public static function createAnotacao(): Anotacao
  {
    if (!isset(self::$instances['anotacao'])) {
      self::$instances['anotacao'] = new Anotacao();
    }
    return self::$instances['anotacao'];
  }

  /**
   * Limpa todas as instâncias (útil para testes)
   */
  public static function clearInstances(): void
  {
    self::$instances = [];
  }
}
