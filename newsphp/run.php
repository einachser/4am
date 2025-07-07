<?php

// Suppress all output by default for cronjob compatibility
ob_start();

require_once __DIR__ . "/vendor/autoload.php";

use App\Config\ConfigLoader;
use App\DataSource\HttpClient;
use App\DataSource\DeezerApiConnector;
use App\DataSource\NewsApiConnector;
use App\DataSource\RssParser;
use App\Processing\DataValidator;
use App\Processing\DuplicateDetector;
use App\Processing\DataFormatter;
use App\Storage\DatabaseManager;
use App\Storage\FileManager;
use App\ErrorHandling\Logger;
use App\ErrorHandling\Notifier;

// Load configuration
$config = new ConfigLoader(__DIR__ . "/config.php");

// Setup Logger
$logger = new Logger(
    $config->get("logging.path"),
    $config->get("logging.level", "info")
);

// Setup Notifier
$notifier = new Notifier($config->get("email_notification", []));

// Set up error handling to log errors and send notifications
set_exception_handler(function ($exception) use ($logger, $notifier) {
    $errorMessage = "Uncaught exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
    $logger->error($errorMessage);
    $notifier->sendErrorNotification($errorMessage);
    exit(1); // Exit with error code
});

set_error_handler(function ($severity, $message, $file, $line) use ($logger, $notifier) {
    if (!(error_reporting() & $severity)) {
        // This error code is not included in error_reporting
        return false;
    }
    $errorMessage = "Error: " . $message . " in " . $file . " on line " . $line;
    $logger->warning($errorMessage); // Log as warning or error based on severity
    // Optionally send notification for specific severities
    if (in_array($severity, [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $notifier->sendErrorNotification($errorMessage);
    }
    return true;
});

// Main script logic
try {
    $logger->info("Script started.");

    // Initialize components
    $httpClient = new HttpClient($config->get("proxy", []));
    $dbManager = new DatabaseManager($config->get("database", []));
    $dataValidator = new DataValidator();
    $duplicateDetector = new DuplicateDetector($dbManager, $config->get("processing.duplicate_check_days", 7));
    $dataFormatter = new DataFormatter();

    //--- Fetch and Process Music Data (Deezer)
    $deezerApiKey = $config->get("api_keys.deezer");
    $deezerBaseUrl = $config->get("api_urls.deezer_base");
    if ($deezerApiKey && $deezerBaseUrl) {
        $deezerApi = new DeezerApiConnector($httpClient, $deezerApiKey, $deezerBaseUrl);
        $musicQuery = "top tracks"; // Example query
        $rawMusicData = $deezerApi->fetchData($musicQuery);
        if ($rawMusicData) {
            // Processing logic for music data...
        }
    }

    //--- Process RSS Feeds
    $rssFeeds = $config->get("rss_feeds", []);
    if (!empty($rssFeeds)) {
        $rssParser = new RssParser($httpClient);
        foreach ($rssFeeds as $feedName => $feedUrl) {
            $logger->info("Processing RSS feed: " . $feedName . " (" . $feedUrl . ")");
            $rawRssArticles = $rssParser->parseFeed($feedUrl);
            if ($rawRssArticles) {
                $newRssArticlesCount = 0;
                foreach ($rawRssArticles as $rssArticle) {
                    $formattedRssArticle = [
                        "title" => $rssArticle["title"] ?? null,
                        "description" => $rssArticle["description"] ?? null,
                        "content" => $rssArticle["content"] ?? $rssArticle["description"] ?? null,
                        "url" => $rssArticle["link"] ?? null,
                        "image_url" => $rssArticle["image"] ?? null,
                        "source" => $feedName,
                        "published_at" => isset($rssArticle["pubDate"]) ? date("Y-m-d H:i:s", strtotime($rssArticle["pubDate"])) : null,
                    ];
                    if ($dataValidator->validateNewsArticle($formattedRssArticle)) {
                        if (!$duplicateDetector->isNewsArticleDuplicate($formattedRssArticle)) {
                            if ($dbManager->insertNewsArticle($formattedRssArticle)) {
                                $logger->info("Inserted new RSS article: " . $formattedRssArticle["title"]);
                                $newRssArticlesCount++;
                            } else {
                                $logger->warning("Failed to insert RSS article: " . $formattedRssArticle["title"]);
                            }
                        } else {
                             $logger->info("Skipped duplicate RSS article: " . $formattedRssArticle["title"]);
                        }
                    } else {
                        $logger->warning("Invalid RSS article data: " . json_encode($rssArticle));
                    }
                }
                $logger->info("Successfully processed " . $newRssArticlesCount . " new RSS articles from " . $feedName . ".");
            } else {
                $logger->error("Failed to parse RSS feed: " . $feedUrl);
            }
        }
    }

    $logger->info("Script finished successfully.");

} catch (Exception $e) {
    $errorMessage = "Script execution failed: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine();
    $logger->error($errorMessage);
    $notifier->sendErrorNotification($errorMessage);
    exit(1); // Exit with error code
}

// End output buffering and discard output for cronjob compatibility
ob_end_clean();

?>
