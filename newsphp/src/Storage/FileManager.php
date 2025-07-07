<?php

namespace App\Storage;

class FileManager
{
    private $htmlOutputDirectory;
    private $jsonOutputDirectory;

    public function __construct(string $htmlOutputDirectory, string $jsonOutputDirectory)
    {
        $this->htmlOutputDirectory = $htmlOutputDirectory;
        $this->jsonOutputDirectory = $jsonOutputDirectory;
        if (!is_dir($this->htmlOutputDirectory)) {
            mkdir($this->htmlOutputDirectory, 0755, true);
        }
        if (!is_dir($this->jsonOutputDirectory)) {
            mkdir($this->jsonOutputDirectory, 0755, true);
        }
    }

    public function saveMusicTrackAsHtml(array $trackData): bool
    {
        $filename = $this->htmlOutputDirectory . "music_" . $trackData["deezer_id"] . ".html";
        $content = "<h3>" . htmlspecialchars($trackData["title"]) . "</h3>";
        $content .= "<p>Artist: " . htmlspecialchars($trackData["artist"]) . "</p>";
        $content .= "<p>Album: " . htmlspecialchars($trackData["album"]) . "</p>";
        if (!empty($trackData["cover_image_url"])) {
            $content .= "<img src=\"" . htmlspecialchars($trackData["cover_image_url"]) . "\" alt=\"Album Cover\" style=\"width:150px;\">";
        }
        // Add more fields as needed
        return file_put_contents($filename, $content) !== false;
    }

    public function saveNewsArticleAsHtml(array $articleData): bool
    {
        $filename = $this->htmlOutputDirectory . "news_" . md5($articleData["url"]) . ".html";
        $content = "<h2>" . htmlspecialchars($articleData["title"]) . "</h2>";
        $content .= "<p>Source: " . htmlspecialchars($articleData["source"]) . " - " . htmlspecialchars($articleData["published_at"]) . "</p>";
        if (!empty($articleData["image_url"])) {
            $content .= "<img src=\"" . htmlspecialchars($articleData["image_url"]) . "\" alt=\"Article Image\" style=\"max-width:100%;\">";
        }
        $content .= "<p>" . htmlspecialchars($articleData["description"]) . "</p>";
        $content .= "<p><a href=\"" . htmlspecialchars($articleData["url"]) . "\" target=\"_blank\">Read More</a></p>";
        // Add more fields as needed
        return file_put_contents($filename, $content) !== false;
    }

    public function saveMusicTracksAsJson(array $tracksData): bool
    {
        $filename = $this->jsonOutputDirectory . "music_tracks.json";
        return file_put_contents($filename, json_encode($tracksData, JSON_PRETTY_PRINT)) !== false;
    }

    public function saveNewsArticlesAsJson(array $articlesData): bool
    {
        $filename = $this->jsonOutputDirectory . "news_articles.json";
        return file_put_contents($filename, json_encode($articlesData, JSON_PRETTY_PRINT)) !== false;
    }
}


