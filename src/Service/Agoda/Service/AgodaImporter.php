<?php

namespace Infotrip\Service\Agoda\Service;

use Infotrip\Domain\Entity\AgodaHotel;
use Infotrip\Domain\Repository\AgodaHotelRepository;
use Infotrip\Service\Agoda\Entities\AgodaImportResponse;

class AgodaImporter
{
    /**
     * @var string
     */
    private $csvImportPath;

    /**
     * @var AgodaImportResponse
     */
    private $agodaImportResponse;

    /**
     * @var AgodaHotelRepository
     */
    private $agodaHotelRepository;

    /**
     * @var array
     */
    private $headerCsvLine = [];

    private $allExistingAgodaHotelIds = [];

    /**
     * @var AgodaHotel[]
     */
    private $agodaHotelsToInsert = [];

    public function __construct(
        AgodaHotelRepository $agodaHotelRepository
    )
    {
        $this->agodaHotelRepository = $agodaHotelRepository;
    }


    /**
     * @param $csvImportPath
     * @return AgodaImportResponse
     * @throws \Exception
     */
    public function importHotels(
        $csvImportPath
    )
    {
        // reset service first
        $this->resetService();

        // set dependencies
        $this->csvImportPath = $csvImportPath;
        $this->agodaImportResponse = new AgodaImportResponse();
        $this->allExistingAgodaHotelIds = $this->agodaHotelRepository->getAllExistingAgodaHotelIds();

        // read csv
        $this->readCsv();

        // insert the remaining bulk
        $this->insertAgodaBulk();

        return $this->agodaImportResponse;
    }

    /**
     * @param array $lineData
     * @throws \Exception
     */
    private function readLine(
        array $lineData
    )
    {
        // in case the line is not valid -> return
        if (! $this->validateLine($lineData)) {
            return;
        }

        // check if the hotelId in not already in our Agoda DB
        if (isset($this->allExistingAgodaHotelIds[$lineData[0]])) {
            $this->agodaImportResponse->incrementAlreadyExistingHotels();
            // -> if is already in our agodo DB -> return
            return;
        }

        // hydrate entity to insert
        $agodaHotel = $this->hydrateAgodaHotel($lineData);

        // add entity to the bulk insert
        $this->agodaHotelsToInsert[] = $agodaHotel;

        // if the bulk reached the limit then -> insert
        if (count($this->agodaHotelsToInsert) >= 1000) {
            $this->insertAgodaBulk();
        }
    }

    /**
     * @throws \Exception
     */
    private function insertAgodaBulk()
    {
        // insert via repository
        $this->agodaHotelRepository->insertBulk($this->agodaHotelsToInsert);

        $this->agodaImportResponse->setInsertedHotels(count($this->agodaHotelsToInsert));

        // reset the bulk variable to 0
        $this->agodaHotelsToInsert = [];
    }

    private function validateLine(
        array $lineData
    )
    {
        if (count($lineData) == 39) {
            $this->agodaImportResponse->incrementValidLines();
            return true;
        }

        return false;
    }

    /**
     * @throws \Exception
     */
    private function readCsv()
    {
        $i = 0;
        $file = fopen($this->csvImportPath, 'r');
        while (($line = fgetcsv($file)) !== FALSE) {

            // jump over the first line
            if ($i==0)  {
                $this->headerCsvLine = $line;
                $i++; continue;
            }

            $this->readLine($line);

            $i++;
        }
        fclose($file);

        $this->agodaImportResponse
            ->setCsvLines($i);
    }


    /**
     * @param $lineData
     * @return AgodaHotel
     */
    private function hydrateAgodaHotel(
        $lineData
    )
    {
        $agoraHotel = new AgodaHotel();

        foreach ($this->headerCsvLine as $index => $fieldName) {
           $methodName = sprintf('set%s', implode('', array_map(function ($val) {
               return ucfirst(strtolower($val));
           }, explode('_', $fieldName))));

            if (method_exists($agoraHotel, $methodName)) {
                $agoraHotel->$methodName($lineData[$index]);
            }
        }

        return $agoraHotel;
    }

    private function resetService()
    {
        $this->csvImportPath = null;
        $this->agodaImportResponse = null;
        $this->headerCsvLine = [];
        $this->agodaHotelsToInsert = [];
        $this->allExistingAgodaHotelIds = [];
    }
}