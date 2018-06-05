<?php
/**
 * Created by PhpStorm.
 * User: cbitoi
 * Date: 08/05/2018
 * Time: 19:22
 */

namespace Infotrip\HotelParser;


use Infotrip\Domain\Entity\Hotel;
use Infotrip\HotelParser\Entity\HotelInfo;

interface HotelParser
{

    /**
     * @param Hotel $hotel
     * @return HotelInfo
     */
    public function parse(
        Hotel $hotel
    );

    /**
     * @param Hotel $hotel
     * @return HotelInfo|null
     */
    public function getCachedEntity(
        Hotel $hotel
    );
}