<?php

namespace SitemapGenerator;

/**
 * Suchmaschinen-Ping-Klasse für automatische Benachrichtigungen
 */
class SearchEnginePing
{
    private $config;
    private $searchEngines;
    
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->initializeSearchEngines();
    }
    
    /**
     * Initialisiert die Suchmaschinen-Konfiguration
     */
    private function initializeSearchEngines()
    {
        $this->searchEngines = [
            'google' => [
                'name' => 'Google',
                'url' => 'https://www.google.com/ping?sitemap={SITEMAP_URL}',
                'enabled' => $this->config->get('ping_google', true),
                'method' => 'GET'
            ],
            'bing' => [
                'name' => 'Bing',
                'url' => 'https://www.bing.com/ping?sitemap={SITEMAP_URL}',
                'enabled' => $this->config->get('ping_bing', true),
                'method' => 'GET'
            ],
            'yandex' => [
                'name' => 'Yandex',
                'url' => 'https://webmaster.yandex.com/ping?sitemap={SITEMAP_URL}',
                'enabled' => $this->config->get('ping_yandex', false),
                'method' => 'GET'
            ],
            'baidu' => [
                'name' => 'Baidu',
                'url' => 'http://ping.baidu.com/ping/RPC2',
                'enabled' => $this->config->get('ping_baidu', false),
                'method' => 'POST',
                'data_template' => '<?xml version="1.0"?><methodCall><methodName>weblogUpdates.extendedPing</methodName><params><param><value><string>{SITE_NAME}</string></value></param><param><value><string>{SITE_URL}</string></value></param><param><value><string>{SITEMAP_URL}</string></value></param></params></methodCall>'
            ],
            'duckduckgo' => [
                'name' => 'DuckDuckGo',
                'url' => 'https://duckduckgo.com/ping?sitemap={SITEMAP_URL}',
                'enabled' => $this->config->get('ping_duckduckgo', false),
                'method' => 'GET'
            ]
        ];
    }
    
    /**
     * Pingt alle aktivierten Suchmaschinen
     * 
     * @param string $sitemapUrl URL der Sitemap
     * @param string $sitemapType Typ der Sitemap
     * @return array Ping-Ergebnisse
     */
    public function ping($sitemapUrl, $sitemapType = 'xml')
    {
        if (!$this->config->get('ping_search_engines', true)) {
            return [];
        }
        
        $results = [];
        
        foreach ($this->searchEngines as $engine => $config) {
            if ($config['enabled']) {
                $this->log("Pinge {$config['name']} für {$sitemapType}-Sitemap...");
                
                $result = $this->pingSearchEngine($engine, $sitemapUrl, $sitemapType);
                $results[$engine] = $result;
                
                $status = $result['success'] ? '✓' : '✗';
                $message = $result['success'] ? 'erfolgreich' : 'fehlgeschlagen';
                $this->log("{$status} {$config['name']}: {$message}");
                
                if (!$result['success'] && !empty($result['error'])) {
                    $this->log("   Fehler: {$result['error']}", 'warning');
                }
                
                // Verzögerung zwischen Pings
                sleep(1);
            }
        }
        
        return $results;
    }
    
    /**
     * Pingt eine spezifische Suchmaschine
     * 
     * @param string $engine Suchmaschinen-Schlüssel
     * @param string $sitemapUrl Sitemap-URL
     * @param string $sitemapType Sitemap-Typ
     * @return array Ping-Ergebnis
     */
    private function pingSearchEngine($engine, $sitemapUrl, $sitemapType)
    {
        $engineConfig = $this->searchEngines[$engine];
        
        try {
            if ($engineConfig['method'] === 'GET') {
                return $this->sendGetRequest($engineConfig, $sitemapUrl);
            } else {
                return $this->sendPostRequest($engineConfig, $sitemapUrl);
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'http_code' => null,
                'response' => null
            ];
        }
    }
    
    /**
     * Sendet GET-Request an Suchmaschine
     * 
     * @param array $engineConfig Suchmaschinen-Konfiguration
     * @param string $sitemapUrl Sitemap-URL
     * @return array Ergebnis
     */
    private function sendGetRequest($engineConfig, $sitemapUrl)
    {
        $url = str_replace('{SITEMAP_URL}', urlencode($sitemapUrl), $engineConfig['url']);
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_USERAGENT => $this->config->get('user_agent', 'Sitemap Generator Bot 1.0'),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER => false
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($response === false) {
            throw new \Exception("cURL-Fehler: {$error}");
        }
        
        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'response' => $response,
            'error' => $httpCode >= 400 ? "HTTP {$httpCode}" : null
        ];
    }
    
    /**
     * Sendet POST-Request an Suchmaschine
     * 
     * @param array $engineConfig Suchmaschinen-Konfiguration
     * @param string $sitemapUrl Sitemap-URL
     * @return array Ergebnis
     */
    private function sendPostRequest($engineConfig, $sitemapUrl)
    {
        $url = $engineConfig['url'];
        
        // POST-Daten vorbereiten
        $postData = $this->preparePostData($engineConfig, $sitemapUrl);
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_USERAGENT => $this->config->get('user_agent', 'Sitemap Generator Bot 1.0'),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => $this->getPostHeaders($engineConfig),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER => false
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($response === false) {
            throw new \Exception("cURL-Fehler: {$error}");
        }
        
        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'response' => $response,
            'error' => $httpCode >= 400 ? "HTTP {$httpCode}" : null
        ];
    }
    
    /**
     * Bereitet POST-Daten vor
     * 
     * @param array $engineConfig Suchmaschinen-Konfiguration
     * @param string $sitemapUrl Sitemap-URL
     * @return string POST-Daten
     */
    private function preparePostData($engineConfig, $sitemapUrl)
    {
        if (isset($engineConfig['data_template'])) {
            $baseUrl = $this->config->get('base_url');
            $siteName = parse_url($baseUrl, PHP_URL_HOST);
            
            return str_replace(
                ['{SITE_NAME}', '{SITE_URL}', '{SITEMAP_URL}'],
                [$siteName, $baseUrl, $sitemapUrl],
                $engineConfig['data_template']
            );
        }
        
        return '';
    }
    
    /**
     * Gibt HTTP-Headers für POST-Requests zurück
     * 
     * @param array $engineConfig Suchmaschinen-Konfiguration
     * @return array Headers
     */
    private function getPostHeaders($engineConfig)
    {
        $headers = [];
        
        if (isset($engineConfig['data_template'])) {
            if (strpos($engineConfig['data_template'], '<?xml') === 0) {
                $headers[] = 'Content-Type: text/xml';
            } else {
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            }
        }
        
        return $headers;
    }
    
    /**
     * Pingt mehrere Sitemaps gleichzeitig
     * 
     * @param array $sitemaps Array von Sitemap-URLs mit Typen
     * @return array Ping-Ergebnisse
     */
    public function pingMultiple($sitemaps)
    {
        $results = [];
        
        foreach ($sitemaps as $sitemap) {
            $url = $sitemap['url'];
            $type = $sitemap['type'] ?? 'xml';
            
            $this->log("Pinge Suchmaschinen für {$type}-Sitemap: {$url}");
            $results[$type] = $this->ping($url, $type);
        }
        
        return $results;
    }
    
    /**
     * Pingt nur Google (schneller für Tests)
     * 
     * @param string $sitemapUrl Sitemap-URL
     * @return array Ping-Ergebnis
     */
    public function pingGoogleOnly($sitemapUrl)
    {
        if (!$this->searchEngines['google']['enabled']) {
            return ['google' => ['success' => false, 'error' => 'Google-Ping ist deaktiviert']];
        }
        
        $result = $this->pingSearchEngine('google', $sitemapUrl, 'xml');
        return ['google' => $result];
    }
    
    /**
     * Testet die Ping-Funktionalität
     * 
     * @return array Test-Ergebnisse
     */
    public function testPing()
    {
        $testUrl = $this->config->get('base_url') . '/sitemap.xml';
        $results = [];
        
        $this->log("Teste Ping-Funktionalität mit Test-URL: {$testUrl}");
        
        foreach ($this->searchEngines as $engine => $config) {
            if ($config['enabled']) {
                $this->log("Teste {$config['name']}...");
                
                try {
                    $result = $this->pingSearchEngine($engine, $testUrl, 'test');
                    $results[$engine] = [
                        'name' => $config['name'],
                        'success' => $result['success'],
                        'http_code' => $result['http_code'],
                        'error' => $result['error']
                    ];
                } catch (Exception $e) {
                    $results[$engine] = [
                        'name' => $config['name'],
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
            }
        }
        
        return $results;
    }
    
    /**
     * Überprüft den Status einer Sitemap bei Google
     * 
     * @param string $sitemapUrl Sitemap-URL
     * @return array Status-Informationen
     */
    public function checkGoogleStatus($sitemapUrl)
    {
        // Google Search Console API würde hier verwendet werden
        // Für diese Implementation verwenden wir eine vereinfachte Prüfung
        
        $this->log("Prüfe Google-Status für Sitemap: {$sitemapUrl}");
        
        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $sitemapUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_NOBODY => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_USERAGENT => 'Googlebot/2.1 (+http://www.google.com/bot.html)',
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            return [
                'accessible' => $httpCode === 200,
                'http_code' => $httpCode,
                'last_checked' => date('c')
            ];
            
        } catch (Exception $e) {
            return [
                'accessible' => false,
                'error' => $e->getMessage(),
                'last_checked' => date('c')
            ];
        }
    }
    
    /**
     * Generiert Ping-Bericht
     * 
     * @param array $results Ping-Ergebnisse
     * @return string Bericht-Text
     */
    public function generatePingReport($results)
    {
        $report = str_repeat('=', 60) . PHP_EOL;
        $report .= 'SUCHMASCHINEN-PING BERICHT' . PHP_EOL;
        $report .= str_repeat('=', 60) . PHP_EOL;
        $report .= 'Zeitpunkt: ' . date('d.m.Y H:i:s') . PHP_EOL;
        $report .= PHP_EOL;
        
        $totalEngines = 0;
        $successfulPings = 0;
        
        foreach ($results as $sitemapType => $engineResults) {
            $report .= "Sitemap-Typ: " . strtoupper($sitemapType) . PHP_EOL;
            $report .= str_repeat('-', 30) . PHP_EOL;
            
            foreach ($engineResults as $engine => $result) {
                $totalEngines++;
                $status = $result['success'] ? '✓ ERFOLG' : '✗ FEHLER';
                $engineName = $this->searchEngines[$engine]['name'] ?? ucfirst($engine);
                
                $report .= sprintf("%-15s: %s", $engineName, $status);
                
                if (isset($result['http_code'])) {
                    $report .= " (HTTP {$result['http_code']})";
                }
                
                if (!$result['success'] && !empty($result['error'])) {
                    $report .= " - {$result['error']}";
                }
                
                $report .= PHP_EOL;
                
                if ($result['success']) {
                    $successfulPings++;
                }
            }
            
            $report .= PHP_EOL;
        }
        
        $report .= str_repeat('=', 60) . PHP_EOL;
        $report .= sprintf("ZUSAMMENFASSUNG: %d von %d Pings erfolgreich", $successfulPings, $totalEngines) . PHP_EOL;
        
        if ($totalEngines > 0) {
            $successRate = round($successfulPings / $totalEngines * 100, 1);
            $report .= sprintf("Erfolgsrate: %.1f%%", $successRate) . PHP_EOL;
        }
        
        $report .= str_repeat('=', 60) . PHP_EOL;
        
        return $report;
    }
    
    /**
     * Speichert Ping-Ergebnisse in Log-Datei
     * 
     * @param array $results Ping-Ergebnisse
     */
    public function logPingResults($results)
    {
        $report = $this->generatePingReport($results);
        
        $logFile = $this->config->get('ping_log_file', 'logs/ping-results.log');
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logFile, $report . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Gibt verfügbare Suchmaschinen zurück
     * 
     * @return array Suchmaschinen-Liste
     */
    public function getAvailableSearchEngines()
    {
        $engines = [];
        
        foreach ($this->searchEngines as $key => $config) {
            $engines[$key] = [
                'name' => $config['name'],
                'enabled' => $config['enabled'],
                'method' => $config['method']
            ];
        }
        
        return $engines;
    }
    
    /**
     * Aktiviert oder deaktiviert eine Suchmaschine
     * 
     * @param string $engine Suchmaschinen-Schlüssel
     * @param bool $enabled Aktiviert/Deaktiviert
     */
    public function setSearchEngineEnabled($engine, $enabled)
    {
        if (isset($this->searchEngines[$engine])) {
            $this->searchEngines[$engine]['enabled'] = $enabled;
        }
    }
    
    /**
     * Logging-Funktion
     * 
     * @param string $message Nachricht
     * @param string $level Log-Level
     */
    private function log($message, $level = 'info')
    {
        if ($this->config->get('enable_logging', true)) {
            $timestamp = date('Y-m-d H:i:s');
            $logMessage = "[{$timestamp}] [PING] [{$level}] {$message}" . PHP_EOL;
            
            // Konsolen-Ausgabe
            echo $logMessage;
            
            // Log-Datei
            $logFile = $this->config->get('log_file', 'logs/sitemap-generator.log');
            $logDir = dirname($logFile);
            
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            
            file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
        }
    }
}

