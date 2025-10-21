<?php

namespace App\Patterns;

/**
 * Template Method Pattern - Define esqueleto de algoritmo
 *
 * Usado para exportação de relatórios em diferentes formatos
 */
abstract class RelatorioExporter
{
  protected array $dados;
  protected string $titulo;

  public function __construct(array $dados, string $titulo)
  {
    $this->dados = $dados;
    $this->titulo = $titulo;
  }

  /**
   * Template Method - Define passos da exportação
   */
  final public function export(): string
  {
    $this->prepararDados();
    $header = $this->gerarHeader();
    $body = $this->gerarBody();
    $footer = $this->gerarFooter();

    return $this->montar($header, $body, $footer);
  }

  /**
   * Hook method - pode ser sobrescrito
   */
  protected function prepararDados(): void
  {
    // Implementação padrão (nada)
  }

  /**
   * Métodos abstratos - devem ser implementados
   */
  abstract protected function gerarHeader(): string;
  abstract protected function gerarBody(): string;
  abstract protected function gerarFooter(): string;
  abstract protected function montar(string $header, string $body, string $footer): string;
  abstract public function getContentType(): string;
  abstract public function getFileExtension(): string;
}

/**
 * Exportador CSV
 */
class CsvRelatorioExporter extends RelatorioExporter
{
  protected function gerarHeader(): string
  {
    if (empty($this->dados)) {
      return '';
    }

    return implode(',', array_keys($this->dados[0])) . "\n";
  }

  protected function gerarBody(): string
  {
    $body = '';
    foreach ($this->dados as $row) {
      $body .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\n";
    }
    return $body;
  }

  protected function gerarFooter(): string
  {
    return "\n\"Total de registros: " . count($this->dados) . "\"";
  }

  protected function montar(string $header, string $body, string $footer): string
  {
    return chr(0xEF) . chr(0xBB) . chr(0xBF) . $header . $body . $footer;
  }

  public function getContentType(): string
  {
    return 'text/csv; charset=utf-8';
  }

  public function getFileExtension(): string
  {
    return 'csv';
  }
}

/**
 * Exportador JSON
 */
class JsonRelatorioExporter extends RelatorioExporter
{
  protected function gerarHeader(): string
  {
    return '{"titulo":"' . $this->titulo . '","data":"' . date('Y-m-d H:i:s') . '","registros":[';
  }

  protected function gerarBody(): string
  {
    $items = [];
    foreach ($this->dados as $row) {
      $items[] = json_encode($row, JSON_UNESCAPED_UNICODE);
    }
    return implode(',', $items);
  }

  protected function gerarFooter(): string
  {
    return '],"total":' . count($this->dados) . '}';
  }

  protected function montar(string $header, string $body, string $footer): string
  {
    return $header . $body . $footer;
  }

  public function getContentType(): string
  {
    return 'application/json; charset=utf-8';
  }

  public function getFileExtension(): string
  {
    return 'json';
  }
}

/**
 * Exportador HTML
 */
class HtmlRelatorioExporter extends RelatorioExporter
{
  protected function gerarHeader(): string
  {
    $html = '<!DOCTYPE html><html><head><meta charset="utf-8">';
    $html .= '<title>' . htmlspecialchars($this->titulo) . '</title>';
    $html .= '<style>table{border-collapse:collapse;width:100%;}th,td{border:1px solid #ddd;padding:8px;}th{background:#4CAF50;color:white;}</style>';
    $html .= '</head><body><h1>' . htmlspecialchars($this->titulo) . '</h1>';
    $html .= '<p>Data: ' . date('d/m/Y H:i:s') . '</p><table><thead><tr>';

    if (!empty($this->dados)) {
      foreach (array_keys($this->dados[0]) as $col) {
        $html .= '<th>' . htmlspecialchars($col) . '</th>';
      }
    }

    $html .= '</tr></thead><tbody>';
    return $html;
  }

  protected function gerarBody(): string
  {
    $html = '';
    foreach ($this->dados as $row) {
      $html .= '<tr>';
      foreach ($row as $cell) {
        $html .= '<td>' . htmlspecialchars($cell) . '</td>';
      }
      $html .= '</tr>';
    }
    return $html;
  }

  protected function gerarFooter(): string
  {
    return '</tbody></table><p><strong>Total: ' . count($this->dados) . ' registros</strong></p></body></html>';
  }

  protected function montar(string $header, string $body, string $footer): string
  {
    return $header . $body . $footer;
  }

  public function getContentType(): string
  {
    return 'text/html; charset=utf-8';
  }

  public function getFileExtension(): string
  {
    return 'html';
  }
}
