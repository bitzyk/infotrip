<?php

namespace Infotrip\ApiProvider\Provider\Agoda;

use Infotrip\ApiProvider\AbstractAvailabilityProvider;
use Infotrip\ApiProvider\Entity\Standard\AvailabilityRequest;
use Infotrip\ApiProvider\Entity\Standard\AvailabilityResponse;
use Infotrip\ApiProvider\IAvailabilityProvider;

class AgodaAvailabilityProvider extends AbstractAvailabilityProvider
    implements IAvailabilityProvider
{

    const AUTHORIZATION = '1814466:daffcf5e-1a0e-45e5-b03a-8d68d8be67a3';

    protected function _checkAvailability()
    {
        $request = new \HttpRequest();

        $request
            ->setHeaders([
                'Authorization' => self::AUTHORIZATION,
                'Content-Type' => 'application/json',
            ]);

        $request
            ->setRawPostData(
                '{
"criteria": {
"additional": { "currency": "USD", "discountOnly": false, "language": "en-us", "occupancy": {
"numberOfAdult": 2,
"numberOfChildren": 1 }
},
"checkInDate": "2018-12-25", "checkOutDate": "2018-12-27", "hotelId": [
407854 ]
} }'
            );

        $request->send();


        var_dump($request->getResponseCode());
        print_r($request->getResponseBody());

        exit;

        $availabilityResponse = new AvailabilityResponse();

        $availabilityResponse->setCurrency('EUR');
        $availabilityResponse->setPricePerNight(60);

        return $availabilityResponse;
    }

    protected function setProviderHotelId()
    {
        $this->availabilityRequest->setProviderHotelId(
            $this->availabilityRequest->getHotelAssoc()->getHotelIdAgoda()
        );
    }


}