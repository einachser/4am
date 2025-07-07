<?php

namespace SitemapGenerator;

/**
 * URL-Crawler für das Sammeln aller URLs einer Website
 */
class UrlCrawler
{
    private $config;
    private $visitedUrls = [];
    private $foundUrls = [];
    private $robotsRules = [];
    private $baseUrl;
    private $baseDomain;
    
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->baseUrl = rtrim($config->get('base_url'), '/');
        $this->baseDomain = parse_url($this->baseUrl, PHP_URL_HOST);
        
        if ($config->get('follow_robots_txt', true)) {
            $this->loadRobotsTxt();
        }
    }
    
    /**
     * Startet das Crawling der Website
     * 
     * @return array Gefundene URLs mit Metadaten
     */
    public function crawl()
    {
        $this->foundUrls = [];
        $this->visitedUrls = [];
        
        // Cache prüfen
        if ($this->config->get('enable_caching', true)) {
            $cachedUrls = $this->loadFromCache();
            if ($cachedUrls !== false) {
                return $cachedUrls;
            }
        }
        
        // Crawling starten
        $this->crawlUrl($this->baseUrl, 0);
        
        // URLs verarbeiten und anreichern
        $processedUrls = $this->processUrls();
        
        // Cache speichern
        if ($this->config->get('enable_caching', true)) {
            $this->saveToCache($processedUrls);
        }
        
        return $processedUrls;
    }
    
    /**
     * Crawlt eine einzelne URL
     * 
     * @param string $url URL zum Crawlen
     * @param int $depth Aktuelle Tiefe
     */
    private function crawlUrl($url, $depth)
    {
        // Maximale Tiefe prüfen
        if ($depth > $this->config->get('max_depth', 10)) {
            return;
        }
        
        // Maximale URL-Anzahl prüfen
        if (count($this->foundUrls) >= $this->config->get('max_urls', 50000)) {
            return;
        }
        
        // URL normalisieren
        $url = $this->normalizeUrl($url);
        
        // Bereits besucht?
        if (in_array($url, $this->visitedUrls)) {
            return;
        }
        
        // URL-Filter anwenden
        if (!$this->shouldCrawlUrl($url)) {
            return;
        }
        
        $this->visitedUrls[] = $url;
        
        // HTTP-Request ausführen
        $response = $this->fetchUrl($url);
        
        if (!$response || $response['status_code'] >= 400) {
            return;
        }
        
        // URL zu gefundenen URLs hinzufügen
        $this->foundUrls[$url] = [
            'url' => $url,
            'status_code' => $response['status_code'],
            'content_type' => $response['content_type'],
            'last_modified' => $response['last_modified'],
            'content_length' => $response['content_length'],
            'depth' => $depth,
            'images' => [],
            'videos' => [],
            'links' => []
        ];
        
        // Nur HTML-Seiten weiter analysieren
        if (strpos($response['content_type'], 'text/html') !== false) {
            $this->analyzeHtmlContent($url, $response['content'], $depth);
        }
        
        // Verzögerung zwischen Requests
        $delay = $this->config->get('delay_between_requests', 1);
        if ($delay > 0) {
            sleep($delay);
        }
    }
    
    /**
     * Analysiert HTML-Inhalt und extrahiert Links, Bilder und Videos
     * 
     * @param string $url Basis-URL
     * @param string $content HTML-Inhalt
     * @param int $depth Aktuelle Tiefe
     */
    private function analyzeHtmlContent($url, $content, $depth)
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($content);
        $xpath = new \DOMXPath($dom);
        
        // Links extrahieren
        $links = $xpath->query('//a[@href]');
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            $absoluteUrl = $this->resolveUrl($href, $url);
            
            if ($absoluteUrl && $this->shouldCrawlUrl($absoluteUrl)) {
                $this->foundUrls[$url]['links'][] = $absoluteUrl;
                $this->crawlUrl($absoluteUrl, $depth + 1);
            }
        }
        
        // Bilder extrahieren
        $images = $xpath->query('//img[@src]');
        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            $absoluteUrl = $this->resolveUrl($src, $url);
            
            if ($absoluteUrl) {
                $imageInfo = [
                    'url' => $absoluteUrl,
                    'alt' => $img->getAttribute('alt'),
                    'title' => $img->getAttribute('title'),
                    'caption' => $this->extractImageCaption($img)
                ];
                
                $this->foundUrls[$url]['images'][] = $imageInfo;
            }
        }
        
        // Videos extrahieren
        $videos = $xpath->query('//video[@src] | //video/source[@src]');
        foreach ($videos as $video) {
            $src = $video->getAttribute('src');
            $absoluteUrl = $this->resolveUrl($src, $url);
            
            if ($absoluteUrl) {
                $videoInfo = [
                    'url' => $absoluteUrl,
                    'title' => $video->getAttribute('title'),
                    'description' => $this->extractVideoDescription($video)
                ];
                
                $this->foundUrls[$url]['videos'][] = $videoInfo;
            }
        }
    }
    
    /**
     * Führt HTTP-Request aus
     * 
     * @param string $url URL
     * @return array|false Response-Daten
     */
    private function fetchUrl($url)
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => $this->config->get('follow_redirects', true),
            CURLOPT_TIMEOUT => $this->config->get('timeout', 30),
            CURLOPT_USERAGENT => $this->config->get('user_agent', 'Sitemap Generator Bot 1.0'),
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
            'headers' => $headers,
            'last_modified' => $this->extractLastModified($headers),
            'content_length' => strlen($content)
        ];
    }
    
    /**
     * Normalisiert eine URL
     * 
     * @param string $url URL
     * @return string Normalisierte URL
     */
    private function normalizeUrl($url)
    {
        // Query-Parameter entfernen falls konfiguriert
        if ($this->config->get('ignore_query_parameters', false)) {
            $url = strtok($url, '?');
        }
        
        // Fragment entfernen
        $url = strtok($url, '#');
        
        // Trailing Slash normalisieren
        if (substr($url, -1) === '/' && $url !== $this->baseUrl . '/') {
            $url = rtrim($url, '/');
        }
        
        return $url;
    }
    
    /**
     * Prüft ob eine URL gecrawlt werden soll
     * 
     * @param string $url URL
     * @return bool
     */
    private function shouldCrawlUrl($url)
    {
        // Externe Links prüfen
        if (!$this->config->get('crawl_external_links', false)) {
            $domain = parse_url($url, PHP_URL_HOST);
            if ($domain !== $this->baseDomain) {
                return false;
            }
        }
        
        // Robots.txt prüfen
        if (!$this->isAllowedByRobots($url)) {
            return false;
        }
        
        // Exclude-Pattern prüfen
        $excludePatterns = $this->config->get('exclude_patterns', []);
        foreach ($excludePatterns as $pattern) {
            if (fnmatch($pattern, $url)) {
                return false;
            }
        }
        
        // Include-Pattern prüfen
        $includePatterns = $this->config->get('include_patterns', []);
        if (!empty($includePatterns)) {
            $included = false;
            foreach ($includePatterns as $pattern) {
                if (fnmatch($pattern, $url)) {
                    $included = true;
                    break;
                }
            }
            if (!$included) {
                return false;
            }
        }
        
        return true;
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
     * Lädt robots.txt und parst die Regeln
     */
    private function loadRobotsTxt()
    {
        $robotsUrl = $this->baseUrl . '/robots.txt';
        $response = $this->fetchUrl($robotsUrl);
        
        if ($response && $response['status_code'] === 200) {
            $this->parseRobotsTxt($response['content']);
        }
    }
    
    /**
     * Parst robots.txt Inhalt
     * 
     * @param string $content robots.txt Inhalt
     */
    private function parseRobotsTxt($content)
    {
        $lines = explode("\n", $content);
        $currentUserAgent = null;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (empty($line) || substr($line, 0, 1) === '#') {
                continue;
            }
            
            if (preg_match('/^User-agent:\s*(.+)$/i', $line, $matches)) {
                $currentUserAgent = strtolower(trim($matches[1]));
            } elseif (preg_match('/^Disallow:\s*(.+)$/i', $line, $matches) && $currentUserAgent) {
                $path = trim($matches[1]);
                if ($currentUserAgent === '*' || strpos($this->config->get('user_agent'), $currentUserAgent) !== false) {
                    $this->robotsRules[] = $path;
                }
            }
        }
    }
    
    /**
     * Prüft ob URL durch robots.txt erlaubt ist
     * 
     * @param string $url URL
     * @return bool
     */
    private function isAllowedByRobots($url)
    {
        if (empty($this->robotsRules)) {
            return true;
        }
        
        $path = parse_url($url, PHP_URL_PATH);
        
        foreach ($this->robotsRules as $rule) {
            if (fnmatch($rule, $path)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Verarbeitet gefundene URLs und reichert sie mit Metadaten an
     * 
     * @return array Verarbeitete URLs
     */
    private function processUrls()
    {
        $processed = [];
        
        foreach ($this->foundUrls as $url => $data) {
            $processed[] = [
                'url' => $url,
                'lastmod' => $data['last_modified'] ?: date('c'),
                'changefreq' => $this->config->getChangefreqForUrl($url),
                'priority' => $this->config->getPriorityForUrl($url),
                'images' => $data['images'],
                'videos' => $data['videos'],
                'mobile' => $this->isMobileFriendly($url),
                'status_code' => $data['status_code'],
                'content_type' => $data['content_type']
            ];
        }
        
        // Nach Priorität sortieren
        usort($processed, function($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });
        
        return $processed;
    }
    
    /**
     * Prüft ob eine Seite mobilfreundlich ist
     * 
     * @param string $url URL
     * @return bool
     */
    private function isMobileFriendly($url)
    {
        // Einfache Heuristik - könnte erweitert werden
        $response = $this->fetchUrl($url);
        
        if (!$response || strpos($response['content_type'], 'text/html') === false) {
            return false;
        }
        
        $content = $response['content'];
        
        // Viewport Meta-Tag prüfen
        if (preg_match('/<meta[^>]+name=["\']viewport["\'][^>]*>/i', $content)) {
            return true;
        }
        
        // Responsive CSS prüfen
        if (preg_match('/@media[^{]*\([^)]*max-width[^)]*\)/i', $content)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Extrahiert Last-Modified Header
     * 
     * @param string $headers HTTP Headers
     * @return string|null
     */
    private function extractLastModified($headers)
    {
        if (preg_match('/Last-Modified:\s*(.+)/i', $headers, $matches)) {
            return date('c', strtotime(trim($matches[1])));
        }
        
        return null;
    }
    
    /**
     * Extrahiert Bildunterschrift
     * 
     * @param \DOMElement $img Bild-Element
     * @return string|null
     */
    private function extractImageCaption($img)
    {
        // Suche nach figcaption
        $parent = $img->parentNode;
        if ($parent && $parent->nodeName === 'figure') {
            $figcaptions = $parent->getElementsByTagName('figcaption');
            if ($figcaptions->length > 0) {
                return trim($figcaptions->item(0)->textContent);
            }
        }
        
        return null;
    }
    
    /**
     * Extrahiert Video-Beschreibung
     * 
     * @param \DOMElement $video Video-Element
     * @return string|null
     */
    private function extractVideoDescription($video)
    {
        // Suche nach description oder data-description Attribut
        $description = $video->getAttribute('data-description');
        if ($description) {
            return $description;
        }
        
        // Suche nach nachfolgendem Text
        $nextSibling = $video->nextSibling;
        if ($nextSibling && $nextSibling->nodeType === XML_TEXT_NODE) {
            return trim($nextSibling->textContent);
        }
        
        return null;
    }
    
    /**
     * Lädt URLs aus Cache
     * 
     * @return array|false
     */
    private function loadFromCache()
    {
        $cacheFile = $this->config->get('cache_file', 'cache/urls.cache');
        
        if (!file_exists($cacheFile)) {
            return false;
        }
        
        $cacheAge = time() - filemtime($cacheFile);
        $cacheDuration = $this->config->get('cache_duration', 3600);
        
        if ($cacheAge > $cacheDuration) {
            return false;
        }
        
        $data = file_get_contents($cacheFile);
        return unserialize($data);
    }
    
    /**
     * Speichert URLs in Cache
     * 
     * @param array $urls URLs
     */
    private function saveToCache($urls)
    {
        $cacheFile = $this->config->get('cache_file', 'cache/urls.cache');
        $cacheDir = dirname($cacheFile);
        
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        file_put_contents($cacheFile, serialize($urls));
    }
}

