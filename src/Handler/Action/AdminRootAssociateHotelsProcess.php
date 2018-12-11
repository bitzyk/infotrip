<?php

namespace Infotrip\Handler\Action;

use Infotrip\Domain\Repository\UserHotelRepository;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class AdminRootAssociateHotelsProcess extends AbstractRootAdminPageAction
{
    /**
     * @var UserHotelRepository
     */
    private $userHotelRepository;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->userHotelRepository = $this->container->get(\Infotrip\Domain\Repository\UserHotelRepository::class);

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


        $assocHotels = $this->userHotelRepository
            ->associateHotelsToUser(
                $request->getParam('userId'),
                $request->getParam('hotelIds')
            );

        return $response
            ->withRedirect(
                $this->routerHelper->getAdminRootAssociateHotelsUrl() . '?successMessage=A number of ' . $assocHotels . ' have been associated.',
                301
            );
    }

}