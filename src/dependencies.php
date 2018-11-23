<?php

use Slim\Container;
use Doctrine\ORM\EntityManager;
use Infotrip\Domain\Repository\HotelRepository;
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function (Container $container) {
    $settings = $container->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function (Container $container) {
    $settings = $container->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

$container[\Infotrip\HotelParser\BookingComParser::class] = function (Container $container) {
    $bookingParser = new \Infotrip\HotelParser\BookingComParser(
        $container->get('cachebookingHotel')
    );

    return $bookingParser;
};

$container['cachebookingHotel'] = function (Container $container) {
    $cache = new \Desarrolla2\Cache\Adapter\File($container->get('settings')['cache']['bookingHotelCacheDir']);
    $cache->setOption('ttl', $container->get('settings')['cache']['bookingHotelCacheTTL']);
    return $cache;
};

$container[\Infotrip\HotelParser\AvailabilityChecker\BookingComAvailabilityChecker::class] = function (Container $container) {
    $service = new \Infotrip\HotelParser\AvailabilityChecker\BookingComAvailabilityChecker();
    return $service;
};

$container[\Infotrip\HotelParser\ImageCatcher\ImageCatcher::class] = function (Container $container) {
    $service = new \Infotrip\HotelParser\ImageCatcher\ImageCatcher(
        $container->get('settings')['cache']['bookingImagesCacheDir']
    );
    return $service;
};

$container[\Infotrip\ViewHelpers\RouteHelper::class] = function (Container $container) {

    return function($request) use ($container) {
        $routeHelper = new \Infotrip\ViewHelpers\RouteHelper(
            $container->get('router'),
            $request
        );

        return $routeHelper;
    };
};

$container['viewHelpers'] = function (Container $container) {

    return function($request) use ($container) {

        $routeHelper = $container->get(\Infotrip\ViewHelpers\RouteHelper::class);
        $routeHelper = $routeHelper($request);

        $randomUrlsHelper = new \Infotrip\ViewHelpers\RandomUrlsHelper($routeHelper);

        return array(
            'routeHelper' => $routeHelper,
            'randomUrlsHelper' => $randomUrlsHelper,
        );
    };
};

// doctrine
require_once APP_ROOT . '/dependencies-doctrine.php';

$container[HotelRepository::class] = function ($container) {
    /** @var EntityManager $entityManager */
    $entityManager = $container[EntityManager::class];
    return  $entityManager
        ->getRepository('Infotrip\Domain\Entity\Hotel');
};

$container[\Infotrip\Domain\Repository\ResourceContentRepository::class] = function ($container) {
    /** @var EntityManager $entityManager */
    $entityManager = $container[EntityManager::class];
    return  $entityManager
        ->getRepository('Infotrip\Domain\Entity\ResourceContent');
};

$container[\Infotrip\Utils\UI\Homepage::class] = function (Container $container) {

    return function($request) use ($container) {
        $routeHelperClosure = $container->get(\Infotrip\ViewHelpers\RouteHelper::class);
        /** @var \Infotrip\ViewHelpers\RouteHelper $routeHelper */
        $routeHelper = $routeHelperClosure($request);

        $homepage = new \Infotrip\Utils\UI\Homepage(
            $routeHelper
        );

        return $homepage;
    };

};