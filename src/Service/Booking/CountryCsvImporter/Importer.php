<?php

namespace Infotrip\Service\Booking\CountryCsvImporter;

use Infotrip\Domain\Entity\Hotel;
use Infotrip\Domain\Repository\HotelRepository;
use Infotrip\Service\Booking\CountryCsvImporter\Entity\HotelCsvLine;
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

    const PATH_PROCESSED_IMPORT = '/public_html/var/booking_hotel_csv/processed';

    const PATH_ERROR_IMPORT = '/public_html/var/booking_hotel_csv/error';

    public function __construct(
        LineParserInterface $lineParser,
        HotelRepository $hotelRepository
    )
    {
        $this->lineParser = $lineParser;
        $this->hotelRepository = $hotelRepository;
        set_time_limit(0);
        ini_set('memory_limit', '1G');
    }

    public function import()
    {
        $importResult = new ImportResult();

        $nextToImportFilePath = $this->getNextToImportFilePath();

        if (! $nextToImportFilePath) {
            $importResult->setStatusSuccess(true);
            return $importResult;
        }


        $fh = fopen($nextToImportFilePath, 'r');

        while (($lineData = fgetcsv($fh)) !== FALSE) {

            try {
                $hotelCsvLine = $this->hydrateCsvLine($lineData, $importResult);
                if ($hotelCsvLine instanceof HotelCsvLine) {
                    $this->importCsvLine($hotelCsvLine, $importResult);
                }
            } catch (\Exception $e) {
                // exception found -> move the file to the error queue -> do not ocntinue further
                rename($nextToImportFilePath, $this->getErrorPath() . '/' . basename($nextToImportFilePath));
                return $importResult;
            }

        }
        fclose($fh);

        // move the file to the processed folder
        rename($nextToImportFilePath, $this->getProcessedPath() . '/' . basename($nextToImportFilePath));

        $importResult
            ->setStatusSuccess(true);

        return $importResult;
    }

    /**
     * @param array $lineData
     * @param ImportResult $importResult
     * @return Entity\HotelCsvLine|null
     * @throws \Exception
     */
    private function hydrateCsvLine(
        array $lineData,
        ImportResult $importResult
    )
    {
        static $i = 1;

        $importResult->setCsvLines($importResult->getCsvLines() + 1);

        // validate the line -> if the line data is not valid -> continue
        if (count($lineData) !== 4) {
            return null;
        }

        $importResult->setCsvValidLines($importResult->getCsvValidLines() + 1);


        // if if is the header line -> continue
        if (
            ($i === 1) &&
            (strpos(strtolower($lineData[0]), 'hotels') !== false)
        ) {
            $importResult->setHasHeader(true);
            return null;
        }

        $i++;

        return $this->lineParser->parse(
            $lineData
        );
    }

    /**
     * @param HotelCsvLine $hotelCsvLine
     * @param ImportResult $importResult
     * @throws \Exception
     */
    private function importCsvLine(
        HotelCsvLine $hotelCsvLine,
        ImportResult $importResult
    )
    {
        $hotelDbRow = $this->hotelRepository->getHotel(
            $hotelCsvLine->getHotelIdBooking(),
            false
        );

        $hotelExist = $hotelDbRow instanceof Hotel;


        $hotelEntity = ($hotelExist) ? $hotelDbRow : new Hotel();

        $this->mapHotelInfoFromCsvLine($hotelEntity, $hotelCsvLine);

        if ($hotelExist) {
            $this->hotelRepository->updateHotel($hotelEntity);
            $importResult->setUpdatedHotels($importResult->getUpdatedHotels() + 1);
        } else {
            $this->hotelRepository->insertHotel($hotelEntity);
            $importResult->setInsertedHotels($importResult->getInsertedHotels() + 1);
        }

        $this->hotelRepository->clear();
    }

    /**
     * @param Hotel $hotel
     * @param HotelCsvLine $hotelCsvLine
     */
    private function mapHotelInfoFromCsvLine(
        Hotel $hotel,
        HotelCsvLine $hotelCsvLine
    )
    {
        $hotel
            ->setCountryCode($hotelCsvLine->getCountryCode())
            ->setName($hotelCsvLine->getHotelName())
            ->setBookingHotelUrl($hotelCsvLine->getBookingUrl())
            ->setCityUnique($hotelCsvLine->getCity())
            ->setCityHotel($hotelCsvLine->getCity())
            ->setVisible(1)
        ;

        if (
            ! $hotel->getId()
        ) {
            $hotel->setId($hotelCsvLine->getHotelIdBooking());
        }
    }

    /**
     * @return null|string
     */
    private function getNextToImportFilePath()
    {
        $queueDir = $this->getQueuePath();

        $files = array_diff(scandir($queueDir), ['..', '.']);

        $nextToImportFilePath = null;
        if (! empty($files)) {
            $nextToImportFilePath = $queueDir . '/' . array_shift($files);
        }

        return $nextToImportFilePath;
    }

    private function getQueuePath()
    {
        return realpath(APP_ROOT . '/..' . self::PATH_QUEUE_IMPORT);
    }

    private function getProcessedPath()
    {
        return realpath(APP_ROOT . '/..' . self::PATH_PROCESSED_IMPORT);
    }

    private function getErrorPath()
    {
        return realpath(APP_ROOT . '/..' . self::PATH_ERROR_IMPORT);
    }

}