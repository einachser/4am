<?php

/**
 * PHP Sitemap Generator
 * 
 * Ein umfassender Sitemap-Generator für alle Arten von Sitemaps
 * gemäß Google Sitemap-Protokoll
 * 
 * @author Manus AI
 * @version 1.0.0
 */

namespace SitemapGenerator;

require_once 'Config.php';
require_once 'UrlCrawler.php';
require_once 'generators/XmlSitemapGenerator.php';
require_once 'generators/HtmlSitemapGenerator.php';
require_once 'generators/TextSitemapGenerator.php';
require_once 'generators/MobileSitemapGenerator.php';
require_once 'generators/ImageSitemapGenerator.php';
require_once 'generators/VideoSitemapGenerator.php';
require_once 'SearchEnginePing.php';

class SitemapGenerator
{
    private $config;
    private $urlCrawler;
    private $searchEnginePing;
    private $generators = [];
    
    public function __construct($configFile = null)
    {
        $this->config = new Config($configFile);
        $this->urlCrawler = new UrlCrawler($this->config);
        $this->searchEnginePing = new SearchEnginePing($this->config);
        
        $this->initializeGenerators();
    }
    
    /**
     * Initialisiert alle verfügbaren Sitemap-Generatoren
     */
    private function initializeGenerators()
    {
        $this->generators = [
            'xml' => new Generators\XmlSitemapGenerator($this->config),
            'html' => new Generators\HtmlSitemapGenerator($this->config),
            'text' => new Generators\TextSitemapGenerator($this->config),
            'mobile' => new Generators\MobileSitemapGenerator($this->config),
            'image' => new Generators\ImageSitemapGenerator($this->config),
            'video' => new Generators\VideoSitemapGenerator($this->config)
        ];
    }
    
    /**
     * Generiert alle konfigurierten Sitemap-Typen
     * 
     * @param array $types Spezifische Typen zu generieren (optional)
     * @return array Ergebnisse der Generierung
     */
    public function generateAll($types = null)
    {
        $results = [];
        $enabledTypes = $types ?? $this->config->getEnabledSitemapTypes();
        
        // URLs crawlen
        $this->log('Starte URL-Crawling...');
        $urls = $this->urlCrawler->crawl();
        $this->log(sprintf('Gefunden: %d URLs', count($urls)));
        
        // Sitemaps generieren
        foreach ($enabledTypes as $type) {
            if (isset($this->generators[$type])) {
                $this->log("Generiere {$type}-Sitemap...");
                
                try {
                    $result = $this->generators[$type]->generate($urls);
                    $results[$type] = $result;
                    $this->log("✓ {$type}-Sitemap erfolgreich generiert: " . $result['file']);
                } catch (Exception $e) {
                    $error = "✗ Fehler bei {$type}-Sitemap: " . $e->getMessage();
                    $this->log($error);
                    $results[$type] = ['error' => $error];
                }
            }
        }
        
        // Suchmaschinen benachrichtigen
        if ($this->config->get('ping_search_engines', true)) {
            $this->pingSearchEngines($results);
        }
        
        return $results;
    }
    
    /**
     * Generiert einen spezifischen Sitemap-Typ
     * 
     * @param string $type Sitemap-Typ
     * @return array Ergebnis der Generierung
     */
    public function generate($type)
    {
        return $this->generateAll([$type]);
    }
    
    /**
     * Benachrichtigt Suchmaschinen über neue Sitemaps
     * 
     * @param array $results Generierungsergebnisse
     */
    private function pingSearchEngines($results)
    {
        $this->log('Benachrichtige Suchmaschinen...');
        
        foreach ($results as $type => $result) {
            if (isset($result['url']) && !isset($result['error'])) {
                $pingResults = $this->searchEnginePing->ping($result['url'], $type);
                
                foreach ($pingResults as $engine => $success) {
                    $status = $success ? '✓' : '✗';
                    $this->log("{$status} {$engine}: {$type}-Sitemap");
                }
            }
        }
    }
    
    /**
     * Führt eine vollständige Sitemap-Generierung durch (für Cron-Jobs)
     * 
     * @param array $options Zusätzliche Optionen
     * @return array Vollständige Ergebnisse
     */
    public function runCronJob($options = [])
    {
        $this->log('=== Cron-Job gestartet ===');
        $startTime = microtime(true);
        
        try {
            $results = $this->generateAll();
            
            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);
            
            $this->log("=== Cron-Job abgeschlossen in {$duration}s ===");
            
            return [
                'success' => true,
                'duration' => $duration,
                'results' => $results,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
        } catch (Exception $e) {
            $this->log('✗ Cron-Job Fehler: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ];
        }
    }
    
    /**
     * Logging-Funktion
     * 
     * @param string $message Nachricht
     */
    private function log($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
        
        // Konsolen-Ausgabe
        echo $logMessage;
        
        // Log-Datei
        if ($this->config->get('enable_logging', true)) {
            $logFile = $this->config->get('log_file', 'logs/sitemap-generator.log');
            file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
        }
    }
    
    /**
     * Gibt die Konfiguration zurück
     * 
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * Gibt verfügbare Generatoren zurück
     * 
     * @return array
     */
    public function getAvailableGenerators()
    {
        return array_keys($this->generators);
    }
    
    /**
     * Validiert die Konfiguration
     * 
     * @return array Validierungsergebnisse
     */
    public function validateConfig()
    {
        $errors = [];
        
        // Basis-URL prüfen
        if (!$this->config->get('base_url')) {
            $errors[] = 'base_url ist erforderlich';
        }
        
        // Ausgabe-Verzeichnis prüfen
        $outputDir = $this->config->get('output_dir', 'output');
        if (!is_dir($outputDir) && !mkdir($outputDir, 0755, true)) {
            $errors[] = "Ausgabe-Verzeichnis kann nicht erstellt werden: {$outputDir}";
        }
        
        // Schreibrechte prüfen
        if (!is_writable($outputDir)) {
            $errors[] = "Ausgabe-Verzeichnis ist nicht beschreibbar: {$outputDir}";
        }
        
        return $errors;
    }
}

