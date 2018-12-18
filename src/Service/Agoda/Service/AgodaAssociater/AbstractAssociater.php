<?php

namespace Infotrip\Service\Agoda\Service\AgodaAssociater;


use Infotrip\Domain\Repository\AgodaHotelRepository;
use Infotrip\Domain\Repository\HotelAssocRepository;
use Infotrip\Domain\Repository\HotelRepository;
use Infotrip\Service\Agoda\Entities\AgodaAssociateResponse;

abstract class AbstractAssociater
{
    /**
     * @var AgodaHotelRepository
     */
    protected $agodaHotelRepository;

    /**
     * @var HotelAssocRepository
     */
    protected $hotelAssocRepository;

    /**
     * @var HotelRepository
     */
    protected $hotelRepository;

    abstract public function associate(
        AgodaAssociateResponse $agodaAssociateResponse
    );

    public function __construct(
        AgodaHotelRepository $agodaHotelRepository,
        HotelAssocRepository $hotelAssocRepository,
        HotelRepository $hotelRepository
    )
    {
        $this->agodaHotelRepository = $agodaHotelRepository;
        $this->hotelAssocRepository = $hotelAssocRepository;
        $this->hotelRepository = $hotelRepository;
    }

}