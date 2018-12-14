<?php

namespace Infotrip\Domain\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\OptimisticLockException;
use Infotrip\Domain\Entity\Hotel;
use Infotrip\Domain\Entity\HotelOwnerUser;
use Infotrip\Domain\Entity\UserHotel;
use Infotrip\HotelParser\ImageCatcher\ImageCatcher;

class UserHotelRepository extends EntityRepository
{

    /**
     * @var HotelRepository
     */
    private $hotelRepository;
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

            $hotelToAssociate = $this->hotelRepository
                ->getHotel($hotelId);

            $userHotel = new UserHotel();
            $userHotel
                ->setHotelId($hotelToAssociate->getId())
                ->setUserId($userId);

            try {
                $this->getEntityManager()->persist($userHotel);
                $this->getEntityManager()->flush($userHotel);
                $assocHotels++;

                $this->moveHotelImagesToAdministrableDirectory($hotelToAssociate);

            } catch (\Exception $e) {
                continue;
            }
        }

        return $assocHotels;
    }

    /**
     * @param Hotel $hotel
     */
    public function moveHotelImagesToAdministrableDirectory(
        Hotel $hotel
    )
    {
        $administrableImagePath = $hotel->getAdministrableImagePath();

        if (! file_exists($administrableImagePath)) {
            mkdir($administrableImagePath, 0755);
            copy(
                ADMINISTRABLE_IMAGE_PATH .'/.htaccess',
                $administrableImagePath . '/.htaccess'
            );
        }

        foreach ($hotel->getImages() as $image) {
            if (
                file_exists($image->getServerLocationSrc())
            ) {
                $baseName = basename($image->getSrc());

                $newImagePath = $administrableImagePath . '/' . $baseName;

                file_put_contents($newImagePath, file_get_contents($image->getServerLocationSrc()));
                unlink($image->getServerLocationSrc());
            }
        }
    }

    /**
     * @param HotelRepository $hotelRepository
     */
    public function setHotelRepository($hotelRepository)
    {
        $this->hotelRepository = $hotelRepository;
    }


    /**
     * @param $hotelId
     * @param $userId
     * @throws OptimisticLockException
     */
    public function deleteHotelAssociation(
        $hotelId,
        $userId
    )
    {
        $hotelToUserAssociations = $this->findBy([
            'hotel_id' => $hotelId,
            'user_id' => $userId,
        ]);

        foreach ($hotelToUserAssociations as $hotelToUserAssociation) {
            $this->getEntityManager()->remove($hotelToUserAssociation);
            $this->getEntityManager()->flush($hotelToUserAssociation);
        }
    }


}