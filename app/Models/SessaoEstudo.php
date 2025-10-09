<?php

namespace App\Models;

use DateTime;

/**
 * Modelo para a tabela de sessoes_estudo.
 */
class SessaoEstudo extends BaseModel
{
  protected string $table = 'sessoes_estudo';

  /**
   * Salva ou atualiza uma sessão de estudo.
   */
  public function salvar(array $dados): bool
  {
    $duracao = $this->calcularDuracao($dados['data_inicio'], $dados['data_fim']);

    if (isset($dados['id']) && $dados['id']) {
      // Atualizar
      $stmt = $this->db->prepare(
        "UPDATE {$this->table} SET data_inicio = ?, data_fim = ?, duracao_minutos = ?, observacoes = ?, conteudo_id = ? WHERE id = ? AND usuario_id = ?"
      );
      return $stmt->execute([
        $dados['data_inicio'],
        $dados['data_fim'],
        $duracao,
        $dados['observacoes'],
        $dados['conteudo_id'],
        $dados['id'],
        $dados['usuario_id']
      ]);
    }

    // Inserir
    $stmt = $this->db->prepare(
      "INSERT INTO {$this->table} (data_inicio, data_fim, duracao_minutos, observacoes, conteudo_id, usuario_id) VALUES (?, ?, ?, ?, ?, ?)"
    );
    return $stmt->execute([
      $dados['data_inicio'],
      $dados['data_fim'],
      $duracao,
      $dados['observacoes'],
      $dados['conteudo_id'],
      $dados['usuario_id']
    ]);
  }

  /**
   * Calcula a duração da sessão em minutos.
   *
   * @param string $inicio
   * @param string $fim
   * @return int
   */
  public function calcularDuracao(string $inicio, string $fim): int
  {
    $dataInicio = new DateTime($inicio);
    $dataFim = new DateTime($fim);
    $diferenca = $dataFim->getTimestamp() - $dataInicio->getTimestamp();
    return round($diferenca / 60);
  }

  /**
   * Busca sessões por conteúdo.
   *
   * @param int $conteudoId
   * @return array
   */
  public function buscarPorConteudo(int $conteudoId): array
  {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE conteudo_id = ? ORDER BY data_inicio DESC");
    $stmt->execute([$conteudoId]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Busca sessões por período.
   *
   * @param int $usuarioId
   * @param string $dataInicio
   * @param string $dataFim
   * @return array
   */
  public function buscarPorPeriodo(int $usuarioId, string $dataInicio, string $dataFim): array
  {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE usuario_id = ? AND data_inicio BETWEEN ? AND ? ORDER BY data_inicio DESC");
    $stmt->execute([$usuarioId, $dataInicio, $dataFim]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }
}
