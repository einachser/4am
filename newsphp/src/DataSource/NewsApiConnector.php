<?php

namespace App\DataSource;

class NewsApiConnector implements ApiConnector
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
        $url = $this->baseUrl . "everything?q=" . urlencode($query) . "&apiKey=" . $this->apiKey;
        $response = $this->httpClient->get($url);

        if ($response) {
            $data = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($data["articles"])) {
                return $data["articles"]; // Return the array of articles
            }
        }
        return null;
    }
}


