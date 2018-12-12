<?php

namespace Infotrip\Handler\Action;

use Infotrip\Domain\Entity\Hotel;
use Infotrip\Domain\Repository\HotelRepository;
use Infotrip\Utils\JQueryFileUpload\UploadHandler;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class AdminEditHotelImageHandlerProcess extends AbstractAdminPageAction
{

    /**
     * @var HotelRepository
     */
    private $hotelRepository;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->hotelRepository = $this->container->get(HotelRepository::class);
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

        if (! $this->hotelOwnerUser
            ->hotelIdIsOneOfAssociatedHotels($args['hotelId'])) {
            throw new \Exception('Invalid request');
        }

        $hotel = $this->hotelRepository->getHotel($args['hotelId']);

        $uploadHandlerClosure = $this->container->get(\Infotrip\Utils\JQueryFileUpload\UploadHandler::class);

        error_reporting(E_ALL | E_STRICT);
        $uploadHandlerClosure(
            $request,
            $hotel->getAdministrableImagePath() .'/',
            $hotel->getAdministrableImageUrl() . '/',
            $hotel->getId()
        );
    }

}