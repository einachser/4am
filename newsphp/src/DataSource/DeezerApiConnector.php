<?php

namespace App\DataSource;

class DeezerApiConnector implements ApiConnector
{
    private $httpClient;
    private $apiKey;
    private $baseUrl;

    public function __construct(HttpClient $httpClient, string $apiKey, string $baseUrl)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
    }

    public function fetchData(string $query): ?array
    {
        // Deezer API typically doesn't use an API key in the URL for public search, but for other endpoints it might.
        // For search, it's usually open. Assuming for now it's for rate limiting or specific access.
        // If an API key is truly needed for search, it might be in headers or a different URL parameter.
        // For this example, we'll assume the API key is not directly used in the search URL.
        $url = $this->baseUrl . "search?q=" . urlencode($query);
        
        // If Deezer API requires an API key in headers, uncomment and modify:
        // $headers = ["Authorization: Bearer " . $this->apiKey];
        // $response = $this->httpClient->get($url, $headers);

        $response = $this->httpClient->get($url);

        if ($response) {
            $data = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($data["data"])) {
                return $data["data"]; // Return the array of tracks
            }
        }
        return null;
    }
}


