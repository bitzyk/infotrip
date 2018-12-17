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
}