<?php

namespace Infotrip\Domain\Entity;

class City
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $noHotels;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getNoHotels()
    {
        return $this->noHotels;
    }

    /**
     * @param int $noHotels
     * @return City
     */
    public function setNoHotels($noHotels)
    {
        $this->noHotels = $noHotels;
        return $this;
    }


}