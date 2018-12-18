<?php

namespace Infotrip\Domain\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;


class AgodaHotelRepository extends EntityRepository
{

    const ASSOC_LEVEL_1_STATEMENT = '
select ah.hotel_id as agodaHotelId,  h.id as bookingHotelId
from agoda_hotel ah
  left join hotels h on (h.name = ah.hotel_name and lower(h.cc1) = lower(ah.countryisocode) and lower(h.city_hotel) = lower(ah.city) and h.zip = ah.zipcode)
  left join hotels_assoc assoc1 on assoc1.hotel_id_agoda = ah.hotel_id
  left join hotels_assoc assoc2 on assoc2.hotel_id_booking = h.id
where assoc1.hotel_id_agoda is null and assoc2.hotel_id_booking is null and h.id is not null
            ';

    const ASSOC_LEVEL_2_STATEMENT = '
select ah.hotel_id as agodaHotelId,  h.id as bookingHotelId
from agoda_hotel ah
  left join hotels h on (h.name = ah.hotel_name and lower(h.cc1) = lower(ah.countryisocode) and lower(h.city_hotel) = lower(ah.city))
  left join hotels_assoc assoc1 on assoc1.hotel_id_agoda = ah.hotel_id
  left join hotels_assoc assoc2 on assoc2.hotel_id_booking = h.id
where assoc1.hotel_id_agoda is null and assoc2.hotel_id_booking is null and h.id is not null
            ';


    /**
     * @param $agodaHotels
     * @throws \Doctrine\ORM\OptimisticLockException
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


    /**
     * @param $level
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAssocHotels(
        $level
    )
    {
        if ($level == 1) {
            $sql = self::ASSOC_LEVEL_1_STATEMENT;
        } elseif ($level == 2) {
            $sql = self::ASSOC_LEVEL_2_STATEMENT;
        }

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);

        $stmt->execute();

        $hotelsInfo = [];
        $assocAgoda = $assocBooking =  [];
        while ($row = $stmt->fetch()) {

            // already associated (unique association testriction) -> continue
            if (
                isset($assocAgoda[$row['agodaHotelId']]) ||
                isset($assocBooking[$row['bookingHotelId']])
            ) {
                continue;
            }

            $hotelsInfo[] = array(
                'agodaHotelId' => $row['agodaHotelId'],
                'bookingHotelId' => $row['bookingHotelId'],
            );
            $assocAgoda[$row['agodaHotelId']] = $row['agodaHotelId'];
            $assocBooking[$row['bookingHotelId']] = $row['bookingHotelId'];

        }

        return $hotelsInfo;
    }

    /**
     * @param $level
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAssocHotelsLevel3()
    {
        // get unassoc agoda hotels
        $stmt = $this->getEntityManager()->getConnection()->prepare(
            '
            select ah.hotel_name, ah.countryisocode, ah.city, ah.hotel_id
from agoda_hotel ah
left join hotels_assoc ass on ah.hotel_id = ass.hotel_id_agoda
where ass.hotel_id_agoda is null 
            '
        );

        $stmt->execute();

        $agodaHotelsInfo = [];
        while ($row = $stmt->fetch()) {

            $key = sprintf(
                '%s_%s_%s',
                str_replace('hotel', '', str_replace(' ', '', mb_strtolower($row['hotel_name']))),
                strtolower($row['city']),
                strtolower($row['countryisocode'])
            );

            $agodaHotelsInfo[$key] = $row['hotel_id'];
        }

        $stmt2 = $this->getEntityManager()->getConnection()->prepare(
            '
select h.id, h.cc1, h.city_hotel, h.name
from hotels h
  left join hotels_assoc ass on h.id = ass.hotel_id_booking
where ass.hotel_id_booking is null
            '
        );

        $stmt2->execute();

        $bookingHotelsInfo = [];
        while ($row = $stmt2->fetch()) {

            $key = sprintf(
                '%s_%s_%s',
                str_replace('hotel', '', str_replace(' ', '', mb_strtolower($row['name']))),
                strtolower($row['city_hotel']),
                strtolower($row['cc1'])
            );

            $bookingHotelsInfo[$key] = $row['id'];
        }

        return [
            'agodaHotelsInfo' => $agodaHotelsInfo,
            'bookingHotelsInfo' => $bookingHotelsInfo,
        ];
    }
}