<?php

namespace App\DataSource;

use App\ErrorHandling\Logger; // Import the Logger class

class HttpClient
{
    private $proxy = null;
    private $logger; // Add a logger property

    public function __construct(array $proxyConfig = [], Logger $logger = null) // Make logger optional for backward compatibility
    {
        if (isset($proxyConfig["enabled"]) && $proxyConfig["enabled"] === true) {
            $this->proxy = $proxyConfig;
        }
        $this->logger = $logger; // Assign the logger
    }

    public function get(string $url, array $headers = []): ?string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if ($this->proxy) {
            $proxyUrl = $this->proxy["host"] . ":" . $this->proxy["port"];
            curl_setopt($ch, CURLOPT_PROXY, $proxyUrl);
            if (isset($this->proxy["username"]) && isset($this->proxy["password"])) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxy["username"] . ":" . $this->proxy["password"]);
            }
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $errorMessage = "cURL Error: " . curl_error($ch) . " for URL: " . $url;
            if ($this->logger) {
                $this->logger->error($errorMessage);
            }
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return $response;
        } else {
            $errorMessage = "HTTP Error: " . $httpCode . " for URL: " . $url;
            if ($this->logger) {
                $this->logger->error($errorMessage);
            }
            return null;
        }
    }
}
