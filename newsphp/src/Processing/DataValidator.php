<?php

namespace App\Processing;

class DataValidator
{
    public function validateMusicTrack(array $trackData): bool
    {
        return isset(
            $trackData["title"],
            $trackData["artist"]
        ) &&
        !empty($trackData["title"]) &&
        !empty($trackData["artist"]);
    }

    public function validateNewsArticle(array $articleData): bool
    {
        return isset(
            $articleData["title"],
            $articleData["url"],
            $articleData["publishedAt"]
        ) &&
        !empty($articleData["title"]) &&
        !empty($articleData["url"]) &&
        !empty($articleData["publishedAt"]);
    }
}


