<?php

namespace Infotrip\Service\Agoda\Entities;


class AgodaImportResponse
{

    /**
     * @var int
     */
    private $csvLines = 0;

    private $validLines = 0;

    private $insertedHotels = 0;

    private $alreadyExistingHotels = 0;

    /**
     * @return int
     */
    public function getCsvLines()
    {
        return $this->csvLines;
    }

    /**
     * @param int $csvLines
     * @return AgodaImportResponse
     */
    public function setCsvLines($csvLines)
    {
        $this->csvLines = $csvLines;
        return $this;
    }

    /**
     * @return int
     */
    public function getValidLines()
    {
        return $this->validLines;
    }

    /**
     * @return AgodaImportResponse
     */
    public function incrementValidLines()
    {
        $this->validLines++;
        return $this;
    }

    /**
     * @return int
     */
    public function getInsertedHotels()
    {
        return $this->insertedHotels;
    }

    /**
     * @param int $insertedHotels
     * @return AgodaImportResponse
     */
    public function incrementInsertedHotels($insertedHotels)
    {
        $this->insertedHotels += $insertedHotels;
        return $this;
    }

    /**
     * @return int
     */
    public function getAlreadyExistingHotels()
    {
        return $this->alreadyExistingHotels;
    }

    /**
     * @return AgodaImportResponse
     */
    public function incrementAlreadyExistingHotels()
    {
        $this->alreadyExistingHotels++;
        return $this;
    }


}