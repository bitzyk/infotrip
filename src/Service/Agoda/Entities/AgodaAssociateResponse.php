<?php

namespace Infotrip\Service\Agoda\Entities;

class AgodaAssociateResponse
{
    /**
     * @var int
     */
    private $newAssociations = 0;

    /**
     * @var string
     */
    private $nameAssociation = '';

    /**
     * @return int
     */
    public function getNewAssociations()
    {
        return $this->newAssociations;
    }

    /**
     * @param int $newAssociations
     * @return AgodaAssociateResponse
     */
    public function incrementNewAssociations($newAssociations)
    {
        $this->newAssociations += $newAssociations;
        return $this;
    }

    /**
     * @return string
     */
    public function getNameAssociation()
    {
        return $this->nameAssociation;
    }

    /**
     * @param string $nameAssociation
     * @return AgodaAssociateResponse
     */
    public function setNameAssociation($nameAssociation)
    {
        $this->nameAssociation = $nameAssociation;
        return $this;
    }


}