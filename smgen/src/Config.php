<?php

namespace SitemapGenerator;

/**
 * Konfigurationsklasse für den Sitemap-Generator
 */
class Config
{
    private $config = [];
    private $defaultConfig = [
        // Basis-Konfiguration
        'base_url' => '',
        'output_dir' => 'output',
        'max_urls' => 50000,
        'max_depth' => 10,
        'delay_between_requests' => 1, // Sekunden
        
        // Sitemap-Typen
        'enabled_sitemap_types' => ['xml', 'html', 'text'],
        
        // XML-Sitemap Einstellungen
        'xml_sitemap_filename' => 'sitemap.xml',
        'xml_sitemap_index_filename' => 'sitemap-index.xml',
        'xml_max_urls_per_file' => 50000,
        'xml_compress' => true,
        
        // HTML-Sitemap Einstellungen
        'html_sitemap_filename' => 'sitemap.html',
        'html_template' => null,
        'html_group_by_directory' => true,
        
        // Text-Sitemap Einstellungen
        'text_sitemap_filename' => 'sitemap.txt',
        
        // Mobile-Sitemap Einstellungen
        'mobile_sitemap_filename' => 'sitemap-mobile.xml',
        'mobile_user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)',
        
        // Bilder-Sitemap Einstellungen
        'image_sitemap_filename' => 'sitemap-images.xml',
        'image_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
        'image_max_per_page' => 1000,
        
        // Video-Sitemap Einstellungen
        'video_sitemap_filename' => 'sitemap-videos.xml',
        'video_extensions' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'],
        
        // URL-Crawler Einstellungen
        'crawl_external_links' => false,
        'follow_redirects' => true,
        'ignore_query_parameters' => false,
        'exclude_patterns' => [
            '/admin/',
            '/wp-admin/',
            '/login',
            '/logout',
            '*.pdf',
            '*.doc',
            '*.docx'
        ],
        'include_patterns' => [],
        
        // Suchmaschinen-Ping
        'ping_search_engines' => true,
        'ping_google' => true,
        'ping_bing' => true,
        'ping_yandex' => false,
        'ping_baidu' => false,
        
        // Logging
        'enable_logging' => true,
        'log_file' => 'logs/sitemap-generator.log',
        'log_level' => 'info', // debug, info, warning, error
        
        // Caching
        'enable_caching' => true,
        'cache_duration' => 3600, // Sekunden
        'cache_file' => 'cache/urls.cache',
        
        // HTTP-Einstellungen
        'user_agent' => 'Sitemap Generator Bot 1.0',
        'timeout' => 30,
        'follow_robots_txt' => true,
        
        // Prioritäten und Änderungsfrequenzen
        'default_priority' => 0.5,
        'default_changefreq' => 'weekly',
        'priority_rules' => [
            '/' => 1.0,
            '/about' => 0.8,
            '/contact' => 0.7,
            '/blog' => 0.9,
            '/products' => 0.8
        ],
        'changefreq_rules' => [
            '/' => 'daily',
            '/blog' => 'daily',
            '/news' => 'hourly',
            '/products' => 'weekly'
        ]
    ];
    
    public function __construct($configFile = null)
    {
        $this->config = $this->defaultConfig;
        
        if ($configFile && file_exists($configFile)) {
            $this->loadFromFile($configFile);
        }
    }
    
    /**
     * Lädt Konfiguration aus einer Datei
     * 
     * @param string $configFile Pfad zur Konfigurationsdatei
     */
    public function loadFromFile($configFile)
    {
        $extension = pathinfo($configFile, PATHINFO_EXTENSION);
        
        switch (strtolower($extension)) {
            case 'json':
                $this->loadFromJson($configFile);
                break;
            case 'php':
                $this->loadFromPhp($configFile);
                break;
            case 'ini':
                $this->loadFromIni($configFile);
                break;
            default:
                throw new \Exception("Nicht unterstütztes Konfigurationsformat: {$extension}");
        }
    }
    
    /**
     * Lädt Konfiguration aus JSON-Datei
     */
    private function loadFromJson($file)
    {
        $json = file_get_contents($file);
        $config = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Ungültige JSON-Konfigurationsdatei: ' . json_last_error_msg());
        }
        
        $this->config = array_merge($this->config, $config);
    }
    
    /**
     * Lädt Konfiguration aus PHP-Datei
     */
    private function loadFromPhp($file)
    {
        $config = include $file;
        
        if (!is_array($config)) {
            throw new \Exception('PHP-Konfigurationsdatei muss ein Array zurückgeben');
        }
        
        $this->config = array_merge($this->config, $config);
    }
    
    /**
     * Lädt Konfiguration aus INI-Datei
     */
    private function loadFromIni($file)
    {
        $config = parse_ini_file($file, true);
        
        if ($config === false) {
            throw new \Exception('Fehler beim Lesen der INI-Konfigurationsdatei');
        }
        
        // Flache Struktur für INI-Dateien
        $flatConfig = [];
        foreach ($config as $section => $values) {
            if (is_array($values)) {
                foreach ($values as $key => $value) {
                    $flatConfig[$key] = $value;
                }
            } else {
                $flatConfig[$section] = $values;
            }
        }
        
        $this->config = array_merge($this->config, $flatConfig);
    }
    
    /**
     * Gibt einen Konfigurationswert zurück
     * 
     * @param string $key Schlüssel
     * @param mixed $default Standardwert
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return isset($this->config[$key]) ? $this->config[$key] : $default;
    }
    
    /**
     * Setzt einen Konfigurationswert
     * 
     * @param string $key Schlüssel
     * @param mixed $value Wert
     */
    public function set($key, $value)
    {
        $this->config[$key] = $value;
    }
    
    /**
     * Gibt alle Konfigurationswerte zurück
     * 
     * @return array
     */
    public function getAll()
    {
        return $this->config;
    }
    
    /**
     * Gibt aktivierte Sitemap-Typen zurück
     * 
     * @return array
     */
    public function getEnabledSitemapTypes()
    {
        return $this->get('enabled_sitemap_types', ['xml']);
    }
    
    /**
     * Prüft ob ein Sitemap-Typ aktiviert ist
     * 
     * @param string $type Sitemap-Typ
     * @return bool
     */
    public function isSitemapTypeEnabled($type)
    {
        return in_array($type, $this->getEnabledSitemapTypes());
    }
    
    /**
     * Gibt die Priorität für eine URL zurück
     * 
     * @param string $url URL
     * @return float
     */
    public function getPriorityForUrl($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $rules = $this->get('priority_rules', []);
        
        // Exakte Übereinstimmung
        if (isset($rules[$path])) {
            return $rules[$path];
        }
        
        // Pattern-Matching
        foreach ($rules as $pattern => $priority) {
            if (fnmatch($pattern, $path)) {
                return $priority;
            }
        }
        
        return $this->get('default_priority', 0.5);
    }
    
    /**
     * Gibt die Änderungsfrequenz für eine URL zurück
     * 
     * @param string $url URL
     * @return string
     */
    public function getChangefreqForUrl($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $rules = $this->get('changefreq_rules', []);
        
        // Exakte Übereinstimmung
        if (isset($rules[$path])) {
            return $rules[$path];
        }
        
        // Pattern-Matching
        foreach ($rules as $pattern => $changefreq) {
            if (fnmatch($pattern, $path)) {
                return $changefreq;
            }
        }
        
        return $this->get('default_changefreq', 'weekly');
    }
    
    /**
     * Validiert die Konfiguration
     * 
     * @return array Fehler-Array
     */
    public function validate()
    {
        $errors = [];
        
        // Basis-URL prüfen
        if (!$this->get('base_url')) {
            $errors[] = 'base_url ist erforderlich';
        } elseif (!filter_var($this->get('base_url'), FILTER_VALIDATE_URL)) {
            $errors[] = 'base_url ist keine gültige URL';
        }
        
        // Ausgabe-Verzeichnis prüfen
        $outputDir = $this->get('output_dir');
        if (!$outputDir) {
            $errors[] = 'output_dir ist erforderlich';
        }
        
        // Numerische Werte prüfen
        $numericFields = ['max_urls', 'max_depth', 'delay_between_requests', 'xml_max_urls_per_file'];
        foreach ($numericFields as $field) {
            $value = $this->get($field);
            if ($value !== null && (!is_numeric($value) || $value < 0)) {
                $errors[] = "{$field} muss eine positive Zahl sein";
            }
        }
        
        return $errors;
    }
    
    /**
     * Speichert die aktuelle Konfiguration in eine Datei
     * 
     * @param string $file Dateipfad
     * @param string $format Format (json, php, ini)
     */
    public function saveToFile($file, $format = 'json')
    {
        switch (strtolower($format)) {
            case 'json':
                file_put_contents($file, json_encode($this->config, JSON_PRETTY_PRINT));
                break;
            case 'php':
                $content = "<?php\n\nreturn " . var_export($this->config, true) . ";\n";
                file_put_contents($file, $content);
                break;
            default:
                throw new \Exception("Nicht unterstütztes Speicherformat: {$format}");
        }
    }
}

