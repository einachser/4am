<?php
/**
 * YouTube API Konfiguration für 4AM Techno Website
 */

// YouTube API Konfiguration
define('YOUTUBE_API_KEY', '; // Hier den echten API Key eintragen
define('YOUTUBE_CHANNEL_ID', 'UCSIQsJz88OfyACfTr3hZ7vA'); // 4AM Techno Kanal ID

// Cache-Einstellungen
define('YOUTUBE_CACHE_DURATION', 3600); // Cache für 1 Stunde (3600 Sekunden)
define('YOUTUBE_CACHE_DIR', $_SERVER['DOCUMENT_ROOT'] . '/cache/youtube/');

// Anzahl der Videos pro Seite
define('YOUTUBE_VIDEOS_PER_PAGE', 21);
define('YOUTUBE_MAX_VIDEOS', 210); // Maximale Anzahl Videos zum Abrufen

// Erstelle Cache-Verzeichnis falls es nicht existiert
if (!file_exists(YOUTUBE_CACHE_DIR)) {
    mkdir(YOUTUBE_CACHE_DIR, 0755, true);
}

/**
 * Cache-Funktionen für YouTube API Responses
 */
class YouTubeCache {
    
    /**
     * Ruft Daten aus dem Cache ab
     * @param string $key Cache-Schlüssel
     * @return mixed|null Gecachte Daten oder null wenn nicht vorhanden/abgelaufen
     */
    public static function get($key) {
        $filename = YOUTUBE_CACHE_DIR . md5($key) . '.cache';
        
        if (!file_exists($filename)) {
            return null;
        }
        
        $data = file_get_contents($filename);
        $cache = json_decode($data, true);
        
        if (!$cache || !isset($cache['timestamp']) || !isset($cache['data'])) {
            return null;
        }
        
        // Prüfe ob Cache abgelaufen ist
        if (time() - $cache['timestamp'] > YOUTUBE_CACHE_DURATION) {
            unlink($filename);
            return null;
        }
        
        return $cache['data'];
    }
    
    /**
     * Speichert Daten im Cache
     * @param string $key Cache-Schlüssel
     * @param mixed $data Zu cachende Daten
     * @return bool True bei Erfolg, false bei Fehler
     */
    public static function set($key, $data) {
        $filename = YOUTUBE_CACHE_DIR . md5($key) . '.cache';
        
        $cache = [
            'timestamp' => time(),
            'data' => $data
        ];
        
        return file_put_contents($filename, json_encode($cache)) !== false;
    }
    
    /**
     * Löscht einen Cache-Eintrag
     * @param string $key Cache-Schlüssel
     * @return bool True bei Erfolg, false bei Fehler
     */
    public static function delete($key) {
        $filename = YOUTUBE_CACHE_DIR . md5($key) . '.cache';
        
        if (file_exists($filename)) {
            return unlink($filename);
        }
        
        return true;
    }
    
    /**
     * Löscht alle Cache-Einträge
     * @return bool True bei Erfolg, false bei Fehler
     */
    public static function clear() {
        $files = glob(YOUTUBE_CACHE_DIR . '*.cache');
        
        foreach ($files as $file) {
            if (!unlink($file)) {
                return false;
            }
        }
        
        return true;
    }
}
?>

