# PHP Sitemap Generator

Ein umfassender, professioneller Sitemap-Generator für PHP, der alle Arten von Sitemaps gemäß Google Sitemap-Protokoll erstellt und automatisch Suchmaschinen benachrichtigt.

## 🚀 Features

### Sitemap-Typen
- **XML-Sitemaps** - Standard-Sitemaps gemäß Google-Protokoll
- **HTML-Sitemaps** - Benutzerfreundliche HTML-Sitemaps
- **Text-Sitemaps** - Einfache Textdateien mit URLs
- **Mobile-Sitemaps** - Spezielle Sitemaps für mobilfreundliche Seiten
- **Bilder-Sitemaps** - SEO-optimierte Sitemaps für Bilder
- **Video-Sitemaps** - Sitemaps für Video-Content

### Automatisierung
- **Cron-Job-Unterstützung** - Vollautomatische Generierung
- **Suchmaschinen-Ping** - Automatische Benachrichtigung von Google, Bing & Co.
- **Kommandozeilen-Interface** - Einfache Integration in bestehende Workflows
- **Konfigurierbare Zeitpläne** - Flexible Ausführungszeiten

### Erweiterte Funktionen
- **Sitemap-Index** - Automatische Aufteilung großer Sitemaps
- **Komprimierung** - Gzip-Komprimierung für bessere Performance
- **Caching** - Intelligentes Caching für schnellere Ausführung
- **Robots.txt-Unterstützung** - Respektiert robots.txt-Regeln
- **Detaillierte Logging** - Umfassendes Logging und Monitoring

## 📋 Systemanforderungen

- **PHP 7.4+** (empfohlen: PHP 8.0+)
- **PHP Extensions:**
  - curl
  - dom
  - libxml
  - json
- **Schreibrechte** für Output-Verzeichnisse
- **Cron-Zugang** für automatische Ausführung (optional)

## 🛠 Installation

### Schnelle Installation

```bash
# Repository klonen oder herunterladen
git clone https://github.com/example/php-sitemap-generator.git
cd php-sitemap-generator

# Setup-Script ausführen
./setup.sh
```

### Manuelle Installation

```bash
# Verzeichnisse erstellen
mkdir -p output logs cache config examples

# Berechtigungen setzen
chmod 755 output logs cache
chmod +x sitemap-cli.php

# Konfiguration kopieren
cp config/config.json config/my-config.json
```

## ⚙️ Konfiguration

### Basis-Konfiguration

Bearbeiten Sie `config/my-config.json`:

```json
{
  "base_url": "https://ihre-website.de",
  "output_dir": "output",
  "enabled_sitemap_types": ["xml", "html", "text"],
  "ping_search_engines": true
}
```

### Erweiterte Konfiguration

```json
{
  "base_url": "https://ihre-website.de",
  "output_dir": "output",
  "max_urls": 50000,
  "max_depth": 10,
  "delay_between_requests": 1,
  
  "enabled_sitemap_types": [
    "xml", "html", "text", "mobile", "image", "video"
  ],
  
  "exclude_patterns": [
    "/admin/*",
    "/wp-admin/*",
    "*.pdf"
  ],
  
  "priority_rules": {
    "/": 1.0,
    "/blog": 0.9,
    "/products": 0.8
  },
  
  "changefreq_rules": {
    "/": "daily",
    "/blog": "daily",
    "/products": "weekly"
  }
}
```

## 🚀 Verwendung

### Kommandozeile

```bash
# Alle Sitemaps generieren
./sitemap-cli.php --config=config/my-config.json

# Nur XML-Sitemap generieren
./sitemap-cli.php --config=config/my-config.json --generate=xml

# Mehrere Typen generieren
./sitemap-cli.php --config=config/my-config.json --generate=xml,html,text

# Detaillierte Analyse für Medien-Sitemaps
./sitemap-cli.php --config=config/my-config.json --generate=image,video --detailed

# Konfiguration validieren
./sitemap-cli.php --config=config/my-config.json --validate-config

# Ping-Funktionalität testen
./sitemap-cli.php --config=config/my-config.json --test-ping

# Als Cron-Job ausführen (ohne Ausgabe)
./sitemap-cli.php --config=config/my-config.json --cron --quiet
```

### PHP-Code

```php
<?php
require_once 'src/SitemapGenerator.php';

use SitemapGenerator\SitemapGenerator;

// Generator initialisieren
$generator = new SitemapGenerator('config/my-config.json');

// Alle Sitemaps generieren
$results = $generator->generateAll();

// Spezifische Sitemap generieren
$xmlResult = $generator->generate('xml');

// Cron-Job ausführen
$cronResult = $generator->runCronJob();
?>
```

## 🕐 Cron-Jobs einrichten

### Beispiele für crontab

```bash
# Täglich um 2:00 Uhr alle Sitemaps generieren
0 2 * * * /pfad/zu/sitemap-cli.php --config=/pfad/zu/config.json --cron --quiet

# Stündlich XML-Sitemap aktualisieren
0 * * * * /pfad/zu/sitemap-cli.php --config=/pfad/zu/config.json --generate=xml --cron --quiet

# Wöchentlich detaillierte Medien-Analyse
0 3 * * 0 /pfad/zu/sitemap-cli.php --config=/pfad/zu/config.json --generate=image,video --detailed --cron
```

### Cron-Job einrichten

```bash
# Crontab bearbeiten
crontab -e

# Beispiel-Zeile hinzufügen
0 2 * * * /var/www/sitemap-generator/sitemap-cli.php --config=/var/www/sitemap-generator/config/config.json --cron --quiet
```

## 📊 Sitemap-Typen im Detail

### XML-Sitemaps

Standard-Sitemaps gemäß Google Sitemap-Protokoll mit Unterstützung für:
- URL-Prioritäten (0.0 - 1.0)
- Änderungsfrequenzen (always, hourly, daily, weekly, monthly, yearly, never)
- Letzte Änderungsdaten
- Mobile-Annotationen
- Bilder- und Video-Metadaten
- Automatische Sitemap-Indizes für große Websites

### HTML-Sitemaps

Benutzerfreundliche HTML-Sitemaps mit:
- Responsivem Design
- Gruppierung nach Verzeichnissen
- Suchfunktion
- Badges für besondere Eigenschaften (Mobile, Bilder, Videos)
- Anpassbaren Templates

### Mobile-Sitemaps

Spezielle Sitemaps für mobilfreundliche Seiten:
- Automatische Erkennung mobilfreundlicher Seiten
- Viewport-Meta-Tag-Analyse
- Responsive-CSS-Erkennung
- AMP-Seiten-Unterstützung

### Bilder-Sitemaps

SEO-optimierte Sitemaps für Bilder:
- Automatische Bilderkennung
- Alt-Text und Caption-Extraktion
- Bildgrößen und Formate
- Lizenz-Informationen
- Geo-Location-Daten

### Video-Sitemaps

Umfassende Video-SEO-Unterstützung:
- YouTube und Vimeo Integration
- Thumbnail-Generierung
- Video-Metadaten (Dauer, Beschreibung, Tags)
- Plattform-Einschränkungen
- Familienfreundlichkeits-Bewertung

## 🔧 Erweiterte Features

### Suchmaschinen-Ping

Automatische Benachrichtigung von:
- **Google** - Sofortige Indexierung
- **Bing** - Microsoft-Suchmaschine
- **Yandex** - Russische Suchmaschine
- **Baidu** - Chinesische Suchmaschine
- **DuckDuckGo** - Datenschutzorientierte Suche

### Caching-System

Intelligentes Caching für bessere Performance:
- URL-Caching mit konfigurierbarer Lebensdauer
- Inkrementelle Updates
- Cache-Invalidierung bei Änderungen

### Logging und Monitoring

Umfassendes Logging-System:
- Detaillierte Ausführungsprotokolle
- Fehlerbehandlung und -protokollierung
- Performance-Metriken
- Ping-Ergebnis-Tracking

## 📁 Projektstruktur

```
php-sitemap-generator/
├── src/                          # Quellcode
│   ├── SitemapGenerator.php      # Hauptklasse
│   ├── Config.php                # Konfigurationsverwaltung
│   ├── UrlCrawler.php            # URL-Crawler
│   ├── SearchEnginePing.php      # Suchmaschinen-Ping
│   └── generators/               # Sitemap-Generatoren
│       ├── XmlSitemapGenerator.php
│       ├── HtmlSitemapGenerator.php
│       ├── TextSitemapGenerator.php
│       ├── MobileSitemapGenerator.php
│       ├── ImageSitemapGenerator.php
│       └── VideoSitemapGenerator.php
├── config/                       # Konfigurationsdateien
│   ├── config.json              # JSON-Konfiguration
│   └── config.php               # PHP-Konfiguration
├── examples/                     # Beispiele und Dokumentation
│   └── crontab-examples.txt     # Cron-Job-Beispiele
├── output/                       # Generierte Sitemaps
├── logs/                         # Log-Dateien
├── cache/                        # Cache-Dateien
├── sitemap-cli.php              # Kommandozeilen-Interface
├── setup.sh                     # Setup-Script
└── README.md                    # Diese Dokumentation
```

## 🔍 Fehlerbehebung

### Häufige Probleme

**Problem: "Permission denied" Fehler**
```bash
# Lösung: Berechtigungen setzen
chmod 755 output logs cache
chmod +x sitemap-cli.php
```

**Problem: "PHP extension missing"**
```bash
# Ubuntu/Debian
sudo apt-get install php-curl php-dom php-xml

# CentOS/RHEL
sudo yum install php-curl php-dom php-xml
```

**Problem: Cron-Job funktioniert nicht**
```bash
# Vollständige Pfade verwenden
/usr/bin/php /vollständiger/pfad/zu/sitemap-cli.php --config=/vollständiger/pfad/zu/config.json --cron
```

### Debug-Modus

```bash
# Verbose-Ausgabe aktivieren
./sitemap-cli.php --config=config/my-config.json --verbose

# Konfiguration validieren
./sitemap-cli.php --config=config/my-config.json --validate-config

# Log-Dateien prüfen
tail -f logs/sitemap-generator.log
```

## 📈 Performance-Optimierung

### Große Websites

Für Websites mit vielen URLs:

```json
{
  "max_urls": 100000,
  "xml_max_urls_per_file": 50000,
  "delay_between_requests": 0.5,
  "enable_caching": true,
  "cache_duration": 7200
}
```

### Ressourcenschonende Ausführung

```json
{
  "delay_between_requests": 2,
  "timeout": 60,
  "max_depth": 5
}
```

## 🔒 Sicherheit

### Empfohlene Sicherheitsmaßnahmen

1. **Verzeichnisschutz**: Schützen Sie das Projektverzeichnis vor direktem Web-Zugriff
2. **Konfigurationsdateien**: Speichern Sie sensible Daten außerhalb des Web-Roots
3. **Log-Dateien**: Begrenzen Sie Zugriff auf Log-Dateien
4. **Cron-Jobs**: Verwenden Sie dedizierte Benutzerkonten für Cron-Jobs

### .htaccess Beispiel

```apache
# Zugriff auf Konfiguration und Logs verweigern
<Files "*.json">
    Deny from all
</Files>

<Files "*.log">
    Deny from all
</Files>

<Directory "logs">
    Deny from all
</Directory>

<Directory "cache">
    Deny from all
</Directory>
```

## 🤝 Beitragen

Wir freuen uns über Beiträge! Bitte beachten Sie:

1. **Issues**: Melden Sie Bugs oder Feature-Requests über GitHub Issues
2. **Pull Requests**: Folgen Sie den Coding-Standards
3. **Tests**: Fügen Sie Tests für neue Features hinzu
4. **Dokumentation**: Aktualisieren Sie die Dokumentation

## 📄 Lizenz

Dieses Projekt steht unter der MIT-Lizenz. Siehe LICENSE-Datei für Details.

## 🆘 Support

### Community-Support

- **GitHub Issues**: Für Bug-Reports und Feature-Requests
- **Dokumentation**: Umfassende Dokumentation in diesem Repository
- **Beispiele**: Praktische Beispiele im `examples/` Verzeichnis

### Kommerzielle Unterstützung

Für kommerzielle Unterstützung, Anpassungen oder Schulungen kontaktieren Sie uns.

## 🏆 Credits

Entwickelt von **Manus AI** mit Fokus auf:
- Performance und Skalierbarkeit
- Einfache Bedienung und Installation
- Vollständige Google-Protokoll-Konformität
- Umfassende Automatisierung

---

**PHP Sitemap Generator** - Professionelle Sitemap-Generierung für moderne Websites.

