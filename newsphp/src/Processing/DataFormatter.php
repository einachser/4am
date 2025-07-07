<?php

namespace App\Processing;

class DataFormatter
{
    public function formatMusicTrack(array $rawData): array
    {
        $formatted = [
            "title" => $rawData["title"] ?? null,
            "artist" => $rawData["artist"]["name"] ?? ($rawData["artist"] ?? null), // Handle different Deezer response structures
            "album" => $rawData["album"]["title"] ?? null,
            "release_date" => isset($rawData["release_date"]) ? date("Y-m-d", strtotime($rawData["release_date"])) : null,
            "genre" => $rawData["genre"]["name"] ?? null,
            "duration" => $rawData["duration"] ?? null, // in seconds
            "cover_image_url" => $rawData["album"]["cover_xl"] ?? $rawData["album"]["cover_big"] ?? null,
            "deezer_id" => $rawData["id"] ?? null,
        ];

        // Clean up text fields
        foreach (["title", "artist", "album", "genre"] as $field) {
            if (isset($formatted[$field])) {
                $formatted[$field] = strip_tags($formatted[$field]);
                $formatted[$field] = trim($formatted[$field]);
            }
        }

        return $formatted;
    }

    public function formatNewsArticle(array $rawData): array
    {
        $formatted = [
            "title" => $rawData["title"] ?? null,
            "description" => $rawData["description"] ?? null,
            "content" => $rawData["content"] ?? null,
            "url" => $rawData["url"] ?? null,
            "image_url" => $rawData["urlToImage"] ?? null,
            "source" => $rawData["source"]["name"] ?? null,
            "published_at" => isset($rawData["publishedAt"]) ? date("Y-m-d H:i:s", strtotime($rawData["publishedAt"])) : null,
        ];

        // Clean up text fields
        foreach (["title", "description", "content", "source"] as $field) {
            if (isset($formatted[$field])) {
                $formatted[$field] = strip_tags($formatted[$field]);
                $formatted[$field] = trim($formatted[$field]);
            }
        }

        return $formatted;
    }
}


