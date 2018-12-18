<?php

namespace Infotrip\Service\Agoda\Service\AgodaAssociater;


use Infotrip\Domain\Entity\HotelAssoc;
use Infotrip\Service\Agoda\Entities\AgodaAssociateResponse;

class AssociaterLevel3 extends AbstractAssociater
{

    const ASSOC_NAME = 'Association level 3: Identic country code, city and not identic hotel name (removing hotel keywords) for unassociated hotels';

    /**
     * @param AgodaAssociateResponse $agodaAssociateResponse
     * @return AgodaAssociateResponse
     * @throws \Exception
     */
    public function associate(
        AgodaAssociateResponse $agodaAssociateResponse
    )
    {
        $assocHotelsLevel3 = $this->agodaHotelRepository->getAssocHotelsLevel3();

        $toAssociate = [];
        $assocAgoda = $assocBooking =  [];

        foreach ($assocHotelsLevel3['agodaHotelsInfo'] as $key => $agodaHotelId) {
            if (isset($assocHotelsLevel3['bookingHotelsInfo'][$key])) {

                // already associated (unique association testriction) -> continue
                if (
                    isset($assocAgoda[$agodaHotelId]) ||
                    isset($assocBooking[$assocHotelsLevel3['bookingHotelsInfo'][$key]])
                ) {
                    continue;
                }

                $toAssociate[] = array(
                    'agodaHotelId' => $agodaHotelId,
                    'bookingHotelId' => $assocHotelsLevel3['bookingHotelsInfo'][$key],
                );

                $assocAgoda[$agodaHotelId] = $agodaHotelId;
                $assocBooking[$assocHotelsLevel3['bookingHotelsInfo'][$key]] = $assocHotelsLevel3['bookingHotelsInfo'][$key];
            }
        }

        $this->insertToAssociate($agodaAssociateResponse, $toAssociate);

        $agodaAssociateResponse
            ->setNameAssociation(self::ASSOC_NAME);

        return $agodaAssociateResponse;
    }


}