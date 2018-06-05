<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Slim\Container;

require_once __DIR__ . '/vendor/autoload.php';

/** @var Container $container */
$container = new Container(require __DIR__ . '/src/settings.php');

// doctrine
require_once APP_ROOT . '/dependencies-doctrine.php';

ConsoleRunner::run(
    ConsoleRunner::createHelperSet($container[EntityManager::class])
);