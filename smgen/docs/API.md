# API-Dokumentation

Diese Dokumentation beschreibt die PHP-API des Sitemap-Generators für Entwickler, die den Generator in ihre eigenen Anwendungen integrieren möchten.

## Inhaltsverzeichnis

1. [Grundlagen](#grundlagen)
2. [SitemapGenerator Klasse](#sitemapgenerator-klasse)
3. [Config Klasse](#config-klasse)
4. [UrlCrawler Klasse](#urlcrawler-klasse)
5. [Sitemap-Generatoren](#sitemap-generatoren)
6. [SearchEnginePing Klasse](#searchengineping-klasse)
7. [Beispiele](#beispiele)
8. [Fehlerbehandlung](#fehlerbehandlung)

## Grundlagen

### Autoloader

```php
<?php
require_once 'src/SitemapGenerator.php';

use SitemapGenerator\SitemapGenerator;
use SitemapGenerator\Config;
use SitemapGenerator\UrlCrawler;
use SitemapGenerator\SearchEnginePing;
```

### Namespace-Struktur

```
SitemapGenerator\
├── SitemapGenerator          # Hauptklasse
├── Config                    # Konfigurationsverwaltung
├── UrlCrawler               # URL-Crawler
├── SearchEnginePing         # Suchmaschinen-Ping
└── Generators\              # Sitemap-Generatoren
    ├── XmlSitemapGenerator
    ├── HtmlSitemapGenerator
    ├── TextSitemapGenerator
    ├── MobileSitemapGenerator
    ├── ImageSitemapGenerator
    └── VideoSitemapGenerator
```

## SitemapGenerator Klasse

Die Hauptklasse für die Sitemap-Generierung.

### Konstruktor

```php
public function __construct($configFile = null)
```

**Parameter:**
- `$configFile` (string|null): Pfad zur Konfigurationsdatei

**Beispiel:**
```php
$generator = new SitemapGenerator('config/config.json');
// oder
$generator = new SitemapGenerator(); // Verwendet Standard-Konfiguration
```

### Methoden

#### generateAll()

Generiert alle aktivierten Sitemap-Typen.

```php
public function generateAll($types = null)
```

**Parameter:**
- `$types` (array|null): Spezifische Typen zu generieren (optional)

**Rückgabe:**
- `array`: Ergebnisse der Generierung

**Beispiel:**
```php
// Alle aktivierten Typen generieren
$results = $generator->generateAll();

// Nur bestimmte Typen generieren
$results = $generator->generateAll(['xml', 'html']);
```

**Rückgabe-Format:**
```php
[
    'xml' => [
        'file' => '/pfad/zu/sitemap.xml',
        'url' => 'https://example.com/sitemap.xml',
        'urls_count' => 1250,
        'type' => 'xml'
    ],
    'html' => [
        'file' => '/pfad/zu/sitemap.html',
        'url' => 'https://example.com/sitemap.html',
        'urls_count' => 1250,
        'type' => 'html'
    ]
]
```

#### generate()

Generiert einen spezifischen Sitemap-Typ.

```php
public function generate($type)
```

**Parameter:**
- `$type` (string): Sitemap-Typ ('xml', 'html', 'text', 'mobile', 'image', 'video')

**Rückgabe:**
- `array`: Ergebnis der Generierung

**Beispiel:**
```php
$result = $generator->generate('xml');
```

#### runCronJob()

Führt eine vollständige Sitemap-Generierung für Cron-Jobs durch.

```php
public function runCronJob($options = [])
```

**Parameter:**
- `$options` (array): Zusätzliche Optionen

**Rückgabe:**
- `array`: Vollständige Ergebnisse mit Timing-Informationen

**Beispiel:**
```php
$result = $generator->runCronJob();

if ($result['success']) {
    echo "Generierung erfolgreich in {$result['duration']}s";
} else {
    echo "Fehler: {$result['error']}";
}
```

#### getConfig()

Gibt die Konfiguration zurück.

```php
public function getConfig()
```

**Rückgabe:**
- `Config`: Konfigurationsobjekt

#### validateConfig()

Validiert die aktuelle Konfiguration.

```php
public function validateConfig()
```

**Rückgabe:**
- `array`: Array mit Validierungsfehlern (leer wenn gültig)

**Beispiel:**
```php
$errors = $generator->validateConfig();

if (empty($errors)) {
    echo "Konfiguration ist gültig";
} else {
    foreach ($errors as $error) {
        echo "Fehler: $error\n";
    }
}
```

## Config Klasse

Verwaltet alle Konfigurationseinstellungen.

### Konstruktor

```php
public function __construct($configFile = null)
```

### Methoden

#### get()

Gibt einen Konfigurationswert zurück.

```php
public function get($key, $default = null)
```

**Parameter:**
- `$key` (string): Konfigurationsschlüssel
- `$default` (mixed): Standardwert falls Schlüssel nicht existiert

**Beispiel:**
```php
$config = new Config('config.json');
$baseUrl = $config->get('base_url');
$maxUrls = $config->get('max_urls', 50000);
```

#### set()

Setzt einen Konfigurationswert.

```php
public function set($key, $value)
```

**Beispiel:**
```php
$config->set('base_url', 'https://neue-domain.com');
```

#### loadFromFile()

Lädt Konfiguration aus einer Datei.

```php
public function loadFromFile($configFile)
```

**Unterstützte Formate:**
- JSON (.json)
- PHP (.php)
- INI (.ini)

#### getPriorityForUrl()

Gibt die Priorität für eine URL zurück.

```php
public function getPriorityForUrl($url)
```

#### getChangefreqForUrl()

Gibt die Änderungsfrequenz für eine URL zurück.

```php
public function getChangefreqForUrl($url)
```

## UrlCrawler Klasse

Crawlt URLs einer Website.

### Konstruktor

```php
public function __construct(Config $config)
```

### Methoden

#### crawl()

Startet das Crawling der Website.

```php
public function crawl()
```

**Rückgabe:**
- `array`: Array von URL-Objekten mit Metadaten

**URL-Objekt-Format:**
```php
[
    'url' => 'https://example.com/page',
    'lastmod' => '2024-01-15T10:30:00+00:00',
    'changefreq' => 'weekly',
    'priority' => 0.8,
    'images' => [
        [
            'url' => 'https://example.com/image.jpg',
            'alt' => 'Beschreibung',
            'title' => 'Titel',
            'caption' => 'Bildunterschrift'
        ]
    ],
    'videos' => [
        [
            'url' => 'https://example.com/video.mp4',
            'title' => 'Video-Titel',
            'description' => 'Beschreibung',
            'thumbnail' => 'https://example.com/thumb.jpg'
        ]
    ],
    'mobile' => true,
    'status_code' => 200,
    'content_type' => 'text/html'
]
```

## Sitemap-Generatoren

Alle Generatoren implementieren das `SitemapGeneratorInterface`.

### Interface

```php
interface SitemapGeneratorInterface
{
    public function generate(array $urls);
    public function validateUrls(array $urls);
    public function getMimeType();
    public function getFileExtension();
}
```

### XmlSitemapGenerator

```php
use SitemapGenerator\Generators\XmlSitemapGenerator;

$generator = new XmlSitemapGenerator($config);
$result = $generator->generate($urls);
```

**Spezielle Methoden:**
- `generateSitemapIndex()`: Erstellt Sitemap-Index für große Websites
- `validateXml()`: Validiert generiertes XML
- `getStatistics()`: Gibt Statistiken zurück

### HtmlSitemapGenerator

```php
use SitemapGenerator\Generators\HtmlSitemapGenerator;

$generator = new HtmlSitemapGenerator($config);
$result = $generator->generate($urls);
```

### MobileSitemapGenerator

```php
use SitemapGenerator\Generators\MobileSitemapGenerator;

$generator = new MobileSitemapGenerator($config);

// Standard-Generierung
$result = $generator->generate($urls);

// Detaillierte Analyse
$result = $generator->generateDetailed($urls);
```

### ImageSitemapGenerator

```php
use SitemapGenerator\Generators\ImageSitemapGenerator;

$generator = new ImageSitemapGenerator($config);

// Standard-Generierung
$result = $generator->generate($urls);

// Mit detaillierter Bildanalyse
$result = $generator->generateDetailed($urls);
```

### VideoSitemapGenerator

```php
use SitemapGenerator\Generators\VideoSitemapGenerator;

$generator = new VideoSitemapGenerator($config);

// Standard-Generierung
$result = $generator->generate($urls);

// Mit detaillierter Videoanalyse
$result = $generator->generateDetailed($urls);
```

## SearchEnginePing Klasse

Benachrichtigt Suchmaschinen über neue Sitemaps.

### Konstruktor

```php
public function __construct(Config $config)
```

### Methoden

#### ping()

Pingt alle aktivierten Suchmaschinen.

```php
public function ping($sitemapUrl, $sitemapType = 'xml')
```

**Parameter:**
- `$sitemapUrl` (string): URL der Sitemap
- `$sitemapType` (string): Typ der Sitemap

**Rückgabe:**
- `array`: Ping-Ergebnisse pro Suchmaschine

**Beispiel:**
```php
$ping = new SearchEnginePing($config);
$results = $ping->ping('https://example.com/sitemap.xml');

foreach ($results as $engine => $result) {
    if ($result['success']) {
        echo "✓ $engine: Erfolgreich\n";
    } else {
        echo "✗ $engine: {$result['error']}\n";
    }
}
```

#### pingMultiple()

Pingt mehrere Sitemaps gleichzeitig.

```php
public function pingMultiple($sitemaps)
```

**Parameter:**
- `$sitemaps` (array): Array von Sitemap-Informationen

**Beispiel:**
```php
$sitemaps = [
    ['url' => 'https://example.com/sitemap.xml', 'type' => 'xml'],
    ['url' => 'https://example.com/sitemap-images.xml', 'type' => 'image']
];

$results = $ping->pingMultiple($sitemaps);
```

#### testPing()

Testet die Ping-Funktionalität.

```php
public function testPing()
```

## Beispiele

### Einfache Sitemap-Generierung

```php
<?php
require_once 'src/SitemapGenerator.php';

use SitemapGenerator\SitemapGenerator;

try {
    // Generator initialisieren
    $generator = new SitemapGenerator('config/config.json');
    
    // Konfiguration validieren
    $errors = $generator->validateConfig();
    if (!empty($errors)) {
        throw new Exception('Konfigurationsfehler: ' . implode(', ', $errors));
    }
    
    // Sitemaps generieren
    $results = $generator->generateAll();
    
    // Ergebnisse ausgeben
    foreach ($results as $type => $result) {
        if (isset($result['error'])) {
            echo "Fehler bei $type: {$result['error']}\n";
        } else {
            echo "✓ $type: {$result['file']} ({$result['urls_count']} URLs)\n";
        }
    }
    
} catch (Exception $e) {
    echo "Fehler: " . $e->getMessage() . "\n";
}
?>
```

### Erweiterte Konfiguration

```php
<?php
use SitemapGenerator\Config;
use SitemapGenerator\SitemapGenerator;

// Konfiguration programmatisch erstellen
$config = new Config();
$config->set('base_url', 'https://example.com');
$config->set('output_dir', '/var/www/sitemaps');
$config->set('enabled_sitemap_types', ['xml', 'html', 'image']);
$config->set('max_urls', 10000);

// Prioritätsregeln setzen
$config->set('priority_rules', [
    '/' => 1.0,
    '/products/*' => 0.8,
    '/blog/*' => 0.7
]);

// Generator mit Konfiguration initialisieren
$generator = new SitemapGenerator();
$generator->getConfig()->loadFromArray($config->getAll());

$results = $generator->generateAll();
?>
```

### Detaillierte Medien-Analyse

```php
<?php
use SitemapGenerator\Generators\ImageSitemapGenerator;
use SitemapGenerator\Generators\VideoSitemapGenerator;
use SitemapGenerator\Config;
use SitemapGenerator\UrlCrawler;

$config = new Config('config/config.json');
$crawler = new UrlCrawler($config);

// URLs crawlen
$urls = $crawler->crawl();

// Detaillierte Bilder-Analyse
$imageGenerator = new ImageSitemapGenerator($config);
$imageResult = $imageGenerator->generateDetailed($urls);

echo "Bilder-Sitemap: {$imageResult['file']}\n";
echo "Statistiken: {$imageResult['stats_file']}\n";

// Detaillierte Video-Analyse
$videoGenerator = new VideoSitemapGenerator($config);
$videoResult = $videoGenerator->generateDetailed($urls);

echo "Video-Sitemap: {$videoResult['file']}\n";
echo "Statistiken: {$videoResult['stats_file']}\n";
?>
```

### Custom Sitemap-Generator

```php
<?php
use SitemapGenerator\Generators\AbstractSitemapGenerator;

class CustomSitemapGenerator extends AbstractSitemapGenerator
{
    public function generate(array $urls)
    {
        $validUrls = $this->validateUrls($urls);
        $filename = 'custom-sitemap.txt';
        $filePath = $this->generateFilePath($filename);
        
        $content = "# Custom Sitemap\n";
        foreach ($validUrls as $url) {
            $content .= $url['url'] . " | Priority: " . $url['priority'] . "\n";
        }
        
        $this->writeFile($filePath, $content);
        
        return [
            'file' => $filePath,
            'url' => $this->generateSitemapUrl($filename),
            'urls_count' => count($validUrls),
            'type' => 'custom'
        ];
    }
    
    public function validateUrls(array $urls)
    {
        return $this->filterByStatusCode($urls, [200]);
    }
    
    public function getMimeType()
    {
        return 'text/plain';
    }
    
    public function getFileExtension()
    {
        return 'txt';
    }
}

// Verwendung
$config = new Config('config/config.json');
$customGenerator = new CustomSitemapGenerator($config);
$urls = $crawler->crawl();
$result = $customGenerator->generate($urls);
?>
```

### Batch-Verarbeitung

```php
<?php
// Mehrere Websites verarbeiten
$websites = [
    ['config' => 'config/site1.json', 'name' => 'Website 1'],
    ['config' => 'config/site2.json', 'name' => 'Website 2'],
    ['config' => 'config/site3.json', 'name' => 'Website 3']
];

foreach ($websites as $site) {
    echo "Verarbeite {$site['name']}...\n";
    
    try {
        $generator = new SitemapGenerator($site['config']);
        $results = $generator->generateAll();
        
        echo "✓ {$site['name']}: " . count($results) . " Sitemaps generiert\n";
        
    } catch (Exception $e) {
        echo "✗ {$site['name']}: Fehler - " . $e->getMessage() . "\n";
    }
    
    // Pause zwischen Websites
    sleep(5);
}
?>
```

## Fehlerbehandlung

### Exception-Typen

Der Sitemap-Generator wirft verschiedene Exception-Typen:

```php
try {
    $generator = new SitemapGenerator('invalid-config.json');
    $results = $generator->generateAll();
    
} catch (InvalidArgumentException $e) {
    // Ungültige Parameter
    echo "Parameterfehler: " . $e->getMessage();
    
} catch (RuntimeException $e) {
    // Laufzeitfehler (Dateisystem, Netzwerk)
    echo "Laufzeitfehler: " . $e->getMessage();
    
} catch (Exception $e) {
    // Allgemeine Fehler
    echo "Allgemeiner Fehler: " . $e->getMessage();
}
```

### Logging

```php
// Logging aktivieren
$config->set('enable_logging', true);
$config->set('log_file', 'logs/api-usage.log');
$config->set('log_level', 'debug');

// Log-Nachrichten werden automatisch geschrieben
$generator = new SitemapGenerator($config);
$results = $generator->generateAll();

// Log-Datei lesen
$logContent = file_get_contents('logs/api-usage.log');
echo $logContent;
```

### Validierung

```php
// Konfiguration validieren
$errors = $generator->validateConfig();
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "Konfigurationsfehler: $error\n";
    }
    exit(1);
}

// URLs validieren
$xmlGenerator = new XmlSitemapGenerator($config);
$validUrls = $xmlGenerator->validateUrls($urls);

if (count($validUrls) < count($urls)) {
    $invalid = count($urls) - count($validUrls);
    echo "Warnung: $invalid URLs wurden als ungültig erkannt\n";
}
```

### Performance-Monitoring

```php
// Ausführungszeit messen
$startTime = microtime(true);

$results = $generator->generateAll();

$endTime = microtime(true);
$duration = round($endTime - $startTime, 2);

echo "Generierung abgeschlossen in {$duration}s\n";

// Speicherverbrauch
$memoryUsage = memory_get_peak_usage(true);
echo "Speicherverbrauch: " . round($memoryUsage / 1024 / 1024, 2) . " MB\n";
```

---

Diese API-Dokumentation bietet eine vollständige Referenz für die Integration des PHP Sitemap Generators in eigene Anwendungen. Für weitere Beispiele siehe das `examples/` Verzeichnis.

