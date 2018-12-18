<?php

namespace Infotrip\Service\Agoda\Service\AgodaAssociater;

use Infotrip\Service\Agoda\Entities\AgodaAssociateResponse;

class AssociaterLevel1 extends AbstractAssociater
{

    /**
     * @param AgodaAssociateResponse $agodaAssociateResponse
     * @return AgodaAssociateResponse
     * @throws \Exception
     */
    public function associate(
        AgodaAssociateResponse $agodaAssociateResponse
    )
    {
        $toAssociate = $this->agodaHotelRepository->getAssocHotels($level = 1);

        $this->insertToAssociate($agodaAssociateResponse, $toAssociate);

        $agodaAssociateResponse
            ->setNameAssociation('Association level 1: Identic hotel name, country code, city and zipcode for unassociated hotels');

        return $agodaAssociateResponse;
    }


}