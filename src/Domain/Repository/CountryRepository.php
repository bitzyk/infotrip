<?php

namespace Infotrip\Domain\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Infotrip\Domain\Entity\Hotel;

class CountryRepository extends EntityRepository
{

    public function __construct(EntityManager $em, Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
    }

    /**
     * @param string $countryCode
     * @return string
     * @throws \Exception
     */
    public function getContinentIdForCountryCode(
        $countryCode
    )
    {
        /** @var Hotel $hotel */
        $hotel = $this->getEntityManager()
            ->getRepository('Infotrip\Domain\Entity\Hotel')
            ->findOneBy(array(
                'countryCode' => $countryCode
            ));

        if (! $hotel instanceof Hotel) {
            throw new \Exception(sprintf(
                'Continent id does not exist for country code: `%s`',
                $countryCode
            ));
        }

        return $hotel->getContinentId();
    }

}