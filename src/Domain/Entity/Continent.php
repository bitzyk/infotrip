<?php
/**
 * Created by PhpStorm.
 * User: cbitoi
 * Date: 05/09/2018
 * Time: 17:53
 */

namespace Infotrip\Domain\Entity;


use Doctrine\ORM\EntityManager;
use Infotrip\Domain\Repository\HotelRepository;

class Continent
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    public static $CONTINENT_ID = [
        1 => 'North America',
        2 => 'Central America',
        3 => 'South America',
        5 => 'Africa',
        6 => 'Europe',
        7 => 'Middle East', // Middle East
        8 => 'Asia',
        9 => 'Australia & Pacific',
        10 => 'Caribbean',
    ];

    /**
     * Continent constructor.
     * @param $id
     * @throws \Exception
     */
    public function __construct(
        $id = null,
        $name = null
    )
    {
        if ($id) {
            $this->id = $id;
            $this->computeContinentNameFromId();
        } elseif ($name) {
            $this->name = $name;
            $this->computeContinentIdFromName();

        }
    }

    /**
     * @param EntityManager $entityManager
     * @return Country[]
     */
    public function getCountries(
        EntityManager $entityManager
    )
    {
        // count how many related hotels there is
        $query = $entityManager
            ->createQuery(
                "SELECT distinct(h.countryCode) as countryCode FROM Infotrip\Domain\Entity\Hotel h WHERE h.continentId = :continentId"
            );

        $query->setParameter('continentId', $this->id);
        $rows = $query->getResult();

        $countries = [];
        if (count($rows)) {
            foreach ($rows as $row) {
                try {
                    $country = new Country($row['countryCode']);
                    $country->setContinent($this);
                    $countries[] = $country;

                } catch (\Exception $e) {}
            }
        }

        return $countries;
    }

    /**
     * @throws \Exception
     */
    private function computeContinentNameFromId()
    {
        if (isset(self::$CONTINENT_ID[$this->id])) {
            $this->name = self::$CONTINENT_ID[$this->id];
        } else {
            throw new \Exception('Invalid continent id.');
        }
    }

    /**
     * @throws \Exception
     */
    private function computeContinentIdFromName()
    {
        if (in_array($this->name, self::$CONTINENT_ID)) {
            $continentsFlipped = array_flip(self::$CONTINENT_ID);
            $this->id = $continentsFlipped[$this->name];
        } else {
            throw new \Exception('Invalid continent id.');
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

}