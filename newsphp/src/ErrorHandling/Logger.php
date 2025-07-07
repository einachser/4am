<?php

namespace App\ErrorHandling;

class Logger
{
    private $logFilePath;
    private $logLevel;

    private $levels = [
        'debug'   => 0,
        'info'    => 1,
        'warning' => 2,
        'error'   => 3,
    ];

    public function __construct(string $logFilePath, string $logLevel = 'info')
    {
        $this->logFilePath = $logFilePath;
        $this->logLevel = strtolower($logLevel);
        $logDir = dirname($this->logFilePath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }
    }

    public function log(string $message, string $level = 'info')
    {
        $level = strtolower($level);
        if (!isset($this->levels[$level])) {
            $level = 'info'; // Default to info if invalid level is provided
        }

        if ($this->levels[$level] < $this->levels[$this->logLevel]) {
            return; // Don't log if current level is lower than configured level
        }

        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] [" . strtoupper($level) . "] {$message}" . PHP_EOL;
        file_put_contents($this->logFilePath, $logEntry, FILE_APPEND);
    }

    public function debug(string $message) { $this->log($message, 'debug'); }
    public function info(string $message) { $this->log($message, 'info'); }
    public function warning(string $message) { $this->log($message, 'warning'); }
    public function error(string $message) { $this->log($message, 'error'); }
}


