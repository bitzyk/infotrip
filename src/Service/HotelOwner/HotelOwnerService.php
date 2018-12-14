<?php

namespace Infotrip\Service\HotelOwner;

use Infotrip\Domain\Repository\HotelRepository;
use Infotrip\Domain\Repository\UserHotelRepository;

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

    public function __construct(
        HotelRepository $hotelRepository,
        UserHotelRepository $userHotelRepository
    )
    {

        $this->hotelRepository = $hotelRepository;
        $this->userHotelRepository = $userHotelRepository;
    }

    /**
     * @param $hotelId
     * @param $userId
     * @throws \Exception
     */
    public function deleteHotel(
        $hotelId,
        $userId
    )
    {
        // delete from hotel repository by setting the visibility to 0
        $this->hotelRepository->deleteHotel($hotelId);
        $this->userHotelRepository->deleteHotelAssociation($hotelId, $userId);
    }
}