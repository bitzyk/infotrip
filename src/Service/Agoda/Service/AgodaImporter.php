<?php

namespace Infotrip\Service\Agoda\Service;

use Infotrip\Service\Agoda\Entities\AgodaImportResponse;

class AgodaImporter
{
    /**
     * @var string
     */
    private $csvImportPath;

    /**
     * @var array
     */
    private $toImportIds = [];

    /**
     * @var AgodaImportResponse
     */
    private $agodaImportResponse;


    /**
     * @param $csvImportPath
     * @return AgodaImportResponse
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

        // read csv
        $this->readCsv();

        return $this->agodaImportResponse;
    }

    private function readLine(
        array $lineData
    )
    {
        // in case the line is not valid -> return
        if (! $this->validateLine($lineData)) {
            return;
        }

        // set ids to import
        $this->toImportIds[$lineData[0]] = $lineData[0];

        // check if the hotelId in not already in our Agoda DB
            // -> if is already in our agodo DB -> return


        // hydrate entity to insert

        // add entity to the bulk insert

        // if the bulk reached the limit then -> insert
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


    private function readCsv()
    {
        $i = 0;
        $file = fopen($this->csvImportPath, 'r');
        while (($line = fgetcsv($file)) !== FALSE) {

            // jump over the first line
            if ($i==0)  {
                $i++; continue;
            }

            $this->readLine($line);

            $i++;
        }
        fclose($file);

        $this->agodaImportResponse
            ->setCsvLines($i);
    }

    private function resetService()
    {
        $this->csvImportPath = null;
        $this->toImportIds = [];
        $this->agodaImportResponse = null;
    }

}