<?php

/**
 * PHP Sitemap Generator - Konfigurationsdatei
 * 
 * Diese Datei muss ein Array mit Konfigurationswerten zurückgeben.
 */

return [
    // Basis-Konfiguration
    'base_url' => 'https://example.com',
    'output_dir' => 'output',
    'max_urls' => 50000,
    'max_depth' => 10,
    'delay_between_requests' => 1,
    
    // Aktivierte Sitemap-Typen
    'enabled_sitemap_types' => [
        'xml',
        'html',
        'text',
        'mobile',
        'image',
        'video'
    ],
    
    // XML-Sitemap Einstellungen
    'xml_sitemap_filename' => 'sitemap.xml',
    'xml_sitemap_index_filename' => 'sitemap-index.xml',
    'xml_max_urls_per_file' => 50000,
    'xml_compress' => true,
    
    // HTML-Sitemap Einstellungen
    'html_sitemap_filename' => 'sitemap.html',
    'html_template' => null, // Pfad zu eigenem Template
    'html_group_by_directory' => true,
    
    // Text-Sitemap Einstellungen
    'text_sitemap_filename' => 'sitemap.txt',
    
    // Mobile-Sitemap Einstellungen
    'mobile_sitemap_filename' => 'sitemap-mobile.xml',
    'mobile_user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)',
    
    // Bilder-Sitemap Einstellungen
    'image_sitemap_filename' => 'sitemap-images.xml',
    'image_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
    'image_max_per_page' => 1000,
    
    // Video-Sitemap Einstellungen
    'video_sitemap_filename' => 'sitemap-videos.xml',
    'video_extensions' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'],
    
    // URL-Crawler Einstellungen
    'crawl_external_links' => false,
    'follow_redirects' => true,
    'ignore_query_parameters' => false,
    'exclude_patterns' => [
        '/admin/*',
        '/wp-admin/*',
        '/login',
        '/logout',
        '*.pdf',
        '*.doc',
        '*.docx'
    ],
    'include_patterns' => [],
    
    // Suchmaschinen-Ping
    'ping_search_engines' => true,
    'ping_google' => true,
    'ping_bing' => true,
    'ping_yandex' => false,
    'ping_baidu' => false,
    
    // Logging
    'enable_logging' => true,
    'log_file' => 'logs/sitemap-generator.log',
    'log_level' => 'info',
    
    // Caching
    'enable_caching' => true,
    'cache_duration' => 3600, // 1 Stunde
    'cache_file' => 'cache/urls.cache',
    
    // HTTP-Einstellungen
    'user_agent' => 'Sitemap Generator Bot 1.0',
    'timeout' => 30,
    'follow_robots_txt' => true,
    
    // Prioritäten und Änderungsfrequenzen
    'default_priority' => 0.5,
    'default_changefreq' => 'weekly',
    'priority_rules' => [
        '/' => 1.0,
        '/about' => 0.8,
        '/contact' => 0.7,
        '/blog' => 0.9,
        '/products' => 0.8,
        '/services' => 0.8,
        '/portfolio' => 0.7,
        '/news' => 0.9,
        '/events' => 0.6
    ],
    'changefreq_rules' => [
        '/' => 'daily',
        '/blog' => 'daily',
        '/news' => 'hourly',
        '/products' => 'weekly',
        '/about' => 'monthly',
        '/contact' => 'monthly'
    ]
];

