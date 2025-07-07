# Verwendungsbeispiele

Diese Sammlung zeigt praktische Anwendungsfälle des PHP Sitemap Generators für verschiedene Website-Typen und Szenarien.

## Inhaltsverzeichnis

1. [E-Commerce Website](#e-commerce-website)
2. [Blog/News-Website](#blognews-website)
3. [Portfolio-Website](#portfolio-website)
4. [Mehrsprachige Website](#mehrsprachige-website)
5. [WordPress Integration](#wordpress-integration)
6. [Große Unternehmenswebsite](#große-unternehmenswebsite)
7. [Entwicklungsumgebung](#entwicklungsumgebung)
8. [Monitoring und Alerts](#monitoring-und-alerts)

## E-Commerce Website

### Konfiguration für Online-Shop

```json
{
  "base_url": "https://mein-shop.de",
  "output_dir": "/var/www/html/sitemaps",
  "max_urls": 100000,
  "max_depth": 8,
  "delay_between_requests": 0.5,
  
  "enabled_sitemap_types": [
    "xml",
    "html",
    "image",
    "mobile"
  ],
  
  "exclude_patterns": [
    "/admin/*",
    "/checkout/*",
    "/cart/*",
    "/account/*",
    "/api/*",
    "*.pdf",
    "/search*"
  ],
  
  "include_patterns": [
    "/products/*",
    "/categories/*",
    "/brands/*"
  ],
  
  "priority_rules": {
    "/": 1.0,
    "/products/*": 0.9,
    "/categories/*": 0.8,
    "/brands/*": 0.7,
    "/sale/*": 0.9,
    "/new-arrivals/*": 0.8,
    "/about": 0.5,
    "/contact": 0.5,
    "/shipping": 0.4,
    "/returns": 0.4
  },
  
  "changefreq_rules": {
    "/": "daily",
    "/products/*": "weekly",
    "/categories/*": "weekly",
    "/sale/*": "daily",
    "/new-arrivals/*": "daily",
    "/blog/*": "weekly"
  },
  
  "image_extensions": ["jpg", "jpeg", "png", "webp"],
  "image_max_per_page": 50,
  
  "ping_search_engines": true,
  "ping_google": true,
  "ping_bing": true,
  
  "enable_caching": true,
  "cache_duration": 1800
}
```

### Cron-Job für E-Commerce

```bash
# Häufige Updates für Produktseiten (alle 2 Stunden)
0 */2 * * * /var/www/shop/sitemap-cli.php --config=/var/www/shop/config/shop-config.json --generate=xml --cron --quiet

# Tägliche vollständige Generierung (nachts)
0 2 * * * /var/www/shop/sitemap-cli.php --config=/var/www/shop/config/shop-config.json --generate=all --cron --quiet

# Wöchentliche detaillierte Bilder-Analyse (Sonntag)
0 3 * * 0 /var/www/shop/sitemap-cli.php --config=/var/www/shop/config/shop-config.json --generate=image --detailed --cron
```

### PHP-Integration für Produktupdates

```php
<?php
// Nach Produktupdate Sitemap aktualisieren
class ProductManager {
    private $sitemapGenerator;
    
    public function __construct() {
        $this->sitemapGenerator = new SitemapGenerator('config/shop-config.json');
    }
    
    public function updateProduct($productId, $data) {
        // Produkt aktualisieren
        $this->saveProduct($productId, $data);
        
        // Sitemap aktualisieren (nur XML für Performance)
        $this->updateSitemap();
    }
    
    private function updateSitemap() {
        try {
            $result = $this->sitemapGenerator->generate('xml');
            
            // Suchmaschinen benachrichtigen
            $ping = new SearchEnginePing($this->sitemapGenerator->getConfig());
            $ping->pingGoogleOnly($result['url']);
            
        } catch (Exception $e) {
            error_log("Sitemap-Update fehlgeschlagen: " . $e->getMessage());
        }
    }
}
?>
```

## Blog/News-Website

### Konfiguration für News-Site

```json
{
  "base_url": "https://news-blog.de",
  "output_dir": "public/sitemaps",
  "max_urls": 25000,
  "max_depth": 6,
  "delay_between_requests": 1,
  
  "enabled_sitemap_types": [
    "xml",
    "html",
    "text",
    "image",
    "video"
  ],
  
  "exclude_patterns": [
    "/admin/*",
    "/wp-admin/*",
    "/author/*",
    "/tag/*",
    "/search*",
    "/feed*",
    "*.xml",
    "*.rss"
  ],
  
  "priority_rules": {
    "/": 1.0,
    "/latest": 0.9,
    "/breaking-news": 1.0,
    "/politics/*": 0.8,
    "/sports/*": 0.7,
    "/technology/*": 0.8,
    "/entertainment/*": 0.6,
    "/archive/*": 0.3,
    "/about": 0.4,
    "/contact": 0.4
  },
  
  "changefreq_rules": {
    "/": "hourly",
    "/latest": "hourly",
    "/breaking-news": "always",
    "/politics/*": "daily",
    "/sports/*": "daily",
    "/technology/*": "daily",
    "/archive/*": "never"
  },
  
  "video_extensions": ["mp4", "webm", "mov"],
  
  "ping_search_engines": true,
  "ping_google": true,
  "ping_bing": true,
  "ping_yandex": true,
  
  "enable_caching": true,
  "cache_duration": 900
}
```

### Automatische Updates bei neuen Artikeln

```php
<?php
// WordPress Hook für automatische Sitemap-Updates
add_action('publish_post', 'update_sitemap_on_publish');
add_action('post_updated', 'update_sitemap_on_update');

function update_sitemap_on_publish($post_id) {
    // Nur bei öffentlichen Posts
    if (get_post_status($post_id) === 'publish') {
        update_news_sitemap();
    }
}

function update_sitemap_on_update($post_id) {
    if (get_post_status($post_id) === 'publish') {
        update_news_sitemap();
    }
}

function update_news_sitemap() {
    try {
        $generator = new SitemapGenerator(ABSPATH . 'config/news-config.json');
        
        // Nur XML-Sitemap für schnelle Updates
        $result = $generator->generate('xml');
        
        // Google News Ping
        $ping = new SearchEnginePing($generator->getConfig());
        $ping->pingGoogleOnly($result['url']);
        
        // Log für Monitoring
        error_log("Sitemap automatisch aktualisiert: " . $result['url']);
        
    } catch (Exception $e) {
        error_log("Automatisches Sitemap-Update fehlgeschlagen: " . $e->getMessage());
    }
}
?>
```

### Cron-Jobs für News-Website

```bash
# Sehr häufige Updates für Breaking News (alle 15 Minuten)
*/15 * * * * /var/www/news/sitemap-cli.php --config=/var/www/news/config/news-config.json --generate=xml --cron --quiet

# Stündliche HTML-Sitemap Updates
0 * * * * /var/www/news/sitemap-cli.php --config=/var/www/news/config/news-config.json --generate=html --cron --quiet

# Tägliche vollständige Generierung mit Medien
0 1 * * * /var/www/news/sitemap-cli.php --config=/var/www/news/config/news-config.json --generate=all --detailed --cron
```

## Portfolio-Website

### Konfiguration für Kreative

```json
{
  "base_url": "https://portfolio.designer.com",
  "output_dir": "dist/sitemaps",
  "max_urls": 1000,
  "max_depth": 4,
  "delay_between_requests": 2,
  
  "enabled_sitemap_types": [
    "xml",
    "html",
    "image",
    "video"
  ],
  
  "priority_rules": {
    "/": 1.0,
    "/portfolio": 0.9,
    "/portfolio/*": 0.8,
    "/about": 0.7,
    "/contact": 0.6,
    "/blog": 0.5,
    "/blog/*": 0.4
  },
  
  "changefreq_rules": {
    "/": "weekly",
    "/portfolio": "monthly",
    "/portfolio/*": "monthly",
    "/about": "yearly",
    "/contact": "yearly",
    "/blog": "weekly",
    "/blog/*": "never"
  },
  
  "image_extensions": ["jpg", "jpeg", "png", "webp", "svg"],
  "image_max_per_page": 100,
  
  "video_extensions": ["mp4", "webm", "mov"],
  
  "ping_search_engines": true,
  "enable_caching": true,
  "cache_duration": 7200
}
```

### Manuelle Aktualisierung nach Portfolio-Updates

```php
<?php
// Script für manuelle Aktualisierung nach Portfolio-Änderungen
class PortfolioSitemapManager {
    private $generator;
    
    public function __construct() {
        $this->generator = new SitemapGenerator('config/portfolio-config.json');
    }
    
    public function updateAfterPortfolioChange() {
        echo "Aktualisiere Portfolio-Sitemaps...\n";
        
        try {
            // Vollständige Generierung mit detaillierter Medien-Analyse
            $results = $this->generator->generateAll();
            
            // Detaillierte Bilder-Analyse für Portfolio-Bilder
            $imageGenerator = new ImageSitemapGenerator($this->generator->getConfig());
            $urls = (new UrlCrawler($this->generator->getConfig()))->crawl();
            $imageResult = $imageGenerator->generateDetailed($urls);
            
            echo "✓ Sitemaps aktualisiert:\n";
            foreach ($results as $type => $result) {
                echo "  - $type: {$result['urls_count']} URLs\n";
            }
            
            echo "✓ Bilder-Analyse: {$imageResult['images_count']} Bilder\n";
            
            // Suchmaschinen benachrichtigen
            $ping = new SearchEnginePing($this->generator->getConfig());
            $pingResults = $ping->pingMultiple([
                ['url' => $results['xml']['url'], 'type' => 'xml'],
                ['url' => $imageResult['url'], 'type' => 'image']
            ]);
            
            echo "✓ Suchmaschinen benachrichtigt\n";
            
        } catch (Exception $e) {
            echo "✗ Fehler: " . $e->getMessage() . "\n";
        }
    }
}

// Verwendung
$manager = new PortfolioSitemapManager();
$manager->updateAfterPortfolioChange();
?>
```

## Mehrsprachige Website

### Konfiguration für Deutsche Version

```json
{
  "base_url": "https://example.com/de",
  "output_dir": "public/sitemaps/de",
  "max_urls": 10000,
  
  "enabled_sitemap_types": ["xml", "html", "mobile"],
  
  "include_patterns": [
    "/de/*"
  ],
  
  "exclude_patterns": [
    "/en/*",
    "/fr/*",
    "/es/*"
  ],
  
  "priority_rules": {
    "/de": 1.0,
    "/de/produkte/*": 0.9,
    "/de/blog/*": 0.7,
    "/de/ueber-uns": 0.6,
    "/de/kontakt": 0.5
  },
  
  "xml_sitemap_filename": "sitemap-de.xml",
  "html_sitemap_filename": "sitemap-de.html"
}
```

### Konfiguration für Englische Version

```json
{
  "base_url": "https://example.com/en",
  "output_dir": "public/sitemaps/en",
  "max_urls": 10000,
  
  "enabled_sitemap_types": ["xml", "html", "mobile"],
  
  "include_patterns": [
    "/en/*"
  ],
  
  "exclude_patterns": [
    "/de/*",
    "/fr/*",
    "/es/*"
  ],
  
  "priority_rules": {
    "/en": 1.0,
    "/en/products/*": 0.9,
    "/en/blog/*": 0.7,
    "/en/about": 0.6,
    "/en/contact": 0.5
  },
  
  "xml_sitemap_filename": "sitemap-en.xml",
  "html_sitemap_filename": "sitemap-en.html"
}
```

### Batch-Verarbeitung für alle Sprachen

```php
<?php
// Mehrsprachige Sitemap-Generierung
class MultilingualSitemapGenerator {
    private $languages = [
        'de' => 'config/config-de.json',
        'en' => 'config/config-en.json',
        'fr' => 'config/config-fr.json',
        'es' => 'config/config-es.json'
    ];
    
    public function generateAllLanguages() {
        $results = [];
        
        foreach ($this->languages as $lang => $configFile) {
            echo "Generiere Sitemaps für Sprache: $lang\n";
            
            try {
                $generator = new SitemapGenerator($configFile);
                $langResults = $generator->generateAll();
                
                $results[$lang] = $langResults;
                echo "✓ $lang: " . count($langResults) . " Sitemaps generiert\n";
                
                // Kurze Pause zwischen Sprachen
                sleep(2);
                
            } catch (Exception $e) {
                echo "✗ $lang: Fehler - " . $e->getMessage() . "\n";
                $results[$lang] = ['error' => $e->getMessage()];
            }
        }
        
        // Haupt-Sitemap-Index erstellen
        $this->createMainSitemapIndex($results);
        
        return $results;
    }
    
    private function createMainSitemapIndex($results) {
        $indexContent = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $indexContent .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($results as $lang => $langResults) {
            if (isset($langResults['xml'])) {
                $indexContent .= '  <sitemap>' . "\n";
                $indexContent .= '    <loc>' . htmlspecialchars($langResults['xml']['url']) . '</loc>' . "\n";
                $indexContent .= '    <lastmod>' . date('c') . '</lastmod>' . "\n";
                $indexContent .= '  </sitemap>' . "\n";
            }
        }
        
        $indexContent .= '</sitemapindex>' . "\n";
        
        file_put_contents('public/sitemaps/sitemap-index.xml', $indexContent);
        echo "✓ Haupt-Sitemap-Index erstellt\n";
    }
}

// Verwendung
$multiGenerator = new MultilingualSitemapGenerator();
$results = $multiGenerator->generateAllLanguages();
?>
```

### Cron-Jobs für mehrsprachige Website

```bash
# Alle Sprachen täglich um 2:00 Uhr (gestaffelt)
0 2 * * * /var/www/site/sitemap-cli.php --config=/var/www/site/config/config-de.json --cron --quiet
10 2 * * * /var/www/site/sitemap-cli.php --config=/var/www/site/config/config-en.json --cron --quiet
20 2 * * * /var/www/site/sitemap-cli.php --config=/var/www/site/config/config-fr.json --cron --quiet
30 2 * * * /var/www/site/sitemap-cli.php --config=/var/www/site/config/config-es.json --cron --quiet

# Haupt-Index nach allen Sprachen erstellen
40 2 * * * /usr/bin/php /var/www/site/scripts/create-main-index.php
```

## WordPress Integration

### Plugin-Integration

```php
<?php
/**
 * WordPress Plugin: Advanced Sitemap Generator
 */

class AdvancedSitemapPlugin {
    private $generator;
    
    public function __construct() {
        add_action('init', [$this, 'init']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('wp_ajax_generate_sitemap', [$this, 'ajax_generate_sitemap']);
        
        // Automatische Updates
        add_action('publish_post', [$this, 'auto_update_sitemap']);
        add_action('publish_page', [$this, 'auto_update_sitemap']);
    }
    
    public function init() {
        $configFile = WP_CONTENT_DIR . '/sitemap-config.json';
        
        if (!file_exists($configFile)) {
            $this->create_default_config($configFile);
        }
        
        $this->generator = new SitemapGenerator($configFile);
    }
    
    private function create_default_config($configFile) {
        $config = [
            'base_url' => home_url(),
            'output_dir' => ABSPATH . 'sitemaps',
            'enabled_sitemap_types' => ['xml', 'html'],
            'exclude_patterns' => [
                '/wp-admin/*',
                '/wp-content/*',
                '/wp-includes/*',
                '/feed*',
                '*.xml'
            ],
            'priority_rules' => [
                '/' => 1.0,
                '/blog' => 0.8,
                '/page/*' => 0.6
            ]
        ];
        
        file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));
    }
    
    public function add_admin_menu() {
        add_options_page(
            'Sitemap Generator',
            'Sitemaps',
            'manage_options',
            'sitemap-generator',
            [$this, 'admin_page']
        );
    }
    
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>Sitemap Generator</h1>
            
            <div id="sitemap-status"></div>
            
            <button id="generate-sitemap" class="button button-primary">
                Sitemaps generieren
            </button>
            
            <script>
            jQuery('#generate-sitemap').click(function() {
                jQuery.post(ajaxurl, {
                    action: 'generate_sitemap'
                }, function(response) {
                    jQuery('#sitemap-status').html(response);
                });
            });
            </script>
        </div>
        <?php
    }
    
    public function ajax_generate_sitemap() {
        try {
            $results = $this->generator->generateAll();
            
            echo '<div class="notice notice-success"><p>Sitemaps erfolgreich generiert:</p><ul>';
            foreach ($results as $type => $result) {
                echo '<li>' . $type . ': <a href="' . $result['url'] . '" target="_blank">' . $result['url'] . '</a></li>';
            }
            echo '</ul></div>';
            
        } catch (Exception $e) {
            echo '<div class="notice notice-error"><p>Fehler: ' . $e->getMessage() . '</p></div>';
        }
        
        wp_die();
    }
    
    public function auto_update_sitemap($post_id) {
        // Nur bei öffentlichen Posts/Pages
        if (get_post_status($post_id) === 'publish') {
            try {
                $this->generator->generate('xml');
            } catch (Exception $e) {
                error_log('Automatisches Sitemap-Update fehlgeschlagen: ' . $e->getMessage());
            }
        }
    }
}

new AdvancedSitemapPlugin();
?>
```

## Große Unternehmenswebsite

### Enterprise-Konfiguration

```json
{
  "base_url": "https://enterprise.com",
  "output_dir": "/var/www/sitemaps",
  "max_urls": 500000,
  "max_depth": 12,
  "delay_between_requests": 0.2,
  
  "enabled_sitemap_types": [
    "xml",
    "html",
    "text",
    "mobile",
    "image",
    "video"
  ],
  
  "xml_max_urls_per_file": 45000,
  "xml_compress": true,
  
  "exclude_patterns": [
    "/admin/*",
    "/api/*",
    "/internal/*",
    "/test/*",
    "/staging/*",
    "/dev/*",
    "*.pdf",
    "*.doc*",
    "*.xls*",
    "/search*",
    "/filter*"
  ],
  
  "include_patterns": [
    "/products/*",
    "/services/*",
    "/solutions/*",
    "/industries/*",
    "/resources/*",
    "/news/*",
    "/careers/*"
  ],
  
  "priority_rules": {
    "/": 1.0,
    "/products": 0.9,
    "/products/*": 0.8,
    "/services": 0.9,
    "/services/*": 0.8,
    "/solutions": 0.8,
    "/solutions/*": 0.7,
    "/industries": 0.7,
    "/industries/*": 0.6,
    "/news": 0.6,
    "/news/*": 0.5,
    "/careers": 0.5,
    "/careers/*": 0.4,
    "/about": 0.6,
    "/contact": 0.7
  },
  
  "changefreq_rules": {
    "/": "daily",
    "/products": "weekly",
    "/products/*": "monthly",
    "/services": "weekly",
    "/services/*": "monthly",
    "/news": "daily",
    "/news/*": "weekly",
    "/careers": "weekly",
    "/careers/*": "monthly"
  },
  
  "ping_search_engines": true,
  "ping_google": true,
  "ping_bing": true,
  
  "enable_caching": true,
  "cache_duration": 3600,
  
  "user_agent": "Enterprise Sitemap Bot 1.0",
  "timeout": 60
}
```

### Distributed Processing

```php
<?php
// Verteilte Verarbeitung für große Websites
class DistributedSitemapGenerator {
    private $config;
    private $workers = [];
    
    public function __construct($configFile) {
        $this->config = new Config($configFile);
        $this->initializeWorkers();
    }
    
    private function initializeWorkers() {
        // Verschiedene Worker für verschiedene Bereiche
        $this->workers = [
            'products' => [
                'include_patterns' => ['/products/*'],
                'max_urls' => 100000
            ],
            'services' => [
                'include_patterns' => ['/services/*'],
                'max_urls' => 50000
            ],
            'news' => [
                'include_patterns' => ['/news/*'],
                'max_urls' => 25000
            ],
            'general' => [
                'exclude_patterns' => ['/products/*', '/services/*', '/news/*'],
                'max_urls' => 25000
            ]
        ];
    }
    
    public function generateDistributed() {
        $results = [];
        
        foreach ($this->workers as $workerName => $workerConfig) {
            echo "Starte Worker: $workerName\n";
            
            // Worker-spezifische Konfiguration
            $config = clone $this->config;
            foreach ($workerConfig as $key => $value) {
                $config->set($key, $value);
            }
            
            // Separates Output-Verzeichnis
            $outputDir = $this->config->get('output_dir') . '/' . $workerName;
            $config->set('output_dir', $outputDir);
            
            try {
                $generator = new SitemapGenerator();
                $generator->getConfig()->loadFromArray($config->getAll());
                
                $workerResults = $generator->generateAll();
                $results[$workerName] = $workerResults;
                
                echo "✓ Worker $workerName abgeschlossen\n";
                
            } catch (Exception $e) {
                echo "✗ Worker $workerName fehlgeschlagen: " . $e->getMessage() . "\n";
                $results[$workerName] = ['error' => $e->getMessage()];
            }
        }
        
        // Master-Sitemap-Index erstellen
        $this->createMasterIndex($results);
        
        return $results;
    }
    
    private function createMasterIndex($results) {
        $indexContent = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $indexContent .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($results as $workerName => $workerResults) {
            if (isset($workerResults['xml'])) {
                $indexContent .= '  <sitemap>' . "\n";
                $indexContent .= '    <loc>' . htmlspecialchars($workerResults['xml']['url']) . '</loc>' . "\n";
                $indexContent .= '    <lastmod>' . date('c') . '</lastmod>' . "\n";
                $indexContent .= '  </sitemap>' . "\n";
            }
        }
        
        $indexContent .= '</sitemapindex>' . "\n";
        
        $masterFile = $this->config->get('output_dir') . '/sitemap-master.xml';
        file_put_contents($masterFile, $indexContent);
        
        echo "✓ Master-Sitemap-Index erstellt: $masterFile\n";
    }
}

// Verwendung
$distributed = new DistributedSitemapGenerator('config/enterprise-config.json');
$results = $distributed->generateDistributed();
?>
```

## Entwicklungsumgebung

### Development-Konfiguration

```json
{
  "base_url": "http://localhost:8000",
  "output_dir": "public/dev-sitemaps",
  "max_urls": 1000,
  "max_depth": 5,
  "delay_between_requests": 0,
  
  "enabled_sitemap_types": ["xml", "html"],
  
  "exclude_patterns": [
    "/admin/*",
    "/test/*",
    "/debug/*",
    "*.log"
  ],
  
  "ping_search_engines": false,
  "enable_caching": false,
  
  "enable_logging": true,
  "log_level": "debug",
  "log_file": "logs/dev-sitemap.log"
}
```

### Development-Script

```php
<?php
// Development-Helper für lokale Entwicklung
class DevSitemapHelper {
    private $generator;
    
    public function __construct() {
        $this->generator = new SitemapGenerator('config/dev-config.json');
    }
    
    public function quickGenerate() {
        echo "🚀 Schnelle Sitemap-Generierung für Development...\n";
        
        $startTime = microtime(true);
        
        try {
            // Nur XML für schnelle Tests
            $result = $this->generator->generate('xml');
            
            $duration = round(microtime(true) - $startTime, 2);
            
            echo "✅ XML-Sitemap generiert in {$duration}s\n";
            echo "📁 Datei: {$result['file']}\n";
            echo "🔗 URL: {$result['url']}\n";
            echo "📊 URLs: {$result['urls_count']}\n";
            
            // Sitemap validieren
            $this->validateSitemap($result['file']);
            
        } catch (Exception $e) {
            echo "❌ Fehler: " . $e->getMessage() . "\n";
        }
    }
    
    private function validateSitemap($file) {
        $content = file_get_contents($file);
        
        // Basis-XML-Validierung
        $dom = new DOMDocument();
        if ($dom->loadXML($content)) {
            echo "✅ XML ist gültig\n";
            
            // URL-Anzahl zählen
            $urls = $dom->getElementsByTagName('url');
            echo "📈 Gefundene URLs: " . $urls->length . "\n";
            
        } else {
            echo "❌ XML ist ungültig\n";
        }
    }
    
    public function watchMode() {
        echo "👀 Watch-Modus gestartet (Ctrl+C zum Beenden)...\n";
        
        $lastModified = 0;
        
        while (true) {
            // Prüfe auf Dateiänderungen
            $currentModified = $this->getLastModified();
            
            if ($currentModified > $lastModified) {
                echo "\n🔄 Änderungen erkannt, regeneriere Sitemap...\n";
                $this->quickGenerate();
                $lastModified = $currentModified;
            }
            
            sleep(5);
        }
    }
    
    private function getLastModified() {
        $files = glob('src/**/*.php');
        $lastModified = 0;
        
        foreach ($files as $file) {
            $modified = filemtime($file);
            if ($modified > $lastModified) {
                $lastModified = $modified;
            }
        }
        
        return $lastModified;
    }
}

// CLI-Interface für Development
if (php_sapi_name() === 'cli') {
    $helper = new DevSitemapHelper();
    
    $command = $argv[1] ?? 'generate';
    
    switch ($command) {
        case 'generate':
            $helper->quickGenerate();
            break;
            
        case 'watch':
            $helper->watchMode();
            break;
            
        default:
            echo "Verwendung: php dev-helper.php [generate|watch]\n";
            break;
    }
}
?>
```

## Monitoring und Alerts

### Monitoring-Script

```php
<?php
// Monitoring und Alert-System
class SitemapMonitor {
    private $config;
    private $alertEmail;
    
    public function __construct($configFile, $alertEmail) {
        $this->config = new Config($configFile);
        $this->alertEmail = $alertEmail;
    }
    
    public function runHealthCheck() {
        $issues = [];
        
        // Sitemap-Dateien prüfen
        $issues = array_merge($issues, $this->checkSitemapFiles());
        
        // Ping-Status prüfen
        $issues = array_merge($issues, $this->checkPingStatus());
        
        // Performance prüfen
        $issues = array_merge($issues, $this->checkPerformance());
        
        // Log-Dateien prüfen
        $issues = array_merge($issues, $this->checkLogFiles());
        
        if (!empty($issues)) {
            $this->sendAlert($issues);
        }
        
        return $issues;
    }
    
    private function checkSitemapFiles() {
        $issues = [];
        $outputDir = $this->config->get('output_dir');
        
        // XML-Sitemap prüfen
        $xmlFile = $outputDir . '/' . $this->config->get('xml_sitemap_filename', 'sitemap.xml');
        
        if (!file_exists($xmlFile)) {
            $issues[] = "XML-Sitemap fehlt: $xmlFile";
        } else {
            $age = time() - filemtime($xmlFile);
            if ($age > 86400) { // Älter als 24 Stunden
                $issues[] = "XML-Sitemap ist veraltet: " . round($age / 3600, 1) . " Stunden alt";
            }
            
            // XML-Validierung
            $content = file_get_contents($xmlFile);
            $dom = new DOMDocument();
            if (!$dom->loadXML($content)) {
                $issues[] = "XML-Sitemap ist ungültig: $xmlFile";
            }
        }
        
        return $issues;
    }
    
    private function checkPingStatus() {
        $issues = [];
        
        if ($this->config->get('ping_search_engines', true)) {
            $ping = new SearchEnginePing($this->config);
            $results = $ping->testPing();
            
            foreach ($results as $engine => $result) {
                if (!$result['success']) {
                    $issues[] = "Ping fehlgeschlagen für {$result['name']}: {$result['error']}";
                }
            }
        }
        
        return $issues;
    }
    
    private function checkPerformance() {
        $issues = [];
        
        // Generierungszeit messen
        $startTime = microtime(true);
        
        try {
            $generator = new SitemapGenerator();
            $generator->getConfig()->loadFromArray($this->config->getAll());
            
            // Nur XML für Performance-Test
            $result = $generator->generate('xml');
            
            $duration = microtime(true) - $startTime;
            
            if ($duration > 300) { // Länger als 5 Minuten
                $issues[] = "Sitemap-Generierung zu langsam: " . round($duration, 2) . "s";
            }
            
        } catch (Exception $e) {
            $issues[] = "Generierung fehlgeschlagen: " . $e->getMessage();
        }
        
        return $issues;
    }
    
    private function checkLogFiles() {
        $issues = [];
        $logFile = $this->config->get('log_file', 'logs/sitemap-generator.log');
        
        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
            
            // Nach Fehlern in den letzten 24 Stunden suchen
            $lines = explode("\n", $logContent);
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            
            foreach ($lines as $line) {
                if (strpos($line, $yesterday) !== false && 
                    (strpos($line, '[error]') !== false || strpos($line, '[ERROR]') !== false)) {
                    $issues[] = "Fehler im Log gefunden: " . trim($line);
                }
            }
        }
        
        return $issues;
    }
    
    private function sendAlert($issues) {
        $subject = "Sitemap Generator Alert - " . $this->config->get('base_url');
        $message = "Folgende Probleme wurden erkannt:\n\n";
        
        foreach ($issues as $issue) {
            $message .= "- $issue\n";
        }
        
        $message .= "\nZeitpunkt: " . date('Y-m-d H:i:s');
        $message .= "\nServer: " . gethostname();
        
        mail($this->alertEmail, $subject, $message);
        
        // Auch in Log schreiben
        error_log("Sitemap Monitor Alert: " . count($issues) . " Probleme erkannt");
    }
}

// Monitoring-Cron-Job
$monitor = new SitemapMonitor('config/config.json', 'admin@example.com');
$issues = $monitor->runHealthCheck();

if (empty($issues)) {
    echo "✅ Alle Checks erfolgreich\n";
} else {
    echo "❌ " . count($issues) . " Probleme gefunden\n";
    foreach ($issues as $issue) {
        echo "  - $issue\n";
    }
}
?>
```

### Monitoring Cron-Job

```bash
# Tägliches Monitoring um 6:00 Uhr
0 6 * * * /var/www/sitemap-generator/monitor.php >> /var/log/sitemap-monitor.log 2>&1

# Wöchentlicher ausführlicher Report (Sonntag 7:00 Uhr)
0 7 * * 0 /var/www/sitemap-generator/weekly-report.php | mail -s "Wöchentlicher Sitemap Report" admin@example.com
```

Diese Beispiele zeigen die Vielseitigkeit und Anpassungsfähigkeit des PHP Sitemap Generators für verschiedene Anwendungsfälle und Website-Typen.

