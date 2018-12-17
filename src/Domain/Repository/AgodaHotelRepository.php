<?php

namespace Infotrip\Domain\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;


class AgodaHotelRepository extends EntityRepository
{

    public function insertBulk(
        $agodaHotels
    )
    {
        var_dump(count($agodaHotels));
        exit;
    }
}