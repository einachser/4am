#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Kommandozeilen-Interface für den PHP Sitemap Generator
 * 
 * Verwendung:
 * php sitemap-cli.php [Optionen]
 * 
 * Beispiele:
 * php sitemap-cli.php --config=config.json --generate=all
 * php sitemap-cli.php --config=config.json --generate=xml,html
 * php sitemap-cli.php --test-ping
 * php sitemap-cli.php --validate-config
 */

// Autoloader


use SitemapGenerator\SitemapGenerator;
use SitemapGenerator\Config;

class SitemapCLI
{
    private $options = [];
    private $generator;
    
    public function __construct($argv)
    {
        $this->parseArguments($argv);
        $this->initializeGenerator();
    }
    
    /**
     * Parst Kommandozeilen-Argumente
     */
    private function parseArguments($argv)
    {
        $this->options = [
            'config' => null,
            'generate' => null,
            'output' => null,
            'base-url' => null,
            'help' => false,
            'version' => false,
            'test-ping' => false,
            'validate-config' => false,
            'verbose' => false,
            'quiet' => false,
            'cron' => false,
            'detailed' => false
        ];
        
        for ($i = 1; $i < count($argv); $i++) {
            $arg = $argv[$i];
            
            if (strpos($arg, '--') === 0) {
                $parts = explode('=', substr($arg, 2), 2);
                $key = $parts[0];
                $value = isset($parts[1]) ? $parts[1] : true;
                
                if (array_key_exists($key, $this->options)) {
                    $this->options[$key] = $value;
                }
            } elseif (strpos($arg, '-') === 0) {
                $flags = substr($arg, 1);
                for ($j = 0; $j < strlen($flags); $j++) {
                    switch ($flags[$j]) {
                        case 'h':
                            $this->options['help'] = true;
                            break;
                        case 'v':
                            $this->options['verbose'] = true;
                            break;
                        case 'q':
                            $this->options['quiet'] = true;
                            break;
                        case 'c':
                            $this->options['cron'] = true;
                            break;
                    }
                }
            }
        }
    }
    
    /**
     * Initialisiert den Sitemap-Generator
     */
    private function initializeGenerator()
    {
        try {
            $configFile = $this->options['config'];
            $this->generator = new SitemapGenerator($configFile);
            
            // CLI-spezifische Konfiguration überschreiben
            if ($this->options['base-url']) {
                $this->generator->getConfig()->set('base_url', $this->options['base-url']);
            }
            
            if ($this->options['output']) {
                $this->generator->getConfig()->set('output_dir', $this->options['output']);
            }
            
            if ($this->options['quiet']) {
                $this->generator->getConfig()->set('enable_logging', false);
            }
            
        } catch (Exception $e) {
            $this->error("Fehler beim Initialisieren: " . $e->getMessage());
            exit(1);
        }
    }
    
    /**
     * Führt die CLI-Anwendung aus
     */
    public function run()
    {
        if ($this->options['help']) {
            $this->showHelp();
            return;
        }
        
        if ($this->options['version']) {
            $this->showVersion();
            return;
        }
        
        if ($this->options['validate-config']) {
            $this->validateConfig();
            return;
        }
        
        if ($this->options['test-ping']) {
            $this->testPing();
            return;
        }
        
        if ($this->options['generate']) {
            $this->generateSitemaps();
            return;
        }
        
        if ($this->options['cron']) {
            $this->runCronJob();
            return;
        }
        
        // Standard: Alle Sitemaps generieren
        $this->generateSitemaps();
    }
    
    /**
     * Generiert Sitemaps
     */
    private function generateSitemaps()
    {
        try {
            $this->info("Starte Sitemap-Generierung...");
            
            $types = null;
            if ($this->options['generate'] && $this->options['generate'] !== 'all') {
                $types = explode(',', $this->options['generate']);
                $types = array_map('trim', $types);
            }
            
            $startTime = microtime(true);
            
            if ($this->options['detailed']) {
                $results = $this->generateDetailedSitemaps($types);
            } else {
                $results = $this->generator->generateAll($types);
            }
            
            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);
            
            $this->success("Sitemap-Generierung abgeschlossen in {$duration}s");
            $this->showResults($results);
            
        } catch (Exception $e) {
            $this->error("Fehler bei der Generierung: " . $e->getMessage());
            exit(1);
        }
    }
    
    /**
     * Generiert detaillierte Sitemaps
     */
    private function generateDetailedSitemaps($types)
    {
        $results = [];
        $enabledTypes = $types ?? $this->generator->getConfig()->getEnabledSitemapTypes();
        
        foreach ($enabledTypes as $type) {
            $this->info("Generiere detaillierte {$type}-Sitemap...");
            
            switch ($type) {
                case 'mobile':
                    $generator = new \SitemapGenerator\Generators\MobileSitemapGenerator($this->generator->getConfig());
                    $urls = $this->generator->getUrlCrawler()->crawl();
                    $results[$type] = $generator->generateDetailed($urls);
                    break;
                    
                case 'image':
                    $generator = new \SitemapGenerator\Generators\ImageSitemapGenerator($this->generator->getConfig());
                    $urls = $this->generator->getUrlCrawler()->crawl();
                    $results[$type] = $generator->generateDetailed($urls);
                    break;
                    
                case 'video':
                    $generator = new \SitemapGenerator\Generators\VideoSitemapGenerator($this->generator->getConfig());
                    $urls = $this->generator->getUrlCrawler()->crawl();
                    $results[$type] = $generator->generateDetailed($urls);
                    break;
                    
                default:
                    $results[$type] = $this->generator->generate($type);
                    break;
            }
        }
        
        return $results;
    }
    
    /**
     * Führt Cron-Job aus
     */
    private function runCronJob()
    {
        try {
            $this->info("Starte Cron-Job...");
            
            $result = $this->generator->runCronJob();
            
            if ($result['success']) {
                $this->success("Cron-Job erfolgreich abgeschlossen");
                $this->showResults($result['results']);
            } else {
                $this->error("Cron-Job fehlgeschlagen: " . $result['error']);
                exit(1);
            }
            
        } catch (Exception $e) {
            $this->error("Cron-Job Fehler: " . $e->getMessage());
            exit(1);
        }
    }
    
    /**
     * Validiert die Konfiguration
     */
    private function validateConfig()
    {
        $this->info("Validiere Konfiguration...");
        
        $errors = $this->generator->validateConfig();
        
        if (empty($errors)) {
            $this->success("Konfiguration ist gültig");
        } else {
            $this->error("Konfigurationsfehler gefunden:");
            foreach ($errors as $error) {
                $this->output("  - {$error}");
            }
            exit(1);
        }
    }
    
    /**
     * Testet Ping-Funktionalität
     */
    private function testPing()
    {
        $this->info("Teste Ping-Funktionalität...");
        
        $ping = new \SitemapGenerator\SearchEnginePing($this->generator->getConfig());
        $results = $ping->testPing();
        
        $this->output("\nTest-Ergebnisse:");
        foreach ($results as $engine => $result) {
            $status = $result['success'] ? '✓' : '✗';
            $this->output("  {$status} {$result['name']}");
            
            if (!$result['success'] && !empty($result['error'])) {
                $this->output("    Fehler: {$result['error']}");
            }
        }
    }
    
    /**
     * Zeigt Generierungsergebnisse an
     */
    private function showResults($results)
    {
        if (!$this->options['quiet']) {
            $this->output("\nGenerierte Sitemaps:");
            
            foreach ($results as $type => $result) {
                if (isset($result['error'])) {
                    $this->output("  ✗ {$type}: {$result['error']}");
                } else {
                    $this->output("  ✓ {$type}: {$result['file']}");
                    
                    if (isset($result['urls_count'])) {
                        $this->output("    URLs: {$result['urls_count']}");
                    }
                    
                    if (isset($result['images_count'])) {
                        $this->output("    Bilder: {$result['images_count']}");
                    }
                    
                    if (isset($result['videos_count'])) {
                        $this->output("    Videos: {$result['videos_count']}");
                    }
                    
                    if (isset($result['compressed_file'])) {
                        $this->output("    Komprimiert: {$result['compressed_file']}");
                    }
                    
                    if (isset($result['stats_file'])) {
                        $this->output("    Statistiken: {$result['stats_file']}");
                    }
                }
            }
        }
    }
    
    /**
     * Zeigt Hilfe an
     */
    private function showHelp()
    {
        $this->output("PHP Sitemap Generator - Kommandozeilen-Interface");
        $this->output("");
        $this->output("VERWENDUNG:");
        $this->output("  php sitemap-cli.php [Optionen]");
        $this->output("");
        $this->output("OPTIONEN:");
        $this->output("  --config=FILE          Konfigurationsdatei (JSON, PHP oder INI)");
        $this->output("  --generate=TYPES       Sitemap-Typen generieren (all, xml, html, text, mobile, image, video)");
        $this->output("  --output=DIR           Ausgabe-Verzeichnis");
        $this->output("  --base-url=URL         Basis-URL der Website");
        $this->output("  --detailed             Detaillierte Analyse für Medien-Sitemaps");
        $this->output("  --test-ping            Ping-Funktionalität testen");
        $this->output("  --validate-config      Konfiguration validieren");
        $this->output("  --cron                 Als Cron-Job ausführen");
        $this->output("  --verbose, -v          Ausführliche Ausgabe");
        $this->output("  --quiet, -q            Keine Ausgabe");
        $this->output("  --help, -h             Diese Hilfe anzeigen");
        $this->output("  --version              Version anzeigen");
        $this->output("");
        $this->output("BEISPIELE:");
        $this->output("  php sitemap-cli.php --config=config.json");
        $this->output("  php sitemap-cli.php --generate=xml,html --base-url=https://example.com");
        $this->output("  php sitemap-cli.php --generate=image --detailed");
        $this->output("  php sitemap-cli.php --test-ping");
        $this->output("  php sitemap-cli.php --cron --quiet");
    }
    
    /**
     * Zeigt Version an
     */
    private function showVersion()
    {
        $this->output("PHP Sitemap Generator v1.0.0");
        $this->output("Copyright (c) 2024 Manus AI");
    }
    
    /**
     * Ausgabe-Funktionen
     */
    private function output($message)
    {
        if (!$this->options['quiet']) {
            echo $message . PHP_EOL;
        }
    }
    
    private function info($message)
    {
        if ($this->options['verbose'] && !$this->options['quiet']) {
            echo "[INFO] {$message}" . PHP_EOL;
        }
    }
    
    private function success($message)
    {
        if (!$this->options['quiet']) {
            echo "[✓] {$message}" . PHP_EOL;
        }
    }
    
    private function error($message)
    {
        fwrite(STDERR, "[✗] {$message}" . PHP_EOL);
    }
}

// CLI ausführen
if (php_sapi_name() === 'cli') {
    $cli = new SitemapCLI($argv);
    $cli->run();
} else {
    echo "Dieses Script kann nur über die Kommandozeile ausgeführt werden." . PHP_EOL;
    exit(1);
}

