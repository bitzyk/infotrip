<?php

namespace Infotrip\Domain\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Infotrip\Domain\Entity\HotelAssoc;


class HotelAssocRepository extends EntityRepository
{

    /**
     * @param HotelAssoc[]
     * @throws \Exception
     */
    public function insertBulk(
        $hotelAssoc
    )
    {
        foreach ($hotelAssoc as $assocEntity) {
            $this->getEntityManager()->persist($assocEntity);
        }

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }

    /**
     * @param $internalHotelId
     * @return HotelAssoc
     */
    public function getAssociationForInternalHotelId(
        $internalHotelId
    )
    {
        /** @var HotelAssoc $association */
        $association = $this->findOneBy([
            'hotel_id_booking' => $internalHotelId
        ]);

        return $association;
    }
}