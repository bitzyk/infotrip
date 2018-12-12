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

$container['cacheGeneric'] = function (Container $container) {
    $cache = new \Desarrolla2\Cache\Adapter\File($container->get('settings')['cache']['genericCacheDir']);
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

$container[\Infotrip\Utils\JQueryFileUpload\UploadHandler::class] = function (Container $container) {

    return function($uploadDir, $uploadUrl) use ($container) {
        return new \Infotrip\Utils\JQueryFileUpload\UploadHandler(
            [
                'upload_dir' => $uploadDir,
                'upload_url' => $uploadUrl,
            ]
        );
    };
};


// doctrine
require_once APP_ROOT . '/dependencies-doctrine.php';

$container[HotelRepository::class] = function (Container $container) {
    /** @var EntityManager $entityManager */
    $entityManager = $container[EntityManager::class];

    /** @var HotelRepository $repository */
    $repository = $entityManager->getRepository('Infotrip\Domain\Entity\Hotel');

    $repository
        ->setFileCache($container->get('cacheGeneric'));

    return $repository;
};

$container[\Infotrip\Domain\Repository\UserHotelRepository::class] = function (Container $container) {
    /** @var EntityManager $entityManager */
    $entityManager = $container[EntityManager::class];

    /** @var \Infotrip\Domain\Repository\UserHotelRepository $userHotelRepository */
    $userHotelRepository = $entityManager->getRepository('Infotrip\Domain\Entity\UserHotel');

    /** @var HotelRepository $hotelRepository */
    $hotelRepository = $container->get(HotelRepository::class);

    $userHotelRepository
        ->setHotelRepository($hotelRepository);

    return $userHotelRepository;
};

$container[\Infotrip\Domain\Repository\ResourceContentRepository::class] = function ($container) {
    /** @var EntityManager $entityManager */
    $entityManager = $container[EntityManager::class];
    return  $entityManager
        ->getRepository('Infotrip\Domain\Entity\ResourceContent');
};

$container[\Infotrip\Domain\Repository\AuthUserRepository::class] = function ($container) {
    /** @var EntityManager $entityManager */
    $entityManager = $container[EntityManager::class];
    return  $entityManager
        ->getRepository('Infotrip\Domain\Entity\AuthUser');
};


$container[\Infotrip\Utils\Google\Recaptcha\V2::class] = function (Container $container) {
    return new \Infotrip\Utils\Google\Recaptcha\V2();
};

$container[\PHPAuth\Config::class] = function (Container $container) {

    /** @var \Doctrine\ORM\EntityManager $em */
    $em = $container->get(\Doctrine\ORM\EntityManager::class);

    /** @var \PDO $emWrappedConnection */
    $emWrappedConnection = $em->getConnection()->getWrappedConnection();

    return new \PHPAuth\Config($emWrappedConnection);
};

$container[\PHPAuth\Auth::class] = function (Container $container) {

    /** @var \Doctrine\ORM\EntityManager $em */
    $em = $container->get(\Doctrine\ORM\EntityManager::class);

    /** @var \PDO $emWrappedConnection */
    $emWrappedConnection = $em->getConnection()->getWrappedConnection();

    $config = $container->get(\PHPAuth\Config::class);

    return new \PHPAuth\Auth($emWrappedConnection, $config);
};


$container[\Infotrip\Utils\UI\Homepage::class] = function (Container $container) {

    return function($request) use ($container) {
        $routeHelperClosure = $container->get(\Infotrip\ViewHelpers\RouteHelper::class);
        /** @var \Infotrip\ViewHelpers\RouteHelper $routeHelper */
        $routeHelper = $routeHelperClosure($request);

        $homepage = new \Infotrip\Utils\UI\Homepage(
            $routeHelper,
            $container->get(HotelRepository::class),
            $container->get('cacheGeneric')
        );

        return $homepage;
    };

};

$container[\Infotrip\Utils\UI\Admin\AdminMenu::class] = function (Container $container) {

    return function($request) use ($container) {

        $routeHelperClosure = $container->get(\Infotrip\ViewHelpers\RouteHelper::class);
        /** @var \Infotrip\ViewHelpers\RouteHelper $routeHelper */
        $routeHelper = $routeHelperClosure($request);

        $service = new \Infotrip\Utils\UI\Admin\AdminMenu(
            $routeHelper
        );
        return $service;
    };
};

$container[\Infotrip\Utils\UI\Admin\AdminBreadcrumb::class] = function (Container $container) {
    return function($request) use ($container) {

        $adminMenuClosure = $container->get(\Infotrip\Utils\UI\Admin\AdminMenu::class);

        /** @var \Infotrip\Utils\UI\Admin\AdminMenu $adminMenu */
        $adminMenu = $adminMenuClosure($request);

        $service = new \Infotrip\Utils\UI\Admin\AdminBreadcrumb($adminMenu);
        return $service;
    };
};

$container[\Infotrip\Utils\UI\Admin\Admin::class] = function (Container $container) {
    return function($request) use ($container) {

        $adminMenuClosure = $container->get(\Infotrip\Utils\UI\Admin\AdminMenu::class);

        /** @var \Infotrip\Utils\UI\Admin\AdminMenu $adminMenu */
        $adminMenu = $adminMenuClosure($request);

        $adminBreadcrumbClosure = $container->get(\Infotrip\Utils\UI\Admin\AdminBreadcrumb::class);

        /** @var \Infotrip\Utils\UI\Admin\AdminBreadcrumb $adminBreadcrumb */
        $adminBreadcrumb = $adminBreadcrumbClosure($request);

        $service = new \Infotrip\Utils\UI\Admin\Admin($adminMenu, $adminBreadcrumb);
        return $service;
    };
};