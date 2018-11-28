<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

// hotel page
$app->get('/hotel/{hotelName}', function (Request $request, Response $response, array $args) {

    /** @var $this \Slim\Container */

    // get hotelId param
    $hotelId = (int) $request->getParam('hid');

    // sanitize check
    if (! $hotelId) {
        return $response
            ->withStatus(400)
            ->withHeader('Content-Type', 'text/html')
            ->write('Bad Request');
    }
    /** @var $hotelRepository \Infotrip\Domain\Repository\HotelRepository */
    $hotelRepository = $this->get(\Infotrip\Domain\Repository\HotelRepository::class);

    // get hotel for the given hotel url
    try {
        $hotel = $hotelRepository
            ->getHotel($hotelId);
    } catch (\Exception $e) {
        return $response
            ->withStatus(400)
            ->withHeader('Content-Type', 'text/html')
            ->write($e->getMessage());
    }

    // safe check hotel name against url
    if (strtolower($hotel->getName()) != strtolower(urldecode($args['hotelName']))) {

        $routeHelper = $this->get(\Infotrip\ViewHelpers\RouteHelper::class);
        /** @var \Infotrip\ViewHelpers\RouteHelper $routerHelper */
        $routerHelper = $routeHelper($request);

        // redirect to the correct url
        return $response
            ->withRedirect(
                $routerHelper->getHotelUrl(
                $hotel->getName(),
                $hotel->getId()
            ), 301
            );

    }

    /** @var \Infotrip\HotelParser\BookingComParser $bookingParser */
    $bookingParser = $this->get(\Infotrip\HotelParser\BookingComParser::class);

    // request external hotel info (cached)
    $hotel
        ->requestExternalHotelInfo($bookingParser, true);


    $args['hotel'] = $hotel;
    $viewHelpers = $this->get('viewHelpers');
    $args['viewHelpers'] = $viewHelpers($request);


    // Render index view
    return $this->renderer->render($response, 'hotel/index.phtml', $args);

})->setName('hotelRoute');

$app->get('/hotel-redirect/{hotelName}/{hotelCity}', function (Request $request, Response $response, array $args) {

    /** @var $this \Slim\Container */

    $hotelName = $args['hotelName'];
    $hotelCity = $args['hotelCity'];

    // sanitize check
    if (! $hotelName || ! $hotelCity) {
        return $response
            ->withStatus(400)
            ->withHeader('Content-Type', 'text/html')
            ->write('Bad Request');
    }
    /** @var $hotelRepository \Infotrip\Domain\Repository\HotelRepository */
    $hotelRepository = $this->get(\Infotrip\Domain\Repository\HotelRepository::class);

    $routeHelper = $this->get(\Infotrip\ViewHelpers\RouteHelper::class);
    /** @var \Infotrip\ViewHelpers\RouteHelper $routerHelper */
    $routerHelper = $routeHelper($request);


    // get hotel for the given hotel url
    try {
        $hotels = $hotelRepository
            ->getHotelsInArea(
                [
                    'city' => $hotelCity,
                    'hotelName' => $hotelName,
                ]
            );

       if (
           count($hotels) &&
           ($hotel = $hotels[0]) &&
            $hotel instanceof \Infotrip\Domain\Entity\Hotel
       ) {
           return $response
               ->withRedirect($routerHelper->getHotelUrl(
                   $hotel->getName(),
                   $hotel->getId()
               ), 301);

           exit;
       }

    } catch (\Exception $e) {
        return $response
            ->withStatus(400)
            ->withHeader('Content-Type', 'text/html')
            ->write($e->getMessage());
    }

    return $response
        ->withStatus(400)
        ->withHeader('Content-Type', 'text/html')
        ->write('Bad Request');

})->setName('hotelRedirect');


// check availabily redirecter
$app->get('/hotel/check-availability/{hotelName}', function (Request $request, Response $response, array $args) use ($app) {
    /** @var $this \Slim\Container */

    // get hotelId
    $hotelId = (int) $request->getParam('hid');

    // sanitize check
    if (! $hotelId) {
        return $response
            ->withStatus(400)
            ->withHeader('Content-Type', 'text/html')
            ->write('Bad Request');
    }


    /** @var $hotelRepository \Infotrip\Domain\Repository\HotelRepository */
    $hotelRepository = $this->get(\Infotrip\Domain\Repository\HotelRepository::class);

    // get hotel for the given hotell url
    try {
        $hotel = $hotelRepository
            ->getHotel($hotelId);
    } catch (\Exception $e) {
        return $response
            ->withStatus(400)
            ->withHeader('Content-Type', 'text/html')
            ->write($e->getMessage());
    }

    /** @var \Infotrip\HotelParser\AvailabilityChecker\BookingComAvailabilityChecker $bookingComAvailabilityChecker */
    $bookingComAvailabilityChecker = $this->get(\Infotrip\HotelParser\AvailabilityChecker\BookingComAvailabilityChecker::class);

    // compute availability url
    $availabilityUrl = $bookingComAvailabilityChecker->getAvailabilityUrl(
        $request,
        $hotel
    );

    // redirect to booking.com
    return $response
        ->withRedirect($availabilityUrl, 301);


})->setName('hotelCheckAvailabilityRoute');


$app->post('/cache-images', function (Request $request, Response $response, array $args) {

    /** @var $this \Slim\Container */


    // get non cached images
    $nonCachedImages = (array) $request->getParam('nonCachedImages');

    if (empty($nonCachedImages)) {
        $response->withStatus(400)
            ->withHeader('Content-Type', 'text/html')
            ->write('Bad Request');
    }

    /** @var \Infotrip\HotelParser\ImageCatcher\ImageCatcher $imageCatcher */
    $imageCatcher = $this->get(\Infotrip\HotelParser\ImageCatcher\ImageCatcher::class);

    // catch & cache non cached images
    $imageCatcher
        ->catchImages(
            $nonCachedImages
        );
})->setName('cacheImagesRoute');


$app->post('/cache-hotel', function (Request $request, Response $response, array $args) {

    /** @var $this \Slim\Container */

    // get hotelId param
    $hotelId = (int) $request->getParam('hid');
    // sanitize check
    if (! $hotelId) {
        return $response
            ->withStatus(400)
            ->withHeader('Content-Type', 'text/html')
            ->write('Bad Request');
    }
    /** @var $hotelRepository \Infotrip\Domain\Repository\HotelRepository */
    $hotelRepository = $this->get(\Infotrip\Domain\Repository\HotelRepository::class);

    // get hotel for the given hotel url
    try {
        $hotel = $hotelRepository
            ->getHotel($hotelId);
    } catch (\Exception $e) {
        return $response
            ->withStatus(400)
            ->withHeader('Content-Type', 'text/html')
            ->write($e->getMessage());
    }

    /** @var \Infotrip\HotelParser\BookingComParser $bookingParser */
    $bookingParser = $this->get(\Infotrip\HotelParser\BookingComParser::class);

    // request external hotel info (cached)
    $hotel
        ->requestExternalHotelInfo($bookingParser, $cached = false);

    return $response
        ->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($hotel));

})->setName('cacheHotelRoute');

$app->get('/hotels-in', function (Request $request, Response $response, array $args) {

    /** @var $hotelRepository \Infotrip\Domain\Repository\HotelRepository */
    $hotelRepository = $this->get(\Infotrip\Domain\Repository\HotelRepository::class);

    $areaSearch = [
        'city' => $request->getParam('city'),
        'country' => $request->getParam('country'),
        'continent' => $request->getParam('continent'),
    ];
    $hotelsInArea = $hotelRepository
        ->getHotelsInArea($areaSearch);

    $viewHelpers = $this->get('viewHelpers');

    $args['viewHelpers'] = $viewHelpers($request);
    $args['hotels'] = $hotelsInArea;
    $args['areaSearch'] = $areaSearch;

    // Render index view
    return $this->renderer->render($response, 'hotelsIn/index.phtml', $args);

})->setName('hotelsIn');

$app->get('/hotels-search', function (Request $request, Response $response, array $args) {

    /** @var $hotelRepository \Infotrip\Domain\Repository\HotelRepository */
    $hotelRepository = $this->get(\Infotrip\Domain\Repository\HotelRepository::class);

    $term = $request->getParam('term');

    if (! $term) {
        return $response
            ->withStatus(400)
            ->withHeader('Content-Type', 'text/html')
            ->write('Bad Request');
    }

    $hotelsSearchResult = $hotelRepository
        ->getHotelsByTerm($term);

    $viewHelpers = $this->get('viewHelpers');

    $args['viewHelpers'] = $viewHelpers($request);
    $args['hotels'] = $hotelsSearchResult->getHotelsResult();
    $args['hotelsSearchResult'] = $hotelsSearchResult;

    // Render index view
    return $this->renderer->render($response, 'hotelsIn/index.phtml', $args);

})->setName('hotelsSearchRoute');


$app->get('/', function (Request $request, Response $response, array $args) {

    $viewHelpersClosure = $this->get('viewHelpers');
    $homepageClosure = $this->get(\Infotrip\Utils\UI\Homepage::class);

    /** @var \Infotrip\Utils\UI\Homepage $homepage */
    $homepage = $homepageClosure($request);

    $args['viewHelpers'] = $viewHelpersClosure($request);
    $args['topDestinations'] = $homepage->getTopDestinations();
    $args['todayDeal'] = $homepage->getTodayDeal();


    // Render index view
    return $this->renderer->render($response, 'homepage/index.phtml', $args);

})->setName('homepage');

$app->get('/contact', function (Request $request, Response $response, array $args) {
    $viewHelpers = $this->get('viewHelpers');

    $args['viewHelpers'] = $viewHelpers($request);

    // Render index view
    return $this->renderer->render($response, 'contact/index.phtml', $args);


})->setName('contact');

$app->get('/about-infotrip', function (Request $request, Response $response, array $args) {
    $viewHelpers = $this->get('viewHelpers');

    $args['viewHelpers'] = $viewHelpers($request);

    // Render index view
    return $this->renderer->render($response, 'about/index.phtml', $args);
})->setName('about');

$app->get('/terms-and-condition', function (Request $request, Response $response, array $args) {
    $viewHelpers = $this->get('viewHelpers');

    $args['viewHelpers'] = $viewHelpers($request);

    // Render index view
    return $this->renderer->render($response, 'terms/index.phtml', $args);
})->setName('terms');


$app->get('/cookies-and-privacy', function (Request $request, Response $response, array $args) {
    $viewHelpers = $this->get('viewHelpers');

    $args['viewHelpers'] = $viewHelpers($request);

    // Render index view
    return $this->renderer->render($response, 'privacy/index.phtml', $args);
})->setName('privacy');


$app->get('/list-countries/{continentName}', function (Request $request, Response $response, array $args) {

    $continentName = urldecode($args['continentName']);

    $continent = new \Infotrip\Domain\Entity\Continent(null, $continentName);

    /** @var \Doctrine\ORM\EntityManager $em */
    $em = $this->get(\Doctrine\ORM\EntityManager::class);
    $contries = $continent->getCountries($em);


    /** @var $resourceContentRepository \Infotrip\Domain\Repository\ResourceContentRepository */
    $resourceContentRepository = $this->get(\Infotrip\Domain\Repository\ResourceContentRepository::class);
    $resourceContent = $resourceContentRepository
        ->getResourceContent(strtolower($continentName));


    $viewHelpers = $this->get('viewHelpers');

    $args['viewHelpers'] = $viewHelpers($request);
    $args['contries'] = $contries;
    $args['resourceContent'] = $resourceContent;

    // Render index view
    return $this->renderer->render($response, 'listCountries/index.phtml', $args);

})->setName('listContries');

$app->get('/list-cities/{countryName}/{countryId}', function (Request $request, Response $response, array $args) {

    ini_set('memory_limit', '1G');
    $countryId = urldecode($args['countryId']);

    $country = new \Infotrip\Domain\Entity\Country($countryId);

    /** @var \Doctrine\ORM\EntityManager $em */
    $em = $this->get(\Doctrine\ORM\EntityManager::class);
    $cities = $country->getCities($em);

    /** @var $resourceContentRepository \Infotrip\Domain\Repository\ResourceContentRepository */
    $resourceContentRepository = $this->get(\Infotrip\Domain\Repository\ResourceContentRepository::class);
    $resourceContent = $resourceContentRepository
        ->getResourceContent(strtolower($country->getName()));

    $viewHelpers = $this->get('viewHelpers');

    $args['viewHelpers'] = $viewHelpers($request);
    $args['cities'] = $cities;
    $args['resourceContent'] = $resourceContent;

    // Render index view
    return $this->renderer->render($response, 'listCities/index.phtml', $args);

})->setName('listCities');


$app->get('/list-hotels/{city}', function (Request $request, Response $response, array $args) {

    $cityUnique = urldecode($args['city']);
    $pag = $request->getParam('pag');

    /** @var $hotelRepository \Infotrip\Domain\Repository\HotelRepository */
    $hotelRepository = $this->get(\Infotrip\Domain\Repository\HotelRepository::class);
    $hotelSearchResult = $hotelRepository
        ->getHotelsByCity($cityUnique, $pag);

    /** @var $resourceContentRepository \Infotrip\Domain\Repository\ResourceContentRepository */
    $resourceContentRepository = $this->get(\Infotrip\Domain\Repository\ResourceContentRepository::class);
    $resourceContent = $resourceContentRepository
        ->getResourceContent(strtolower($cityUnique));


    $viewHelpers = $this->get('viewHelpers');

    $args['viewHelpers'] = $viewHelpers($request);
    $args['hotelSearchResult'] = $hotelSearchResult;
    $args['resourceContent'] = $resourceContent;
    $args['cityUnique'] = $cityUnique;

    // Render index view
    return $this->renderer->render($response, 'listHotels/index.phtml', $args);

})->setName('listHotels');

$app->get('/hotel-owner-login-register', function (Request $request, Response $response, array $args) {

    $viewHelpers = $this->get('viewHelpers');
    $args['viewHelpers'] = $viewHelpers($request);

    return $this->renderer->render($response, 'hotelOwners/login-register.phtml', $args);

})->setName('hotelOwnerLoginRegister');

// start processes

$app->get('/process-refresh-top-deal', function (Request $request, Response $response, array $args) {

    /** @var $hotelRepository \Infotrip\Domain\Repository\HotelRepository */
    $hotelRepository = $this->get(\Infotrip\Domain\Repository\HotelRepository::class);

    $hotelRepository
        ->getRandomHotel();

    echo json_encode(array(
        'success' => true,
    ));

})->setName('processRefrehTopDeal');




// start admin