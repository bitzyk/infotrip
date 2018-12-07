<?php

namespace Infotrip\Utils\UI\Admin;


class AdminBreadcrumb
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

    public function buildBreadcrumb()
    {
        echo __METHOD__;
        exit;
    }

}