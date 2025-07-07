<?php

namespace SitemapGenerator\Generators;

require_once 'AbstractSitemapGenerator.php';

/**
 * Video-Sitemap-Generator für Video-SEO
 */
class VideoSitemapGenerator extends AbstractSitemapGenerator
{
    /**
     * Generiert Video-Sitemap
     * 
     * @param array $urls URLs mit Metadaten
     * @return array Ergebnis der Generierung
     */
    public function generate(array $urls)
    {
        $validUrls = $this->validateUrls($urls);
        $filename = $this->config->get('video_sitemap_filename', 'sitemap-videos.xml');
        $filePath = $this->generateFilePath($filename);
        
        $xml = $this->generateVideoXmlContent($validUrls);
        $this->writeFile($filePath, $xml);
        
        $result = [
            'file' => $filePath,
            'url' => $this->generateSitemapUrl($filename),
            'urls_count' => count($validUrls),
            'videos_count' => $this->countTotalVideos($validUrls),
            'type' => 'video'
        ];
        
        // Komprimierung falls aktiviert
        if ($this->config->get('xml_compress', true)) {
            $compressedPath = $this->compressFile($filePath);
            $result['compressed_file'] = $compressedPath;
            $result['compressed_url'] = $this->generateSitemapUrl($filename . '.gz');
        }
        
        $this->log("Video-Sitemap generiert: {$filename} ({$result['urls_count']} URLs, {$result['videos_count']} Videos)");
        
        return $result;
    }
    
    /**
     * Generiert XML-Inhalt für Video-Sitemap
     * 
     * @param array $urls URLs
     * @return string XML-Inhalt
     */
    private function generateVideoXmlContent($urls)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . PHP_EOL;
        $xml .= '        xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">' . PHP_EOL;
        
        foreach ($urls as $urlData) {
            if (!empty($urlData['videos']) && count($urlData['videos']) > 0) {
                $xml .= $this->generateVideoUrlXml($urlData);
            }
        }
        
        $xml .= '</urlset>' . PHP_EOL;
        
        return $xml;
    }
    
    /**
     * Generiert XML für eine URL mit Videos
     * 
     * @param array $urlData URL-Daten
     * @return string XML-Fragment
     */
    private function generateVideoUrlXml($urlData)
    {
        $xml = '  <url>' . PHP_EOL;
        $xml .= '    <loc>' . $this->escapeXml($urlData['url']) . '</loc>' . PHP_EOL;
        
        // Letzte Änderung
        if (!empty($urlData['lastmod'])) {
            $xml .= '    <lastmod>' . $this->formatDate($urlData['lastmod']) . '</lastmod>' . PHP_EOL;
        }
        
        // Videos hinzufügen
        foreach ($urlData['videos'] as $video) {
            $xml .= $this->generateVideoXml($video, $urlData['url']);
        }
        
        $xml .= '  </url>' . PHP_EOL;
        
        return $xml;
    }
    
    /**
     * Generiert XML für ein einzelnes Video
     * 
     * @param array $videoData Video-Daten
     * @param string $pageUrl URL der Seite
     * @return string XML-Fragment
     */
    private function generateVideoXml($videoData, $pageUrl)
    {
        if (empty($videoData['url'])) {
            return '';
        }
        
        // Video-URL zu absoluter URL auflösen
        $videoUrl = $this->resolveVideoUrl($videoData['url'], $pageUrl);
        
        if (!$videoUrl) {
            return '';
        }
        
        $xml = '    <video:video>' . PHP_EOL;
        
        // Thumbnail-URL (erforderlich)
        $thumbnailUrl = $this->getThumbnailUrl($videoData, $pageUrl);
        if ($thumbnailUrl) {
            $xml .= '      <video:thumbnail_loc>' . $this->escapeXml($thumbnailUrl) . '</video:thumbnail_loc>' . PHP_EOL;
        }
        
        // Titel (erforderlich)
        $title = $this->getVideoTitle($videoData, $pageUrl);
        if ($title) {
            $xml .= '      <video:title>' . $this->escapeXml($title) . '</video:title>' . PHP_EOL;
        }
        
        // Beschreibung (erforderlich)
        $description = $this->getVideoDescription($videoData, $pageUrl);
        if ($description) {
            $xml .= '      <video:description>' . $this->escapeXml($description) . '</video:description>' . PHP_EOL;
        }
        
        // Content-Location oder Player-Location
        if ($this->isDirectVideoUrl($videoUrl)) {
            $xml .= '      <video:content_loc>' . $this->escapeXml($videoUrl) . '</video:content_loc>' . PHP_EOL;
        } else {
            $xml .= '      <video:player_loc>' . $this->escapeXml($videoUrl) . '</video:player_loc>' . PHP_EOL;
        }
        
        // Optionale Felder
        
        // Dauer
        if (!empty($videoData['duration']) && is_numeric($videoData['duration'])) {
            $duration = max(0, min(28800, intval($videoData['duration']))); // Max 8 Stunden
            $xml .= '      <video:duration>' . $duration . '</video:duration>' . PHP_EOL;
        }
        
        // Veröffentlichungsdatum
        if (!empty($videoData['publication_date'])) {
            $xml .= '      <video:publication_date>' . $this->formatDate($videoData['publication_date']) . '</video:publication_date>' . PHP_EOL;
        }
        
        // Ablaufdatum
        if (!empty($videoData['expiration_date'])) {
            $xml .= '      <video:expiration_date>' . $this->formatDate($videoData['expiration_date']) . '</video:expiration_date>' . PHP_EOL;
        }
        
        // Bewertung
        if (!empty($videoData['rating']) && is_numeric($videoData['rating'])) {
            $rating = max(0.0, min(5.0, floatval($videoData['rating'])));
            $xml .= '      <video:rating>' . number_format($rating, 1) . '</video:rating>' . PHP_EOL;
        }
        
        // Anzahl Aufrufe
        if (!empty($videoData['view_count']) && is_numeric($videoData['view_count'])) {
            $xml .= '      <video:view_count>' . intval($videoData['view_count']) . '</video:view_count>' . PHP_EOL;
        }
        
        // Familienfreundlich
        if (isset($videoData['family_friendly'])) {
            $familyFriendly = $videoData['family_friendly'] ? 'yes' : 'no';
            $xml .= '      <video:family_friendly>' . $familyFriendly . '</video:family_friendly>' . PHP_EOL;
        }
        
        // Tags
        if (!empty($videoData['tags']) && is_array($videoData['tags'])) {
            $tags = array_slice($videoData['tags'], 0, 32); // Max 32 Tags
            foreach ($tags as $tag) {
                $xml .= '      <video:tag>' . $this->escapeXml($tag) . '</video:tag>' . PHP_EOL;
            }
        }
        
        // Kategorie
        if (!empty($videoData['category'])) {
            $xml .= '      <video:category>' . $this->escapeXml($videoData['category']) . '</video:category>' . PHP_EOL;
        }
        
        // Uploader
        if (!empty($videoData['uploader'])) {
            $uploaderInfo = '';
            if (!empty($videoData['uploader_info'])) {
                $uploaderInfo = ' info="' . $this->escapeXml($videoData['uploader_info']) . '"';
            }
            $xml .= '      <video:uploader' . $uploaderInfo . '>' . $this->escapeXml($videoData['uploader']) . '</video:uploader>' . PHP_EOL;
        }
        
        // Live-Stream
        if (!empty($videoData['live'])) {
            $xml .= '      <video:live>yes</video:live>' . PHP_EOL;
        }
        
        // Plattform-Einschränkungen
        if (!empty($videoData['platform'])) {
            $relationship = $videoData['platform_relationship'] ?? 'allow';
            $xml .= '      <video:platform relationship="' . $relationship . '">' . $this->escapeXml($videoData['platform']) . '</video:platform>' . PHP_EOL;
        }
        
        // Länder-Einschränkungen
        if (!empty($videoData['restriction'])) {
            $relationship = $videoData['restriction_relationship'] ?? 'allow';
            $xml .= '      <video:restriction relationship="' . $relationship . '">' . $this->escapeXml($videoData['restriction']) . '</video:restriction>' . PHP_EOL;
        }
        
        // Preis
        if (!empty($videoData['price'])) {
            $currency = $videoData['price_currency'] ?? 'EUR';
            $xml .= '      <video:price currency="' . $currency . '">' . $this->escapeXml($videoData['price']) . '</video:price>' . PHP_EOL;
        }
        
        $xml .= '    </video:video>' . PHP_EOL;
        
        return $xml;
    }
    
    /**
     * Löst Video-URL zu absoluter URL auf
     * 
     * @param string $videoUrl Video-URL
     * @param string $pageUrl Seiten-URL
     * @return string|null Absolute Video-URL
     */
    private function resolveVideoUrl($videoUrl, $pageUrl)
    {
        if (empty($videoUrl)) {
            return null;
        }
        
        // Bereits absolute URL
        if (parse_url($videoUrl, PHP_URL_SCHEME)) {
            return $videoUrl;
        }
        
        // Protocol-relative URL
        if (substr($videoUrl, 0, 2) === '//') {
            $scheme = parse_url($pageUrl, PHP_URL_SCHEME);
            return $scheme . ':' . $videoUrl;
        }
        
        // Absolute Pfad
        if (substr($videoUrl, 0, 1) === '/') {
            $scheme = parse_url($pageUrl, PHP_URL_SCHEME);
            $host = parse_url($pageUrl, PHP_URL_HOST);
            $port = parse_url($pageUrl, PHP_URL_PORT);
            
            $result = $scheme . '://' . $host;
            if ($port) {
                $result .= ':' . $port;
            }
            $result .= $videoUrl;
            
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
        $result .= $basePath . '/' . $videoUrl;
        
        return $result;
    }
    
    /**
     * Ermittelt Thumbnail-URL für ein Video
     * 
     * @param array $videoData Video-Daten
     * @param string $pageUrl Seiten-URL
     * @return string|null Thumbnail-URL
     */
    private function getThumbnailUrl($videoData, $pageUrl)
    {
        // Explizit angegebenes Thumbnail
        if (!empty($videoData['thumbnail'])) {
            return $this->resolveVideoUrl($videoData['thumbnail'], $pageUrl);
        }
        
        // Poster-Attribut von Video-Tag
        if (!empty($videoData['poster'])) {
            return $this->resolveVideoUrl($videoData['poster'], $pageUrl);
        }
        
        // YouTube-Video Thumbnail generieren
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $videoData['url'], $matches)) {
            return 'https://img.youtube.com/vi/' . $matches[1] . '/maxresdefault.jpg';
        }
        
        // Vimeo-Video Thumbnail (würde API-Call erfordern)
        if (preg_match('/vimeo\.com\/(\d+)/', $videoData['url'], $matches)) {
            // Vereinfachtes Thumbnail (echte Implementation würde Vimeo API nutzen)
            return 'https://vumbnail.com/' . $matches[1] . '.jpg';
        }
        
        return null;
    }
    
    /**
     * Ermittelt Titel für ein Video
     * 
     * @param array $videoData Video-Daten
     * @param string $pageUrl Seiten-URL
     * @return string|null Video-Titel
     */
    private function getVideoTitle($videoData, $pageUrl)
    {
        if (!empty($videoData['title'])) {
            return $videoData['title'];
        }
        
        // Fallback: Dateiname ohne Erweiterung
        $path = parse_url($videoData['url'], PHP_URL_PATH);
        $filename = basename($path);
        $title = pathinfo($filename, PATHINFO_FILENAME);
        
        if ($title) {
            return ucfirst(str_replace(['_', '-'], ' ', $title));
        }
        
        return 'Video';
    }
    
    /**
     * Ermittelt Beschreibung für ein Video
     * 
     * @param array $videoData Video-Daten
     * @param string $pageUrl Seiten-URL
     * @return string|null Video-Beschreibung
     */
    private function getVideoDescription($videoData, $pageUrl)
    {
        if (!empty($videoData['description'])) {
            return $videoData['description'];
        }
        
        // Fallback: Generische Beschreibung
        $title = $this->getVideoTitle($videoData, $pageUrl);
        return "Video: {$title}";
    }
    
    /**
     * Prüft ob es sich um eine direkte Video-URL handelt
     * 
     * @param string $url URL
     * @return bool
     */
    private function isDirectVideoUrl($url)
    {
        $videoExtensions = $this->config->get('video_extensions', ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm']);
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        
        return in_array($extension, $videoExtensions);
    }
    
    /**
     * Zählt die Gesamtanzahl der Videos
     * 
     * @param array $urls URLs
     * @return int Anzahl Videos
     */
    private function countTotalVideos($urls)
    {
        $count = 0;
        
        foreach ($urls as $url) {
            if (!empty($url['videos'])) {
                $count += count($url['videos']);
            }
        }
        
        return $count;
    }
    
    /**
     * Analysiert Videos einer URL und reichert sie mit Metadaten an
     * 
     * @param string $url URL
     * @param array $videos Gefundene Videos
     * @return array Angereicherte Videos
     */
    private function enrichVideoData($url, $videos)
    {
        $enrichedVideos = [];
        
        foreach ($videos as $video) {
            $videoUrl = $this->resolveVideoUrl($video['url'], $url);
            
            if (!$videoUrl) {
                continue;
            }
            
            // Video-Informationen abrufen
            $videoInfo = $this->getVideoInfo($videoUrl);
            
            $enrichedVideo = array_merge($video, [
                'url' => $videoUrl,
                'file_size' => $videoInfo['file_size'] ?? null,
                'content_type' => $videoInfo['content_type'] ?? null,
                'is_accessible' => $videoInfo['is_accessible'] ?? false
            ]);
            
            // Thumbnail automatisch ermitteln falls nicht vorhanden
            if (empty($enrichedVideo['thumbnail'])) {
                $thumbnail = $this->getThumbnailUrl($enrichedVideo, $url);
                if ($thumbnail) {
                    $enrichedVideo['thumbnail'] = $thumbnail;
                }
            }
            
            $enrichedVideos[] = $enrichedVideo;
        }
        
        return $enrichedVideos;
    }
    
    /**
     * Ruft Informationen über ein Video ab
     * 
     * @param string $videoUrl Video-URL
     * @return array Video-Informationen
     */
    private function getVideoInfo($videoUrl)
    {
        $info = [];
        
        try {
            // HTTP HEAD Request für Dateigröße und Content-Type
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $videoUrl,
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
            
            $info['is_accessible'] = ($httpCode === 200);
            
            if ($httpCode === 200) {
                if ($contentLength > 0) {
                    $info['file_size'] = $contentLength;
                }
                
                // Content-Type aus Headers extrahieren
                if (preg_match('/Content-Type:\s*([^\s;]+)/i', $headers, $matches)) {
                    $info['content_type'] = strtolower($matches[1]);
                }
            }
            
        } catch (Exception $e) {
            $this->log("Fehler beim Abrufen der Video-Informationen für {$videoUrl}: " . $e->getMessage(), 'warning');
        }
        
        return $info;
    }
    
    /**
     * Generiert erweiterte Video-Sitemap mit detaillierten Informationen
     * 
     * @param array $urls URLs
     * @return array Ergebnis mit zusätzlichen Video-Informationen
     */
    public function generateDetailed(array $urls)
    {
        $this->log("Starte detaillierte Video-Analyse...");
        
        $detailedUrls = [];
        foreach ($urls as $url) {
            if (!empty($url['videos'])) {
                $enrichedVideos = $this->enrichVideoData($url['url'], $url['videos']);
                $url['videos'] = $enrichedVideos;
                
                $this->log("Video-Analyse für {$url['url']}: " . count($enrichedVideos) . " Videos verarbeitet");
            }
            $detailedUrls[] = $url;
        }
        
        $result = $this->generate($detailedUrls);
        
        // Zusätzliche Statistik-Datei erstellen
        $statsFilename = str_replace('.xml', '-video-stats.txt', 
                                   $this->config->get('video_sitemap_filename', 'sitemap-videos.xml'));
        $statsFilePath = $this->generateFilePath($statsFilename);
        
        $statsContent = $this->generateVideoStatistics($detailedUrls);
        $this->writeFile($statsFilePath, $statsContent);
        
        $result['stats_file'] = $statsFilePath;
        $result['stats_url'] = $this->generateSitemapUrl($statsFilename);
        
        return $result;
    }
    
    /**
     * Generiert Video-Statistiken
     * 
     * @param array $urls URLs mit Video-Details
     * @return string Statistik-Text
     */
    private function generateVideoStatistics($urls)
    {
        $stats = [
            'total_urls' => count($urls),
            'urls_with_videos' => 0,
            'total_videos' => 0,
            'accessible_videos' => 0,
            'platforms' => [],
            'avg_videos_per_page' => 0
        ];
        
        foreach ($urls as $url) {
            if (!empty($url['videos']) && count($url['videos']) > 0) {
                $stats['urls_with_videos']++;
                $stats['total_videos'] += count($url['videos']);
                
                foreach ($url['videos'] as $video) {
                    if (!empty($video['is_accessible'])) {
                        $stats['accessible_videos']++;
                    }
                    
                    // Plattform-Erkennung
                    $platform = $this->detectVideoPlatform($video['url']);
                    if ($platform) {
                        $stats['platforms'][$platform] = ($stats['platforms'][$platform] ?? 0) + 1;
                    }
                }
            }
        }
        
        if ($stats['urls_with_videos'] > 0) {
            $stats['avg_videos_per_page'] = round($stats['total_videos'] / $stats['urls_with_videos'], 1);
        }
        
        $content = str_repeat('=', 80) . PHP_EOL;
        $content .= 'VIDEO-SITEMAP STATISTIKEN' . PHP_EOL;
        $content .= str_repeat('=', 80) . PHP_EOL;
        $content .= PHP_EOL;
        $content .= sprintf("Gesamt URLs: %d", $stats['total_urls']) . PHP_EOL;
        $content .= sprintf("URLs mit Videos: %d", $stats['urls_with_videos']) . PHP_EOL;
        $content .= sprintf("Gesamt Videos: %d", $stats['total_videos']) . PHP_EOL;
        $content .= sprintf("Zugängliche Videos: %d", $stats['accessible_videos']) . PHP_EOL;
        $content .= sprintf("Durchschnittliche Videos pro Seite: %.1f", $stats['avg_videos_per_page']) . PHP_EOL;
        $content .= PHP_EOL;
        
        // Plattform-Verteilung
        if (!empty($stats['platforms'])) {
            $content .= "Plattform-Verteilung:" . PHP_EOL;
            arsort($stats['platforms']);
            foreach ($stats['platforms'] as $platform => $count) {
                $percentage = round($count / $stats['total_videos'] * 100, 1);
                $content .= sprintf("  %s: %d (%.1f%%)", $platform, $count, $percentage) . PHP_EOL;
            }
            $content .= PHP_EOL;
        }
        
        return $content;
    }
    
    /**
     * Erkennt Video-Plattform anhand der URL
     * 
     * @param string $url Video-URL
     * @return string|null Plattform-Name
     */
    private function detectVideoPlatform($url)
    {
        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            return 'YouTube';
        }
        
        if (strpos($url, 'vimeo.com') !== false) {
            return 'Vimeo';
        }
        
        if (strpos($url, 'dailymotion.com') !== false) {
            return 'Dailymotion';
        }
        
        if (strpos($url, 'wistia.com') !== false) {
            return 'Wistia';
        }
        
        if ($this->isDirectVideoUrl($url)) {
            return 'Direkt';
        }
        
        return 'Unbekannt';
    }
    
    /**
     * Validiert URLs für Video-Sitemap
     * 
     * @param array $urls URLs
     * @return array Validierte URLs
     */
    public function validateUrls(array $urls)
    {
        $validUrls = [];
        
        foreach ($urls as $url) {
            // Nur URLs mit Videos
            if (empty($url['videos']) || count($url['videos']) === 0) {
                continue;
            }
            
            // Nur erfolgreiche URLs
            if (!in_array($url['status_code'], [200])) {
                continue;
            }
            
            $validUrls[] = $url;
        }
        
        $this->log("Video-URLs validiert: " . count($validUrls) . " von " . count($urls) . " URLs haben Videos");
        
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

