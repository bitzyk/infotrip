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

    public function importHotels(
        $csvImportPath
    )
    {
        return $this->agodaImporter->importHotels($csvImportPath);
    }

    public function associateHotels()
    {
        return $this->agodaAssociater->associateHotels();
    }
}