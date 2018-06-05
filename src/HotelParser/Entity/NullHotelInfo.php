<?php
/**
 * Created by PhpStorm.
 * User: cbitoi
 * Date: 14/05/2018
 * Time: 22:33
 */

namespace Infotrip\HotelParser\Entity;


class NullHotelInfo extends HotelInfo
{
    /**
     * @return string
     */
    public function getRatingText()
    {
        return 'loading';
    }


}