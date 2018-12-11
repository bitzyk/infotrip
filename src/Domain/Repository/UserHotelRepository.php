<?php

namespace Infotrip\Domain\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\OptimisticLockException;
use Infotrip\Domain\Entity\Hotel;
use Infotrip\Domain\Entity\HotelOwnerUser;
use Infotrip\Domain\Entity\UserHotel;

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
                "SELECT uh.hotel_id, h.name, h.countryCode, h.cityHotel, h.address, h.zip, h.minrate " .
                  "FROM Infotrip\Domain\Entity\UserHotel uh
                  JOIN Infotrip\Domain\Entity\Hotel h WITH uh.hotel_id = h.id
                  WHERE uh.user_id = :userId AND h.visible = 1"
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
                    ->setMinrate($row['minrate'])
                ;

                $hotelOwnerUser
                    ->addAssociatedHotels($hotel);
            }
        }

        return $this;
    }

    /**
     * @param $userId
     * @param $hotelIds
     * @return int
     * @throws \Exception
     */
    public function associateHotelsToUser(
        $userId,
        $hotelIds
    )
    {
        $hotelIdsArr = explode(',', $hotelIds);

        $assocHotels = 0;

        foreach ($hotelIdsArr as $hotelId) {
            $hotelId = (int)trim($hotelId);

            $userHotel = new UserHotel();
            $userHotel
                ->setHotelId($hotelId)
                ->setUserId($userId);

            print_r($userHotel);
            try {
                $this->getEntityManager()->persist($userHotel);
                $this->getEntityManager()->flush($userHotel);
                $assocHotels++;
            } catch (\Exception $e) {
                continue;
            }
        }

        return $assocHotels;
    }


}