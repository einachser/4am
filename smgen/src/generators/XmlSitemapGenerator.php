<?php

namespace SitemapGenerator\Generators;

require_once 'AbstractSitemapGenerator.php';

/**
 * XML-Sitemap-Generator gemäß Google Sitemap-Protokoll
 */
class XmlSitemapGenerator extends AbstractSitemapGenerator
{
    /**
     * Generiert XML-Sitemap(s)
     * 
     * @param array $urls URLs mit Metadaten
     * @return array Ergebnis der Generierung
     */
    public function generate(array $urls)
    {
        $validUrls = $this->validateUrls($urls);
        $maxUrlsPerFile = $this->config->get('xml_max_urls_per_file', 50000);
        
        // Prüfen ob Sitemap-Index benötigt wird
        if (count($validUrls) > $maxUrlsPerFile) {
            return $this->generateSitemapIndex($validUrls, $maxUrlsPerFile);
        } else {
            return $this->generateSingleSitemap($validUrls);
        }
    }
    
    /**
     * Generiert eine einzelne XML-Sitemap
     * 
     * @param array $urls URLs
     * @return array Ergebnis
     */
    private function generateSingleSitemap($urls)
    {
        $filename = $this->config->get('xml_sitemap_filename', 'sitemap.xml');
        $filePath = $this->generateFilePath($filename);
        
        $xml = $this->generateXmlContent($urls);
        $this->writeFile($filePath, $xml);
        
        $result = [
            'file' => $filePath,
            'url' => $this->generateSitemapUrl($filename),
            'urls_count' => count($urls),
            'type' => 'xml'
        ];
        
        // Komprimierung falls aktiviert
        if ($this->config->get('xml_compress', true)) {
            $compressedPath = $this->compressFile($filePath);
            $result['compressed_file'] = $compressedPath;
            $result['compressed_url'] = $this->generateSitemapUrl($filename . '.gz');
        }
        
        $this->log("XML-Sitemap generiert: {$filename} ({$result['urls_count']} URLs)");
        
        return $result;
    }
    
    /**
     * Generiert Sitemap-Index für große Websites
     * 
     * @param array $urls Alle URLs
     * @param int $maxUrlsPerFile Maximale URLs pro Datei
     * @return array Ergebnis
     */
    private function generateSitemapIndex($urls, $maxUrlsPerFile)
    {
        $chunks = array_chunk($urls, $maxUrlsPerFile);
        $sitemapFiles = [];
        
        // Einzelne Sitemap-Dateien generieren
        foreach ($chunks as $index => $chunk) {
            $filename = $this->generateFilename('sitemap', 'xml', $index + 1);
            $filePath = $this->generateFilePath($filename);
            
            $xml = $this->generateXmlContent($chunk);
            $this->writeFile($filePath, $xml);
            
            $sitemapInfo = [
                'file' => $filePath,
                'url' => $this->generateSitemapUrl($filename),
                'lastmod' => date('c'),
                'urls_count' => count($chunk)
            ];
            
            // Komprimierung falls aktiviert
            if ($this->config->get('xml_compress', true)) {
                $compressedPath = $this->compressFile($filePath);
                $sitemapInfo['compressed_file'] = $compressedPath;
                $sitemapInfo['compressed_url'] = $this->generateSitemapUrl($filename . '.gz');
                $sitemapInfo['url'] = $sitemapInfo['compressed_url']; // Index verweist auf komprimierte Version
            }
            
            $sitemapFiles[] = $sitemapInfo;
            
            $this->log("XML-Sitemap-Teil generiert: {$filename} ({$sitemapInfo['urls_count']} URLs)");
        }
        
        // Sitemap-Index generieren
        $indexFilename = $this->config->get('xml_sitemap_index_filename', 'sitemap-index.xml');
        $indexFilePath = $this->generateFilePath($indexFilename);
        
        $indexXml = $this->generateSitemapIndexXml($sitemapFiles);
        $this->writeFile($indexFilePath, $indexXml);
        
        $result = [
            'file' => $indexFilePath,
            'url' => $this->generateSitemapUrl($indexFilename),
            'type' => 'xml_index',
            'sitemap_files' => $sitemapFiles,
            'total_urls' => count($urls),
            'sitemap_count' => count($sitemapFiles)
        ];
        
        $this->log("XML-Sitemap-Index generiert: {$indexFilename} ({$result['sitemap_count']} Sitemaps, {$result['total_urls']} URLs insgesamt)");
        
        return $result;
    }
    
    /**
     * Generiert XML-Inhalt für eine Sitemap
     * 
     * @param array $urls URLs
     * @return string XML-Inhalt
     */
    private function generateXmlContent($urls)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        
        // Zusätzliche Namespaces für erweiterte Funktionen
        $xml .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';
        $xml .= ' xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"';
        $xml .= ' xmlns:mobile="http://www.google.com/schemas/sitemap-mobile/1.0"';
        
        $xml .= '>' . PHP_EOL;
        
        foreach ($urls as $urlData) {
            $xml .= $this->generateUrlXml($urlData);
        }
        
        $xml .= '</urlset>' . PHP_EOL;
        
        return $xml;
    }
    
    /**
     * Generiert XML für eine einzelne URL
     * 
     * @param array $urlData URL-Daten
     * @return string XML-Fragment
     */
    private function generateUrlXml($urlData)
    {
        $xml = '  <url>' . PHP_EOL;
        $xml .= '    <loc>' . $this->escapeXml($urlData['url']) . '</loc>' . PHP_EOL;
        
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
        
        // Mobile-Annotation
        if (!empty($urlData['mobile'])) {
            $xml .= '    <mobile:mobile/>' . PHP_EOL;
        }
        
        // Bilder
        if (!empty($urlData['images']) && is_array($urlData['images'])) {
            foreach ($urlData['images'] as $image) {
                $xml .= $this->generateImageXml($image);
            }
        }
        
        // Videos
        if (!empty($urlData['videos']) && is_array($urlData['videos'])) {
            foreach ($urlData['videos'] as $video) {
                $xml .= $this->generateVideoXml($video);
            }
        }
        
        $xml .= '  </url>' . PHP_EOL;
        
        return $xml;
    }
    
    /**
     * Generiert XML für ein Bild
     * 
     * @param array $imageData Bild-Daten
     * @return string XML-Fragment
     */
    private function generateImageXml($imageData)
    {
        if (empty($imageData['url']) || !$this->isValidUrl($imageData['url'])) {
            return '';
        }
        
        $xml = '    <image:image>' . PHP_EOL;
        $xml .= '      <image:loc>' . $this->escapeXml($imageData['url']) . '</image:loc>' . PHP_EOL;
        
        if (!empty($imageData['caption'])) {
            $xml .= '      <image:caption>' . $this->escapeXml($imageData['caption']) . '</image:caption>' . PHP_EOL;
        }
        
        if (!empty($imageData['title'])) {
            $xml .= '      <image:title>' . $this->escapeXml($imageData['title']) . '</image:title>' . PHP_EOL;
        }
        
        if (!empty($imageData['alt'])) {
            $xml .= '      <image:caption>' . $this->escapeXml($imageData['alt']) . '</image:caption>' . PHP_EOL;
        }
        
        $xml .= '    </image:image>' . PHP_EOL;
        
        return $xml;
    }
    
    /**
     * Generiert XML für ein Video
     * 
     * @param array $videoData Video-Daten
     * @return string XML-Fragment
     */
    private function generateVideoXml($videoData)
    {
        if (empty($videoData['url']) || !$this->isValidUrl($videoData['url'])) {
            return '';
        }
        
        $xml = '    <video:video>' . PHP_EOL;
        $xml .= '      <video:content_loc>' . $this->escapeXml($videoData['url']) . '</video:content_loc>' . PHP_EOL;
        
        if (!empty($videoData['title'])) {
            $xml .= '      <video:title>' . $this->escapeXml($videoData['title']) . '</video:title>' . PHP_EOL;
        }
        
        if (!empty($videoData['description'])) {
            $xml .= '      <video:description>' . $this->escapeXml($videoData['description']) . '</video:description>' . PHP_EOL;
        }
        
        if (!empty($videoData['thumbnail_loc'])) {
            $xml .= '      <video:thumbnail_loc>' . $this->escapeXml($videoData['thumbnail_loc']) . '</video:thumbnail_loc>' . PHP_EOL;
        }
        
        if (!empty($videoData['duration']) && is_numeric($videoData['duration'])) {
            $xml .= '      <video:duration>' . intval($videoData['duration']) . '</video:duration>' . PHP_EOL;
        }
        
        if (!empty($videoData['publication_date'])) {
            $xml .= '      <video:publication_date>' . $this->formatDate($videoData['publication_date']) . '</video:publication_date>' . PHP_EOL;
        }
        
        $xml .= '    </video:video>' . PHP_EOL;
        
        return $xml;
    }
    
    /**
     * Generiert XML für Sitemap-Index
     * 
     * @param array $sitemapFiles Sitemap-Dateien
     * @return string XML-Inhalt
     */
    private function generateSitemapIndexXml($sitemapFiles)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        
        foreach ($sitemapFiles as $sitemapFile) {
            $xml .= '  <sitemap>' . PHP_EOL;
            $xml .= '    <loc>' . $this->escapeXml($sitemapFile['url']) . '</loc>' . PHP_EOL;
            
            if (!empty($sitemapFile['lastmod'])) {
                $xml .= '    <lastmod>' . $this->formatDate($sitemapFile['lastmod']) . '</lastmod>' . PHP_EOL;
            }
            
            $xml .= '  </sitemap>' . PHP_EOL;
        }
        
        $xml .= '</sitemapindex>' . PHP_EOL;
        
        return $xml;
    }
    
    /**
     * Validiert URLs für XML-Sitemap
     * 
     * @param array $urls URLs
     * @return array Validierte URLs
     */
    public function validateUrls(array $urls)
    {
        $validUrls = [];
        
        foreach ($urls as $url) {
            // Basis-Validierung
            if (!$this->isValidUrl($url['url'])) {
                $this->log("Ungültige URL übersprungen: " . $url['url'], 'warning');
                continue;
            }
            
            // Status-Code prüfen
            if (!in_array($url['status_code'], [200, 301, 302])) {
                $this->log("URL mit ungültigem Status-Code übersprungen: " . $url['url'] . " (Status: " . $url['status_code'] . ")", 'warning');
                continue;
            }
            
            // URL-Länge prüfen (Google-Limit: 2048 Zeichen)
            if (strlen($url['url']) > 2048) {
                $this->log("URL zu lang übersprungen: " . $url['url'], 'warning');
                continue;
            }
            
            $validUrls[] = $url;
        }
        
        $this->log("URLs validiert: " . count($validUrls) . " von " . count($urls) . " URLs sind gültig");
        
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
    
    /**
     * Validiert XML-Sitemap gegen Schema
     * 
     * @param string $xmlContent XML-Inhalt
     * @return array Validierungsergebnisse
     */
    public function validateXml($xmlContent)
    {
        $errors = [];
        
        // XML-Syntax prüfen
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        
        if (!$dom->loadXML($xmlContent)) {
            $xmlErrors = libxml_get_errors();
            foreach ($xmlErrors as $error) {
                $errors[] = "XML-Syntax-Fehler: " . trim($error->message);
            }
            libxml_clear_errors();
        }
        
        // Weitere Validierungen könnten hier hinzugefügt werden
        // z.B. Schema-Validierung gegen sitemap.xsd
        
        return $errors;
    }
    
    /**
     * Gibt Statistiken über die generierte Sitemap zurück
     * 
     * @param array $urls URLs
     * @return array Statistiken
     */
    public function getStatistics($urls)
    {
        $stats = [
            'total_urls' => count($urls),
            'urls_with_images' => 0,
            'urls_with_videos' => 0,
            'mobile_urls' => 0,
            'priority_distribution' => [],
            'changefreq_distribution' => []
        ];
        
        foreach ($urls as $url) {
            // Bilder zählen
            if (!empty($url['images'])) {
                $stats['urls_with_images']++;
            }
            
            // Videos zählen
            if (!empty($url['videos'])) {
                $stats['urls_with_videos']++;
            }
            
            // Mobile URLs zählen
            if (!empty($url['mobile'])) {
                $stats['mobile_urls']++;
            }
            
            // Prioritäts-Verteilung
            $priority = $url['priority'] ?? 0.5;
            $priorityRange = floor($priority * 10) / 10;
            $stats['priority_distribution'][$priorityRange] = ($stats['priority_distribution'][$priorityRange] ?? 0) + 1;
            
            // Changefreq-Verteilung
            $changefreq = $url['changefreq'] ?? 'weekly';
            $stats['changefreq_distribution'][$changefreq] = ($stats['changefreq_distribution'][$changefreq] ?? 0) + 1;
        }
        
        return $stats;
    }
}

