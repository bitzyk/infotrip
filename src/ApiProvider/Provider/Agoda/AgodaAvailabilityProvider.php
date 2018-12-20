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

    const API_URL = 'http://affiliateapi7643.agoda.com/affiliateservice/lt_v1';

    /**
     * @return AvailabilityResponse
     * @throws \Exception
     */
    protected function _checkAvailability()
    {
        $request = $this->setRequest();
        $httpResponse = $request->send();

        if(
            $httpResponse->getStatus() != 200 ||
            ! $this->responseIsValid($httpResponse)
        ) {
            return null;
        }

        return $this->hydrateAvailabilityResponse($httpResponse);
    }

    /**
     * @return string
     */
    protected function getProviderHotelId()
    {
        return $this->availabilityRequest->getHotelAssoc()->getHotelIdAgoda();
    }

    /**
     * @param \HTTP_Request2_Response $httpResponse
     * @return AvailabilityResponse
     * @throws \Exception
     */
    private function hydrateAvailabilityResponse(
        \HTTP_Request2_Response $httpResponse
    )
    {
        $decodedResponse = json_decode($httpResponse->getBody());

        $availabilityResponse = new AvailabilityResponse();

        $availabilityResponse->setCurrency($decodedResponse->results[0]->currency);
        $availabilityResponse->setPricePerNight((float)$decodedResponse->results[0]->dailyRate);
        $availabilityResponse->setIncludeBreakfast((boolean)$decodedResponse->results[0]->includeBreakfast);
        $availabilityResponse->setLandingURL($decodedResponse->results[0]->landingURL);
        $availabilityResponse->setHotelName($decodedResponse->results[0]->hotelName);
        $availabilityResponse->setRoomtypeName($decodedResponse->results[0]->landingURL);
        $availabilityResponse->setProviderImageUrl('/img/providers/agoda-min.png');

        return $availabilityResponse;
    }

    /**
     * @return \HTTP_Request2
     * @throws \HTTP_Request2_LogicException
     */
    private function setRequest()
    {
        $request = new \HTTP_Request2();
        $request->setUrl(self::API_URL);
        $request->setHeader(
            'Authorization', self::AUTHORIZATION
        );
        $request->setHeader(
            'Content-Type', 'application/json'
        );

        $request->setMethod(\HTTP_Request2::METHOD_POST);

        $request->setBody($this->getBody());
        return $request;
    }

    /**
     * @param \HTTP_Request2_Response $httpResponse
     * @return bool
     * @throws \HTTP_Request2_Exception
     */
    private function responseIsValid(
        \HTTP_Request2_Response $httpResponse
    )
    {
        $isValid = false;

        $decodedResponse = json_decode($httpResponse->getBody());

        if (isset($decodedResponse->results[0])) {
            $isValid = true;
        }

        return $isValid;
    }

    private function getBody()
    {
        $data = [
            'criteria' => [
                'additional' =>
                    [
                        'currency' => $this->availabilityRequest->getCurrency(),
                        'discountOnly' => false,
                        'language' => 'en-us',
                        'occupancy' => [
                            'numberOfAdult' => 2,
                            'numberOfChildren' => 1
                        ]

                    ],
                'checkInDate' => $this->availabilityRequest->getCheckInDate()->format('Y-m-d'),
                'checkOutDate' => $this->availabilityRequest->getCheckOutDate()->format('Y-m-d'),
                'hotelId' => [
                    $this->availabilityRequest->getProviderHotelId()
                ]
            ]
        ];


        return json_encode($data);
    }

}