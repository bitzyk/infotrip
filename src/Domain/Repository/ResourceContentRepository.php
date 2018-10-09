<?php

namespace Infotrip\Domain\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Infotrip\Domain\Entity\ResourceContent;

class ResourceContentRepository extends EntityRepository
{

    public function __construct(EntityManager $em, Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
    }

    /**
     * @param $resourceName
     * @return ResourceContent|null
     * @throws \Exception
     */
    public function getResourceContent(
        $resourceName
    )
    {
        $entity = $this->getEntityManager()
            ->getRepository('Infotrip\Domain\Entity\ResourceContent')
            ->findOneBy(array(
                'resourceName' => $resourceName,
            ));

        return $entity;
    }

}