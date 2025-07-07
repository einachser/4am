<?php

namespace App\DataSource;

use App\ErrorHandling\Logger; // Import the Logger class

class RssParser
{
    private $httpClient;
    private $logger; // Add a logger property

    public function __construct(HttpClient $httpClient, Logger $logger) // Add Logger to constructor
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger; // Assign the logger
    }

    public function parseFeed(string $url): ?array
    {
        $xmlContent = $this->httpClient->get($url);

        if ($xmlContent) {
            // Decode HTML entities before parsing to handle non-XML compliant characters
            $xmlContent = html_entity_decode($xmlContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($xmlContent);

            if ($xml === false) {
                // Log XML parsing errors
                foreach (libxml_get_errors() as $error) {
                    $this->logger->error("XML Parsing Error for URL " . $url . ": " . $error->message);
                }
                libxml_clear_errors();
                return null;
            }

            $articles = [];
            foreach ($xml->channel->item as $item) {
                $articles[] = [
                    'title'       => (string)$item->title,
                    'description' => (string)$item->description,
                    'link'        => (string)$item->link,
                    'pubDate'     => (string)$item->pubDate,
                    'guid'        => (string)$item->guid,
                    // Add more fields as needed, e.g., image from media:content or enclosure
                ];
            }
            return $articles;
        }
        return null;
    }
}
