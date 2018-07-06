<?php

namespace Infotrip\Domain\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Infotrip\Domain\Entity\Hotel;
use Infotrip\Domain\Entity\HotelSearchResult;

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
                'id' => $hotelId,
                'visible' => '1',
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
                  WHERE h.id != :hotelId AND h.cityUnique = :cityUnique AND h.visible = :visible"
        );
        $query->setParameter('hotelId', $hotelOrigin->getId());
        $query->setParameter('cityUnique', $hotelOrigin->getCityUnique());
        $query->setParameter('visible', '1');

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
            ->andWhere(
                'h.visible = :visible'
            )
            ->setParameter(
                ':cityUnique', $hotelOrigin->getCityUnique()
            )
            ->setParameter(
                ':hotelId', $hotelOrigin->getId()
            )
            ->setParameter(
                ':visible', 1
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
     * @throws \Exception
     */
    public function getHotelsInArea(
        array $areas,
        $relatedHotelsNo = 12
    )
    {
        $where = [];
        $where[] = 'h.visible = :visible';

        if (isset($areas['city']) && $areas['city']) {
            $where[] = 'h.cityUnique = :cityUnique';
        }

        if (isset($areas['country']) && $areas['country']) {
            $where[] = 'h.countryCode = :country';
        }

        if (
            isset($areas['continent']) &&
            $areas['continent']
        ) {
            $continentId = $this->getContinentId($areas['continent']);
            $where[] = 'h.continentId = :continentId';
        }

        if (isset($areas['hotelName']) && $areas['hotelName']) {
            $where[] = 'LOWER(h.name) = :hotelName';
        }

        if (isset($areas['hotelNameLike']) && $areas['hotelNameLike']) {
            $where[] = "LOWER(h.name) LIKE :hotelNameLike";
        }

        $sql = sprintf(
            'SELECT COUNT(h) FROM Infotrip\Domain\Entity\Hotel h WHERE %s',
            implode(' AND ', $where)
        );

        // count how many related hotels there is
        $query = $this->getEntityManager()->createQuery(
            $sql
        );

        $query->setParameter('visible', '1');

        if (isset($areas['city']) && $areas['city']) {
            $query->setParameter('cityUnique', $areas['city']);
        }

        if (isset($areas['country']) && $areas['country']) {
            $query->setParameter('country', $areas['country']);
        }

        if (isset($continentId)) {
            $query->setParameter('continentId', $continentId);
        }

        if (isset($areas['hotelName']) && $areas['hotelName']) {
            $query->setParameter('hotelName', strtolower($areas['hotelName']));
        }

        if (isset($areas['hotelNameLike']) && $areas['hotelNameLike']) {
            $query->setParameter('hotelNameLike', "%".strtolower($areas['hotelNameLike'] ."%" ));
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

        $qb
            ->where(
                'h.visible = :visible'
            )
            ->setParameter(
                ':visible', '1'
            );

        if (isset($areas['city']) && $areas['city']) {
            $qb
                ->andWhere(
                    'h.cityUnique = :cityUnique'
                )
                ->setParameter(
                    ':cityUnique', $areas['city']
                );
        }

        if (isset($areas['country']) && $areas['country']) {
            $qb
                ->andWhere(
                    'h.countryCode = :country'
                )
                ->setParameter(
                    ':country', $areas['country']
                );
        }

        if (isset($continentId)) {
            $qb
                ->andWhere(
                    'h.continentId = :continentId'
                )
                ->setParameter(
                    ':continentId', $continentId
                );
        }

        if (isset($areas['hotelName']) && $areas['hotelName']) {
            $qb
                ->andWhere(
                    'LOWER(h.name) = :hotelName'
                )
                ->setParameter(
                    ':hotelName', strtolower($areas['hotelName'])
                );
        }

        if (isset($areas['hotelNameLike']) && $areas['hotelNameLike']) {
            $qb
                ->andWhere(
                    'LOWER(h.name) LIKE :hotelNameLike'
                )
                ->setParameter(
                    ':hotelNameLike', '%' . strtolower($areas['hotelNameLike'] . '%')
                );
        }

        $qb->setFirstResult($offset)
            ->setMaxResults($relatedHotelsNo);

        $hotels = $qb->getQuery()->getResult();

        return $hotels;
    }

    /**
     * @param $term
     * @return HotelSearchResult
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getHotelsByTerm(
        $term
    )
    {
        $hotelSearchResult = new HotelSearchResult();
        $hotelSearchResult->setTerm($term);

        if ($this->isTermACountry($term) === true) {
            $hotelSearchResult->setTermIsCountry(true);
            $cc1 = array_search(strtolower($term), array_map('strtolower', Hotel::$COUNTRY_CODE_LIST));
            $hotelSearchResult->setHotelsResult(
                $this->getHotelsInArea(
                    ['country' => $cc1],
                    20
                )
            );
        } elseif ($this->isTermACity($term) === true) {
            $hotelSearchResult->setTermIsCity(true);
            $hotelSearchResult->setHotelsResult(
                $this->getHotelsInArea(
                    ['city' => $term],
                    20
                )
            );
        } elseif ($this->isTermAHotel($term) === true) {
            $hotelSearchResult->setTermIsHotelName(true);
            $hotelSearchResult->setHotelsResult(
                $this->getHotelsInArea(
                    ['hotelName' => $term],
                    20
                )
            );
        } else {
            $hotelSearchResult->setTermExistInHotelName(true);
            $hotelSearchResult->setHotelsResult(
                $this->getHotelsInArea(
                    ['hotelNameLike' => $term],
                    20
                )
            );
        }

        return $hotelSearchResult;
    }

    /**
     * @param $continentName
     * @return false|int|string
     * @throws \Exception
     */
    protected function getContinentId(
        $continentName
    )
    {
        $continentId = array_search($continentName, Hotel::$CONTINENT_ID);

        if ($continentId === false) {
            throw new \Exception('Invalid continent');
        }

        return $continentId;
    }

    /**
     * @param $term
     * @return bool - true if the terms is a country, false otherwise
     */
    private function isTermACountry($term)
    {
        // count how many related hotels there is
        $query = $this->getEntityManager()
            ->createQuery(
            "SELECT h.id FROM Infotrip\Domain\Entity\Hotel h
                  WHERE h.countryCode = :cc1"
            )
            ->setMaxResults(1);

        $cc1 = array_search(strtolower($term), array_map('strtolower', Hotel::$COUNTRY_CODE_LIST));
        if (! $cc1) {
            return false;
        }
        $query->setParameter('cc1', strtolower($cc1));

        $hotels = $query->getResult();

        if (count($hotels)) {
            return true;
        }

        return false;
    }

    /**
     * @param $term
     * @return bool
     */
    private function isTermACity(
        $term
    )
    {
        // count how many related hotels there is
        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT h.id FROM Infotrip\Domain\Entity\Hotel h
                  WHERE LOWER(h.cityUnique) = :cityUnique"
            )
            ->setMaxResults(1);

        $query->setParameter('cityUnique', strtolower($term));

        $hotels = $query->getResult();

        if (count($hotels)) {
            return true;
        }

        return false;
    }

    private function isTermAHotel(
        $term
    )
    {
        // count how many related hotels there is
        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT h.id FROM Infotrip\Domain\Entity\Hotel h
                  WHERE LOWER(h.name) = :name"
            )
            ->setMaxResults(1);

        $query->setParameter('name', strtolower($term));

        $hotels = $query->getResult();

        if (count($hotels)) {
            return true;
        }

        return false;
    }

}