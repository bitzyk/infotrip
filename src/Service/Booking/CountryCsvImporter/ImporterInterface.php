<?php

namespace Infotrip\Service\Booking\CountryCsvImporter;

use Infotrip\Service\Booking\CountryCsvImporter\Entity\ImportResult;

interface ImporterInterface
{
    /**
     * @return ImportResult
     */
    public function import();

}