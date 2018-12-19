<?php

namespace Infotrip\Utils\UI\Admin;

use Infotrip\Domain\Entity\HotelOwnerUser;
use Infotrip\Utils\UI\Admin\Entity\BreadcrumbItem;
use Infotrip\Utils\UI\Admin\Entity\MenuItem;

class Admin implements IAdmin
{
    /**
     * @var AdminMenu
     */
    private $adminMenu;

    /**
     * @var AdminBreadcrumb
     */
    private $adminBreadcrumb;

    public function __construct(
        AdminMenu $adminMenu,
        AdminBreadcrumb $adminBreadcrumb
    )
    {
        $this->adminMenu = $adminMenu;
        $this->adminBreadcrumb = $adminBreadcrumb;
    }

    /**
     * @return MenuItem[]
     */
    public function getMenu(
        HotelOwnerUser $hotelOwnerUser
    )
    {
        // hide delegation
        return $this->adminMenu->getMenu($hotelOwnerUser);
    }

    /**
     * @param HotelOwnerUser $hotelOwnerUser
     * @return BreadcrumbItem
     */
    public function getBreadcrumb(
        HotelOwnerUser $hotelOwnerUser
    )
    {
        return $this->adminBreadcrumb->buildBreadcrumb($hotelOwnerUser);
    }


}