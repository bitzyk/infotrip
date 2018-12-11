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

$app->get('/hotel-owner-login-register', function (Request $request, Response $response, array $args) {

    $viewHelpers = $this->get('viewHelpers');
    $args['viewHelpers'] = $viewHelpers($request);
    $args['registerError'] = $request->getParam('registerError');
    $args['registerSuccess'] = $request->getParam('registerSuccess');
    $args['loginError'] = $request->getParam('loginError');
    $args['loginSuccess'] = $request->getParam('loginSuccess');

    return $this->renderer->render($response, 'hotelOwners/login-register.phtml', $args);

})->setName('hotelOwnerLoginRegister');

$app->post('/hotel-owner-register', function (Request $request, Response $response, array $args) {

    /** @var \Infotrip\Utils\Google\Recaptcha\V2  $googleCaptchaV2 */
    $googleCaptchaV2 = $this->get(\Infotrip\Utils\Google\Recaptcha\V2::class);

    $routeHelper = $this->get(\Infotrip\ViewHelpers\RouteHelper::class);
    /** @var \Infotrip\ViewHelpers\RouteHelper $routerHelper */
    $routerHelper = $routeHelper($request);

    /** @var \PHPAuth\Auth $authService */
    $authService = $this->get(\PHPAuth\Auth::class);

    if (
        ! $googleCaptchaV2->captchaIsValid($request->getParam('g-recaptcha-response'))
    ) {
        // redirect in case of error
        return $response
            ->withRedirect(
                $routerHelper->getHotelOwnerLoginRegisterUrl() . '?registerError=Invalid captcha',
                301
            );
    }

    $registerData = $request->getParam('register');

    if (
        ! is_array($registerData) ||
        ! isset($registerData['email']) ||
        ! isset($registerData['password'])
    ) {
        // redirect in case of error
        return $response
            ->withRedirect(
                $routerHelper->getHotelOwnerLoginRegisterUrl() . '?registerError=Invalid data',
                301
            );
    }

    $authResponse = $authService->register(
        $registerData['email'],
        $registerData['password'],
        $registerData['password'],
        [],
        null,
        true
    );


    if (
        isset($authResponse['error']) &&
        $authResponse['error']
    ) {
        $errorMessage = isset($authResponse['message']) ? $authResponse['message'] : '';

        // redirect in case of error
        return $response
            ->withRedirect(
                $routerHelper->getHotelOwnerLoginRegisterUrl() . '?registerError=' . $errorMessage,
                301
            );
    }


    return $response
        ->withRedirect(
            $routerHelper->getHotelOwnerLoginRegisterUrl() . '?registerSuccess=' . $authResponse['message'],
            301
        );



})->setName('hotelOwnerRegister');


$app->post('/hotel-owner-login', function (Request $request, Response $response, array $args) {

    /** @var \Infotrip\Utils\Google\Recaptcha\V2  $googleCaptchaV2 */
    $googleCaptchaV2 = $this->get(\Infotrip\Utils\Google\Recaptcha\V2::class);

    $routeHelper = $this->get(\Infotrip\ViewHelpers\RouteHelper::class);
    /** @var \Infotrip\ViewHelpers\RouteHelper $routerHelper */
    $routerHelper = $routeHelper($request);

    /** @var \PHPAuth\Auth $authService */
    $authService = $this->get(\PHPAuth\Auth::class);

    if (
    ! $googleCaptchaV2->captchaIsValid($request->getParam('g-recaptcha-response'))
    ) {
        // redirect in case of error
        return $response
            ->withRedirect(
                $routerHelper->getHotelOwnerLoginRegisterUrl() . '?loginError=Invalid captcha',
                301
            );
    }

    $loginData = $request->getParam('login');

    if (
        ! is_array($loginData) ||
        ! isset($loginData['email']) || ! $loginData['email'] ||
        ! isset($loginData['password']) || ! $loginData['password']
    ) {
        // redirect in case of error
        return $response
            ->withRedirect(
                $routerHelper->getHotelOwnerLoginRegisterUrl() . '?loginError=Invalid login data',
                301
            );
    }

    $authResponse = $authService->login(
        $loginData['email'],
        $loginData['password']
    );


    if (
        isset($authResponse['error']) &&
        $authResponse['error']
    ) {
        $errorMessage = isset($authResponse['message']) ? $authResponse['message'] : '';

        // redirect in case of error
        return $response
            ->withRedirect(
                $routerHelper->getHotelOwnerLoginRegisterUrl() . '?loginError=' . $errorMessage,
                301
            );
    }

    return $response
        ->withRedirect(
            $routerHelper->getHotelOwnerAdminDashbordUrl(),
            301
        );



})->setName('hotelOwnerLogin');

$app->get('/hotel-owner-activate-landing', function (Request $request, Response $response, array $args) {

    $viewHelpers = $this->get('viewHelpers');
    $args['viewHelpers'] = $viewHelpers($request);
    $args['activateError'] = $request->getParam('activateError');
    $args['activateSuccess'] = $request->getParam('activateSuccess');

    return $this->renderer->render($response, 'hotelOwners/activate.phtml', $args);

})->setName('hotelOwnerActivateLanding');


$app->post('/hotel-owner-activate', function (Request $request, Response $response, array $args) {

    /** @var \Infotrip\Utils\Google\Recaptcha\V2  $googleCaptchaV2 */
    $googleCaptchaV2 = $this->get(\Infotrip\Utils\Google\Recaptcha\V2::class);

    $routeHelper = $this->get(\Infotrip\ViewHelpers\RouteHelper::class);
    /** @var \Infotrip\ViewHelpers\RouteHelper $routerHelper */
    $routerHelper = $routeHelper($request);

    /** @var \PHPAuth\Auth $authService */
    $authService = $this->get(\PHPAuth\Auth::class);

    if (
    ! $googleCaptchaV2->captchaIsValid($request->getParam('g-recaptcha-response'))
    ) {
        // redirect in case of error
        return $response
            ->withRedirect(
                $routerHelper->getHotelOwnerActivateLandingUrl() . '?activateError=Invalid captcha',
                301
            );
    }


    $key = $request->getParam('key');

    if (
        ! isset($key) || ! $key
    ) {
        // redirect in case of error
        return $response
            ->withRedirect(
                $routerHelper->getHotelOwnerActivateLandingUrl() . '?activateError=Invalid activation key',
                301
            );
    }

     $authResponse = $authService->activate($key);

    if (
        isset($authResponse['error']) &&
        $authResponse['error']
    ) {
        $errorMessage = isset($authResponse['message']) ? $authResponse['message'] : '';

        // redirect in case of error
        return $response
            ->withRedirect(
                $routerHelper->getHotelOwnerActivateLandingUrl() . '?activateError=' . $errorMessage,
                301
            );
    }


    return $response
        ->withRedirect(
            $routerHelper->getHotelOwnerLoginRegisterUrl() . '?loginSuccess=' . $authResponse['message'],
            301
        );

})->setName('hotelOwnerActivate');


$app->get('/hotel-owner-admin-logout', function (Request $request, Response $response, array $args) {

    $viewHelpers = $this->get('viewHelpers');
    $args['viewHelpers'] = $viewHelpers($request);

    $routeHelper = $this->get(\Infotrip\ViewHelpers\RouteHelper::class);
    /** @var \Infotrip\ViewHelpers\RouteHelper $routerHelper */
    $routerHelper = $routeHelper($request);

    /** @var \PHPAuth\Auth $authService */
    $authService = $this->get(\PHPAuth\Auth::class);

    if (! $authService->isLogged()) {
        return $response
            ->withRedirect(
                $routerHelper->getHotelOwnerLoginRegisterUrl() . '?loginError=Login session has expired.',
                301
            );
    }

    $authService
        ->logout($authService->getCurrentSessionHash());

    return $response
        ->withRedirect(
            $routerHelper->getHotelOwnerLoginRegisterUrl() . '?loginSuccess=You have successfully logout.',
            301
        );

})->setName('hotelOwnerAdminLogout');

// admin pages
$app->get('/hotel-owner-admin-dashbord', \Infotrip\Handler\Action\AdminDashbord::class)->setName('hotelOwnerAdminDashbord');
$app->get('/hotel-owner-admin-social-media', \Infotrip\Handler\Action\AdminSocialMedia::class)->setName('hotelOwnerAdminSocialMedia');
$app->get('/hotel-owner-admin-add-new-hotel', \Infotrip\Handler\Action\AdminAddNewHotel::class)->setName('hotelOwnerAdminAddNewHotel');
$app->get('/hotel-owner-admin-account-settings', \Infotrip\Handler\Action\AdminAcountSettings::class)->setName('hotelOwnerAdminAccountSettings');
$app->post('/hotel-owner-admin-account-settings-process', \Infotrip\Handler\Action\AdminAcountSettingsProcess::class)->setName('hotelOwnerAdminAccountSettingsProcess');
$app->get('/hotel-owner-admin/hotel-edit/{hotelId}', \Infotrip\Handler\Action\AdminEditHotel::class)->setName('hotelOwnerAdminEditHotel');
$app->post('/hotel-owner-admin/hotel-edit-process/{hotelId}', \Infotrip\Handler\Action\AdminEditHotelProcess::class)->setName('hotelOwnerAdminEditHotelProcess');
$app->get('/hotel-owner-admin/hotel-delete/{hotelId}', \Infotrip\Handler\Action\AdminDeleteHotel::class)->setName('hotelOwnerAdminDeleteHotel');

// admin root pages
$app->get('/admin-root/associate-hotels', \Infotrip\Handler\Action\AdminRootAssociateHotels::class)->setName('adminRootAssociateHotels');