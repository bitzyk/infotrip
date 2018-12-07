<?php

namespace Infotrip\Handler\Action;

use Slim\Http\Request;
use Slim\Http\Response;
use Psr\Container\ContainerInterface;

abstract class Action
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Slim\Views\PhpRenderer
     */
    protected $renderer;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->renderer = $this->container->get('renderer');

    }

    abstract public function __invoke(Request $request, Response $response, $args = []);
}