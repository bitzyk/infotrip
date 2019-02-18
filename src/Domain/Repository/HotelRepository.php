<?php

namespace Infotrip\Domain\Repository;

use Desarrolla2\Cache\Adapter\File;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Infotrip\Domain\Entity\Hotel;
use Infotrip\Domain\Entity\HotelSearchResult;
use Infotrip\Utils\Pagination;

class HotelRepository extends EntityRepository
{

    /**
     * @var File
     */
    protected $fileCache;

    const KEY_CACHE_RANDOM_HOTEL = 'RANDOM_HOTEL';

    public function __construct(EntityManager $em, Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
    }

    /**
     * @param int $hotelId
     * @param bool $strict
     * @param bool $onlyVisible
     * @return Hotel
     * @throws \Exception
     */
    public function getHotel(
        $hotelId,
        $strict = true,
        $onlyVisible = true
    )
    {
        $where = array(
            'id' => $hotelId,
        );

        if ($onlyVisible) {
            $where['visible'] = '1';
        }

        $hotel = $this->getEntityManager()
            ->getRepository('Infotrip\Domain\Entity\Hotel')
            ->findOneBy($where);

        if (
            ! $hotel instanceof Hotel &&
            $strict
        ) {
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
     * @param null|Pagination $pagination
     * @return Hotel[]
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getHotelsInArea(
        array $areas,
        Pagination $pagination = null
    )
    {
        $where = [];
        $where[] = 'h.visible = :visible';

        if (isset($areas['city']) && $areas['city']) {
            $where[] = '(h.cityUnique = :cityUnique OR h.cityUniqueOld = :cityUniqueOld)';
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
            $query->setParameter('cityUniqueOld', $areas['city']);
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
        if (
            ! $pagination instanceof Pagination
        )
        {
            $relatedHotelsNo = 12;

            $offset = rand(
                0,
                (($relatedHotelsCount - $relatedHotelsNo) > 0) ?
                    ($relatedHotelsCount - $relatedHotelsNo) : 0
            );
        } else {
            $pagination
                ->setNoResults($relatedHotelsCount);

            $offset = $pagination->getOffset();
            $relatedHotelsNo = $pagination->getNoResultsPerPage();
        }

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
                    '(h.cityUnique = :cityUnique OR h.cityUniqueOld = :cityUniqueOld)'
                )
                ->setParameter(
                    ':cityUnique', $areas['city']
                )
                ->setParameter(
                    ':cityUniqueOld', $areas['city']
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
            $cc1 = array_search(strtolower($term), array_map('strtolower', \Infotrip\Domain\Entity\Country::$COUNTRY_CODE_LIST));
            $hotelSearchResult->setHotelsResult(
                $this->getHotelsInArea(
                    ['country' => $cc1]
                )
            );
        } elseif ($this->isTermACity($term) === true) {
            $hotelSearchResult->setTermIsCity(true);
            $hotelSearchResult->setHotelsResult(
                $this->getHotelsInArea(
                    ['city' => $term]
                )
            );
        } elseif ($this->isTermAHotel($term) === true) {
            $hotelSearchResult->setTermIsHotelName(true);
            $hotelSearchResult->setHotelsResult(
                $this->getHotelsInArea(
                    ['hotelName' => $term]
                )
            );
        } else {
            $hotelSearchResult->setTermExistInHotelName(true);
            $hotelSearchResult->setHotelsResult(
                $this->getHotelsInArea(
                    ['hotelNameLike' => $term]
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
        $continentId = array_search($continentName, \Infotrip\Domain\Entity\Continent::$CONTINENT_ID);

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

        $cc1 = array_search(strtolower($term), array_map('strtolower', \Infotrip\Domain\Entity\Country::$COUNTRY_CODE_LIST));
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

    /**
     * @param $cityUnique
     * @param $noPag
     * @return HotelSearchResult
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getHotelsByCity($cityUnique, $noPag)
    {
        $hotelSearchResult = new HotelSearchResult();
        $hotelSearchResult->setTerm($cityUnique);
        $hotelSearchResult->setTermIsCity(true);

        $pagination = new Pagination();
        $pagination
            ->setNoPag($noPag);

        $hotelSearchResult
            ->setPagination($pagination);

        $hotelSearchResult->setHotelsResult(
            $this->getHotelsInArea(
                ['city' => $cityUnique],
                $pagination
            )
        );

        return $hotelSearchResult;
    }

    /**
     * @param bool $cached
     * @return Hotel
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getRandomHotel(
        $cached = true
    )
    {
        $sql = 'SELECT id from hotels order by rand() limit 1';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);

        $stmt->execute();
        $row = $stmt->fetch();

        /** @var Hotel $entity */
        $entity = $this->find($row['id']);

        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        if (
            $cached &&
            $this->fileCache instanceof File &&
            $entity instanceof Hotel
        ) {

            $this->fileCache->setOption('ttl', 3600 * 24);

            $this->fileCache->set(
                self::KEY_CACHE_RANDOM_HOTEL,
                json_encode($entity)
            );
        }

        return $entity;
    }

    /**
     * @param File $fileCache
     */
    public function setFileCache($fileCache)
    {
        $this->fileCache = $fileCache;
    }

    /**
     * @param Hotel $hotel
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteHotel(
        Hotel $hotel
    )
    {
        // set hotel visible to 0
        $hotel
            ->setVisible(0);

        $this->getEntityManager()->merge($hotel);
        $this->getEntityManager()->flush($hotel);
    }

    /**
     * @param Hotel $hotel
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateHotel(
        Hotel $hotel
    )
    {
        $this->getEntityManager()->merge($hotel);
        $this->getEntityManager()->flush($hotel);
    }

    /**
     * @param Hotel $hotel
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function insertHotel(
        Hotel $hotel
    )
    {
        $this->getEntityManager()->persist($hotel);
        $this->getEntityManager()->flush($hotel);
    }

    /**
     *
     */
    public function clear()
    {
        $this->getEntityManager()->clear();
    }

    /**
     * @param $hotelId
     * @return Hotel
     * @throws \Exception
     */
    public function getApiHotel(
        $hotelId
    )
    {
        // count how many related hotels there is
        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT h.name, h.minrate, h.bookingHotelUrl, h.currencycode FROM Infotrip\Domain\Entity\Hotel h
                  WHERE h.id = :hotelId"
            );

        $query->setParameter('hotelId', $hotelId);

        $hotelData = $query->getSingleResult();

        $hotel = new Hotel();
        $hotel->setName($hotelData['name']);
        $hotel->setMinrate($hotelData['minrate']);
        $hotel->setBookingHotelUrl($hotelData['bookingHotelUrl']);
        $hotel->setCurrencycode($hotelData['currencycode']);

        return $hotel;
    }

}