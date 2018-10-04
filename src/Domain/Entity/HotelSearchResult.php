<?php
/**
 * Created by PhpStorm.
 * User: cbitoi
 * Date: 13/06/2018
 * Time: 22:31
 */

namespace Infotrip\Domain\Entity;


use Infotrip\Utils\Pagination;

class HotelSearchResult
{
    /**
     * @var string
     */
    private $term = '';

    /**
     * @var boolean
     */
    private $termIsCountry = false;

    /**
     * @var boolean
     */
    private $termIsCity = false;

    /**
     * @var boolean
     */
    private $termIsHotelName = false;

    /**
     * @var boolean
     */
    private $termExistInHotelName = false;

    /**
     * @var Hotel[]
     */
    private $hotelsResult = array();

    /**
     * @var Pagination
     */
    private $pagination;

    /**
     * @return string
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * @param string $term
     */
    public function setTerm($term)
    {
        $this->term = $term;
    }

    /**
     * @return bool
     */
    public function isTermIsCountry()
    {
        return $this->termIsCountry;
    }

    /**
     * @param bool $termIsCountry
     */
    public function setTermIsCountry($termIsCountry)
    {
        $this->termIsCountry = $termIsCountry;
    }

    /**
     * @return bool
     */
    public function isTermIsCity()
    {
        return $this->termIsCity;
    }

    /**
     * @param bool $termIsCity
     */
    public function setTermIsCity($termIsCity)
    {
        $this->termIsCity = $termIsCity;
    }

    /**
     * @return bool
     */
    public function isTermIsHotelName()
    {
        return $this->termIsHotelName;
    }

    /**
     * @param bool $termIsHotelName
     */
    public function setTermIsHotelName($termIsHotelName)
    {
        $this->termIsHotelName = $termIsHotelName;
    }

    /**
     * @return bool
     */
    public function isTermExistInHotelName()
    {
        return $this->termExistInHotelName;
    }

    /**
     * @param bool $termExistInHotelName
     */
    public function setTermExistInHotelName($termExistInHotelName)
    {
        $this->termExistInHotelName = $termExistInHotelName;
    }

    /**
     * @return Hotel[]
     */
    public function getHotelsResult()
    {
        return $this->hotelsResult;
    }

    /**
     * @param Hotel[] $hotelsResult
     */
    public function setHotelsResult($hotelsResult)
    {
        $this->hotelsResult = $hotelsResult;
    }

    /**
     * @return Pagination
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * @param Pagination $pagination
     */
    public function setPagination($pagination)
    {
        $this->pagination = $pagination;
    }

}