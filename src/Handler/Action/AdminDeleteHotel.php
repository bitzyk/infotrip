<?php

namespace Infotrip\Handler\Action;

use Infotrip\Domain\Repository\HotelRepository;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class AdminDeleteHotel extends AbstractAdminPageAction
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
     * @return array|Response
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

        // delete the hotel
        $this->hotelOwnerUser->deleteHotel($args['hotelId']);

        return $response
            ->withRedirect(
                $this->routerHelper->getHotelOwnerAdminDashbordUrl() . '?successMessage=The hotel have been deleted.',
                301
            );
    }

}