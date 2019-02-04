<?php

namespace Infotrip\Service\Booking\CountryCsvImporter\Service;

use Infotrip\Service\Booking\CountryCsvImporter\Entity\HotelCsvLine;
use Infotrip\Service\Booking\CountryCsvImporter\Entity\ImportResult;

class LineParser implements LineParserInterface
{

    /**
     * @param array $data
     * @return HotelCsvLine
     * @throws \Exception
     */
    public function parse(
        array $data
    )
    {
        $hotelCsvLine = new HotelCsvLine();

        $hotelCsvLine
            ->setHotelIdBooking((int) $data[1])
            ->setHotelName($data[0])
            ->setCity($data[2])
            ->setBookingUrl(strtok($data[3], '?'))

        ;

        // compute and set countryCode
        $countryCode = strtolower(explode('/', parse_url($data[3])['path'])[2]);

        if (
            ! in_array(strtoupper($countryCode), array_keys(\Infotrip\Domain\Entity\Country::$COUNTRY_CODE_LIST))
        ) {
            throw new \Exception(sprintf('Invalid country code `%s`', $countryCode));
        }

        $hotelCsvLine->setCountryCode($countryCode);

        return $hotelCsvLine;
    }

}