<?php

namespace Infotrip\Service\Booking\CountryCsvImporter\Service;

use Infotrip\Service\Booking\CountryCsvImporter\Entity\HotelCsvLine;

class LineParser implements LineParserInterface
{

    /**
     * @param array $data
     * @return HotelCsvLine
     */
    public function parse(array $data)
    {
        $hotelCsvLine = new HotelCsvLine();

        return $hotelCsvLine;
    }

}