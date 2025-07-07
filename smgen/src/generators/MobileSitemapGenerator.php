<?php

namespace SitemapGenerator\Generators;

require_once 'AbstractSitemapGenerator.php';

/**
 * Mobile-Sitemap-Generator für mobilfreundliche Seiten
 */
class MobileSitemapGenerator extends AbstractSitemapGenerator
{
    /**
     * Generiert Mobile-Sitemap
     * 
     * @param array $urls URLs mit Metadaten
     * @return array Ergebnis der Generierung
     */
    public function generate(array $urls)
    {
        $validUrls = $this->validateUrls($urls);
        $filename = $this->config->get('mobile_sitemap_filename', 'sitemap-mobile.xml');
        $filePath = $this->generateFilePath($filename);
        
        $xml = $this->generateMobileXmlContent($validUrls);
        $this->writeFile($filePath, $xml);
        
        $result = [
            'file' => $filePath,
            'url' => $this->generateSitemapUrl($filename),
            'urls_count' => count($validUrls),
            'type' => 'mobile'
        ];
        
        // Komprimierung falls aktiviert
        if ($this->config->get('xml_compress', true)) {
            $compressedPath = $this->compressFile($filePath);
            $result['compressed_file'] = $compressedPath;
            $result['compressed_url'] = $this->generateSitemapUrl($filename . '.gz');
        }
        
        $this->log("Mobile-Sitemap generiert: {$filename} ({$result['urls_count']} URLs)");
        
        return $result;
    }
    
    /**
     * Generiert XML-Inhalt für Mobile-Sitemap
     * 
     * @param array $urls URLs
     * @return string XML-Inhalt
     */
    private function generateMobileXmlContent($urls)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . PHP_EOL;
        $xml .= '        xmlns:mobile="http://www.google.com/schemas/sitemap-mobile/1.0">' . PHP_EOL;
        
        foreach ($urls as $urlData) {
            $xml .= $this->generateMobileUrlXml($urlData);
        }
        
        $xml .= '</urlset>' . PHP_EOL;
        
        return $xml;
    }
    
    /**
     * Generiert XML für eine mobile URL
     * 
     * @param array $urlData URL-Daten
     * @return string XML-Fragment
     */
    private function generateMobileUrlXml($urlData)
    {
        $xml = '  <url>' . PHP_EOL;
        $xml .= '    <loc>' . $this->escapeXml($urlData['url']) . '</loc>' . PHP_EOL;
        
        // Mobile-Annotation (erforderlich für Mobile-Sitemaps)
        $xml .= '    <mobile:mobile/>' . PHP_EOL;
        
        // Letzte Änderung
        if (!empty($urlData['lastmod'])) {
            $xml .= '    <lastmod>' . $this->formatDate($urlData['lastmod']) . '</lastmod>' . PHP_EOL;
        }
        
        // Änderungsfrequenz
        if (!empty($urlData['changefreq'])) {
            $validFreqs = ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'];
            if (in_array($urlData['changefreq'], $validFreqs)) {
                $xml .= '    <changefreq>' . $urlData['changefreq'] . '</changefreq>' . PHP_EOL;
            }
        }
        
        // Priorität
        if (isset($urlData['priority']) && is_numeric($urlData['priority'])) {
            $priority = max(0.0, min(1.0, (float)$urlData['priority']));
            $xml .= '    <priority>' . number_format($priority, 1) . '</priority>' . PHP_EOL;
        }
        
        $xml .= '  </url>' . PHP_EOL;
        
        return $xml;
    }
    
    /**
     * Prüft ob eine URL mobilfreundlich ist
     * 
     * @param string $url URL
     * @return array Mobile-Informationen
     */
    private function analyzeMobileFriendliness($url)
    {
        $mobileInfo = [
            'is_mobile_friendly' => false,
            'viewport_meta' => false,
            'responsive_css' => false,
            'mobile_optimized' => false,
            'amp_version' => null,
            'mobile_user_agent_test' => false
        ];
        
        // HTTP-Request mit Mobile User-Agent
        $mobileUserAgent = $this->config->get('mobile_user_agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)');
        $response = $this->fetchUrlWithUserAgent($url, $mobileUserAgent);
        
        if (!$response || $response['status_code'] >= 400) {
            return $mobileInfo;
        }
        
        $content = $response['content'];
        
        // Viewport Meta-Tag prüfen
        if (preg_match('/<meta[^>]+name=["\']viewport["\'][^>]*>/i', $content)) {
            $mobileInfo['viewport_meta'] = true;
            $mobileInfo['is_mobile_friendly'] = true;
        }
        
        // Responsive CSS prüfen
        if (preg_match('/@media[^{]*\([^)]*max-width[^)]*\)/i', $content) ||
            preg_match('/@media[^{]*\([^)]*min-width[^)]*\)/i', $content)) {
            $mobileInfo['responsive_css'] = true;
            $mobileInfo['is_mobile_friendly'] = true;
        }
        
        // AMP-Version prüfen
        if (preg_match('/<link[^>]+rel=["\']amphtml["\'][^>]+href=["\']([^"\']+)["\'][^>]*>/i', $content, $matches)) {
            $mobileInfo['amp_version'] = $this->resolveUrl($matches[1], $url);
        }
        
        // Mobile-optimierte Elemente prüfen
        if (preg_match('/<meta[^>]+name=["\']mobile-web-app-capable["\'][^>]*>/i', $content) ||
            preg_match('/<meta[^>]+name=["\']apple-mobile-web-app-capable["\'][^>]*>/i', $content)) {
            $mobileInfo['mobile_optimized'] = true;
            $mobileInfo['is_mobile_friendly'] = true;
        }
        
        // Touch-Icons prüfen
        if (preg_match('/<link[^>]+rel=["\']apple-touch-icon["\'][^>]*>/i', $content)) {
            $mobileInfo['mobile_optimized'] = true;
            $mobileInfo['is_mobile_friendly'] = true;
        }
        
        // User-Agent Test erfolgreich
        if ($response['status_code'] === 200) {
            $mobileInfo['mobile_user_agent_test'] = true;
        }
        
        return $mobileInfo;
    }
    
    /**
     * Führt HTTP-Request mit spezifischem User-Agent aus
     * 
     * @param string $url URL
     * @param string $userAgent User-Agent
     * @return array|false Response-Daten
     */
    private function fetchUrlWithUserAgent($url, $userAgent)
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => $this->config->get('timeout', 30),
            CURLOPT_USERAGENT => $userAgent,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        
        curl_close($ch);
        
        if ($response === false) {
            return false;
        }
        
        $headers = substr($response, 0, $headerSize);
        $content = substr($response, $headerSize);
        
        return [
            'status_code' => $httpCode,
            'content_type' => $contentType,
            'content' => $content,
            'headers' => $headers
        ];
    }
    
    /**
     * Löst relative URLs zu absoluten URLs auf
     * 
     * @param string $url Relative oder absolute URL
     * @param string $base Basis-URL
     * @return string|null Absolute URL
     */
    private function resolveUrl($url, $base)
    {
        if (empty($url)) {
            return null;
        }
        
        // Bereits absolute URL
        if (parse_url($url, PHP_URL_SCHEME)) {
            return $url;
        }
        
        // Protocol-relative URL
        if (substr($url, 0, 2) === '//') {
            $scheme = parse_url($base, PHP_URL_SCHEME);
            return $scheme . ':' . $url;
        }
        
        // Absolute Pfad
        if (substr($url, 0, 1) === '/') {
            $scheme = parse_url($base, PHP_URL_SCHEME);
            $host = parse_url($base, PHP_URL_HOST);
            $port = parse_url($base, PHP_URL_PORT);
            
            $result = $scheme . '://' . $host;
            if ($port) {
                $result .= ':' . $port;
            }
            $result .= $url;
            
            return $result;
        }
        
        // Relativer Pfad
        $basePath = dirname(parse_url($base, PHP_URL_PATH));
        if ($basePath === '.') {
            $basePath = '';
        }
        
        $scheme = parse_url($base, PHP_URL_SCHEME);
        $host = parse_url($base, PHP_URL_HOST);
        $port = parse_url($base, PHP_URL_PORT);
        
        $result = $scheme . '://' . $host;
        if ($port) {
            $result .= ':' . $port;
        }
        $result .= $basePath . '/' . $url;
        
        return $result;
    }
    
    /**
     * Generiert erweiterte Mobile-Sitemap mit detaillierten Informationen
     * 
     * @param array $urls URLs
     * @return array Ergebnis mit zusätzlichen Mobile-Informationen
     */
    public function generateDetailed(array $urls)
    {
        $this->log("Starte detaillierte Mobile-Analyse...");
        
        $detailedUrls = [];
        foreach ($urls as $url) {
            if ($this->shouldIncludeUrl($url)) {
                $mobileInfo = $this->analyzeMobileFriendliness($url['url']);
                $url['mobile_details'] = $mobileInfo;
                $detailedUrls[] = $url;
                
                $this->log("Mobile-Analyse für {$url['url']}: " . 
                          ($mobileInfo['is_mobile_friendly'] ? 'mobilfreundlich' : 'nicht mobilfreundlich'));
            }
        }
        
        $result = $this->generate($detailedUrls);
        
        // Zusätzliche Statistik-Datei erstellen
        $statsFilename = str_replace('.xml', '-mobile-stats.txt', 
                                   $this->config->get('mobile_sitemap_filename', 'sitemap-mobile.xml'));
        $statsFilePath = $this->generateFilePath($statsFilename);
        
        $statsContent = $this->generateMobileStatistics($detailedUrls);
        $this->writeFile($statsFilePath, $statsContent);
        
        $result['stats_file'] = $statsFilePath;
        $result['stats_url'] = $this->generateSitemapUrl($statsFilename);
        
        return $result;
    }
    
    /**
     * Generiert Mobile-Statistiken
     * 
     * @param array $urls URLs mit Mobile-Details
     * @return string Statistik-Text
     */
    private function generateMobileStatistics($urls)
    {
        $stats = [
            'total_urls' => count($urls),
            'mobile_friendly' => 0,
            'viewport_meta' => 0,
            'responsive_css' => 0,
            'mobile_optimized' => 0,
            'amp_versions' => 0
        ];
        
        foreach ($urls as $url) {
            if (!empty($url['mobile_details'])) {
                $details = $url['mobile_details'];
                
                if ($details['is_mobile_friendly']) {
                    $stats['mobile_friendly']++;
                }
                if ($details['viewport_meta']) {
                    $stats['viewport_meta']++;
                }
                if ($details['responsive_css']) {
                    $stats['responsive_css']++;
                }
                if ($details['mobile_optimized']) {
                    $stats['mobile_optimized']++;
                }
                if (!empty($details['amp_version'])) {
                    $stats['amp_versions']++;
                }
            }
        }
        
        $content = str_repeat('=', 80) . PHP_EOL;
        $content .= 'MOBILE-SITEMAP STATISTIKEN' . PHP_EOL;
        $content .= str_repeat('=', 80) . PHP_EOL;
        $content .= PHP_EOL;
        $content .= sprintf("Gesamt URLs: %d", $stats['total_urls']) . PHP_EOL;
        $content .= sprintf("Mobilfreundliche URLs: %d (%.1f%%)", 
                           $stats['mobile_friendly'], 
                           ($stats['total_urls'] > 0 ? $stats['mobile_friendly'] / $stats['total_urls'] * 100 : 0)) . PHP_EOL;
        $content .= sprintf("URLs mit Viewport Meta-Tag: %d", $stats['viewport_meta']) . PHP_EOL;
        $content .= sprintf("URLs mit Responsive CSS: %d", $stats['responsive_css']) . PHP_EOL;
        $content .= sprintf("Mobile-optimierte URLs: %d", $stats['mobile_optimized']) . PHP_EOL;
        $content .= sprintf("URLs mit AMP-Version: %d", $stats['amp_versions']) . PHP_EOL;
        $content .= PHP_EOL;
        
        return $content;
    }
    
    /**
     * Prüft ob eine URL in die Mobile-Sitemap aufgenommen werden soll
     * 
     * @param array $url URL-Daten
     * @return bool
     */
    private function shouldIncludeUrl($url)
    {
        // Nur erfolgreiche URLs
        if (!in_array($url['status_code'], [200])) {
            return false;
        }
        
        // Nur HTML-Seiten
        if (strpos($url['content_type'], 'text/html') === false) {
            return false;
        }
        
        // Mobile-Flag prüfen falls vorhanden
        if (isset($url['mobile']) && !$url['mobile']) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Validiert URLs für Mobile-Sitemap
     * 
     * @param array $urls URLs
     * @return array Validierte URLs
     */
    public function validateUrls(array $urls)
    {
        $validUrls = [];
        
        foreach ($urls as $url) {
            if ($this->shouldIncludeUrl($url)) {
                $validUrls[] = $url;
            }
        }
        
        $this->log("Mobile-URLs validiert: " . count($validUrls) . " von " . count($urls) . " URLs sind mobilfreundlich");
        
        return $validUrls;
    }
    
    /**
     * Gibt MIME-Type zurück
     * 
     * @return string
     */
    public function getMimeType()
    {
        return 'application/xml';
    }
    
    /**
     * Gibt Dateiendung zurück
     * 
     * @return string
     */
    public function getFileExtension()
    {
        return 'xml';
    }
}

