<?php

namespace Infotrip\Service\Agoda\Service\AgodaAssociater;


use Infotrip\Domain\Entity\HotelAssoc;
use Infotrip\Service\Agoda\Entities\AgodaAssociateResponse;

class AssociaterLevel1 extends AbstractAssociater
{
    /**
     * @throws \Exception
     */
    public function associate(
        AgodaAssociateResponse $agodaAssociateResponse
    )
    {
        $toAssociate = $this->agodaHotelRepository->test();

        $grouped = array_chunk($toAssociate, 1000);

        $inserted = [];
        foreach ($grouped as $bulkGroup) {
            $bulkEntities = [];
            foreach ($bulkGroup as $row) {
                $hotelAssoc = new HotelAssoc();
                $hotelAssoc
                    ->setHotelIdAgoda($row['agodaHotelId'])
                    ->setHotelIdBooking($row['bookingHotelId']);

                $inserted[$row['agodaHotelId']] = $row['bookingHotelId'];
                $bulkEntities[] = $hotelAssoc;
            }

            //$this->hotelAssocRepository->insertBulk($bulkEntities);

            $agodaAssociateResponse
                ->incrementNewAssociations(count($bulkEntities));
        }
        exit;
    }


}