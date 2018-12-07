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

        if ($this->request->getAttribute('route')) {
            $this->routeName = $this->request->getAttribute('route')->getName();
        }
    }

    public function getHotelUrl($hotelName, $hotelId)
    {
        return $this->router
            ->pathFor(self::HOTEL_ROUTE_NAME, ['hotelName' => urlencode($hotelName)], ['hid' => $hotelId]);
    }

    public function getHotelsInUrl($cityUnique = '', $country = '', $continent = '')
    {
        $queryParams = [];

        if($cityUnique) {
            $queryParams['city'] = $cityUnique;
        }

        if($country) {
            $queryParams['country'] = $country;
        }

        if($continent) {
            $queryParams['continent'] = $continent;
        }

        return $this->router
            ->pathFor('hotelsIn', [], $queryParams);
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

    public function getHotelsSearchUrl()
    {
        return $this->router
            ->pathFor('hotelsSearchRoute');
    }

    public function getContactUrl()
    {
        return $this->router
            ->pathFor('contact');
    }

    public function getAboutUrl()
    {
        return $this->router
            ->pathFor('about');
    }

    public function getTermsUrl()
    {
        return $this->router
            ->pathFor('terms');
    }

    public function getPrivacyUrl()
    {
        return $this->router
            ->pathFor('privacy');
    }

    /**
     * @return bool
     */
    public function isHotelPage()
    {
        return $this->routeName === self::HOTEL_ROUTE_NAME;
    }


    public function getListCountriesUrl($continentName)
    {
        return $this->router
            ->pathFor('listContries', [
                'continentName' => $continentName,
            ]);
    }

    public function getListCitiesUrl($countryId, $countryName)
    {
        return $this->router
            ->pathFor('listCities', [
                'countryName' => urlencode($countryName),
                'countryId' => urlencode($countryId),
            ]);
    }

    public function getListHotelsUrl($city)
    {
        return $this->router
            ->pathFor('listHotels', [
                'city' => urlencode($city)
            ]);
    }


    public function getHotelOwnerLoginRegisterUrl()
    {
        return $this->router
            ->pathFor('hotelOwnerLoginRegister');
    }

    public function getHotelOwnerRegisterUrl()
    {
        return $this->router
            ->pathFor('hotelOwnerRegister');
    }

    public function getHotelOwnerLoginUrl()
    {
        return $this->router
            ->pathFor('hotelOwnerLogin');
    }

    public function getHotelOwnerActivateLandingUrl()
    {
        return $this->router
            ->pathFor('hotelOwnerActivateLanding');
    }

    public function getHotelOwnerActivateUrl()
    {
        return $this->router
            ->pathFor('hotelOwnerActivate');
    }

    public function getHotelOwnerAdminDashbordUrl()
    {
        return $this->router
            ->pathFor('hotelOwnerAdminDashbord');
    }

    public function getHotelOwnerAdminLogoutUrl()
    {
        return $this->router
            ->pathFor('hotelOwnerAdminLogout');
    }
}