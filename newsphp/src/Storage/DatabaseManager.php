<?php

namespace App\Storage;

use PDO;
use PDOException;

class DatabaseManager
{
    private $pdo;

    public function __construct(array $dbConfig)
    {
        $dsn = "mysql:host={$dbConfig["host"]};dbname={$dbConfig["dbname"]};charset=utf8mb4";
        try {
            $this->pdo = new PDO($dsn, $dbConfig["username"], $dbConfig["password"]);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error: "Database connection failed: " . $e->getMessage()
            die("Database connection failed: " . $e->getMessage()); // For CLI, die is acceptable for critical errors
        }
    }

    public function execute(string $sql, array $params = []): bool
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            // Log error: "SQL execution failed: " . $e->getMessage() . " Query: " . $sql
            return false;
        }
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // Log error: "SQL fetchAll failed: " . $e->getMessage() . " Query: " . $sql
            return [];
        }
    }

    public function fetchOne(string $sql, array $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            // Log error: "SQL fetchOne failed: " . $e->getMessage() . " Query: " . $sql
            return null;
        }
    }

    public function insertMusicTrack(array $trackData): bool
    {
        $sql = "INSERT INTO music_tracks (title, artist, album, release_date, genre, duration, cover_image_url, deezer_id) VALUES (:title, :artist, :album, :release_date, :genre, :duration, :cover_image_url, :deezer_id)";
        return $this->execute($sql, [
            ":title" => $trackData["title"],
            ":artist" => $trackData["artist"],
            ":album" => $trackData["album"],
            ":release_date" => $trackData["release_date"],
            ":genre" => $trackData["genre"],
            ":duration" => $trackData["duration"],
            ":cover_image_url" => $trackData["cover_image_url"],
            ":deezer_id" => $trackData["deezer_id"],
        ]);
    }

    public function insertNewsArticle(array $articleData): bool
    {
        $sql = "INSERT INTO news_articles (title, description, content, url, image_url, source, published_at) VALUES (:title, :description, :content, :url, :image_url, :source, :published_at)";
        return $this->execute($sql, [
            ":title" => $articleData["title"],
            ":description" => $articleData["description"],
            ":content" => $articleData["content"],
            ":url" => $articleData["url"],
            ":image_url" => $articleData["image_url"],
            ":source" => $articleData["source"],
            ":published_at" => $articleData["published_at"],
        ]);
    }
}


