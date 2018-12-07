<?php

namespace Infotrip\Utils\UI\Admin;


class Admin
{
    /**
     * @var AdminMenu
     */
    private $adminMenu;

    public function __construct(
        AdminMenu $adminMenu
    )
    {
        $this->adminMenu = $adminMenu;
    }

    /**
     * @return AdminMenu
     */
    public function getAdminMenu()
    {
        return $this->adminMenu;
    }


}