<?php

namespace Infotrip\Handler\Action\Api;

use Infotrip\ApiProvider\AvailabilityRequestFactory;
use \Infotrip\ApiProvider\IAvailabilityProviderAgregator;
use Infotrip\Handler\Action\Action;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class ApiAvailabilityProviderAction extends Action
{

    /**
     * @var AvailabilityRequestFactory
     */
    private $availabilityRequestFactory;

    /**
     * @var IAvailabilityProviderAgregator
     */
    private $availabilityProviderAgregator;


    public function __construct(
        ContainerInterface $container
    )
    {
        parent::__construct($container);

        $this->availabilityRequestFactory = $container->get(\Infotrip\ApiProvider\AvailabilityRequestFactory::class);
        $this->availabilityProviderAgregator = $container->get(\Infotrip\ApiProvider\IAvailabilityProviderAgregator::class);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return \Slim\Http\Response|array
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, $args = [])
    {
        $routeHelperNamespace = $this->container->get(\Infotrip\ViewHelpers\RouteHelper::class);
        $this->routerHelper = $routeHelperNamespace($request);

        $availabilityRequest = $this->availabilityRequestFactory->getAvailabilityRequest(
            $args['hotelId'],
            [
                'currency' => $request->getParam('currency'),
                'checkInDate' => $request->getParam('checkInDate'),
                'checkOutDate' => $request->getParam('checkOutDate'),
            ]
        );

        $response = $this->availabilityProviderAgregator->checkAvailability($availabilityRequest);

        echo json_encode(
            $response
        );
    }
}