<?php

namespace Infotrip\ApiProvider;

use Infotrip\ApiProvider\Entity\Standard\AvailabilityRequest;
use Infotrip\ApiProvider\Entity\Standard\AvailabilityResponse;
use Infotrip\ApiProvider\Provider\Agoda\AgodaAvailabilityProvider;

class AvailabilityProviderAgregator implements IAvailabilityProviderAgregator
{

    /**
     * @var IAvailabilityProvider[]
     */
    private $availabilityProvider = [];

    public function __construct()
    {
        // todo -> read from config
        $this->availabilityProvider[] = new AgodaAvailabilityProvider();
    }

    /**
     * @param AvailabilityRequest $availabilityRequest
     * @return AvailabilityResponse[]
     */
    public function checkAvailability(AvailabilityRequest $availabilityRequest)
    {
        $availabilityResponses = [];

        foreach ($this->availabilityProvider as $provider) {
            $availabilityResponse = $provider->checkAvailability($availabilityRequest);

            if ($availabilityResponse instanceof AvailabilityResponse) {
                $availabilityResponses[] = $availabilityResponse;
            }
        }

        return $availabilityResponses;
    }


}