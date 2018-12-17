<?php

namespace Infotrip\Handler\Action\Agoda;

use Infotrip\Handler\Action\AbstractRootAdminPageAction;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class AdminRootAbstractAgoda extends AbstractRootAdminPageAction
{

    /**
     * @var \Infotrip\Service\Agoda\AgodaService
     */
    protected $agodaService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->agodaService = $container
            ->get(\Infotrip\Service\Agoda\AgodaService::class);

    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return array|\Psr\Http\Message\ResponseInterface|Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, $args = [])
    {
        $parentResponse = parent::__invoke($request, $response, $args);

        if ($parentResponse instanceof \Slim\Http\Response) {
            return $parentResponse;
        } else if(is_array($parentResponse)) {
            $args = $parentResponse;
        }

        return $args;
    }
}