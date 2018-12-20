<?php

namespace Infotrip\ApiProvider\Provider\Agoda;

use Infotrip\ApiProvider\AbstractAvailabilityProvider;
use Infotrip\ApiProvider\Entity\Standard\AvailabilityRequest;
use Infotrip\ApiProvider\Entity\Standard\AvailabilityResponse;
use Infotrip\ApiProvider\IAvailabilityProvider;
use Infotrip\Domain\Entity\Hotel;
use Infotrip\Domain\Repository\HotelRepository;

class BookingComAvailabilityProvider extends AbstractAvailabilityProvider
    implements IAvailabilityProvider
{

    /**
     * @var HotelRepository
     */
    private $hotelRepository;

    public function __construct(
        HotelRepository $hotelRepository
    )
    {
        $this->hotelRepository = $hotelRepository;
    }

    /**
     * @return AvailabilityResponse
     * @throws \Exception
     */
    protected function _checkAvailability()
    {
        $hotel = $this->hotelRepository->getApiHotel($this->availabilityRequest->getProviderHotelId());

        return $this->hydrateAvailabilityResponse(
            $hotel
        );
    }

    /**
     * @param \HTTP_Request2_Response $httpResponse
     * @return AvailabilityResponse
     * @throws \Exception
     */
    private function hydrateAvailabilityResponse(
        Hotel $hotel
    )
    {
        $availabilityResponse = new AvailabilityResponse();

        $availabilityResponse->setCurrency($hotel->getCurrencycode());
        $availabilityResponse->setPricePerNight($hotel->getMinrate());
        $availabilityResponse->setIncludeBreakfast(false);
        $availabilityResponse->setLandingURL($hotel->getBookingHotelUrl());
        $availabilityResponse->setProviderImageUrl('/img/providers/booking-min.png');

        return $availabilityResponse;
    }

    /**
     * @return string
     */
    protected function getProviderHotelId()
    {
        return $this->availabilityRequest->getHotelAssoc()->getHotelIdBooking();
    }



}