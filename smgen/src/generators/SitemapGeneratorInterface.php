<?php

namespace SitemapGenerator\Generators;

/**
 * Interface für alle Sitemap-Generatoren
 */
interface SitemapGeneratorInterface
{
    /**
     * Generiert eine Sitemap aus den gegebenen URLs
     * 
     * @param array $urls Array von URL-Objekten mit Metadaten
     * @return array Ergebnis der Generierung mit 'file' und 'url' Keys
     */
    public function generate(array $urls);
    
    /**
     * Validiert die URLs für diesen Sitemap-Typ
     * 
     * @param array $urls URLs zum Validieren
     * @return array Gefilterte und validierte URLs
     */
    public function validateUrls(array $urls);
    
    /**
     * Gibt den MIME-Type der generierten Sitemap zurück
     * 
     * @return string MIME-Type
     */
    public function getMimeType();
    
    /**
     * Gibt die Dateiendung der generierten Sitemap zurück
     * 
     * @return string Dateiendung
     */
    public function getFileExtension();
}

