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
     * @param bool $cached
     * @return HotelInfo
     */
    public function parse(
        Hotel $hotel, $cached = true
    );

    /**
     * @param Hotel $hotel
     * @return HotelInfo|null
     */
    public function getCachedEntity(
        Hotel $hotel
    );
}