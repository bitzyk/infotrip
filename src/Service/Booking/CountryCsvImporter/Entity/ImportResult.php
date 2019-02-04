<?php

namespace Infotrip\Service\Booking\CountryCsvImporter\Entity;


class ImportResult implements \JsonSerializable
{

    private $statusSuccess = false;

    private $csvLines = 0;

    private $csvValidLines = 0;

    private $updatedHotels = 0;

    private $insertedHotels = 0;

    private $hasHeader = false;

    /**
     * @return bool
     */
    public function isStatusSuccess()
    {
        return $this->statusSuccess;
    }

    /**
     * @param bool $statusSuccess
     * @return ImportResult
     */
    public function setStatusSuccess($statusSuccess)
    {
        $this->statusSuccess = $statusSuccess;
        return $this;
    }

    /**
     * @return int
     */
    public function getCsvLines()
    {
        return $this->csvLines;
    }

    /**
     * @param int $csvLines
     * @return ImportResult
     */
    public function setCsvLines($csvLines)
    {
        $this->csvLines = $csvLines;
        return $this;
    }

    /**
     * @return int
     */
    public function getCsvValidLines()
    {
        return $this->csvValidLines;
    }

    /**
     * @param int $csvValidLines
     * @return ImportResult
     */
    public function setCsvValidLines($csvValidLines)
    {
        $this->csvValidLines = $csvValidLines;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHasHeader()
    {
        return $this->hasHeader;
    }

    /**
     * @param bool $hasHeader
     * @return ImportResult
     */
    public function setHasHeader($hasHeader)
    {
        $this->hasHeader = $hasHeader;
        return $this;
    }

    /**
     * @return int
     */
    public function getUpdatedHotels()
    {
        return $this->updatedHotels;
    }

    /**
     * @param int $updatedHotels
     * @return ImportResult
     */
    public function setUpdatedHotels($updatedHotels)
    {
        $this->updatedHotels = $updatedHotels;
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
     * @return ImportResult
     */
    public function setInsertedHotels($insertedHotels)
    {
        $this->insertedHotels = $insertedHotels;
        return $this;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }


}