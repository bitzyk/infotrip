<?php

namespace Infotrip\Service\Booking\CountryCsvImporter\Entity;


class HotelCsvLine
{

    /**
     * @var int
     */
    private $hotelIdBooking;

    /**
     * @var string
     */
    private $hotelName;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $bookingUrl;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @return int
     */
    public function getHotelIdBooking()
    {
        return $this->hotelIdBooking;
    }

    /**
     * @param int $hotelIdBooking
     * @return HotelCsvLine
     */
    public function setHotelIdBooking($hotelIdBooking)
    {
        $this->hotelIdBooking = $hotelIdBooking;
        return $this;
    }

    /**
     * @return string
     */
    public function getHotelName()
    {
        return $this->hotelName;
    }

    /**
     * @param string $hotelName
     * @return HotelCsvLine
     */
    public function setHotelName($hotelName)
    {
        $this->hotelName = $hotelName;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return HotelCsvLine
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBookingUrl()
    {
        return $this->bookingUrl;
    }

    /**
     * @param mixed $bookingUrl
     * @return HotelCsvLine
     */
    public function setBookingUrl($bookingUrl)
    {
        $this->bookingUrl = $bookingUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     * @return HotelCsvLine
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
        return $this;
    }





}