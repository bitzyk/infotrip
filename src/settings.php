<?php

define('APP_ROOT', __DIR__);
define('RESOURCES_ROOT', realpath(__DIR__ .'/../public_html'));

$env = (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'infotrip') !== false))
    ? 'PROD' : 'DEV';

define('ENV', $env);

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        'doctrine' => [
            // if true, metadata caching is forcefully disabled
            'dev_mode' => true,

            // path where the compiled metadata info will be cached
            // make sure the path exists and it is writable
            'cache_dir' => APP_ROOT . '/../public_html/var/doctrine',

            // you should add any other path containing annotated entity classes
            'metadata_dirs' => [APP_ROOT . '/src/Domain'],

            'connection' => [
                'driver' => 'pdo_mysql',
                'host' => (ENV == 'PROD') ? 'localhost' : '33.33.33.10',
                'port' => 3306,
                'dbname' => (ENV == 'PROD') ? 'infotrip_booking' : 'infotrip',
                'user' => (ENV == 'PROD') ? 'infotrip_infotri' : 'cbitoi',
                'password' => (ENV == 'PROD') ? '98J*KjKIgo=i' : 'arivederci',
                'charset' => 'utf8'
            ]
        ],

        'cache' => [
            'bookingHotelCacheDir' => APP_ROOT . '/../public_html/var/cache/bookingHotel',
            'bookingHotelCacheTTL' => 3600 * 24 * 30, // 30 days
            'bookingImagesCacheDir' => RESOURCES_ROOT . \Infotrip\Domain\Entity\Hotel::CACHE_DIR,
            'bookingImagesCacheTTL' => 3600 * 24 * 365, // 1 year
        ],

    ],
];
