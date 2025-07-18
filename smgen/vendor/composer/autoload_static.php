<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7a86dea8820b108f74e5667542979444
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'SitemapGenerator\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'SitemapGenerator\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'SitemapGenerator\\Config' => __DIR__ . '/../..' . '/src/Config.php',
        'SitemapGenerator\\SearchEnginePing' => __DIR__ . '/../..' . '/src/SearchEnginePing.php',
        'SitemapGenerator\\SitemapGenerator' => __DIR__ . '/../..' . '/src/SitemapGenerator.php',
        'SitemapGenerator\\UrlCrawler' => __DIR__ . '/../..' . '/src/UrlCrawler.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7a86dea8820b108f74e5667542979444::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7a86dea8820b108f74e5667542979444::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit7a86dea8820b108f74e5667542979444::$classMap;

        }, null, ClassLoader::class);
    }
}
