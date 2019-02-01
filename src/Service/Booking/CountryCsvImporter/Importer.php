<?php

namespace Infotrip\Service\Booking\CountryCsvImporter;


use Infotrip\Domain\Repository\HotelRepository;
use Infotrip\Service\Booking\CountryCsvImporter\Entity\ImportResult;
use Infotrip\Service\Booking\CountryCsvImporter\Service\LineParserInterface;

class Importer implements ImporterInterface
{

    /**
     * @var LineParserInterface
     */
    private $lineParser;

    /**
     * @var HotelRepository
     */
    private $hotelRepository;

    public function __construct(
        LineParserInterface $lineParser,
        HotelRepository $hotelRepository
    )
    {
        $this->lineParser = $lineParser;
        $this->hotelRepository = $hotelRepository;
    }


    public function import()
    {
        $importResult = new ImportResult();

        return $importResult;
    }


}