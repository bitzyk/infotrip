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

    const PATH_QUEUE_IMPORT = '/public_html/var/booking_hotel_csv/queue';

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

        $nextToImportFilePath = $this->getNextToImportFilePath();

        if (! $nextToImportFilePath) {
            $importResult->setStatusSuccess(true);
            return $importResult;
        }

        var_dump($nextToImportFilePath);
        exit;

        return $importResult;
    }

    /**
     * @return null|string
     */
    private function getNextToImportFilePath()
    {
        $queueDir = realpath(APP_ROOT . '/..' . self::PATH_QUEUE_IMPORT);

        $files = array_diff(scandir($queueDir), ['..', '.']);

        $nextToImportFilePath = null;
        if (! empty($files)) {
            $nextToImportFilePath = $queueDir . '/' . array_shift($files);
        }

        return $nextToImportFilePath;
    }


}