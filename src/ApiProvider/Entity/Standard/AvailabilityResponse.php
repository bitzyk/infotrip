<?php

namespace Infotrip\ApiProvider\Entity\Standard;


class AvailabilityResponse implements \JsonSerializable
{

    /**
     * @var float
     */
    private $pricePerNight;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var boolean
     */
    private $includeBreakfast = false;

    /**
     * @var string
     */
    private $landingURL;

    /**
     * @var string
     */
    private $providerImageUrl;

    /**
     * @return float
     */
    public function getPricePerNight()
    {
        return $this->pricePerNight;
    }

    /**
     * @param float $pricePerNight
     * @return AvailabilityResponse
     */
    public function setPricePerNight($pricePerNight)
    {
        $this->pricePerNight = $pricePerNight;
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
     * @return AvailabilityResponse
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return bool
     */
    public function isIncludeBreakfast()
    {
        return $this->includeBreakfast;
    }

    /**
     * @param bool $includeBreakfast
     * @return AvailabilityResponse
     */
    public function setIncludeBreakfast($includeBreakfast)
    {
        $this->includeBreakfast = $includeBreakfast;
        return $this;
    }

    /**
     * @return string
     */
    public function getLandingURL()
    {
        return $this->landingURL;
    }

    /**
     * @param string $landingURL
     * @return AvailabilityResponse
     */
    public function setLandingURL($landingURL)
    {
        $this->landingURL = $landingURL;
        return $this;
    }

    /**
     * @return string
     */
    public function getProviderImageUrl()
    {
        return $this->providerImageUrl;
    }

    /**
     * @param string $providerImageUrl
     * @return AvailabilityResponse
     */
    public function setProviderImageUrl($providerImageUrl)
    {
        $this->providerImageUrl = $providerImageUrl;
        return $this;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

}