<?php

namespace App\Config;

class ConfigLoader
{
    private $config = [];

    public function __construct(string $configFilePath)
    {
        if (file_exists($configFilePath)) {
            $this->config = require $configFilePath;
        } else {
            // Log error: "Configuration file not found: " . $configFilePath
            die("Configuration file not found: " . $configFilePath); // Critical error
        }
    }

    public function get(string $key, $default = null)
    {
        $parts = explode(".", $key);
        $current = $this->config;

        foreach ($parts as $part) {
            if (isset($current[$part])) {
                $current = $current[$part];
            } else {
                return $default;
            }
        }
        return $current;
    }
}


