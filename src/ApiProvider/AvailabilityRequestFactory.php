<?php

namespace Infotrip\ApiProvider;

use Infotrip\ApiProvider\Entity\Standard\AvailabilityRequest;
use Infotrip\Domain\Entity\HotelAssoc;
use Infotrip\Domain\Repository\HotelAssocRepository;

class AvailabilityRequestFactory
{
    /**
     * @var HotelAssocRepository
     */
    private $hotelAssocRepository;

    public function __construct(
        HotelAssocRepository $hotelAssocRepository
    )
    {
        $this->hotelAssocRepository = $hotelAssocRepository;
    }

    /**
     * @param $internalHotelId
     * @param array $params
     * @return AvailabilityRequest
     * @throws \Exception
     */
    public function getAvailabilityRequest(
        $internalHotelId,
        array $params
    )
    {
        // validate params
        $this->validateParams($params);

        $association = $this->hotelAssocRepository->getAssociationForInternalHotelId($internalHotelId);

        if (! $association instanceof HotelAssoc) {
            throw new \Exception('Association does not exist');
        }

        $availabilityRequest = new AvailabilityRequest();

        $availabilityRequest
            ->setCurrency($params['currency'])
            ->setCheckInDate(new \DateTime(date('Y-m-d', strtotime($params['checkInDate']))))
            ->setCheckOutDate(new \DateTime(date('Y-m-d', strtotime($params['checkOutDate']))))
            ->setInternalHotelId($internalHotelId)
            ->setHotelAssoc(
                $association
            );

        return $availabilityRequest;
    }

    /**
     * @param array $params
     * @throws \Exception
     */
    private function validateParams(array $params)
    {
        if (
            ! isset($params['currency']) || ! $params['currency'] ||
            ! isset($params['checkInDate']) || ! $params['checkInDate'] ||
            ! isset($params['checkOutDate']) || ! $params['checkOutDate']
        ) {
            throw new \Exception('Invalid request');
        }
    }
}