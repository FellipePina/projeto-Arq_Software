<?php

/**
 * Performance Helper
 * Funções auxiliares para otimização de performance
 */

class PerformanceHelper
{
  /**
   * Verifica se deve usar modo performance baseado em configurações
   */
  public static function shouldUsePerformanceMode(): bool
  {
    // Verifica preferência do usuário
    if (isset($_COOKIE['performanceMode']) && $_COOKIE['performanceMode'] === 'true') {
      return true;
    }

    // Verifica se está em mobile
    if (self::isMobile()) {
      return true;
    }

    return false;
  }

  /**
   * Detecta se é dispositivo móvel
   */
  public static function isMobile(): bool
  {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    return preg_match('/(android|iphone|ipad|mobile)/i', $userAgent);
  }

  /**
   * Retorna classe CSS baseada em modo performance
   */
  public static function getPerformanceClass(string $normalClass, string $performanceClass = ''): string
  {
    if (self::shouldUsePerformanceMode()) {
      return $performanceClass ?: str_replace('-', '-optimized-', $normalClass);
    }
    return $normalClass;
  }

  /**
   * Retorna atributos de lazy loading
   */
  public static function getLazyLoadAttrs(string $src, string $type = 'img'): string
  {
    if ($type === 'img') {
      return "data-lazy data-lazy-src=\"{$src}\" loading=\"lazy\"";
    } elseif ($type === 'bg') {
      return "data-lazy data-lazy-bg=\"{$src}\"";
    }
    return '';
  }

  /**
   * Minifica HTML inline
   */
  public static function minifyHTML(string $html): string
  {
    // Remove comentários HTML
    $html = preg_replace('/<!--(?!<!)[^\[>].*?-->/s', '', $html);

    // Remove espaços em branco extras
    $html = preg_replace('/\s+/', ' ', $html);

    // Remove espaços entre tags
    $html = preg_replace('/>\s+</', '><', $html);

    return trim($html);
  }

  /**
   * Gera hash de cache para assets
   */
  public static function assetVersion(string $file): string
  {
    $path = $_SERVER['DOCUMENT_ROOT'] . $file;
    if (file_exists($path)) {
      return $file . '?v=' . filemtime($path);
    }
    return $file;
  }

  /**
   * Carrega CSS de forma otimizada
   */
  public static function loadCSS(string $href, bool $critical = false): string
  {
    $version = self::assetVersion($href);

    if ($critical) {
      return "<link rel=\"stylesheet\" href=\"{$version}\">";
    }

    // Carrega de forma assíncrona
    return "<link rel=\"preload\" href=\"{$version}\" as=\"style\" onload=\"this.onload=null;this.rel='stylesheet'\">
                <noscript><link rel=\"stylesheet\" href=\"{$version}\"></noscript>";
  }

  /**
   * Carrega JS de forma otimizada
   */
  public static function loadJS(string $src, bool $critical = false, bool $module = false): string
  {
    $version = self::assetVersion($src);
    $moduleAttr = $module ? ' type="module"' : '';

    if ($critical) {
      return "<script src=\"{$version}\"{$moduleAttr}></script>";
    }

    // Carrega de forma assíncrona
    return "<script src=\"{$version}\" defer{$moduleAttr}></script>";
  }

  /**
   * Inline critical CSS
   */
  public static function inlineCriticalCSS(string $css): string
  {
    $minified = self::minifyCSS($css);
    return "<style>{$minified}</style>";
  }

  /**
   * Minifica CSS
   */
  public static function minifyCSS(string $css): string
  {
    // Remove comentários
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);

    // Remove espaços em branco
    $css = str_replace(["\r\n", "\r", "\n", "\t"], '', $css);
    $css = preg_replace('/\s+/', ' ', $css);
    $css = preg_replace('/\s*([:;{}])\s*/', '$1', $css);

    return trim($css);
  }

  /**
   * Gera preload links para recursos importantes
   */
  public static function generatePreloadLinks(array $resources): string
  {
    $links = [];

    foreach ($resources as $resource => $type) {
      $version = self::assetVersion($resource);
      $links[] = "<link rel=\"preload\" href=\"{$version}\" as=\"{$type}\">";
    }

    return implode("\n", $links);
  }

  /**
   * Gera DNS prefetch para domínios externos
   */
  public static function generateDNSPrefetch(array $domains): string
  {
    $links = [];

    foreach ($domains as $domain) {
      $links[] = "<link rel=\"dns-prefetch\" href=\"{$domain}\">";
      $links[] = "<link rel=\"preconnect\" href=\"{$domain}\" crossorigin>";
    }

    return implode("\n", $links);
  }

  /**
   * Retorna configurações de performance em JSON
   */
  public static function getPerformanceConfig(): string
  {
    $config = [
      'performanceMode' => self::shouldUsePerformanceMode(),
      'isMobile' => self::isMobile(),
      'lazyLoad' => true,
      'animationSpeed' => self::shouldUsePerformanceMode() ? 'fast' : 'normal'
    ];

    return json_encode($config);
  }

  /**
   * Comprime resposta com GZIP
   */
  public static function enableCompression(): void
  {
    if (!headers_sent() && extension_loaded('zlib')) {
      ini_set('zlib.output_compression', 'On');
      ini_set('zlib.output_compression_level', '6');
    }
  }

  /**
   * Define headers de cache
   */
  public static function setCacheHeaders(int $maxAge = 86400): void
  {
    if (!headers_sent()) {
      header('Cache-Control: public, max-age=' . $maxAge);
      header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $maxAge) . ' GMT');
    }
  }

  /**
   * Define headers de segurança
   */
  public static function setSecurityHeaders(): void
  {
    if (!headers_sent()) {
      header('X-Content-Type-Options: nosniff');
      header('X-Frame-Options: SAMEORIGIN');
      header('X-XSS-Protection: 1; mode=block');
      header('Referrer-Policy: strict-origin-when-cross-origin');
    }
  }

  /**
   * Otimiza imagem (redimensiona se muito grande)
   */
  public static function optimizeImage(string $imagePath, int $maxWidth = 1920): array
  {
    if (!file_exists($imagePath)) {
      return ['error' => 'File not found'];
    }

    $imageInfo = getimagesize($imagePath);
    if (!$imageInfo) {
      return ['error' => 'Invalid image'];
    }

    [$width, $height] = $imageInfo;

    if ($width <= $maxWidth) {
      return ['optimized' => false, 'path' => $imagePath];
    }

    // Calcula novas dimensões
    $newWidth = $maxWidth;
    $newHeight = intval($height * ($maxWidth / $width));

    return [
      'optimized' => true,
      'originalWidth' => $width,
      'originalHeight' => $height,
      'newWidth' => $newWidth,
      'newHeight' => $newHeight,
      'path' => $imagePath
    ];
  }

  /**
   * Gera srcset responsivo para imagens
   */
  public static function generateSrcSet(string $imagePath, array $sizes = [320, 640, 960, 1280, 1920]): string
  {
    $srcset = [];
    $pathInfo = pathinfo($imagePath);

    foreach ($sizes as $size) {
      $filename = $pathInfo['filename'] . '-' . $size . 'w.' . $pathInfo['extension'];
      $resizedPath = $pathInfo['dirname'] . '/' . $filename;
      $srcset[] = "{$resizedPath} {$size}w";
    }

    return implode(', ', $srcset);
  }

  /**
   * Calcula tempo de carregamento estimado
   */
  public static function estimateLoadTime(int $fileSize, string $connectionType = '4g'): float
  {
    // Velocidades médias em Mbps
    $speeds = [
      'slow-2g' => 0.05,
      '2g' => 0.25,
      '3g' => 0.7,
      '4g' => 10,
      '5g' => 100
    ];

    $speedMbps = $speeds[$connectionType] ?? 10;
    $speedBps = $speedMbps * 1024 * 1024 / 8; // Converte para bytes por segundo

    return round($fileSize / $speedBps, 2);
  }

  /**
   * Log de performance
   */
  public static function logPerformance(string $action, float $duration): void
  {
    // Log apenas em ambiente localhost
    if (
      isset($_SERVER['SERVER_NAME']) &&
      (strpos($_SERVER['SERVER_NAME'], 'localhost') !== false ||
        strpos($_SERVER['SERVER_NAME'], '127.0.0.1') !== false)
    ) {
      error_log(sprintf('[PERFORMANCE] %s took %.4f seconds', $action, $duration));
    }
  }

  /**
   * Inicia medição de performance
   */
  public static function startMeasure(): float
  {
    return microtime(true);
  }

  /**
   * Finaliza medição de performance
   */
  public static function endMeasure(float $start, string $label = ''): float
  {
    $duration = microtime(true) - $start;
    if ($label) {
      self::logPerformance($label, $duration);
    }
    return $duration;
  }
}
