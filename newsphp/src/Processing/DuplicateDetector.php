<?php

namespace App\Processing;

use App\Storage\DatabaseManager;

class DuplicateDetector
{
    private $dbManager;
    private $duplicateCheckDays;

    public function __construct(DatabaseManager $dbManager, int $duplicateCheckDays = 7)
    {
        $this->dbManager = $dbManager;
        $this->duplicateCheckDays = $duplicateCheckDays;
    }

    public function isMusicTrackDuplicate(array $trackData): bool
    {
        if (isset($trackData["deezer_id"]) && !empty($trackData["deezer_id"])) {
            $sql = "SELECT COUNT(*) FROM music_tracks WHERE deezer_id = :deezer_id";
            $params = [":deezer_id" => $trackData["deezer_id"]];
            if ($this->dbManager->fetchOne($sql, $params) > 0) {
                return true;
            }
        }

        // Fallback: Check by title and artist within a time frame
        $checkDate = date("Y-m-d H:i:s", strtotime("-" . $this->duplicateCheckDays . " days"));
        $sql = "SELECT COUNT(*) FROM music_tracks WHERE title = :title AND artist = :artist AND created_at >= :check_date";
        $params = [
            ":title" => $trackData["title"],
            ":artist" => $trackData["artist"],
            ":check_date" => $checkDate
        ];
        return $this->dbManager->fetchOne($sql, $params) > 0;
    }

    public function isNewsArticleDuplicate(array $articleData): bool
    {
        if (isset($articleData["url"]) && !empty($articleData["url"])) {
            $sql = "SELECT COUNT(*) FROM news_articles WHERE url = :url";
            $params = [":url" => $articleData["url"]];
            if ($this->dbManager->fetchOne($sql, $params) > 0) {
                return true;
            }
        }

        // Fallback: Check by title within a time frame
        $checkDate = date("Y-m-d H:i:s", strtotime("-" . $this->duplicateCheckDays . " days"));
        $sql = "SELECT COUNT(*) FROM news_articles WHERE title = :title AND published_at >= :check_date";
        $params = [
            ":title" => $articleData["title"],
            ":check_date" => $checkDate
        ];
        return $this->dbManager->fetchOne($sql, $params) > 0;
    }
}


