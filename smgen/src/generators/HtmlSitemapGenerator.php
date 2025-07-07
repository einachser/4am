<?php

namespace SitemapGenerator\Generators;

require_once 'AbstractSitemapGenerator.php';

/**
 * HTML-Sitemap-Generator für benutzerfreundliche Sitemaps
 */
class HtmlSitemapGenerator extends AbstractSitemapGenerator
{
    /**
     * Generiert HTML-Sitemap
     * 
     * @param array $urls URLs mit Metadaten
     * @return array Ergebnis der Generierung
     */
    public function generate(array $urls)
    {
        $validUrls = $this->validateUrls($urls);
        $filename = $this->config->get('html_sitemap_filename', 'sitemap.html');
        $filePath = $this->generateFilePath($filename);
        
        $html = $this->generateHtmlContent($validUrls);
        $this->writeFile($filePath, $html);
        
        $result = [
            'file' => $filePath,
            'url' => $this->generateSitemapUrl($filename),
            'urls_count' => count($validUrls),
            'type' => 'html'
        ];
        
        $this->log("HTML-Sitemap generiert: {$filename} ({$result['urls_count']} URLs)");
        
        return $result;
    }
    
    /**
     * Generiert HTML-Inhalt für die Sitemap
     * 
     * @param array $urls URLs
     * @return string HTML-Inhalt
     */
    private function generateHtmlContent($urls)
    {
        $template = $this->config->get('html_template');
        
        if ($template && file_exists($template)) {
            return $this->generateFromTemplate($urls, $template);
        } else {
            return $this->generateDefaultHtml($urls);
        }
    }
    
    /**
     * Generiert HTML aus einem Template
     * 
     * @param array $urls URLs
     * @param string $templateFile Template-Datei
     * @return string HTML-Inhalt
     */
    private function generateFromTemplate($urls, $templateFile)
    {
        $templateContent = file_get_contents($templateFile);
        
        // Template-Variablen ersetzen
        $replacements = [
            '{{TITLE}}' => 'Sitemap - ' . $this->config->get('base_url'),
            '{{BASE_URL}}' => $this->config->get('base_url'),
            '{{GENERATION_DATE}}' => date('Y-m-d H:i:s'),
            '{{URLS_COUNT}}' => count($urls),
            '{{SITEMAP_CONTENT}}' => $this->generateSitemapContent($urls)
        ];
        
        $html = str_replace(array_keys($replacements), array_values($replacements), $templateContent);
        
        return $html;
    }
    
    /**
     * Generiert Standard-HTML
     * 
     * @param array $urls URLs
     * @return string HTML-Inhalt
     */
    private function generateDefaultHtml($urls)
    {
        $baseUrl = $this->config->get('base_url');
        $title = 'Sitemap - ' . $baseUrl;
        
        $html = '<!DOCTYPE html>' . PHP_EOL;
        $html .= '<html lang="de">' . PHP_EOL;
        $html .= '<head>' . PHP_EOL;
        $html .= '    <meta charset="UTF-8">' . PHP_EOL;
        $html .= '    <meta name="viewport" content="width=device-width, initial-scale=1.0">' . PHP_EOL;
        $html .= '    <title>' . htmlspecialchars($title) . '</title>' . PHP_EOL;
        $html .= '    <style>' . PHP_EOL;
        $html .= $this->getDefaultCss() . PHP_EOL;
        $html .= '    </style>' . PHP_EOL;
        $html .= '</head>' . PHP_EOL;
        $html .= '<body>' . PHP_EOL;
        $html .= '    <div class="container">' . PHP_EOL;
        $html .= '        <header>' . PHP_EOL;
        $html .= '            <h1>Sitemap</h1>' . PHP_EOL;
        $html .= '            <p class="subtitle">Website: <a href="' . htmlspecialchars($baseUrl) . '">' . htmlspecialchars($baseUrl) . '</a></p>' . PHP_EOL;
        $html .= '            <p class="meta">Generiert am: ' . date('d.m.Y H:i:s') . ' | Anzahl URLs: ' . count($urls) . '</p>' . PHP_EOL;
        $html .= '        </header>' . PHP_EOL;
        $html .= '        <main>' . PHP_EOL;
        $html .= $this->generateSitemapContent($urls);
        $html .= '        </main>' . PHP_EOL;
        $html .= '        <footer>' . PHP_EOL;
        $html .= '            <p>Erstellt mit PHP Sitemap Generator</p>' . PHP_EOL;
        $html .= '        </footer>' . PHP_EOL;
        $html .= '    </div>' . PHP_EOL;
        $html .= '</body>' . PHP_EOL;
        $html .= '</html>' . PHP_EOL;
        
        return $html;
    }
    
    /**
     * Generiert den Sitemap-Inhalt
     * 
     * @param array $urls URLs
     * @return string HTML-Inhalt
     */
    private function generateSitemapContent($urls)
    {
        $groupByDirectory = $this->config->get('html_group_by_directory', true);
        
        if ($groupByDirectory) {
            return $this->generateGroupedContent($urls);
        } else {
            return $this->generateSimpleList($urls);
        }
    }
    
    /**
     * Generiert gruppierten Inhalt nach Verzeichnissen
     * 
     * @param array $urls URLs
     * @return string HTML-Inhalt
     */
    private function generateGroupedContent($urls)
    {
        $grouped = $this->groupUrlsByDirectory($urls);
        $html = '';
        
        foreach ($grouped as $directory => $directoryUrls) {
            $html .= '            <section class="directory-section">' . PHP_EOL;
            $html .= '                <h2 class="directory-title">' . PHP_EOL;
            $html .= '                    <span class="directory-icon">📁</span>' . PHP_EOL;
            $html .= '                    ' . htmlspecialchars($directory === '/' ? 'Startseite' : $directory) . PHP_EOL;
            $html .= '                    <span class="url-count">(' . count($directoryUrls) . ' URLs)</span>' . PHP_EOL;
            $html .= '                </h2>' . PHP_EOL;
            $html .= '                <ul class="url-list">' . PHP_EOL;
            
            foreach ($directoryUrls as $url) {
                $html .= $this->generateUrlListItem($url);
            }
            
            $html .= '                </ul>' . PHP_EOL;
            $html .= '            </section>' . PHP_EOL;
        }
        
        return $html;
    }
    
    /**
     * Generiert einfache Liste
     * 
     * @param array $urls URLs
     * @return string HTML-Inhalt
     */
    private function generateSimpleList($urls)
    {
        $html = '            <section class="url-section">' . PHP_EOL;
        $html .= '                <h2>Alle URLs</h2>' . PHP_EOL;
        $html .= '                <ul class="url-list">' . PHP_EOL;
        
        foreach ($urls as $url) {
            $html .= $this->generateUrlListItem($url);
        }
        
        $html .= '                </ul>' . PHP_EOL;
        $html .= '            </section>' . PHP_EOL;
        
        return $html;
    }
    
    /**
     * Generiert ein URL-Listen-Element
     * 
     * @param array $url URL-Daten
     * @return string HTML-Fragment
     */
    private function generateUrlListItem($url)
    {
        $html = '                    <li class="url-item">' . PHP_EOL;
        $html .= '                        <div class="url-main">' . PHP_EOL;
        $html .= '                            <a href="' . htmlspecialchars($url['url']) . '" class="url-link">' . PHP_EOL;
        $html .= '                                ' . htmlspecialchars($this->getUrlTitle($url)) . PHP_EOL;
        $html .= '                            </a>' . PHP_EOL;
        
        // Badges für besondere Eigenschaften
        $badges = $this->generateUrlBadges($url);
        if ($badges) {
            $html .= '                            <div class="url-badges">' . $badges . '</div>' . PHP_EOL;
        }
        
        $html .= '                        </div>' . PHP_EOL;
        
        // URL-Metadaten
        $metadata = $this->generateUrlMetadata($url);
        if ($metadata) {
            $html .= '                        <div class="url-metadata">' . $metadata . '</div>' . PHP_EOL;
        }
        
        $html .= '                    </li>' . PHP_EOL;
        
        return $html;
    }
    
    /**
     * Generiert URL-Badges
     * 
     * @param array $url URL-Daten
     * @return string HTML-Fragment
     */
    private function generateUrlBadges($url)
    {
        $badges = [];
        
        // Mobile-Badge
        if (!empty($url['mobile'])) {
            $badges[] = '<span class="badge badge-mobile">📱 Mobile</span>';
        }
        
        // Bilder-Badge
        if (!empty($url['images']) && count($url['images']) > 0) {
            $count = count($url['images']);
            $badges[] = '<span class="badge badge-images">🖼️ ' . $count . ' Bild' . ($count > 1 ? 'er' : '') . '</span>';
        }
        
        // Video-Badge
        if (!empty($url['videos']) && count($url['videos']) > 0) {
            $count = count($url['videos']);
            $badges[] = '<span class="badge badge-videos">🎥 ' . $count . ' Video' . ($count > 1 ? 's' : '') . '</span>';
        }
        
        // Prioritäts-Badge
        if (isset($url['priority']) && $url['priority'] >= 0.8) {
            $badges[] = '<span class="badge badge-priority">⭐ Hoch</span>';
        }
        
        return implode(' ', $badges);
    }
    
    /**
     * Generiert URL-Metadaten
     * 
     * @param array $url URL-Daten
     * @return string HTML-Fragment
     */
    private function generateUrlMetadata($url)
    {
        $metadata = [];
        
        // Letzte Änderung
        if (!empty($url['lastmod'])) {
            $date = date('d.m.Y', strtotime($url['lastmod']));
            $metadata[] = '<span class="meta-item">📅 Geändert: ' . $date . '</span>';
        }
        
        // Änderungsfrequenz
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
            $metadata[] = '<span class="meta-item">🔄 ' . $freqLabel . '</span>';
        }
        
        // Priorität
        if (isset($url['priority'])) {
            $priority = number_format($url['priority'], 1);
            $metadata[] = '<span class="meta-item">📊 Priorität: ' . $priority . '</span>';
        }
        
        return implode(' | ', $metadata);
    }
    
    /**
     * Gibt den Titel für eine URL zurück
     * 
     * @param array $url URL-Daten
     * @return string Titel
     */
    private function getUrlTitle($url)
    {
        $path = parse_url($url['url'], PHP_URL_PATH);
        
        if ($path === '/' || empty($path)) {
            return 'Startseite';
        }
        
        // Dateiname extrahieren
        $filename = basename($path);
        
        if (empty($filename)) {
            return $path;
        }
        
        // Dateiendung entfernen für bessere Lesbarkeit
        $title = pathinfo($filename, PATHINFO_FILENAME);
        
        // Unterstriche und Bindestriche durch Leerzeichen ersetzen
        $title = str_replace(['_', '-'], ' ', $title);
        
        // Ersten Buchstaben groß schreiben
        $title = ucfirst($title);
        
        return $title ?: $path;
    }
    
    /**
     * Gibt Standard-CSS zurück
     * 
     * @return string CSS-Code
     */
    private function getDefaultCss()
    {
        return '
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }
        
        h1 {
            color: #2c3e50;
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .subtitle {
            font-size: 1.2em;
            color: #7f8c8d;
            margin-bottom: 10px;
        }
        
        .subtitle a {
            color: #3498db;
            text-decoration: none;
        }
        
        .subtitle a:hover {
            text-decoration: underline;
        }
        
        .meta {
            color: #95a5a6;
            font-size: 0.9em;
        }
        
        .directory-section {
            background: white;
            margin-bottom: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .directory-title {
            background: #34495e;
            color: white;
            padding: 20px;
            margin: 0;
            font-size: 1.3em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .directory-icon {
            font-size: 1.2em;
        }
        
        .url-count {
            margin-left: auto;
            font-size: 0.8em;
            opacity: 0.8;
        }
        
        .url-list {
            list-style: none;
            padding: 0;
        }
        
        .url-item {
            border-bottom: 1px solid #ecf0f1;
            padding: 15px 20px;
        }
        
        .url-item:last-child {
            border-bottom: none;
        }
        
        .url-main {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 8px;
        }
        
        .url-link {
            color: #2980b9;
            text-decoration: none;
            font-weight: 500;
            flex: 1;
        }
        
        .url-link:hover {
            color: #3498db;
            text-decoration: underline;
        }
        
        .url-badges {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75em;
            font-weight: 500;
        }
        
        .badge-mobile {
            background: #e74c3c;
            color: white;
        }
        
        .badge-images {
            background: #f39c12;
            color: white;
        }
        
        .badge-videos {
            background: #9b59b6;
            color: white;
        }
        
        .badge-priority {
            background: #27ae60;
            color: white;
        }
        
        .url-metadata {
            font-size: 0.85em;
            color: #7f8c8d;
        }
        
        .meta-item {
            margin-right: 15px;
        }
        
        footer {
            text-align: center;
            padding: 30px;
            color: #95a5a6;
            font-size: 0.9em;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            header {
                padding: 20px;
            }
            
            h1 {
                font-size: 2em;
            }
            
            .url-main {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .url-badges {
                align-self: stretch;
            }
        }';
    }
    
    /**
     * Validiert URLs für HTML-Sitemap
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
        return 'text/html';
    }
    
    /**
     * Gibt Dateiendung zurück
     * 
     * @return string
     */
    public function getFileExtension()
    {
        return 'html';
    }
}

