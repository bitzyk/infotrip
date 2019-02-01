<?php

namespace Infotrip\ApiProvider\Provider\Booking;

use Infotrip\ApiProvider\AbstractAvailabilityProvider;
use Infotrip\ApiProvider\Entity\Standard\AvailabilityRequest;
use Infotrip\ApiProvider\Entity\Standard\AvailabilityResponse;
use Infotrip\ApiProvider\IAvailabilityProvider;
use Infotrip\Domain\Entity\Hotel;
use Infotrip\Domain\Repository\HotelRepository;
use Infotrip\HotelParser\AvailabilityChecker\BookingComAvailabilityChecker;

class BookingComAvailabilityProvider extends AbstractAvailabilityProvider
    implements IAvailabilityProvider
{

    /**
     * @var HotelRepository
     */
    private $hotelRepository;

    /**
     * @var BookingComAvailabilityChecker
     */
    private $bookingComAvailabilityChecker;

    public function __construct(
        HotelRepository $hotelRepository,
        BookingComAvailabilityChecker $bookingComAvailabilityChecker
    )
    {
        $this->hotelRepository = $hotelRepository;
        $this->bookingComAvailabilityChecker = $bookingComAvailabilityChecker;
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
        $availabilityResponse->setLandingURL(
            $this->bookingComAvailabilityChecker->getAvailabilityUrl(
                [
                    'start' => $this->availabilityRequest->getCheckInDate()->format('Y-m-d'),
                    'end' => $this->availabilityRequest->getCheckOutDate()->format('Y-m-d'),
                    'adults' => $this->availabilityRequest->getNoAdult(),
                    'childrens' => $this->availabilityRequest->getNoChildren(),
                ],
                $hotel
            )
        );
        $availabilityResponse->setHotelName($hotel->getName());
        $availabilityResponse->setProviderImageUrl('/img/providers/booking-com-min.png');

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