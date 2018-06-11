<?php
/**
 * Created by PhpStorm.
 * User: cbitoi
 * Date: 10/05/2018
 * Time: 00:48
 */

namespace Infotrip\ViewHelpers;

use Slim\Http\Request;
use Slim\Router;

class RouteHelper
{

    /**
     * @var Router
     */
    private $router;
    /**
     * @var Request
     */
    private $request;

    /**
     * @var string
     */
    private $routeName;

    const HOTEL_ROUTE_NAME = 'hotelRoute';

    public function __construct(Router $router, Request $request)
    {
        $this->router = $router;
        $this->request = $request;

        if ($request->getAttribute('route')) {
            $this->routeName = $request->getAttribute('route')->getName();
        }
    }

    public function getHotelUrl($hotelName, $hotelId)
    {
        return $this->router
            ->pathFor(self::HOTEL_ROUTE_NAME, ['hotelName' => urlencode($hotelName)], ['hid' => $hotelId]);
    }

    public function getHotelsInUrl($cityUnique)
    {
        return $this->router
            ->pathFor('hotelsIn', [], ['city' => $cityUnique]);
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

    public function getContactUrl()
    {
        return $this->router
            ->pathFor('contact');
    }

    /**
     * @return bool
     */
    public function isHotelPage()
    {
        return $this->routeName === self::HOTEL_ROUTE_NAME;
    }
}