<?php
// ===== DEINE EINSTELLUNGEN =====

// 1. Füge hier deinen geheimen YouTube API-Schlüssel ein
$apiKey = 'AIzaSyADqbmAnH1bqjSpWQ80DTCyoIAXoceokrE';

// 2. Deine Kanal-ID
$channelId = 'UCSIQsJz88OfyACfTr3hZ7vA';

// 3. Maximale Anzahl der Videos, die geladen werden sollen
$maxResults = 4; // Wir brauchen nur 4 für die Startseite

// ===== AB HIER NICHTS MEHR ÄNDERN =====

// Setzt den Header, um sicherzustellen, dass der Output als JSON in UTF-8 interpretiert wird
header('Content-Type: application/json; charset=UTF-8');

// Die URL zur YouTube API
$apiUrl = "https://www.googleapis.com/youtube/v3/search?key={$apiKey}&channelId={$channelId}&part=snippet,id&order=date&maxResults={$maxResults}&type=video";

// Prüfen, ob die cURL-Erweiterung überhaupt geladen ist
if (!function_exists('curl_init')) {
    http_response_code(500);
    echo json_encode(['error' => 'cURL ist auf dem Server nicht installiert oder aktiviert.']);
    exit;
}

// cURL Initialisierung
$ch = curl_init();

// cURL Optionen setzen
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Gibt den Transfer als String zurück
curl_setopt($ch, CURLOPT_TIMEOUT, 10);          // Timeout nach 10 Sekunden
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // SSL-Verifizierung erzwingen
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

// Anfrage ausführen
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch); // Mögliche cURL-Fehler abfragen

// cURL schließen
curl_close($ch);

// Fehlerbehandlung
if ($curlError) {
    http_response_code(500);
    echo json_encode(['error' => 'cURL Fehler: ' . $curlError]);
    exit;
}

if ($httpCode != 200) {
    http_response_code($httpCode); // Gibt den originalen Fehlercode von YouTube weiter
    echo json_encode(['error' => 'Fehler von der YouTube API.', 'http_code' => $httpCode, 'response' => json_decode($response)]);
    exit;
}

// Erfolgreiche Antwort von YouTube direkt weiterleiten
echo $response;

?>