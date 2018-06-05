<?php
/**
 * Created by PhpStorm.
 * User: cbitoi
 * Date: 10/05/2018
 * Time: 00:48
 */

namespace Infotrip\ViewHelpers;

use Slim\Router;

class RouteHelper
{

    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getHotelUrl($hotelName, $hotelId)
    {
        return $this->router
            ->pathFor('hotelRoute', ['hotelName' => urlencode($hotelName)], ['hid' => $hotelId]);
    }

    public function getCityUrl($cityUnique)
    {
        return $this->router
            ->pathFor('cityRoute', ['cityUnique' => $cityUnique]);
    }

    public function getCountryUrl($countryUnique)
    {
        return $this->router
            ->pathFor('countryRoute', ['countryUnique' => $countryUnique]);
    }

    public function getContinentUrl($continentName)
    {
        $continentUnique = urlencode(strtolower($continentName));

        if (!$continentUnique) {
            $continentUnique = 'europe';
        }

        return $this->router
            ->pathFor('continentRoute', ['continentUnique' => $continentUnique]);
    }

    public function getHotelCheckAvailabilityUrl($hotelName, $hotelId)
    {
        return $this->router
            ->pathFor('hotelCheckAvailabilityRoute', ['hotelName' => urlencode($hotelName)], ['hid' => $hotelId]);
    }

    public function getCacheImagesUrl()
    {
        return $this->router
            ->pathFor('cacheImagesRoute');
    }

    public function getCacheHotelUrl()
    {
        return $this->router
            ->pathFor('cacheHotelRoute');
    }

    public function getHomepageUrl()
    {
        return $this->router
            ->pathFor('homepage');
    }
}