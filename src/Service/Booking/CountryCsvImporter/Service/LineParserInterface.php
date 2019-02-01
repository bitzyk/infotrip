<?php

namespace Infotrip\Service\Booking\CountryCsvImporter\Service;

use Infotrip\Service\Booking\CountryCsvImporter\Entity\HotelCsvLine;

interface LineParserInterface
{
    /**
     * @param array $data
     * @return HotelCsvLine
     */
    public function parse(array $data);
}