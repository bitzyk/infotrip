<?php

namespace Infotrip\Handler\Action;

use Infotrip\Domain\Entity\Hotel;
use Infotrip\Domain\Repository\HotelRepository;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class AdminAcountSettingsProcess extends AbstractAdminPageAction
{

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
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

        echo 'aici';
        print_r(
            $request->getParsedBody()
        );
        exit;

        // hydrate hotel
        $hotelToEdit = $this->hydrateUpdatedHotel(
            $this->hotelRepository->getHotel($args['hotelId']),
            $request->getParsedBody()
        );

        // update hotel
        $this->hotelRepository
            ->updateHotel($hotelToEdit);

        // redirect
        return $response
            ->withRedirect(
                $this->routerHelper->getHotelOwnerAdminDashbordUrl() . '?successMessage=The hotel have been updated.',
                301
            );
    }

    /**
     * @param Hotel $fromHotel
     * @param array $updatedInfo
     * @return Hotel
     */
    private function hydrateUpdatedHotel(
        Hotel $fromHotel,
        array $updatedInfo
    )
    {
        $fromHotel
            ->setName($updatedInfo['hotelName'])
            ->setDescEn($updatedInfo['hotelDescription'])
            ->setAddress($updatedInfo['hotelAddress'])
            ->setZip($updatedInfo['hotelZipCode'])
            ->setMinrate($updatedInfo['hotelPriceMin']);

        return $fromHotel;
    }
}