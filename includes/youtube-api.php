<?php
/**
 * YouTube API Integration für 4AM Techno Website
 */

class YouTubeAPI {
    private $apiKey;
    private $channelId;
    private $baseUrl = 'https://www.googleapis.com/youtube/v3/';
    
    public function __construct($apiKey, $channelId) {
        $this->apiKey = $apiKey;
        $this->channelId = $channelId;
    }
    
    /**
     * Ruft alle Videos vom Kanal ab
     * (Code unverändert)
     */
    public function getChannelVideos($maxResults = 50) {
        $videos = [];
        $nextPageToken = '';
        
        do {
            $url = $this->baseUrl . 'search?' . http_build_query([
                'key' => $this->apiKey,
                'channelId' => $this->channelId,
                'part' => 'snippet',
                'order' => 'date',
                'type' => 'video',
                'maxResults' => min($maxResults, 50),
                'pageToken' => $nextPageToken
            ]);
            
            $response = $this->makeRequest($url);
            
            if ($response && isset($response['items'])) {
                foreach ($response['items'] as $item) {
                    $videos[] = [
                        'id' => $item['id']['videoId'],
                        'title' => $item['snippet']['title'],
                        'description' => $item['snippet']['description'],
                        'publishedAt' => $item['snippet']['publishedAt'],
                        'thumbnail' => $item['snippet']['thumbnails']['medium']['url'] ?? $item['snippet']['thumbnails']['default']['url'],
                        'url' => 'https://www.youtube.com/watch?v=' . $item['id']['videoId']
                    ];
                }
                
                $nextPageToken = $response['nextPageToken'] ?? '';
                $maxResults -= count($response['items']);
            } else {
                break;
            }
            
        } while ($nextPageToken && $maxResults > 0);
        
        return $videos;
    }

    /**
     * Ruft Kanal-Informationen ab
     * (Code unverändert)
     */
    public function getChannelInfo() {
        $url = $this->baseUrl . 'channels?' . http_build_query([
            'key' => $this->apiKey,
            'id' => $this->channelId,
            'part' => 'snippet,statistics'
        ]);
        
        $response = $this->makeRequest($url);
        
        if ($response && isset($response['items'][0])) {
            $item = $response['items'][0];
            return [
                'id' => $item['id'],
                'title' => $item['snippet']['title'],
                'description' => $item['snippet']['description'],
                'thumbnail' => $item['snippet']['thumbnails']['medium']['url'] ?? $item['snippet']['thumbnails']['default']['url'],
                'subscriberCount' => $item['statistics']['subscriberCount'] ?? 0,
                'videoCount' => $item['statistics']['videoCount'] ?? 0,
                'viewCount' => $item['statistics']['viewCount'] ?? 0
            ];
        }
        
        return null;
    }

    /**
     * NEU: Ruft alle Playlists vom Kanal ab
     * @return array Array mit Playlist-Informationen
     */
    public function getChannelPlaylists() {
        $playlists = [];
        $nextPageToken = '';

        do {
            $url = $this->baseUrl . 'playlists?' . http_build_query([
                'key' => $this->apiKey,
                'channelId' => $this->channelId,
                'part' => 'snippet,contentDetails',
                'maxResults' => 50,
                'pageToken' => $nextPageToken
            ]);

            $response = $this->makeRequest($url);

            if ($response && isset($response['items'])) {
                foreach ($response['items'] as $item) {
                    if ($item['contentDetails']['itemCount'] > 0) { // Nur Playlists mit Videos
                        $playlists[] = [
                            'id' => $item['id'],
                            'title' => $item['snippet']['title'],
                            'description' => $item['snippet']['description'],
                            'thumbnail' => $item['snippet']['thumbnails']['high']['url'] ?? $item['snippet']['thumbnails']['default']['url'],
                            'url' => 'https://www.youtube.com/playlist?list=' . $item['id'],
                            'videoCount' => $item['contentDetails']['itemCount']
                        ];
                    }
                }
                $nextPageToken = $response['nextPageToken'] ?? null;
            } else {
                break;
            }
        } while ($nextPageToken);

        return $playlists;
    }
    
    // --- Private Helper und statische Funktionen (unverändert) ---

    private function makeRequest($url) {
        $context = stream_context_create(['http' => ['timeout' => 30, 'user_agent' => '4AM Techno Website/1.0']]);
        $response = @file_get_contents($url, false, $context);
        if ($response === false) { return null; }
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE || isset($data['error'])) { return null; }
        return $data;
    }
    
    public static function formatNumber($number) {
        if ($number >= 1000000) return round($number / 1000000, 1) . 'M';
        if ($number >= 1000) return round($number / 1000, 1) . 'K';
        return number_format($number);
    }
}
?>