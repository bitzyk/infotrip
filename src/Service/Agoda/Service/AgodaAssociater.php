<?php

namespace Infotrip\Service\Agoda\Service;


use Infotrip\Service\Agoda\Entities\AgodaAssociateResponse;
use Infotrip\Service\Agoda\Service\AgodaAssociater\AssociaterFactory;

class AgodaAssociater
{
    /**
     * @var AssociaterFactory
     */
    private $associaterFactory;

    public function __construct(
        AssociaterFactory $associaterFactory
    )
    {
        $this->associaterFactory = $associaterFactory;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    public function associateHotels($level)
    {
        // set dependency
        ini_set('memory_limit' ,'512M');
        set_time_limit(180);

        $agodaAssociateResponse = new AgodaAssociateResponse();
        $strategy = $this->associaterFactory->getAssociater($level);

        $strategy->associate($agodaAssociateResponse);

        return $agodaAssociateResponse;
    }
}