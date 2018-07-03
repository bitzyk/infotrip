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
            ->withRedirect($routerHelper->getHotelUrl(
                $hotel->getName(),
                $hotel->getId()
            ), 301);

    }

    /** @var \Infotrip\HotelParser\BookingComParser $bookingParser */
    $bookingParser = $this->get(\Infotrip\HotelParser\BookingComParser::class);

    // request external hotel info (cached)
    $hotel
        ->requestExternalHotelInfo($bookingParser);

//    print_r(
//        $hotel->getExternalHotelInfo()
//    );
//    print_r(
//        $hotel->getBookingHotelUrl()
//    );
//    exit;

    $args['hotel'] = $hotel;
    $viewHelpers = $this->get('viewHelpers');
    $args['viewHelpers'] = $viewHelpers($request);


    // Render index view
    return $this->renderer->render($response, 'hotel/index.phtml', $args);

})->setName('hotelRoute');

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

    $viewHelpers = $this->get('viewHelpers');

    $args['viewHelpers'] = $viewHelpers($request);

    // Render index view
    return $this->renderer->render($response, 'homepage/index.phtml', $args);

})->setName('homepage');

$app->get('/contact', function (Request $request, Response $response, array $args) {

})->setName('contact');