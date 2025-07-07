<?php

return [
    'database' => [
        'host'     => 'db5017542175.hosting-data.io',
        'username' => 'dbu2905681',
        'password' => '4amtechno321',
        'dbname'   => 'dbs14055385',
    ],
    'api_keys' => [
        'deezer' => 'YOUR_DEEZER_API_KEY',
        'newsapi' => 'YOUR_NEWSAPI_API_KEY',
    ],
    'api_urls' => [
        'deezer_base' => 'https://proxy.4amtechno.com/api/', // Updated as per user's instruction
        'newsapi_base' => 'https://newsapi.org/v2/',
    ],
    'rss_feeds' => [
    'hipodrome_minimal' => 'https://hipodrome.net/tag/minimal-techno/feed/',
    'juno_funky_house'  => 'https://www.juno.co.uk/funky-club-house/feeds/rss/',
    'juno_electro_house'=> 'https://www.juno.co.uk/electro-house/feeds/rss/',
    'promo_house'       => 'https://feeds2.feedburner.com/NewestTracks-HouseReleasePromo',
    'promo_minimal'     => 'https://feeds2.feedburner.com/NewestTracks-MinimalReleasePromo',
    'promo_tech_house'  => 'https://feeds2.feedburner.com/NewestTracks-TechHouseReleasePromo',
'promo_techno'      => 'https://feeds2.feedburner.com/NewestTracks-TechnoReleasePromo',
    // Add more RSS feeds as needed
],
    'proxy' => [
        'enabled' => false, // Keep disabled as per user's instruction
        'host'    => 'proxy.4amtechno.com',
        'port'    => '443',
        'username' => '',
        'password' => '',
    ],
    'logging' => [
        'path' => __DIR__ . '/logs/script.log',
        'level' => 'info', // debug, info, warning, error
    ],
    'email_notification' => [
        'enabled' => false,
        'to'      => 'info4amtechno.com',
        'from'    => 'script_notifications@yourdomain.com',
        'subject' => 'PHP Script Error Notification',
    ],
    'paths' => [
        'static_html_output' => __DIR__ . '/output/html/',
        'static_json_output' => __DIR__ . '/output/json/',
        'image_cache' => __DIR__ . '/cache/images/',
    ],
    'processing' => [
        'duplicate_check_days' => 7, // Check for duplicates within the last 7 days
    ],
    'optional_features' => [
        'caching_enabled' => false,
        'image_processing_enabled' => false,
        'multilanguage_enabled' => false,
    ],
];
