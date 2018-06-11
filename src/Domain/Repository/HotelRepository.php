<?php

namespace Infotrip\Domain\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Infotrip\Domain\Entity\Hotel;

class HotelRepository extends EntityRepository
{

    public function __construct(EntityManager $em, Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
    }

    /**
     * @param int $hotelId
     * @return Hotel
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \Exception
     */
    public function getHotel(
        $hotelId
    )
    {
        $hotel = $this->getEntityManager()
            ->getRepository('Infotrip\Domain\Entity\Hotel')
            ->findOneBy(array(
                'id' => $hotelId
            ));

        if (! $hotel instanceof Hotel) {
            throw new \Exception('Hotel not found');
        }

        return $hotel;
    }

    /**
     * Get related hotels for the given hotel
     *
     * @param Hotel $hotelOrigin
     * @param int $relatedHotelsNo
     * @return Hotel[]
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getRelatedHotels(
        Hotel $hotelOrigin,
        $relatedHotelsNo = 9
    )
    {
        // count how many related hotels there is
        $query = $this->getEntityManager()->createQuery(
            "SELECT COUNT(h) FROM Infotrip\Domain\Entity\Hotel h
                  WHERE h.id != :hotelId AND h.cityUnique = :cityUnique"
        );
        $query->setParameter('hotelId', $hotelOrigin->getId());
        $query->setParameter('cityUnique', $hotelOrigin->getCityUnique());

        $relatedHotelsCount = $query->getSingleScalarResult();

        if ($relatedHotelsCount == 0) {
           return array();
        }
        // compute offset
        $offset = rand(
            0,
            (($relatedHotelsCount - $relatedHotelsNo) > 0) ?
            ($relatedHotelsCount - $relatedHotelsNo) : 0
            );

        // get related hotels
        $qb = $this->getEntityManager()
            ->getRepository('Infotrip\Domain\Entity\Hotel')
            ->createQueryBuilder('h');

        $qb
            ->where(
                'h.cityUnique = :cityUnique'
            )
            ->andWhere(
                'h.id != :hotelId'
            )
            ->setParameter(
                ':cityUnique', $hotelOrigin->getCityUnique()
            )
            ->setParameter(
                ':hotelId', $hotelOrigin->getId()
            )
            ->setFirstResult($offset)
            ->setMaxResults($relatedHotelsNo);

        $hotels = $qb->getQuery()->getResult();

        return $hotels;
    }


    /**
     * Get related hotels for the given hotel in the specified area
     *
     * @param array $areas
     * @param int $relatedHotelsNo
     *
     * @return Hotel[]
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getHotelsInArea(
        array $areas,
        $relatedHotelsNo = 12
    )
    {
        $where = [];
        if (isset($areas['city']) && $areas['city']) {
            $where[] = 'h.cityUnique = :cityUnique';
        }

        if (isset($areas['country']) && $areas['country']) {
            $where[] = 'h.countryCode = :country';
        }

        $sql = sprintf(
            'SELECT COUNT(h) FROM Infotrip\Domain\Entity\Hotel h WHERE %s',
            implode('AND', $where)
        );

        // count how many related hotels there is
        $query = $this->getEntityManager()->createQuery(
            $sql
        );

        if (isset($areas['city']) && $areas['city']) {
            $query->setParameter('cityUnique', $areas['city']);
        }

        if (isset($areas['country']) && $areas['country']) {
            $query->setParameter('country', $areas['country']);
        }

        $relatedHotelsCount = $query->getSingleScalarResult();

        if ($relatedHotelsCount == 0) {
            return array();
        }
        // compute offset
        $offset = rand(
            0,
            (($relatedHotelsCount - $relatedHotelsNo) > 0) ?
                ($relatedHotelsCount - $relatedHotelsNo) : 0
        );

        // get related hotels
        $qb = $this->getEntityManager()
            ->getRepository('Infotrip\Domain\Entity\Hotel')
            ->createQueryBuilder('h');

        if (isset($areas['city']) && $areas['city']) {
            $qb
                ->where(
                    'h.cityUnique = :cityUnique'
                )
                ->setParameter(
                    ':cityUnique', $areas['city']
                );
        }

        if (isset($areas['country']) && $areas['country']) {
            $qb
                ->where(
                    'h.countryCode = :country'
                )
                ->setParameter(
                    ':country', $areas['country']
                );
        }

        $qb->setFirstResult($offset)
            ->setMaxResults($relatedHotelsNo);

        $hotels = $qb->getQuery()->getResult();

        return $hotels;
    }

}