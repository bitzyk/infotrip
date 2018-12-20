<?php

namespace Infotrip\HotelParser\AvailabilityChecker;

use Infotrip\Domain\Entity\Hotel;

interface AvailabilityChecker
{

    /**
     * @param array $params
     * @param Hotel $hotel
     * @return string - availability url
     */
    public function getAvailabilityUrl(
        array $params,
        Hotel $hotel
    );

}