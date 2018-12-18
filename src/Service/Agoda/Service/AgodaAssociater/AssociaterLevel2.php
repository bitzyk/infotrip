<?php

namespace Infotrip\Service\Agoda\Service\AgodaAssociater;


use Infotrip\Domain\Entity\HotelAssoc;
use Infotrip\Service\Agoda\Entities\AgodaAssociateResponse;

class AssociaterLevel2 extends AbstractAssociater
{

    const ASSOC_NAME = 'Association level 2: Identic hotel name, country code, city for unassociated hotels';

    /**
     * @param AgodaAssociateResponse $agodaAssociateResponse
     * @return AgodaAssociateResponse
     * @throws \Exception
     */
    public function associate(
        AgodaAssociateResponse $agodaAssociateResponse
    )
    {
        $toAssociate = $this->agodaHotelRepository->getAssocHotels($level = 2);

        $this->insertToAssociate($agodaAssociateResponse, $toAssociate);

        $agodaAssociateResponse
            ->setNameAssociation(self::ASSOC_NAME);

        return $agodaAssociateResponse;
    }


}