<?php

namespace Infotrip\Utils\UI\Admin;


use Infotrip\Utils\UI\Admin\Entity\BreadcrumbItem;
use Infotrip\Utils\UI\Admin\Entity\MenuItem;

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
        $menu = $this->adminMenu
            ->getMenu();

        // set parent breadcrumb
        $breadcrumb = new BreadcrumbItem();
        $breadcrumb
            ->setLabel('Home')
            ->setCssIcon('fa-home')
        ;

        // set child breadcrumb
        foreach ($menu as $menuItem) {
            if ($menuItem->isActive()) {

                $next = $this
                    ->buildBreadcrumbForMenuItem($menuItem, $breadcrumb);

                if ($next instanceof BreadcrumbItem)
                    $breadcrumb
                        ->setNext($next)
                    ;

                break;
            }
        }

        return $breadcrumb;
    }

    private function buildBreadcrumbForMenuItem(
        MenuItem $menuItem,
        BreadcrumbItem $previousBreadcrumbItem
    )
    {
        if (! $menuItem->isActive()) {
            return null;
        }

        $breadcrumbItem = (new BreadcrumbItem());

        $breadcrumbItem
            ->setLabel($menuItem->getLabel())
            ->setLink($menuItem->getLink())
            ->setPrevious($previousBreadcrumbItem);
        ;

        if ($menuItem->getSubmenu()) {
            foreach ($menuItem->getSubmenu() as $submenu) {

                $next = $this
                    ->buildBreadcrumbForMenuItem($submenu, $previousBreadcrumbItem);

                if ($next instanceof BreadcrumbItem) {
                    $breadcrumbItem->setNext($next);
                }
            }
        }


        return $breadcrumbItem;
    }

}