<?php

namespace Infotrip\Service\Agoda\Entities;


class AgodaImportResponse
{

    /**
     * @var int
     */
    private $csvLines = 0;

    private $validLines = 0;

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



}