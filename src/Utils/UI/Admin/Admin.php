<?php

namespace Infotrip\Utils\UI\Admin;


class Admin
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
     * @return AdminMenu
     */
    public function getAdminMenu()
    {
        return $this->adminMenu;
    }

    /**
     * @return AdminBreadcrumb
     */
    public function getAdminBreadcrumb()
    {
        return $this->adminBreadcrumb;
    }


}