<?php

namespace Infotrip\Service\Agoda;


use Infotrip\Service\Agoda\Service\AgodaAssociater;
use Infotrip\Service\Agoda\Service\AgodaImporter;

class AgodaService
{

    /**
     * @var AgodaImporter
     */
    private $agodaImporter;

    /**
     * @var AgodaAssociater
     */
    private $agodaAssociater;

    public function __construct(
        AgodaImporter $agodaImporter,
        AgodaAssociater $agodaAssociater
    )
    {

        $this->agodaImporter = $agodaImporter;
        $this->agodaAssociater = $agodaAssociater;
    }

    /**
     * @param string $csvImportPath
     * @return Entities\AgodaImportResponse
     * @throws \Exception
     */
    public function importHotels(
        $csvImportPath
    )
    {
        return $this->agodaImporter->importHotels($csvImportPath);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function associateHotels($level)
    {
        return $this->agodaAssociater->associateHotels($level);
    }
}