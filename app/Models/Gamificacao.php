<?php

namespace App\Models;

/**
 * Classe Gamificacao - Modelo para gerenciar gamificação do usuário
 *
 * Princípios SOLID:
 * - Single Responsibility: gerencia apenas gamificação
 */
class Gamificacao extends BaseModel
{
  protected string $table = 'gamificacao';

  /**
   * Busca dados de gamificação do usuário
   *
   * @param int $usuarioId ID do usuário
   * @return array|false Dados de gamificação
   */
  public function buscarPorUsuario(int $usuarioId)
  {
    $sql = "SELECT * FROM {$this->table} WHERE usuario_id = :usuario_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    $gamificacao = $stmt->fetch();

    // Se não existir, cria registro inicial
    if (!$gamificacao) {
      $this->criar($usuarioId);
      return $this->buscarPorUsuario($usuarioId);
    }

    return $gamificacao;
  }

  /**
   * Cria registro de gamificação inicial
   *
   * @param int $usuarioId ID do usuário
   * @return int|false ID do registro
   */
  private function criar(int $usuarioId)
  {
    $dados = [
      'usuario_id' => $usuarioId,
      'pontos_total' => 0,
      'nivel' => 1,
      'streak_dias' => 0,
      'melhor_streak' => 0,
      'ultimo_acesso' => date('Y-m-d'),
      'pomodoros_concluidos' => 0,
      'tarefas_concluidas' => 0
    ];

    return $this->save($dados);
  }

  /**
   * Adiciona pontos ao usuário
   *
   * @param int $usuarioId ID do usuário
   * @param int $pontos Pontos a adicionar
   * @return bool Sucesso
   */
  public function adicionarPontos(int $usuarioId, int $pontos): bool
  {
    $gamificacao = $this->buscarPorUsuario($usuarioId);
    if (!$gamificacao) {
      return false;
    }

    $novosPontos = $gamificacao['pontos_total'] + $pontos;
    $novoNivel = $this->calcularNivel($novosPontos);

    $sql = "UPDATE {$this->table}
            SET pontos_total = :pontos,
                nivel = :nivel
            WHERE usuario_id = :usuario_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':pontos', $novosPontos, \PDO::PARAM_INT);
    $stmt->bindValue(':nivel', $novoNivel, \PDO::PARAM_INT);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Calcula nível baseado nos pontos
   * Fórmula: nível = floor(pontos / 100) + 1
   *
   * @param int $pontos Total de pontos
   * @return int Nível
   */
  private function calcularNivel(int $pontos): int
  {
    return floor($pontos / 100) + 1;
  }

  /**
   * Atualiza sequência de dias
   *
   * @param int $usuarioId ID do usuário
   * @return bool Sucesso
   */
  public function atualizarSequencia(int $usuarioId): bool
  {
    $gamificacao = $this->buscarPorUsuario($usuarioId);
    if (!$gamificacao) {
      return false;
    }

    $hoje = date('Y-m-d');
    $ultimaAtividade = $gamificacao['ultimo_acesso'];
    $ontem = date('Y-m-d', strtotime('-1 day'));

    // Se última atividade foi hoje, não faz nada
    if ($ultimaAtividade === $hoje) {
      return true;
    }

    // Se última atividade foi ontem, incrementa sequência
    if ($ultimaAtividade === $ontem) {
      $novaSequencia = $gamificacao['streak_dias'] + 1;
      $maiorSequencia = max($gamificacao['melhor_streak'], $novaSequencia);
    } else {
      // Perdeu a sequência
      $novaSequencia = 1;
      $maiorSequencia = $gamificacao['melhor_streak'];
    }

    $sql = "UPDATE {$this->table}
            SET streak_dias = :sequencia,
                melhor_streak = :maior_sequencia,
                ultimo_acesso = :hoje
            WHERE usuario_id = :usuario_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':sequencia', $novaSequencia, \PDO::PARAM_INT);
    $stmt->bindValue(':maior_sequencia', $maiorSequencia, \PDO::PARAM_INT);
    $stmt->bindValue(':hoje', $hoje);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Busca conquistas do usuário
   *
   * @param int $usuarioId ID do usuário
   * @return array Lista de conquistas
   */
  public function buscarConquistas(int $usuarioId): array
  {
    $sql = "SELECT c.*, uc.data_obtencao, uc.id as obtida
            FROM conquistas c
            LEFT JOIN usuario_conquistas uc ON uc.conquista_id = c.id
              AND uc.usuario_id = :usuario_id
            ORDER BY uc.data_obtencao DESC, c.pontos DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Verifica e desbloqueia conquistas
   *
   * @param int $usuarioId ID do usuário
   * @return array Conquistas desbloqueadas
   */
  public function verificarConquistas(int $usuarioId): array
  {
    $gamificacao = $this->buscarPorUsuario($usuarioId);
    $conquistasDesbloqueadas = [];

    // Busca todas as conquistas
    $sql = "SELECT * FROM conquistas";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $conquistas = $stmt->fetchAll();

    foreach ($conquistas as $conquista) {
      // Verifica se já possui a conquista
      $sqlCheck = "SELECT * FROM usuario_conquistas
                   WHERE usuario_id = :usuario_id
                   AND conquista_id = :conquista_id";
      $stmtCheck = $this->db->prepare($sqlCheck);
      $stmtCheck->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
      $stmtCheck->bindValue(':conquista_id', $conquista['id'], \PDO::PARAM_INT);
      $stmtCheck->execute();

      if ($stmtCheck->fetch()) {
        continue; // Já possui
      }

      // Verifica critério baseado no identificador
      $desbloqueou = false;
      switch ($conquista['identificador']) {
        case 'primeiro_pomodoro':
          $desbloqueou = $this->verificarPrimeiroPomodoro($usuarioId);
          break;
        case 'streak_7_dias':
          $desbloqueou = $gamificacao['streak_dias'] >= 7;
          break;
        case 'streak_30_dias':
          $desbloqueou = $gamificacao['streak_dias'] >= 30;
          break;
        case 'nivel_5':
          $desbloqueou = $gamificacao['nivel'] >= 5;
          break;
        case 'nivel_10':
          $desbloqueou = $gamificacao['nivel'] >= 10;
          break;
        case '10_pomodoros':
          $desbloqueou = $this->contarPomodorosCompletos($usuarioId) >= 10;
          break;
        case '50_pomodoros':
          $desbloqueou = $this->contarPomodorosCompletos($usuarioId) >= 50;
          break;
        case '100_pomodoros':
          $desbloqueou = $this->contarPomodorosCompletos($usuarioId) >= 100;
          break;
      }

      if ($desbloqueou) {
        $this->desbloquearConquista($usuarioId, $conquista['id']);
        $this->adicionarPontos($usuarioId, $conquista['pontos']);
        $conquistasDesbloqueadas[] = $conquista;
      }
    }

    return $conquistasDesbloqueadas;
  }

  /**
   * Desbloqueia uma conquista
   *
   * @param int $usuarioId ID do usuário
   * @param int $conquistaId ID da conquista
   * @return bool Sucesso
   */
  private function desbloquearConquista(int $usuarioId, int $conquistaId): bool
  {
    $sql = "INSERT INTO usuario_conquistas (usuario_id, conquista_id, data_obtencao)
            VALUES (:usuario_id, :conquista_id, NOW())";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->bindValue(':conquista_id', $conquistaId, \PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Verifica se completou primeiro Pomodoro
   *
   * @param int $usuarioId ID do usuário
   * @return bool Completou
   */
  private function verificarPrimeiroPomodoro(int $usuarioId): bool
  {
    $sql = "SELECT COUNT(*) as total FROM sessoes_pomodoro
            WHERE usuario_id = :usuario_id
            AND tipo = 'foco'
            AND finalizada = 1
            AND interrompida = 0";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    $resultado = $stmt->fetch();
    return (int) $resultado['total'] >= 1;
  }

  /**
   * Conta Pomodoros completos do usuário
   *
   * @param int $usuarioId ID do usuário
   * @return int Total de Pomodoros
   */
  private function contarPomodorosCompletos(int $usuarioId): int
  {
    $sql = "SELECT COUNT(*) as total FROM sessoes_pomodoro
            WHERE usuario_id = :usuario_id
            AND tipo = 'foco'
            AND finalizada = 1
            AND interrompida = 0";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->execute();

    $resultado = $stmt->fetch();
    return (int) $resultado['total'];
  }

  /**
   * Busca ranking de usuários
   *
   * @param int $limite Limite de usuários
   * @return array Ranking
   */
  public function buscarRanking(int $limite = 10): array
  {
    $sql = "SELECT g.*, u.nome, u.email
            FROM {$this->table} g
            INNER JOIN usuarios u ON u.id = g.usuario_id
            ORDER BY g.pontos_total DESC, g.nivel DESC
            LIMIT :limite";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
  }
}
