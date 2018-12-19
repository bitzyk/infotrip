<?php

namespace Infotrip\ApiProvider;

use Infotrip\ApiProvider\Entity\Standard\AvailabilityRequest;
use Infotrip\ApiProvider\Entity\Standard\AvailabilityResponse;

abstract class AbstractAvailabilityProvider implements IAvailabilityProvider
{
    /**
     * @var AvailabilityRequest
     */
    protected $availabilityRequest;

    public function checkAvailability(AvailabilityRequest $availabilityRequest)
    {
        // set request
        $this->availabilityRequest = $availabilityRequest;

        $this->setProviderHotelId();
        return $this->_checkAvailability();
    }

    /**
     * @return AvailabilityResponse
     */
    abstract protected function _checkAvailability();

    /**
     * @return void
     */
    abstract protected function setProviderHotelId();
}