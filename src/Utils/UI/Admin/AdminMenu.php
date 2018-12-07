<?php

namespace Infotrip\Utils\UI\Admin;

use Infotrip\Utils\UI\Admin\Entity\MenuItem;

class AdminMenu
{
    /**
     * @var \Infotrip\ViewHelpers\RouteHelper
     */
    private $routeHelper;

    public function __construct(
        \Infotrip\ViewHelpers\RouteHelper $routeHelper
    )
    {
        $this->routeHelper = $routeHelper;
    }


    /**
     * @return MenuItem[]
     */
    public function getMenu()
    {

//        var_dump(
//            $this->routeHelper->getRouteName()
//        ); exit;
        $menu = [
            (new MenuItem())
            ->setLabel('Hotels')
            ->setHasSubmenu(true)
            ->setCssClass('fa-h-square')
            ->setCurrentRouteName($this->routeHelper->getRouteName())
            ->setSubmenu(
                [
                    (new MenuItem())
                    ->setLabel('All Hotels')
                    ->setRouteName('hotelOwnerAdminDashbord')
                    ->setLink($this->routeHelper->buildUrlForRoute('hotelOwnerAdminDashbord'))
                    ->setCurrentRouteName($this->routeHelper->getRouteName())
                    ,
                    (new MenuItem())
                        ->setLabel('Add new Hotel')
                        ->setRouteName('hotelOwnerAdminAddNewHotel')
                        ->setLink($this->routeHelper->buildUrlForRoute('hotelOwnerAdminAddNewHotel'))
                        ->setCurrentRouteName($this->routeHelper->getRouteName())
                    ,
                ]
            ),
            (new MenuItem())
                ->setLabel('Social Media')
                ->setRouteName('hotelOwnerAdminSocialMedia')
                ->setLink($this->routeHelper->buildUrlForRoute('hotelOwnerAdminSocialMedia'))
                ->setCssClass('fa-plus-square-o')
                ->setCurrentRouteName($this->routeHelper->getRouteName())
            ,
            (new MenuItem())
                ->setLabel('Logout')
                ->setRouteName('hotelOwnerAdminLogout')
                ->setLink($this->routeHelper->buildUrlForRoute('hotelOwnerAdminLogout'))
                ->setCssClass('fa-sign-in')
                ->setCurrentRouteName($this->routeHelper->getRouteName())
            ,
        ];

        return $menu;
    }
}