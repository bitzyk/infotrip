<?php

namespace Infotrip\Domain\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;


class AgodaHotelRepository extends EntityRepository
{

    /**
     * @param $agodaHotels
     * @throws \Exception
     */
    public function insertBulk(
        $agodaHotels
    )
    {
        foreach ($agodaHotels as $hotel) {
            $this->getEntityManager()->persist($hotel);
        }

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllExistingAgodaHotelIds()
    {

        $stmt = $this->getEntityManager()->getConnection()->prepare(
            'select hotel_id from agoda_hotel'
        );
        $stmt->execute();

        $allExistingAgodaHotelIds = [];

        while ($row = $stmt->fetch()) {
            $allExistingAgodaHotelIds[$row['hotel_id']] = $row['hotel_id'];
        }

        return $allExistingAgodaHotelIds;
    }

    /**
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllRequiredInfoToAssociateForUnassociateHotels()
    {
        $stmt = $this->getEntityManager()->getConnection()->prepare(
            'select hotel_name, hotel_formerly_name, hotel_translated_name from agoda_hotel'
        );
        $stmt->execute();

        $hotelsInfo = [];

        while ($row = $stmt->fetch()) {
            $hotelsInfo[] = array(
                'hotel_name' => $row['hotel_name'],
                'hotel_formerly_name' => $row['hotel_formerly_name'],
                'hotel_translated_name' => $row['hotel_translated_name'],
            );
        }

        return $hotelsInfo;
    }




    public function test()
    {
        $stmt = $this->getEntityManager()->getConnection()->prepare(
            '
select ah.hotel_id as agodaHotelId,  h.id as bookingHotelId
from agoda_hotel ah
left join hotels_assoc assoc on assoc.hotel_id_agoda = ah.hotel_id
left join hotels h on (h.name = ah.hotel_name and lower(h.cc1) = lower(ah.countryisocode) and lower(h.city_hotel) = lower(ah.city))
where assoc.hotel_id_agoda is null and h.id is not null
            '
        );

        $stmt->execute();

        return $stmt->fetchAll();
    }
}