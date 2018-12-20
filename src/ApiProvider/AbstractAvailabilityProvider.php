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

    /**
     * @param AvailabilityRequest $availabilityRequest
     * @return AvailabilityResponse|null
     */
    public function checkAvailability(AvailabilityRequest $availabilityRequest)
    {
        // set request
        $this->availabilityRequest = $availabilityRequest;

        // check if it has association -> if it does not have -> do not run for current provider
        if (! $this->hasProviderAssoc()) {
            return null;
        }

        // set specific provider id
        $this->availabilityRequest->setProviderHotelId(
            $this->getProviderHotelId()
        );

        $this->getProviderHotelId();
        return $this->_checkAvailability();
    }

    /**
     * @return AvailabilityResponse
     */
    abstract protected function _checkAvailability();

    /**
     * @return string
     */
    abstract protected function getProviderHotelId();

    /**
     * @return bool
     */
    private function hasProviderAssoc()
    {
        return (boolean) $this->getProviderHotelId();
    }
}