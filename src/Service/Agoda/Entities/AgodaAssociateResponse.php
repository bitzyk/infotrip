<?php

namespace Infotrip\Service\Agoda\Entities;

class AgodaAssociateResponse
{
    private $newAssociations = 0;

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


}