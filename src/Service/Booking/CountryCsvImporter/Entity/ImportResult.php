<?php

namespace Infotrip\Service\Booking\CountryCsvImporter\Entity;


class ImportResult
{

    private $statusSuccess = false;

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

}