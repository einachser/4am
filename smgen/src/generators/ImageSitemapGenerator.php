<?php

namespace SitemapGenerator\Generators;

require_once 'AbstractSitemapGenerator.php';

/**
 * Bilder-Sitemap-Generator für Bilder-SEO
 */
class ImageSitemapGenerator extends AbstractSitemapGenerator
{
    /**
     * Generiert Bilder-Sitemap
     * 
     * @param array $urls URLs mit Metadaten
     * @return array Ergebnis der Generierung
     */
    public function generate(array $urls)
    {
        $validUrls = $this->validateUrls($urls);
        $filename = $this->config->get('image_sitemap_filename', 'sitemap-images.xml');
        $filePath = $this->generateFilePath($filename);
        
        $xml = $this->generateImageXmlContent($validUrls);
        $this->writeFile($filePath, $xml);
        
        $result = [
            'file' => $filePath,
            'url' => $this->generateSitemapUrl($filename),
            'urls_count' => count($validUrls),
            'images_count' => $this->countTotalImages($validUrls),
            'type' => 'image'
        ];
        
        // Komprimierung falls aktiviert
        if ($this->config->get('xml_compress', true)) {
            $compressedPath = $this->compressFile($filePath);
            $result['compressed_file'] = $compressedPath;
            $result['compressed_url'] = $this->generateSitemapUrl($filename . '.gz');
        }
        
        $this->log("Bilder-Sitemap generiert: {$filename} ({$result['urls_count']} URLs, {$result['images_count']} Bilder)");
        
        return $result;
    }
    
    /**
     * Generiert XML-Inhalt für Bilder-Sitemap
     * 
     * @param array $urls URLs
     * @return string XML-Inhalt
     */
    private function generateImageXmlContent($urls)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . PHP_EOL;
        $xml .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;
        
        foreach ($urls as $urlData) {
            if (!empty($urlData['images']) && count($urlData['images']) > 0) {
                $xml .= $this->generateImageUrlXml($urlData);
            }
        }
        
        $xml .= '</urlset>' . PHP_EOL;
        
        return $xml;
    }
    
    /**
     * Generiert XML für eine URL mit Bildern
     * 
     * @param array $urlData URL-Daten
     * @return string XML-Fragment
     */
    private function generateImageUrlXml($urlData)
    {
        $xml = '  <url>' . PHP_EOL;
        $xml .= '    <loc>' . $this->escapeXml($urlData['url']) . '</loc>' . PHP_EOL;
        
        // Letzte Änderung
        if (!empty($urlData['lastmod'])) {
            $xml .= '    <lastmod>' . $this->formatDate($urlData['lastmod']) . '</lastmod>' . PHP_EOL;
        }
        
        // Bilder hinzufügen (maximal 1000 pro Seite laut Google)
        $maxImages = $this->config->get('image_max_per_page', 1000);
        $images = array_slice($urlData['images'], 0, $maxImages);
        
        foreach ($images as $image) {
            $xml .= $this->generateImageXml($image, $urlData['url']);
        }
        
        $xml .= '  </url>' . PHP_EOL;
        
        return $xml;
    }
    
    /**
     * Generiert XML für ein einzelnes Bild
     * 
     * @param array $imageData Bild-Daten
     * @param string $pageUrl URL der Seite
     * @return string XML-Fragment
     */
    private function generateImageXml($imageData, $pageUrl)
    {
        if (empty($imageData['url']) || !$this->isValidUrl($imageData['url'])) {
            return '';
        }
        
        // Bild-URL zu absoluter URL auflösen
        $imageUrl = $this->resolveImageUrl($imageData['url'], $pageUrl);
        
        if (!$imageUrl || !$this->isImageUrl($imageUrl)) {
            return '';
        }
        
        $xml = '    <image:image>' . PHP_EOL;
        $xml .= '      <image:loc>' . $this->escapeXml($imageUrl) . '</image:loc>' . PHP_EOL;
        
        // Caption (bevorzugt aus figcaption, dann alt-text)
        $caption = $this->getBestCaption($imageData);
        if ($caption) {
            $xml .= '      <image:caption>' . $this->escapeXml($caption) . '</image:caption>' . PHP_EOL;
        }
        
        // Titel
        if (!empty($imageData['title'])) {
            $xml .= '      <image:title>' . $this->escapeXml($imageData['title']) . '</image:title>' . PHP_EOL;
        }
        
        // Geo-Location falls verfügbar
        if (!empty($imageData['geo_location'])) {
            $xml .= '      <image:geo_location>' . $this->escapeXml($imageData['geo_location']) . '</image:geo_location>' . PHP_EOL;
        }
        
        // Lizenz-URL falls verfügbar
        if (!empty($imageData['license'])) {
            $xml .= '      <image:license>' . $this->escapeXml($imageData['license']) . '</image:license>' . PHP_EOL;
        }
        
        $xml .= '    </image:image>' . PHP_EOL;
        
        return $xml;
    }
    
    /**
     * Löst Bild-URL zu absoluter URL auf
     * 
     * @param string $imageUrl Bild-URL
     * @param string $pageUrl Seiten-URL
     * @return string|null Absolute Bild-URL
     */
    private function resolveImageUrl($imageUrl, $pageUrl)
    {
        if (empty($imageUrl)) {
            return null;
        }
        
        // Bereits absolute URL
        if (parse_url($imageUrl, PHP_URL_SCHEME)) {
            return $imageUrl;
        }
        
        // Protocol-relative URL
        if (substr($imageUrl, 0, 2) === '//') {
            $scheme = parse_url($pageUrl, PHP_URL_SCHEME);
            return $scheme . ':' . $imageUrl;
        }
        
        // Absolute Pfad
        if (substr($imageUrl, 0, 1) === '/') {
            $scheme = parse_url($pageUrl, PHP_URL_SCHEME);
            $host = parse_url($pageUrl, PHP_URL_HOST);
            $port = parse_url($pageUrl, PHP_URL_PORT);
            
            $result = $scheme . '://' . $host;
            if ($port) {
                $result .= ':' . $port;
            }
            $result .= $imageUrl;
            
            return $result;
        }
        
        // Relativer Pfad
        $basePath = dirname(parse_url($pageUrl, PHP_URL_PATH));
        if ($basePath === '.') {
            $basePath = '';
        }
        
        $scheme = parse_url($pageUrl, PHP_URL_SCHEME);
        $host = parse_url($pageUrl, PHP_URL_HOST);
        $port = parse_url($pageUrl, PHP_URL_PORT);
        
        $result = $scheme . '://' . $host;
        if ($port) {
            $result .= ':' . $port;
        }
        $result .= $basePath . '/' . $imageUrl;
        
        return $result;
    }
    
    /**
     * Gibt die beste Caption für ein Bild zurück
     * 
     * @param array $imageData Bild-Daten
     * @return string|null Caption
     */
    private function getBestCaption($imageData)
    {
        // Priorität: caption > alt > title
        if (!empty($imageData['caption'])) {
            return $imageData['caption'];
        }
        
        if (!empty($imageData['alt'])) {
            return $imageData['alt'];
        }
        
        if (!empty($imageData['title'])) {
            return $imageData['title'];
        }
        
        return null;
    }
    
    /**
     * Zählt die Gesamtanzahl der Bilder
     * 
     * @param array $urls URLs
     * @return int Anzahl Bilder
     */
    private function countTotalImages($urls)
    {
        $count = 0;
        
        foreach ($urls as $url) {
            if (!empty($url['images'])) {
                $count += count($url['images']);
            }
        }
        
        return $count;
    }
    
    /**
     * Analysiert Bilder einer URL und reichert sie mit Metadaten an
     * 
     * @param string $url URL
     * @param array $images Gefundene Bilder
     * @return array Angereicherte Bilder
     */
    private function enrichImageData($url, $images)
    {
        $enrichedImages = [];
        
        foreach ($images as $image) {
            $imageUrl = $this->resolveImageUrl($image['url'], $url);
            
            if (!$imageUrl || !$this->isImageUrl($imageUrl)) {
                continue;
            }
            
            // Bild-Informationen abrufen
            $imageInfo = $this->getImageInfo($imageUrl);
            
            $enrichedImage = array_merge($image, [
                'url' => $imageUrl,
                'size' => $imageInfo['size'] ?? null,
                'width' => $imageInfo['width'] ?? null,
                'height' => $imageInfo['height'] ?? null,
                'format' => $imageInfo['format'] ?? null,
                'file_size' => $imageInfo['file_size'] ?? null
            ]);
            
            $enrichedImages[] = $enrichedImage;
        }
        
        return $enrichedImages;
    }
    
    /**
     * Ruft Informationen über ein Bild ab
     * 
     * @param string $imageUrl Bild-URL
     * @return array Bild-Informationen
     */
    private function getImageInfo($imageUrl)
    {
        $info = [];
        
        try {
            // HTTP HEAD Request für Dateigröße
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $imageUrl,
                CURLOPT_NOBODY => true,
                CURLOPT_HEADER => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_USERAGENT => $this->config->get('user_agent', 'Sitemap Generator Bot 1.0'),
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);
            
            $headers = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentLength = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
            curl_close($ch);
            
            if ($httpCode === 200) {
                if ($contentLength > 0) {
                    $info['file_size'] = $contentLength;
                }
                
                // Content-Type aus Headers extrahieren
                if (preg_match('/Content-Type:\s*image\/([^\s;]+)/i', $headers, $matches)) {
                    $info['format'] = strtolower($matches[1]);
                }
            }
            
            // Versuche Bildabmessungen zu ermitteln (nur für kleine Bilder)
            if (isset($info['file_size']) && $info['file_size'] < 1024 * 1024) { // < 1MB
                $imageData = @file_get_contents($imageUrl);
                if ($imageData) {
                    $imageSize = @getimagesizefromstring($imageData);
                    if ($imageSize) {
                        $info['width'] = $imageSize[0];
                        $info['height'] = $imageSize[1];
                        $info['size'] = $imageSize[0] . 'x' . $imageSize[1];
                    }
                }
            }
            
        } catch (Exception $e) {
            $this->log("Fehler beim Abrufen der Bild-Informationen für {$imageUrl}: " . $e->getMessage(), 'warning');
        }
        
        return $info;
    }
    
    /**
     * Generiert erweiterte Bilder-Sitemap mit detaillierten Informationen
     * 
     * @param array $urls URLs
     * @return array Ergebnis mit zusätzlichen Bild-Informationen
     */
    public function generateDetailed(array $urls)
    {
        $this->log("Starte detaillierte Bilder-Analyse...");
        
        $detailedUrls = [];
        foreach ($urls as $url) {
            if (!empty($url['images'])) {
                $enrichedImages = $this->enrichImageData($url['url'], $url['images']);
                $url['images'] = $enrichedImages;
                
                $this->log("Bilder-Analyse für {$url['url']}: " . count($enrichedImages) . " Bilder verarbeitet");
            }
            $detailedUrls[] = $url;
        }
        
        $result = $this->generate($detailedUrls);
        
        // Zusätzliche Statistik-Datei erstellen
        $statsFilename = str_replace('.xml', '-image-stats.txt', 
                                   $this->config->get('image_sitemap_filename', 'sitemap-images.xml'));
        $statsFilePath = $this->generateFilePath($statsFilename);
        
        $statsContent = $this->generateImageStatistics($detailedUrls);
        $this->writeFile($statsFilePath, $statsContent);
        
        $result['stats_file'] = $statsFilePath;
        $result['stats_url'] = $this->generateSitemapUrl($statsFilename);
        
        return $result;
    }
    
    /**
     * Generiert Bilder-Statistiken
     * 
     * @param array $urls URLs mit Bild-Details
     * @return string Statistik-Text
     */
    private function generateImageStatistics($urls)
    {
        $stats = [
            'total_urls' => count($urls),
            'urls_with_images' => 0,
            'total_images' => 0,
            'formats' => [],
            'sizes' => [],
            'avg_images_per_page' => 0
        ];
        
        foreach ($urls as $url) {
            if (!empty($url['images']) && count($url['images']) > 0) {
                $stats['urls_with_images']++;
                $stats['total_images'] += count($url['images']);
                
                foreach ($url['images'] as $image) {
                    // Format-Statistiken
                    if (!empty($image['format'])) {
                        $format = strtolower($image['format']);
                        $stats['formats'][$format] = ($stats['formats'][$format] ?? 0) + 1;
                    }
                    
                    // Größen-Statistiken
                    if (!empty($image['size'])) {
                        $stats['sizes'][$image['size']] = ($stats['sizes'][$image['size']] ?? 0) + 1;
                    }
                }
            }
        }
        
        if ($stats['urls_with_images'] > 0) {
            $stats['avg_images_per_page'] = round($stats['total_images'] / $stats['urls_with_images'], 1);
        }
        
        $content = str_repeat('=', 80) . PHP_EOL;
        $content .= 'BILDER-SITEMAP STATISTIKEN' . PHP_EOL;
        $content .= str_repeat('=', 80) . PHP_EOL;
        $content .= PHP_EOL;
        $content .= sprintf("Gesamt URLs: %d", $stats['total_urls']) . PHP_EOL;
        $content .= sprintf("URLs mit Bildern: %d", $stats['urls_with_images']) . PHP_EOL;
        $content .= sprintf("Gesamt Bilder: %d", $stats['total_images']) . PHP_EOL;
        $content .= sprintf("Durchschnittliche Bilder pro Seite: %.1f", $stats['avg_images_per_page']) . PHP_EOL;
        $content .= PHP_EOL;
        
        // Format-Verteilung
        if (!empty($stats['formats'])) {
            $content .= "Format-Verteilung:" . PHP_EOL;
            arsort($stats['formats']);
            foreach ($stats['formats'] as $format => $count) {
                $percentage = round($count / $stats['total_images'] * 100, 1);
                $content .= sprintf("  %s: %d (%.1f%%)", strtoupper($format), $count, $percentage) . PHP_EOL;
            }
            $content .= PHP_EOL;
        }
        
        // Top Bildgrößen
        if (!empty($stats['sizes'])) {
            $content .= "Top Bildgrößen:" . PHP_EOL;
            arsort($stats['sizes']);
            $topSizes = array_slice($stats['sizes'], 0, 10, true);
            foreach ($topSizes as $size => $count) {
                $content .= sprintf("  %s: %d Bilder", $size, $count) . PHP_EOL;
            }
            $content .= PHP_EOL;
        }
        
        return $content;
    }
    
    /**
     * Validiert URLs für Bilder-Sitemap
     * 
     * @param array $urls URLs
     * @return array Validierte URLs
     */
    public function validateUrls(array $urls)
    {
        $validUrls = [];
        
        foreach ($urls as $url) {
            // Nur URLs mit Bildern
            if (empty($url['images']) || count($url['images']) === 0) {
                continue;
            }
            
            // Nur erfolgreiche URLs
            if (!in_array($url['status_code'], [200])) {
                continue;
            }
            
            $validUrls[] = $url;
        }
        
        $this->log("Bilder-URLs validiert: " . count($validUrls) . " von " . count($urls) . " URLs haben Bilder");
        
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

