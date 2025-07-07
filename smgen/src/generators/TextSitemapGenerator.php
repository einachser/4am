<?php

namespace SitemapGenerator\Generators;

require_once 'AbstractSitemapGenerator.php';

/**
 * Text-Sitemap-Generator für einfache Textdateien
 */
class TextSitemapGenerator extends AbstractSitemapGenerator
{
    /**
     * Generiert Text-Sitemap
     * 
     * @param array $urls URLs mit Metadaten
     * @return array Ergebnis der Generierung
     */
    public function generate(array $urls)
    {
        $validUrls = $this->validateUrls($urls);
        $filename = $this->config->get('text_sitemap_filename', 'sitemap.txt');
        $filePath = $this->generateFilePath($filename);
        
        $content = $this->generateTextContent($validUrls);
        $this->writeFile($filePath, $content);
        
        $result = [
            'file' => $filePath,
            'url' => $this->generateSitemapUrl($filename),
            'urls_count' => count($validUrls),
            'type' => 'text'
        ];
        
        $this->log("Text-Sitemap generiert: {$filename} ({$result['urls_count']} URLs)");
        
        return $result;
    }
    
    /**
     * Generiert Text-Inhalt für die Sitemap
     * 
     * @param array $urls URLs
     * @return string Text-Inhalt
     */
    private function generateTextContent($urls)
    {
        $format = $this->config->get('text_format', 'simple');
        
        switch ($format) {
            case 'detailed':
                return $this->generateDetailedFormat($urls);
            case 'grouped':
                return $this->generateGroupedFormat($urls);
            case 'simple':
            default:
                return $this->generateSimpleFormat($urls);
        }
    }
    
    /**
     * Generiert einfaches Format (nur URLs)
     * 
     * @param array $urls URLs
     * @return string Text-Inhalt
     */
    private function generateSimpleFormat($urls)
    {
        $content = $this->generateHeader();
        $content .= PHP_EOL;
        
        foreach ($urls as $url) {
            $content .= $url['url'] . PHP_EOL;
        }
        
        return $content;
    }
    
    /**
     * Generiert detailliertes Format mit Metadaten
     * 
     * @param array $urls URLs
     * @return string Text-Inhalt
     */
    private function generateDetailedFormat($urls)
    {
        $content = $this->generateHeader();
        $content .= PHP_EOL;
        $content .= str_repeat('=', 80) . PHP_EOL;
        $content .= 'DETAILLIERTE SITEMAP' . PHP_EOL;
        $content .= str_repeat('=', 80) . PHP_EOL;
        $content .= PHP_EOL;
        
        foreach ($urls as $index => $url) {
            $content .= sprintf("[%d] %s", $index + 1, $url['url']) . PHP_EOL;
            
            // Metadaten hinzufügen
            if (!empty($url['lastmod'])) {
                $date = date('d.m.Y H:i:s', strtotime($url['lastmod']));
                $content .= "    Letzte Änderung: {$date}" . PHP_EOL;
            }
            
            if (!empty($url['changefreq'])) {
                $freqLabels = [
                    'always' => 'Immer',
                    'hourly' => 'Stündlich',
                    'daily' => 'Täglich',
                    'weekly' => 'Wöchentlich',
                    'monthly' => 'Monatlich',
                    'yearly' => 'Jährlich',
                    'never' => 'Nie'
                ];
                $freqLabel = $freqLabels[$url['changefreq']] ?? $url['changefreq'];
                $content .= "    Änderungsfrequenz: {$freqLabel}" . PHP_EOL;
            }
            
            if (isset($url['priority'])) {
                $priority = number_format($url['priority'], 1);
                $content .= "    Priorität: {$priority}" . PHP_EOL;
            }
            
            // Besondere Eigenschaften
            $features = [];
            if (!empty($url['mobile'])) {
                $features[] = 'Mobile-optimiert';
            }
            if (!empty($url['images']) && count($url['images']) > 0) {
                $features[] = count($url['images']) . ' Bild(er)';
            }
            if (!empty($url['videos']) && count($url['videos']) > 0) {
                $features[] = count($url['videos']) . ' Video(s)';
            }
            
            if (!empty($features)) {
                $content .= "    Eigenschaften: " . implode(', ', $features) . PHP_EOL;
            }
            
            $content .= PHP_EOL;
        }
        
        return $content;
    }
    
    /**
     * Generiert gruppiertes Format nach Verzeichnissen
     * 
     * @param array $urls URLs
     * @return string Text-Inhalt
     */
    private function generateGroupedFormat($urls)
    {
        $content = $this->generateHeader();
        $content .= PHP_EOL;
        $content .= str_repeat('=', 80) . PHP_EOL;
        $content .= 'GRUPPIERTE SITEMAP' . PHP_EOL;
        $content .= str_repeat('=', 80) . PHP_EOL;
        $content .= PHP_EOL;
        
        $grouped = $this->groupUrlsByDirectory($urls);
        
        foreach ($grouped as $directory => $directoryUrls) {
            $dirName = $directory === '/' ? 'STARTSEITE' : strtoupper($directory);
            $content .= str_repeat('-', 40) . PHP_EOL;
            $content .= sprintf("%s (%d URLs)", $dirName, count($directoryUrls)) . PHP_EOL;
            $content .= str_repeat('-', 40) . PHP_EOL;
            $content .= PHP_EOL;
            
            foreach ($directoryUrls as $url) {
                $content .= "• " . $url['url'] . PHP_EOL;
                
                // Kurze Metadaten
                $metadata = [];
                if (isset($url['priority']) && $url['priority'] >= 0.8) {
                    $metadata[] = 'Hohe Priorität';
                }
                if (!empty($url['mobile'])) {
                    $metadata[] = 'Mobile';
                }
                if (!empty($url['images']) && count($url['images']) > 0) {
                    $metadata[] = count($url['images']) . ' Bild(er)';
                }
                if (!empty($url['videos']) && count($url['videos']) > 0) {
                    $metadata[] = count($url['videos']) . ' Video(s)';
                }
                
                if (!empty($metadata)) {
                    $content .= "  (" . implode(', ', $metadata) . ")" . PHP_EOL;
                }
            }
            
            $content .= PHP_EOL;
        }
        
        return $content;
    }
    
    /**
     * Generiert Header für die Text-Sitemap
     * 
     * @return string Header-Text
     */
    private function generateHeader()
    {
        $baseUrl = $this->config->get('base_url');
        $date = date('d.m.Y H:i:s');
        
        $header = str_repeat('=', 80) . PHP_EOL;
        $header .= 'SITEMAP' . PHP_EOL;
        $header .= str_repeat('=', 80) . PHP_EOL;
        $header .= PHP_EOL;
        $header .= "Website: {$baseUrl}" . PHP_EOL;
        $header .= "Generiert am: {$date}" . PHP_EOL;
        $header .= "Generator: PHP Sitemap Generator" . PHP_EOL;
        
        return $header;
    }
    
    /**
     * Generiert Statistiken für die Text-Sitemap
     * 
     * @param array $urls URLs
     * @return string Statistik-Text
     */
    private function generateStatistics($urls)
    {
        $stats = [
            'total_urls' => count($urls),
            'urls_with_images' => 0,
            'urls_with_videos' => 0,
            'mobile_urls' => 0,
            'high_priority_urls' => 0
        ];
        
        foreach ($urls as $url) {
            if (!empty($url['images'])) {
                $stats['urls_with_images']++;
            }
            if (!empty($url['videos'])) {
                $stats['urls_with_videos']++;
            }
            if (!empty($url['mobile'])) {
                $stats['mobile_urls']++;
            }
            if (isset($url['priority']) && $url['priority'] >= 0.8) {
                $stats['high_priority_urls']++;
            }
        }
        
        $content = str_repeat('=', 80) . PHP_EOL;
        $content .= 'STATISTIKEN' . PHP_EOL;
        $content .= str_repeat('=', 80) . PHP_EOL;
        $content .= PHP_EOL;
        $content .= sprintf("Gesamt URLs: %d", $stats['total_urls']) . PHP_EOL;
        $content .= sprintf("URLs mit Bildern: %d", $stats['urls_with_images']) . PHP_EOL;
        $content .= sprintf("URLs mit Videos: %d", $stats['urls_with_videos']) . PHP_EOL;
        $content .= sprintf("Mobile URLs: %d", $stats['mobile_urls']) . PHP_EOL;
        $content .= sprintf("URLs mit hoher Priorität: %d", $stats['high_priority_urls']) . PHP_EOL;
        $content .= PHP_EOL;
        
        return $content;
    }
    
    /**
     * Generiert erweiterte Text-Sitemap mit Statistiken
     * 
     * @param array $urls URLs
     * @return array Ergebnis mit zusätzlicher Statistik-Datei
     */
    public function generateWithStatistics($urls)
    {
        $result = $this->generate($urls);
        
        // Zusätzliche Statistik-Datei erstellen
        $statsFilename = str_replace('.txt', '-stats.txt', $this->config->get('text_sitemap_filename', 'sitemap.txt'));
        $statsFilePath = $this->generateFilePath($statsFilename);
        
        $statsContent = $this->generateStatistics($urls);
        $this->writeFile($statsFilePath, $statsContent);
        
        $result['stats_file'] = $statsFilePath;
        $result['stats_url'] = $this->generateSitemapUrl($statsFilename);
        
        return $result;
    }
    
    /**
     * Exportiert URLs in verschiedenen Textformaten
     * 
     * @param array $urls URLs
     * @param string $format Format (csv, json, yaml)
     * @return array Ergebnis
     */
    public function exportUrls($urls, $format = 'csv')
    {
        $validUrls = $this->validateUrls($urls);
        
        switch (strtolower($format)) {
            case 'csv':
                return $this->exportToCsv($validUrls);
            case 'json':
                return $this->exportToJson($validUrls);
            case 'yaml':
                return $this->exportToYaml($validUrls);
            default:
                throw new \Exception("Nicht unterstütztes Export-Format: {$format}");
        }
    }
    
    /**
     * Exportiert URLs als CSV
     * 
     * @param array $urls URLs
     * @return array Ergebnis
     */
    private function exportToCsv($urls)
    {
        $filename = str_replace('.txt', '.csv', $this->config->get('text_sitemap_filename', 'sitemap.txt'));
        $filePath = $this->generateFilePath($filename);
        
        $csv = "URL,Letzte Änderung,Änderungsfrequenz,Priorität,Mobile,Bilder,Videos" . PHP_EOL;
        
        foreach ($urls as $url) {
            $row = [
                $url['url'],
                $url['lastmod'] ?? '',
                $url['changefreq'] ?? '',
                isset($url['priority']) ? number_format($url['priority'], 1) : '',
                !empty($url['mobile']) ? 'Ja' : 'Nein',
                !empty($url['images']) ? count($url['images']) : '0',
                !empty($url['videos']) ? count($url['videos']) : '0'
            ];
            
            $csv .= '"' . implode('","', $row) . '"' . PHP_EOL;
        }
        
        $this->writeFile($filePath, $csv);
        
        return [
            'file' => $filePath,
            'url' => $this->generateSitemapUrl($filename),
            'urls_count' => count($urls),
            'type' => 'csv'
        ];
    }
    
    /**
     * Exportiert URLs als JSON
     * 
     * @param array $urls URLs
     * @return array Ergebnis
     */
    private function exportToJson($urls)
    {
        $filename = str_replace('.txt', '.json', $this->config->get('text_sitemap_filename', 'sitemap.txt'));
        $filePath = $this->generateFilePath($filename);
        
        $data = [
            'sitemap' => [
                'base_url' => $this->config->get('base_url'),
                'generated_at' => date('c'),
                'total_urls' => count($urls),
                'urls' => $urls
            ]
        ];
        
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->writeFile($filePath, $json);
        
        return [
            'file' => $filePath,
            'url' => $this->generateSitemapUrl($filename),
            'urls_count' => count($urls),
            'type' => 'json'
        ];
    }
    
    /**
     * Exportiert URLs als YAML
     * 
     * @param array $urls URLs
     * @return array Ergebnis
     */
    private function exportToYaml($urls)
    {
        $filename = str_replace('.txt', '.yaml', $this->config->get('text_sitemap_filename', 'sitemap.txt'));
        $filePath = $this->generateFilePath($filename);
        
        $yaml = "sitemap:" . PHP_EOL;
        $yaml .= "  base_url: " . $this->config->get('base_url') . PHP_EOL;
        $yaml .= "  generated_at: " . date('c') . PHP_EOL;
        $yaml .= "  total_urls: " . count($urls) . PHP_EOL;
        $yaml .= "  urls:" . PHP_EOL;
        
        foreach ($urls as $url) {
            $yaml .= "    - url: " . $url['url'] . PHP_EOL;
            if (!empty($url['lastmod'])) {
                $yaml .= "      lastmod: " . $url['lastmod'] . PHP_EOL;
            }
            if (!empty($url['changefreq'])) {
                $yaml .= "      changefreq: " . $url['changefreq'] . PHP_EOL;
            }
            if (isset($url['priority'])) {
                $yaml .= "      priority: " . $url['priority'] . PHP_EOL;
            }
            if (!empty($url['mobile'])) {
                $yaml .= "      mobile: true" . PHP_EOL;
            }
            if (!empty($url['images'])) {
                $yaml .= "      images: " . count($url['images']) . PHP_EOL;
            }
            if (!empty($url['videos'])) {
                $yaml .= "      videos: " . count($url['videos']) . PHP_EOL;
            }
        }
        
        $this->writeFile($filePath, $yaml);
        
        return [
            'file' => $filePath,
            'url' => $this->generateSitemapUrl($filename),
            'urls_count' => count($urls),
            'type' => 'yaml'
        ];
    }
    
    /**
     * Validiert URLs für Text-Sitemap
     * 
     * @param array $urls URLs
     * @return array Validierte URLs
     */
    public function validateUrls(array $urls)
    {
        return $this->filterByStatusCode($urls, [200]);
    }
    
    /**
     * Gibt MIME-Type zurück
     * 
     * @return string
     */
    public function getMimeType()
    {
        return 'text/plain';
    }
    
    /**
     * Gibt Dateiendung zurück
     * 
     * @return string
     */
    public function getFileExtension()
    {
        return 'txt';
    }
}

