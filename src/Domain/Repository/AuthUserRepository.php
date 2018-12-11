<?php

namespace Infotrip\Domain\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;


class AuthUserRepository extends EntityRepository
{

    /**
     * @return \Infotrip\Domain\Entity\AuthUser[]
     */
    public function getAll()
    {
        $users = $this->findBy(
            [],
            [
                'email' => 'ASC'
            ]
        );

        return $users;
    }

}