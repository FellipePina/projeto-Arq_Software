<?php

namespace App\Patterns;

/**
 * Chain of Responsibility Pattern - Validação em cadeia
 *
 * Permite adicionar/remover validadores sem alterar código existente
 */
abstract class ValidationHandler
{
  protected ?ValidationHandler $nextHandler = null;
  protected array $errors = [];

  /**
   * Define próximo handler na cadeia
   */
  public function setNext(ValidationHandler $handler): ValidationHandler
  {
    $this->nextHandler = $handler;
    return $handler;
  }

  /**
   * Processa validação
   */
  public function handle(array $data): bool
  {
    $isValid = $this->validate($data);

    if ($isValid && $this->nextHandler) {
      return $this->nextHandler->handle($data);
    }

    return $isValid;
  }

  /**
   * Retorna erros acumulados
   */
  public function getErrors(): array
  {
    $errors = $this->errors;

    if ($this->nextHandler) {
      $errors = array_merge($errors, $this->nextHandler->getErrors());
    }

    return $errors;
  }

  /**
   * Método abstrato para validação específica
   */
  abstract protected function validate(array $data): bool;
}

/**
 * Validador de campos obrigatórios
 */
class RequiredFieldsValidator extends ValidationHandler
{
  private array $requiredFields;

  public function __construct(array $requiredFields)
  {
    $this->requiredFields = $requiredFields;
  }

  protected function validate(array $data): bool
  {
    $isValid = true;

    foreach ($this->requiredFields as $field) {
      if (empty($data[$field])) {
        $this->errors[] = "Campo '{$field}' é obrigatório";
        $isValid = false;
      }
    }

    return $isValid;
  }
}

/**
 * Validador de tamanho de string
 */
class StringLengthValidator extends ValidationHandler
{
  private array $rules;

  /**
   * @param array $rules ['campo' => ['min' => 3, 'max' => 100]]
   */
  public function __construct(array $rules)
  {
    $this->rules = $rules;
  }

  protected function validate(array $data): bool
  {
    $isValid = true;

    foreach ($this->rules as $field => $limits) {
      if (!isset($data[$field])) {
        continue;
      }

      $length = strlen($data[$field]);
      $min = $limits['min'] ?? 0;
      $max = $limits['max'] ?? PHP_INT_MAX;

      if ($length < $min) {
        $this->errors[] = "Campo '{$field}' deve ter no mínimo {$min} caracteres";
        $isValid = false;
      }

      if ($length > $max) {
        $this->errors[] = "Campo '{$field}' deve ter no máximo {$max} caracteres";
        $isValid = false;
      }
    }

    return $isValid;
  }
}

/**
 * Validador de formato de email
 */
class EmailValidator extends ValidationHandler
{
  private array $emailFields;

  public function __construct(array $emailFields)
  {
    $this->emailFields = $emailFields;
  }

  protected function validate(array $data): bool
  {
    $isValid = true;

    foreach ($this->emailFields as $field) {
      if (isset($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
        $this->errors[] = "Campo '{$field}' deve ser um email válido";
        $isValid = false;
      }
    }

    return $isValid;
  }
}

/**
 * Validador de datas
 */
class DateValidator extends ValidationHandler
{
  private array $dateFields;

  public function __construct(array $dateFields)
  {
    $this->dateFields = $dateFields;
  }

  protected function validate(array $data): bool
  {
    $isValid = true;

    foreach ($this->dateFields as $field) {
      if (isset($data[$field]) && !empty($data[$field])) {
        $date = \DateTime::createFromFormat('Y-m-d', $data[$field]);
        if (!$date || $date->format('Y-m-d') !== $data[$field]) {
          $this->errors[] = "Campo '{$field}' deve ser uma data válida (Y-m-d)";
          $isValid = false;
        }
      }
    }

    return $isValid;
  }
}

/**
 * Validador customizado com callback
 */
class CustomValidator extends ValidationHandler
{
  private $callback;
  private string $errorMessage;

  public function __construct(callable $callback, string $errorMessage)
  {
    $this->callback = $callback;
    $this->errorMessage = $errorMessage;
  }

  protected function validate(array $data): bool
  {
    $isValid = ($this->callback)($data);

    if (!$isValid) {
      $this->errors[] = $this->errorMessage;
    }

    return $isValid;
  }
}
