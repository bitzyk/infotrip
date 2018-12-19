<?php

namespace Infotrip\ApiProvider;

use Infotrip\ApiProvider\Entity\Standard\AvailabilityRequest;
use Infotrip\ApiProvider\Entity\Standard\AvailabilityResponse;

interface IAvailabilityProviderAgregator
{
    /**
     * @param AvailabilityRequest $availabilityRequest
     * @return AvailabilityResponse[]
     */
    public function checkAvailability(AvailabilityRequest $availabilityRequest);

}