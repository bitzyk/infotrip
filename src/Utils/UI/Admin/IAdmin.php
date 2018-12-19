<?php

namespace Infotrip\Utils\UI\Admin;

use Infotrip\Domain\Entity\HotelOwnerUser;
use Infotrip\Utils\UI\Admin\Entity\BreadcrumbItem;
use Infotrip\Utils\UI\Admin\Entity\MenuItem;

interface IAdmin
{
    /**
     * @param HotelOwnerUser $hotelOwnerUser
     * @return MenuItem[]
     */
    public function getMenu(
        HotelOwnerUser $hotelOwnerUser
    );

    /**
     * @param HotelOwnerUser $hotelOwnerUser
     * @return BreadcrumbItem
     */
    public function getBreadcrumb(
        HotelOwnerUser $hotelOwnerUser
    );
}