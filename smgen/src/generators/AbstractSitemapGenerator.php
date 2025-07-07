<?php

namespace SitemapGenerator\Generators;

use SitemapGenerator\Config;

/**
 * Abstrakte Basisklasse für alle Sitemap-Generatoren
 */
abstract class AbstractSitemapGenerator implements SitemapGeneratorInterface
{
    protected $config;
    
    public function __construct(Config $config)
    {
        $this->config = $config;
    }
    
    /**
     * Erstellt das Ausgabe-Verzeichnis falls es nicht existiert
     * 
     * @param string $filePath Pfad zur Ausgabe-Datei
     */
    protected function ensureOutputDirectory($filePath)
    {
        $directory = dirname($filePath);
        
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }
    
    /**
     * Generiert die vollständige URL für eine Sitemap-Datei
     * 
     * @param string $filename Dateiname
     * @return string Vollständige URL
     */
    protected function generateSitemapUrl($filename)
    {
        $baseUrl = rtrim($this->config->get('base_url'), '/');
        return $baseUrl . '/' . $filename;
    }
    
    /**
     * Generiert den vollständigen Dateipfad für eine Sitemap
     * 
     * @param string $filename Dateiname
     * @return string Vollständiger Dateipfad
     */
    protected function generateFilePath($filename)
    {
        $outputDir = $this->config->get('output_dir', 'output');
        return rtrim($outputDir, '/') . '/' . $filename;
    }
    
    /**
     * Schreibt Inhalt in eine Datei
     * 
     * @param string $filePath Dateipfad
     * @param string $content Inhalt
     * @return bool Erfolg
     */
    protected function writeFile($filePath, $content)
    {
        $this->ensureOutputDirectory($filePath);
        
        $result = file_put_contents($filePath, $content);
        
        if ($result === false) {
            throw new \Exception("Fehler beim Schreiben der Datei: {$filePath}");
        }
        
        return true;
    }
    
    /**
     * Komprimiert eine Datei mit gzip
     * 
     * @param string $filePath Pfad zur Originaldatei
     * @return string Pfad zur komprimierten Datei
     */
    protected function compressFile($filePath)
    {
        $compressedPath = $filePath . '.gz';
        
        $originalFile = fopen($filePath, 'rb');
        $compressedFile = gzopen($compressedPath, 'wb9');
        
        if (!$originalFile || !$compressedFile) {
            throw new \Exception("Fehler beim Komprimieren der Datei: {$filePath}");
        }
        
        while (!feof($originalFile)) {
            gzwrite($compressedFile, fread($originalFile, 8192));
        }
        
        fclose($originalFile);
        gzclose($compressedFile);
        
        return $compressedPath;
    }
    
    /**
     * Formatiert ein Datum im ISO 8601 Format
     * 
     * @param string|int $date Datum als String oder Timestamp
     * @return string Formatiertes Datum
     */
    protected function formatDate($date)
    {
        if (is_numeric($date)) {
            return date('c', $date);
        }
        
        if (is_string($date)) {
            $timestamp = strtotime($date);
            if ($timestamp !== false) {
                return date('c', $timestamp);
            }
        }
        
        return date('c');
    }
    
    /**
     * Escaped XML-Zeichen in einem String
     * 
     * @param string $string String zum Escapen
     * @return string Escaped String
     */
    protected function escapeXml($string)
    {
        return htmlspecialchars($string, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }
    
    /**
     * Validiert eine URL
     * 
     * @param string $url URL zum Validieren
     * @return bool Gültig oder nicht
     */
    protected function isValidUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Prüft ob eine URL eine Bild-URL ist
     * 
     * @param string $url URL
     * @return bool
     */
    protected function isImageUrl($url)
    {
        $extensions = $this->config->get('image_extensions', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        
        return in_array($extension, $extensions);
    }
    
    /**
     * Prüft ob eine URL eine Video-URL ist
     * 
     * @param string $url URL
     * @return bool
     */
    protected function isVideoUrl($url)
    {
        $extensions = $this->config->get('video_extensions', ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm']);
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        
        return in_array($extension, $extensions);
    }
    
    /**
     * Filtert URLs basierend auf Status-Code
     * 
     * @param array $urls URLs
     * @param array $allowedCodes Erlaubte Status-Codes
     * @return array Gefilterte URLs
     */
    protected function filterByStatusCode($urls, $allowedCodes = [200, 301, 302])
    {
        return array_filter($urls, function($url) use ($allowedCodes) {
            return in_array($url['status_code'], $allowedCodes);
        });
    }
    
    /**
     * Gruppiert URLs nach Verzeichnis
     * 
     * @param array $urls URLs
     * @return array Gruppierte URLs
     */
    protected function groupUrlsByDirectory($urls)
    {
        $grouped = [];
        
        foreach ($urls as $url) {
            $path = parse_url($url['url'], PHP_URL_PATH);
            $directory = dirname($path);
            
            if ($directory === '.') {
                $directory = '/';
            }
            
            if (!isset($grouped[$directory])) {
                $grouped[$directory] = [];
            }
            
            $grouped[$directory][] = $url;
        }
        
        // Sortiere Verzeichnisse
        ksort($grouped);
        
        return $grouped;
    }
    
    /**
     * Begrenzt die Anzahl der URLs
     * 
     * @param array $urls URLs
     * @param int $limit Maximale Anzahl
     * @return array Begrenzte URLs
     */
    protected function limitUrls($urls, $limit)
    {
        if (count($urls) <= $limit) {
            return $urls;
        }
        
        return array_slice($urls, 0, $limit);
    }
    
    /**
     * Generiert einen eindeutigen Dateinamen
     * 
     * @param string $baseName Basis-Dateiname
     * @param string $extension Dateiendung
     * @param int $index Index für mehrere Dateien
     * @return string Dateiname
     */
    protected function generateFilename($baseName, $extension, $index = null)
    {
        if ($index !== null && $index > 0) {
            return $baseName . '-' . $index . '.' . $extension;
        }
        
        return $baseName . '.' . $extension;
    }
    
    /**
     * Loggt eine Nachricht
     * 
     * @param string $message Nachricht
     * @param string $level Log-Level
     */
    protected function log($message, $level = 'info')
    {
        if ($this->config->get('enable_logging', true)) {
            $timestamp = date('Y-m-d H:i:s');
            $logMessage = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
            
            $logFile = $this->config->get('log_file', 'logs/sitemap-generator.log');
            $this->ensureOutputDirectory($logFile);
            file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
        }
    }
}

