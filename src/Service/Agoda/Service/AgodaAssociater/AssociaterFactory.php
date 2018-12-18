<?php

namespace Infotrip\Service\Agoda\Service\AgodaAssociater;

use Infotrip\Domain\Repository\AgodaHotelRepository;
use Infotrip\Domain\Repository\HotelAssocRepository;
use Infotrip\Domain\Repository\HotelRepository;

class AssociaterFactory
{
    /**
     * @var AgodaHotelRepository
     */
    private $agodaHotelRepository;

    /**
     * @var HotelAssocRepository
     */
    private $hotelAssocRepository;

    /**
     * @var HotelRepository
     */
    private $hotelRepository;

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


    /**
     * @param $level
     * @return AbstractAssociater
     * @throws \Exception
     */
    public function getAssociater($level)
    {
        if ( ! in_array($level, [1, 2, 3])) {
            throw new \Exception('Invalid request');
        }

        $namespace = '\Infotrip\Service\Agoda\Service\AgodaAssociater\AssociaterLevel' . $level;

        return new $namespace(
            $this->agodaHotelRepository,
            $this->hotelAssocRepository,
            $this->hotelRepository
        );
    }
}