<?php

namespace Infotrip\ApiProvider\Provider\Agoda;

use Infotrip\ApiProvider\AbstractAvailabilityProvider;
use Infotrip\ApiProvider\Entity\Standard\AvailabilityRequest;
use Infotrip\ApiProvider\Entity\Standard\AvailabilityResponse;
use Infotrip\ApiProvider\IAvailabilityProvider;

class AgodaAvailabilityProvider extends AbstractAvailabilityProvider
    implements IAvailabilityProvider
{


    protected function _checkAvailability()
    {
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