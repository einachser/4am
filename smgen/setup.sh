#!/bin/bash

# PHP Sitemap Generator - Setup Script
# Dieses Script hilft bei der Installation und Konfiguration

set -e

# Farben für Ausgabe
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Funktionen
print_header() {
    echo -e "${BLUE}"
    echo "=================================================="
    echo "  PHP Sitemap Generator - Setup"
    echo "=================================================="
    echo -e "${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

# Hauptfunktion
main() {
    print_header
    
    # PHP-Version prüfen
    check_php
    
    # Verzeichnisse erstellen
    create_directories
    
    # Berechtigungen setzen
    set_permissions
    
    # Konfiguration erstellen
    create_config
    
    # CLI ausführbar machen
    make_cli_executable
    
    # Cron-Job-Beispiele zeigen
    show_cron_examples
    
    # Abschluss
    print_completion
}

check_php() {
    print_info "Prüfe PHP-Installation..."
    
    if ! command -v php &> /dev/null; then
        print_error "PHP ist nicht installiert oder nicht im PATH verfügbar"
        exit 1
    fi
    
    PHP_VERSION=$(php -r "echo PHP_VERSION;")
    print_success "PHP Version: $PHP_VERSION"
    
    # Mindestversion prüfen (PHP 7.4+)
    if php -r "exit(version_compare(PHP_VERSION, '7.4.0', '<') ? 1 : 0);"; then
        print_error "PHP 7.4 oder höher erforderlich"
        exit 1
    fi
    
    # Erforderliche Extensions prüfen
    REQUIRED_EXTENSIONS=("curl" "dom" "libxml" "json")
    
    for ext in "${REQUIRED_EXTENSIONS[@]}"; do
        if php -m | grep -q "^$ext$"; then
            print_success "PHP Extension '$ext' verfügbar"
        else
            print_error "PHP Extension '$ext' fehlt"
            exit 1
        fi
    done
}

create_directories() {
    print_info "Erstelle Verzeichnisse..."
    
    DIRECTORIES=("output" "logs" "cache" "config" "examples")
    
    for dir in "${DIRECTORIES[@]}"; do
        if [ ! -d "$dir" ]; then
            mkdir -p "$dir"
            print_success "Verzeichnis '$dir' erstellt"
        else
            print_warning "Verzeichnis '$dir' existiert bereits"
        fi
    done
}

set_permissions() {
    print_info "Setze Berechtigungen..."
    
    # Schreibrechte für Output-Verzeichnisse
    chmod 755 output logs cache 2>/dev/null || true
    
    # CLI-Script ausführbar machen
    chmod +x sitemap-cli.php 2>/dev/null || true
    
    print_success "Berechtigungen gesetzt"
}

create_config() {
    print_info "Erstelle Konfiguration..."
    
    # Interaktive Konfiguration
    echo ""
    echo "Bitte geben Sie die Basis-URL Ihrer Website ein:"
    read -p "Basis-URL (z.B. https://example.com): " BASE_URL
    
    if [ -z "$BASE_URL" ]; then
        print_warning "Keine URL eingegeben, verwende Beispiel-Konfiguration"
        BASE_URL="https://example.com"
    fi
    
    # Konfigurationsdatei erstellen
    CONFIG_FILE="config/my-config.json"
    
    cat > "$CONFIG_FILE" << EOF
{
  "base_url": "$BASE_URL",
  "output_dir": "output",
  "max_urls": 50000,
  "max_depth": 10,
  "delay_between_requests": 1,
  
  "enabled_sitemap_types": [
    "xml",
    "html",
    "text"
  ],
  
  "xml_sitemap_filename": "sitemap.xml",
  "xml_compress": true,
  
  "html_sitemap_filename": "sitemap.html",
  "html_group_by_directory": true,
  
  "text_sitemap_filename": "sitemap.txt",
  
  "exclude_patterns": [
    "/admin/*",
    "/wp-admin/*",
    "/login",
    "/logout",
    "*.pdf"
  ],
  
  "ping_search_engines": true,
  "ping_google": true,
  "ping_bing": true,
  
  "enable_logging": true,
  "log_file": "logs/sitemap-generator.log",
  
  "enable_caching": true,
  "cache_duration": 3600,
  "cache_file": "cache/urls.cache",
  
  "user_agent": "Sitemap Generator Bot 1.0",
  "timeout": 30,
  "follow_robots_txt": true,
  
  "default_priority": 0.5,
  "default_changefreq": "weekly"
}
EOF
    
    print_success "Konfigurationsdatei erstellt: $CONFIG_FILE"
}

make_cli_executable() {
    print_info "Mache CLI-Script ausführbar..."
    
    if [ -f "sitemap-cli.php" ]; then
        chmod +x sitemap-cli.php
        print_success "sitemap-cli.php ist jetzt ausführbar"
    else
        print_error "sitemap-cli.php nicht gefunden"
    fi
}

show_cron_examples() {
    print_info "Cron-Job Beispiele:"
    echo ""
    echo "Für tägliche Sitemap-Generierung um 2:00 Uhr:"
    echo "0 2 * * * $(pwd)/sitemap-cli.php --config=$(pwd)/config/my-config.json --cron --quiet"
    echo ""
    echo "Für stündliche XML-Sitemap Updates:"
    echo "0 * * * * $(pwd)/sitemap-cli.php --config=$(pwd)/config/my-config.json --generate=xml --cron --quiet"
    echo ""
    echo "Fügen Sie diese Zeilen zu Ihrer crontab hinzu mit: crontab -e"
    echo ""
}

test_installation() {
    print_info "Teste Installation..."
    
    # Konfiguration validieren
    if php sitemap-cli.php --config=config/my-config.json --validate-config; then
        print_success "Konfiguration ist gültig"
    else
        print_error "Konfigurationsfehler gefunden"
        return 1
    fi
    
    # Test-Generierung (nur XML)
    print_info "Führe Test-Generierung durch..."
    if php sitemap-cli.php --config=config/my-config.json --generate=xml --quiet; then
        print_success "Test-Generierung erfolgreich"
    else
        print_error "Test-Generierung fehlgeschlagen"
        return 1
    fi
    
    # Ping-Test
    print_info "Teste Ping-Funktionalität..."
    php sitemap-cli.php --config=config/my-config.json --test-ping
}

print_completion() {
    echo ""
    print_success "Setup abgeschlossen!"
    echo ""
    print_info "Nächste Schritte:"
    echo "1. Bearbeiten Sie config/my-config.json nach Ihren Bedürfnissen"
    echo "2. Testen Sie die Generierung: ./sitemap-cli.php --config=config/my-config.json"
    echo "3. Richten Sie einen Cron-Job für automatische Updates ein"
    echo "4. Überprüfen Sie die generierten Sitemaps im 'output' Verzeichnis"
    echo ""
    print_info "Dokumentation und Beispiele finden Sie im 'examples' Verzeichnis"
    echo ""
    
    # Frage nach Test
    echo "Möchten Sie die Installation jetzt testen? (j/n)"
    read -p "> " TEST_CHOICE
    
    if [[ $TEST_CHOICE =~ ^[Jj]$ ]]; then
        echo ""
        test_installation
    fi
}

# Script ausführen
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi

