<?php

namespace Infotrip\Service\HotelOwner;

use Infotrip\Domain\Entity\HotelOwnerUser;
use Infotrip\Domain\Repository\HotelRepository;
use Infotrip\Domain\Repository\UserHotelRepository;
use Infotrip\Utils\RecursiveDirectoryRemoval;

class HotelOwnerService
{

    /**
     * @var HotelRepository
     */
    private $hotelRepository;

    /**
     * @var UserHotelRepository
     */
    private $userHotelRepository;

    /**
     * @var RecursiveDirectoryRemoval
     */
    private $directoryRemoval;

    public function __construct(
        HotelRepository $hotelRepository,
        UserHotelRepository $userHotelRepository,
        RecursiveDirectoryRemoval $directoryRemoval

    )
    {
        $this->hotelRepository = $hotelRepository;
        $this->userHotelRepository = $userHotelRepository;
        $this->directoryRemoval = $directoryRemoval;
    }

    /**
     * @param HotelOwnerUser $hotelOwnerUser
     * @param $hotelId
     * @throws \Exception
     */
    public function deleteHotel(
        HotelOwnerUser $hotelOwnerUser,
        $hotelId
    )
    {
        $hotelToDelete = $this->hotelRepository->getHotel($hotelId);

        // delete from hotel repository by setting the visibility to 0
        $this->hotelRepository->deleteHotel($hotelToDelete);

        // delete the hotel association
        $this->userHotelRepository->deleteHotelAssociation($hotelToDelete->getId(), $hotelOwnerUser->getUserId());

        // delete administrable images directory and all the images
        $this->directoryRemoval->removeDirectory($hotelToDelete->getAdministrableImagePath());
    }
}