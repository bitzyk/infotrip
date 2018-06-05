<?php
/**
 * Created by PhpStorm.
 * User: cbitoi
 * Date: 11/05/2018
 * Time: 00:31
 */

namespace Infotrip\HotelParser\AvailabilityChecker;

use Infotrip\Domain\Entity\Hotel;
use Slim\Http\Request;

interface AvailabilityChecker
{

    /**
     * @param Request $request
     * @param Hotel $hotel
     * @return string - availability url
     */
    public function getAvailabilityUrl(
        Request $request,
        Hotel $hotel
    );

}