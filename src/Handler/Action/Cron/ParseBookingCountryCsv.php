<?php

namespace Infotrip\Handler\Action\Cron;

use Infotrip\Handler\Action\Action;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class ParseBookingCountryCsv extends Action
{

    /**
     * @var \Infotrip\Service\Booking\CountryCsvImporter\ImporterInterface
     */
    private $bookingImporterService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->bookingImporterService = $container->get(\Infotrip\Service\Booking\CountryCsvImporter\ImporterInterface::class);
    }

    public function __invoke(Request $request, Response $response, $args = [])
    {
        $importResult= $this->bookingImporterService->import();

        return $response->withJson(array(
            'success' => $importResult->isStatusSuccess()
        ));
    }

}