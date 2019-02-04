<?php

namespace Infotrip\Service\Booking\CountryCsvImporter\Service;

use Infotrip\Service\Booking\CountryCsvImporter\Entity\HotelCsvLine;
use Infotrip\Service\Booking\CountryCsvImporter\Entity\ImportResult;

interface LineParserInterface
{
    /**
     * @param array $data
     * @return HotelCsvLine
     * @throws \Exception
     */
    public function parse(
        array $data
    );
}