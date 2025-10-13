<?php

namespace App\Models;

/**
 * Classe SessaoEstudo - Modelo para gerenciar sessões de estudo
 *
 * Princípios aplicados:
 * - Single Responsibility: apenas operações de sessões de estudo
 * - Clean Code: métodos bem nomeados e com propósito específico
 */
class SessaoEstudo extends BaseModel
{
  protected string $table = 'sessoes_estudo';

  /**
   * Cria uma nova sessão de estudo
   *
   * @param array $dados Dados da sessão
   * @return int|false ID da sessão criada ou false se erro
   */
  public function criar(array $dados)
  {
    // Validações básicas
    if (empty($dados['conteudo_id']) || empty($dados['usuario_id'])) {
      return false;
    }

    // Prepara dados para inserção
    $dadosSessao = [
      'conteudo_id' => (int) $dados['conteudo_id'],
      'usuario_id' => (int) $dados['usuario_id'],
      'data_inicio' => $dados['data_inicio'] ?? date('Y-m-d H:i:s'),
      'data_fim' => $dados['data_fim'] ?? null,
      'duracao_minutos' => $dados['duracao_minutos'] ?? null,
      'observacoes' => trim($dados['observacoes'] ?? '')
    ];

    return $this->save($dadosSessao);
  }

  /**
   * Finaliza uma sessão de estudo
   *
   * @param int $id ID da sessão
   * @param string|null $dataFim Data de fim (opcional, usa atual se não informado)
   * @param string $observacoes Observações da sessão
   * @return bool True se finalizada com sucesso
   */
  public function finalizar(int $id, ?string $dataFim = null, string $observacoes = ''): bool
  {
    $dataFim = $dataFim ?? date('Y-m-d H:i:s');

    // Busca a sessão para calcular duração
    $sessao = $this->findById($id);
    if (!$sessao) {
      return false;
    }

    // Calcula duração em minutos
    $duracaoMinutos = $this->calcularDuracao($sessao['data_inicio'], $dataFim);

    $sql = "UPDATE {$this->table}
                SET data_fim = :data_fim,
                    duracao_minutos = :duracao_minutos,
                    observacoes = :observacoes
                WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':data_fim', $dataFim);
    $stmt->bindValue(':duracao_minutos', $duracaoMinutos, \PDO::PARAM_INT);
    $stmt->bindValue(':observacoes', $observacoes);
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Calcula duração entre duas datas em minutos
   *
   * @param string $dataInicio Data de início
   * @param string $dataFim Data de fim
   * @return int Duração em minutos
   */
  public function calcularDuracao(string $dataInicio, string $dataFim): int
  {
    $inicio = new \DateTime($dataInicio);
    $fim = new \DateTime($dataFim);

    $diferenca = $fim->diff($inicio);

    // Converte para minutos totais
    return ($diferenca->days * 24 * 60) +
      ($diferenca->h * 60) +
      $diferenca->i;
  }

  /**
   * Busca sessões de um conteúdo específico
   *
   * @param int $conteudoId ID do conteúdo
   * @return array Lista de sessões do conteúdo
   */
  public function buscarPorConteudo(int $conteudoId): array
  {
    $sql = "SELECT se.*, ce.titulo as conteudo_titulo
                FROM {$this->table} se
                INNER JOIN conteudos_estudo ce ON se.conteudo_id = ce.id
                WHERE se.conteudo_id = :conteudo_id
                ORDER BY se.data_inicio DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':conteudo_id', $conteudoId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Busca sessões de um usuário em um período
   *
   * @param int $usuarioId ID do usuário
   * @param string $dataInicio Data de início do período
   * @param string $dataFim Data de fim do período
   * @return array Lista de sessões no período
   */
  public function buscarPorPeriodo(int $usuarioId, string $dataInicio, string $dataFim): array
  {
    $sql = "SELECT se.*, ce.titulo as conteudo_titulo, c.nome as categoria_nome
                FROM {$this->table} se
                INNER JOIN conteudos_estudo ce ON se.conteudo_id = ce.id
                LEFT JOIN categorias c ON ce.categoria_id = c.id
                WHERE se.usuario_id = :usuario_id
                    AND DATE(se.data_inicio) >= :data_inicio
                    AND DATE(se.data_inicio) <= :data_fim
                ORDER BY se.data_inicio DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->bindValue(':data_inicio', $dataInicio);
    $stmt->bindValue(':data_fim', $dataFim);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Busca sessões de um usuário
   *
   * @param int $usuarioId ID do usuário
   * @param int $limite Limite de registros (opcional)
   * @return array Lista de sessões do usuário
   */
  public function buscarPorUsuario(int $usuarioId, int $limite = 50): array
  {
    $sql = "SELECT se.*, ce.titulo as conteudo_titulo, c.nome as categoria_nome
                FROM {$this->table} se
                INNER JOIN conteudos_estudo ce ON se.conteudo_id = ce.id
                LEFT JOIN categorias c ON ce.categoria_id = c.id
                WHERE se.usuario_id = :usuario_id
                ORDER BY se.data_inicio DESC
                LIMIT :limite";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
    $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Calcula total de horas estudadas por usuário
   *
   * @param int $usuarioId ID do usuário
   * @param string|null $dataInicio Data de início (opcional)
   * @param string|null $dataFim Data de fim (opcional)
   * @return float Total de horas
   */
  public function calcularTotalHoras(int $usuarioId, ?string $dataInicio = null, ?string $dataFim = null): float
  {
    $sql = "SELECT COALESCE(SUM(duracao_minutos), 0) as total_minutos
                FROM {$this->table}
                WHERE usuario_id = :usuario_id";

    $params = [':usuario_id' => $usuarioId];

    if ($dataInicio) {
      $sql .= " AND DATE(data_inicio) >= :data_inicio";
      $params[':data_inicio'] = $dataInicio;
    }

    if ($dataFim) {
      $sql .= " AND DATE(data_inicio) <= :data_fim";
      $params[':data_fim'] = $dataFim;
    }

    $stmt = $this->db->prepare($sql);

    foreach ($params as $param => $value) {
      $stmt->bindValue($param, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
    }

    $stmt->execute();
    $result = $stmt->fetch();

    // Converte minutos para horas
    return round((int) $result['total_minutos'] / 60, 2);
  }
}
