<?php

namespace App\Models;

/**
 * Classe ConfiguracaoUsuario - Modelo para gerenciar configurações do usuário
 *
 * Princípios SOLID:
 * - Single Responsibility: gerencia apenas configurações
 */
class ConfiguracaoUsuario extends BaseModel
{
  protected string $table = 'configuracoes_usuario';

  /**
   * Busca configurações do usuário
   *
   * @param int $usuarioId ID do usuário
   * @return array|false Configurações
   */
  public function buscarPorUsuario(int $usuarioId)
  {
    $sql = "SELECT * FROM {$this->table} WHERE usuario_id = :usuario_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    $config = $stmt->fetch();

    // Se não existir, cria com valores padrão
    if (!$config) {
      $this->criarPadrao($usuarioId);
      return $this->buscarPorUsuario($usuarioId);
    }

    return $config;
  }

  /**
   * Cria configurações padrão para um usuário
   *
   * @param int $usuarioId ID do usuário
   * @return int|false ID das configurações
   */
  private function criarPadrao(int $usuarioId)
  {
    $configPadrao = [
      'usuario_id' => $usuarioId,
      'pomodoro_foco_minutos' => 25,
      'pomodoro_pausa_curta_minutos' => 5,
      'pomodoro_pausa_longa_minutos' => 15,
      'pomodoro_ciclos_ate_pausa_longa' => 4,
      'pomodoro_som_ativo' => 1,
      'pomodoro_notificacao_ativa' => 1,
      'tema' => 'claro',
      'idioma' => 'pt-BR',
      'notificacao_tarefas' => 1,
      'notificacao_eventos' => 1,
      'notificacao_metas' => 1
    ];

    return $this->save($configPadrao);
  }

  /**
   * Atualiza configurações do usuário
   *
   * @param int $usuarioId ID do usuário
   * @param array $dados Novos dados
   * @return bool Sucesso
   */
  public function atualizar(int $usuarioId, array $dados): bool
  {
    $config = $this->buscarPorUsuario($usuarioId);
    if (!$config) {
      return false;
    }

    $dadosAtualizados = [
      'id' => $config['id'],
      'pomodoro_foco_minutos' => !empty($dados['pomodoro_foco_minutos'])
        ? (int) $dados['pomodoro_foco_minutos']
        : $config['pomodoro_foco_minutos'],
      'pomodoro_pausa_curta_minutos' => !empty($dados['pomodoro_pausa_curta_minutos'])
        ? (int) $dados['pomodoro_pausa_curta_minutos']
        : $config['pomodoro_pausa_curta_minutos'],
      'pomodoro_pausa_longa_minutos' => !empty($dados['pomodoro_pausa_longa_minutos'])
        ? (int) $dados['pomodoro_pausa_longa_minutos']
        : $config['pomodoro_pausa_longa_minutos'],
      'pomodoro_ciclos_ate_pausa_longa' => !empty($dados['pomodoro_ciclos_ate_pausa_longa'])
        ? (int) $dados['pomodoro_ciclos_ate_pausa_longa']
        : $config['pomodoro_ciclos_ate_pausa_longa'],
      'pomodoro_som_ativo' => isset($dados['pomodoro_som_ativo'])
        ? (int) $dados['pomodoro_som_ativo']
        : $config['pomodoro_som_ativo'],
      'pomodoro_notificacao_ativa' => isset($dados['pomodoro_notificacao_ativa'])
        ? (int) $dados['pomodoro_notificacao_ativa']
        : $config['pomodoro_notificacao_ativa'],
      'tema' => !empty($dados['tema'])
        ? $dados['tema']
        : $config['tema'],
      'idioma' => !empty($dados['idioma'])
        ? $dados['idioma']
        : $config['idioma'],
      'notificacao_tarefas' => isset($dados['notificacao_tarefas'])
        ? (int) $dados['notificacao_tarefas']
        : $config['notificacao_tarefas'],
      'notificacao_eventos' => isset($dados['notificacao_eventos'])
        ? (int) $dados['notificacao_eventos']
        : $config['notificacao_eventos'],
      'notificacao_metas' => isset($dados['notificacao_metas'])
        ? (int) $dados['notificacao_metas']
        : $config['notificacao_metas']
    ];

    return $this->save($dadosAtualizados) !== false;
  }

  /**
   * Busca duração do Pomodoro configurada
   *
   * @param int $usuarioId ID do usuário
   * @return int Duração em minutos
   */
  public function buscarDuracaoPomodoro(int $usuarioId): int
  {
    $config = $this->buscarPorUsuario($usuarioId);
    return (int) ($config['pomodoro_foco_minutos'] ?? 25);
  }

  /**
   * Busca duração da pausa curta configurada
   *
   * @param int $usuarioId ID do usuário
   * @return int Duração em minutos
   */
  public function buscarDuracaoPausaCurta(int $usuarioId): int
  {
    $config = $this->buscarPorUsuario($usuarioId);
    return (int) ($config['pomodoro_pausa_curta_minutos'] ?? 5);
  }

  /**
   * Busca duração da pausa longa configurada
   *
   * @param int $usuarioId ID do usuário
   * @return int Duração em minutos
   */
  public function buscarDuracaoPausaLonga(int $usuarioId): int
  {
    $config = $this->buscarPorUsuario($usuarioId);
    return (int) ($config['pomodoro_pausa_longa_minutos'] ?? 15);
  }

  /**
   * Verifica se notificações estão ativadas
   *
   * @param int $usuarioId ID do usuário
   * @return bool Notificações ativadas
   */
  public function notificacoesAtivadas(int $usuarioId): bool
  {
    $config = $this->buscarPorUsuario($usuarioId);
    return (bool) ($config['pomodoro_notificacao_ativa'] ?? true);
  }

  /**
   * Alterna tema (claro/escuro)
   *
   * @param int $usuarioId ID do usuário
   * @return bool Sucesso
   */
  public function alternarTema(int $usuarioId): bool
  {
    $config = $this->buscarPorUsuario($usuarioId);
    if (!$config) {
      return false;
    }

    $novoTema = $config['tema'] === 'claro' ? 'escuro' : 'claro';

    $sql = "UPDATE {$this->table}
            SET tema = :tema
            WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':tema', $novoTema);
    $stmt->bindValue(':id', $config['id'], \PDO::PARAM_INT);

    return $stmt->execute();
  }
}
