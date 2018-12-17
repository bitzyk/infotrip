<?php

namespace Infotrip\Utils\UI\Admin;

use Infotrip\Domain\Entity\HotelOwnerUser;
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
    public function getMenu(
        HotelOwnerUser $hotelOwnerUser
    )
    {
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
                    (new MenuItem())
                        ->setLabel('Edit Hotel')
                        ->setRouteName('hotelOwnerAdminEditHotel')
                        ->setCurrentRouteName($this->routeHelper->getRouteName())
                        ->setVisible(false)
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
                ->setLabel('Admin Setting')
                ->setRouteName('hotelOwnerAdminAccountSettings')
                ->setLink($this->routeHelper->buildUrlForRoute('hotelOwnerAdminAccountSettings'))
                ->setCssClass('fa-cogs')
                ->setCurrentRouteName($this->routeHelper->getRouteName())
            ,
        ];

        if ($hotelOwnerUser->isRootUser()) {
            $menu[] = (new MenuItem())
                ->setLabel('Root user')
                ->setHasSubmenu(true)
                ->setCssClass('fa-cogs')
                ->setCurrentRouteName($this->routeHelper->getRouteName())
                ->setSubmenu(
                    [
                        (new MenuItem())
                            ->setLabel('Associate hotels to user')
                            ->setRouteName('adminRootAssociateHotels')
                            ->setLink($this->routeHelper->buildUrlForRoute('adminRootAssociateHotels'))
                            ->setCurrentRouteName($this->routeHelper->getRouteName())
                        ,
                        (new MenuItem())
                            ->setLabel('Agoda - import hotels')
                            ->setRouteName('adminRootAgodaImportHotels')
                            ->setLink($this->routeHelper->buildUrlForRoute('adminRootAgodaImportHotels'))
                            ->setCurrentRouteName($this->routeHelper->getRouteName())
                        ,
                        (new MenuItem())
                            ->setLabel('Agoda - associate hotels')
                            ->setRouteName('adminRootAgodaAssociateHotels')
                            ->setLink($this->routeHelper->buildUrlForRoute('adminRootAgodaAssociateHotels'))
                            ->setCurrentRouteName($this->routeHelper->getRouteName())
                    ]
                );
        }

        $menu[] = (new MenuItem())
            ->setLabel('Logout')
            ->setRouteName('hotelOwnerAdminLogout')
            ->setLink($this->routeHelper->buildUrlForRoute('hotelOwnerAdminLogout'))
            ->setCssClass('fa-sign-in')
            ->setCurrentRouteName($this->routeHelper->getRouteName());


        return $menu;
    }
}