<?php

namespace Infotrip\Domain\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Infotrip\Domain\Entity\Hotel;
use Infotrip\Domain\Entity\HotelOwnerUser;

class UserHotelRepository extends EntityRepository
{

    /**
     * @param HotelOwnerUser $hotelOwnerUser
     * @return $this
     */
    public function setUserAssociatedHotels(
        HotelOwnerUser $hotelOwnerUser
    )
    {
        // count how many related hotels there is
        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT uh.hotel_id, h.name, h.countryCode, h.cityHotel, h.address, h.zip " .
                  "FROM Infotrip\Domain\Entity\UserHotel uh
                  JOIN Infotrip\Domain\Entity\Hotel h WITH uh.hotel_id = h.id
                  WHERE uh.user_id = :userId"
            );

        $query->setParameter('userId', $hotelOwnerUser->getUserId());

        $result = $query->getResult();

        if (count($result)) {
            foreach ($result as $row) {
                $hotel = new Hotel();
                $hotel
                    ->setId($row['hotel_id'])
                    ->setName($row['name'])
                    ->setCountryCode($row['countryCode'])
                    ->setCityHotel($row['cityHotel'])
                    ->setAddress($row['address'])
                    ->setZip($row['zip'])
                ;

                $hotelOwnerUser
                    ->addAssociatedHotels($hotel);
            }
        }

        return $this;
    }
}