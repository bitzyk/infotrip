<?php

namespace Infotrip\ApiProvider\Entity\Standard;


use Infotrip\Domain\Entity\HotelAssoc;

class AvailabilityRequest
{

    /**
     * @var string
     */
    private $providerHotelId;

    /**
     * @var string
     */
    private $internalHotelId;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var \DateTime
     */
    private $checkInDate;

    /**
     * @var \DateTime
     */
    private $checkOutDate;

    /**
     * @var HotelAssoc
     */
    private $hotelAssoc;

    /**
     * @var int
     */
    private $noAdult = 2;

    /**
     * @var int
     */
    private $noChildren = 0;

    /**
     * @return string
     */
    public function getProviderHotelId()
    {
        return $this->providerHotelId;
    }

    /**
     * @param string $providerHotelId
     * @return AvailabilityRequest
     */
    public function setProviderHotelId($providerHotelId)
    {
        $this->providerHotelId = $providerHotelId;
        return $this;
    }

    /**
     * @return string
     */
    public function getInternalHotelId()
    {
        return $this->internalHotelId;
    }

    /**
     * @param string $internalHotelId
     * @return AvailabilityRequest
     */
    public function setInternalHotelId($internalHotelId)
    {
        $this->internalHotelId = $internalHotelId;
        return $this;
    }


    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return AvailabilityRequest
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCheckInDate()
    {
        return $this->checkInDate;
    }

    /**
     * @param \DateTime $checkInDate
     * @return AvailabilityRequest
     */
    public function setCheckInDate(\DateTime $checkInDate)
    {
        $this->checkInDate = $checkInDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCheckOutDate()
    {
        return $this->checkOutDate;
    }

    /**
     * @param \DateTime $checkOutDate
     * @return AvailabilityRequest
     */
    public function setCheckOutDate(\DateTime $checkOutDate)
    {
        $this->checkOutDate = $checkOutDate;
        return $this;
    }

    /**
     * @return HotelAssoc
     */
    public function getHotelAssoc()
    {
        return $this->hotelAssoc;
    }

    /**
     * @param HotelAssoc $hotelAssoc
     * @return AvailabilityRequest
     */
    public function setHotelAssoc($hotelAssoc)
    {
        $this->hotelAssoc = $hotelAssoc;
        return $this;
    }

    /**
     * @return int
     */
    public function getNoAdult()
    {
        return $this->noAdult;
    }

    /**
     * @param int $noAdult
     * @return AvailabilityRequest
     */
    public function setNoAdult($noAdult)
    {
        $this->noAdult = $noAdult;
        return $this;
    }

    /**
     * @return int
     */
    public function getNoChildren()
    {
        return $this->noChildren;
    }

    /**
     * @param int $noChildren
     * @return AvailabilityRequest
     */
    public function setNoChildren($noChildren)
    {
        $this->noChildren = $noChildren;
        return $this;
    }

}