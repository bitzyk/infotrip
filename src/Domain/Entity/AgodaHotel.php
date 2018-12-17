<?php

namespace Infotrip\Domain\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Events;

/**
 * Class AgodaHotel
 *
 * @ORM\Entity(repositoryClass="Infotrip\Domain\Repository\AgodaHotelRepository")
 * @ORM\Table(name="agoda_hotel")
 *
 * @package Infotrip\Domain\Entity
 */
class AgodaHotel
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="integer")
     */
    private $hotel_id;

    /**
     * @Column(type="integer")
     */
    private $chain_id;

    /**
     * @Column(type="string")
     */
    private $chain_name;

    /**
     * @Column(type="integer")
     */
    private $brand_id;

    /**
     * @Column(type="string")
     */
    private $brand_name;

    /**
     * @Column(type="string")
     */
    private $hotel_name;

    /**
     * @Column(type="string")
     */
    private $hotel_formerly_name;

    /**
     * @Column(type="string")
     */
    private $hotel_translated_name;

    /**
     * @Column(type="string")
     */
    private $addressline1;

    /**
     * @Column(type="string")
     */
    private $addressline2;

    /**
     * @Column(type="string")
     */
    private $zipcode;

    /**
     * @Column(type="string")
     */
    private $city;

    /**
     * @Column(type="string")
     */
    private $state;

    /**
     * @Column(type="string")
     */
    private $country;

    /**
     * @Column(type="string")
     */
    private $countryisocode;

    /**
     * @Column(type="string")
     */
    private $star_rating;

    /**
     * @Column(type="string")
     */
    private $longitude;

    /**
     * @Column(type="string")
     */
    private $latitude;

    /**
     * @Column(type="string")
     */
    private $url;

    /**
     * @Column(type="string")
     */
    private $checkin;

    /**
     * @Column(type="string")
     */
    private $checkout;

    /**
     * @Column(type="string")
     */
    private $numberrooms;

    /**
     * @Column(type="string")
     */
    private $numberfloors;

    /**
     * @Column(type="string")
     */
    private $photo1;

    /**
     * @Column(type="string")
     */
    private $photo2;

    /**
     * @Column(type="string")
     */
    private $photo3;

    /**
     * @Column(type="string")
     */
    private $photo4;

    /**
     * @Column(type="string")
     */
    private $photo5;

    /**
     * @Column(type="string")
     */
    private $overview;

    /**
     * @Column(type="string")
     */
    private $rates_from;

    /**
     * @Column(type="string")
     */
    private $continent_id;

    /**
     * @Column(type="string")
     */
    private $continent_name;

    /**
     * @Column(type="string")
     */
    private $city_id;

    /**
     * @Column(type="string")
     */
    private $country_id;

    /**
     * @Column(type="string")
     */
    private $number_of_reviews;

    /**
     * @Column(type="string")
     */
    private $rating_average;

    /**
     * @Column(type="string")
     */
    private $rates_currency;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return AgodaHotel
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHotelId()
    {
        return $this->hotel_id;
    }

    /**
     * @param mixed $hotel_id
     * @return AgodaHotel
     */
    public function setHotelId($hotel_id)
    {
        $this->hotel_id = $hotel_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getChainId()
    {
        return $this->chain_id;
    }

    /**
     * @param mixed $chain_id
     * @return AgodaHotel
     */
    public function setChainId($chain_id)
    {
        $this->chain_id = $chain_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getChainName()
    {
        return $this->chain_name;
    }

    /**
     * @param mixed $chain_name
     * @return AgodaHotel
     */
    public function setChainName($chain_name)
    {
        $this->chain_name = $chain_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBrandId()
    {
        return $this->brand_id;
    }

    /**
     * @param mixed $brand_id
     * @return AgodaHotel
     */
    public function setBrandId($brand_id)
    {
        $this->brand_id = $brand_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBrandName()
    {
        return $this->brand_name;
    }

    /**
     * @param mixed $brand_name
     * @return AgodaHotel
     */
    public function setBrandName($brand_name)
    {
        $this->brand_name = $brand_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHotelName()
    {
        return $this->hotel_name;
    }

    /**
     * @param mixed $hotel_name
     * @return AgodaHotel
     */
    public function setHotelName($hotel_name)
    {
        $this->hotel_name = $hotel_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHotelFormerlyName()
    {
        return $this->hotel_formerly_name;
    }

    /**
     * @param mixed $hotel_formerly_name
     * @return AgodaHotel
     */
    public function setHotelFormerlyName($hotel_formerly_name)
    {
        $this->hotel_formerly_name = $hotel_formerly_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHotelTranslatedName()
    {
        return $this->hotel_translated_name;
    }

    /**
     * @param mixed $hotel_translated_name
     * @return AgodaHotel
     */
    public function setHotelTranslatedName($hotel_translated_name)
    {
        $this->hotel_translated_name = $hotel_translated_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddressline1()
    {
        return $this->addressline1;
    }

    /**
     * @param mixed $addressline1
     * @return AgodaHotel
     */
    public function setAddressline1($addressline1)
    {
        $this->addressline1 = $addressline1;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddressline2()
    {
        return $this->addressline2;
    }

    /**
     * @param mixed $addressline2
     * @return AgodaHotel
     */
    public function setAddressline2($addressline2)
    {
        $this->addressline2 = $addressline2;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * @param mixed $zipcode
     * @return AgodaHotel
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     * @return AgodaHotel
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     * @return AgodaHotel
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     * @return AgodaHotel
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountryisocode()
    {
        return $this->countryisocode;
    }

    /**
     * @param mixed $countryisocode
     * @return AgodaHotel
     */
    public function setCountryisocode($countryisocode)
    {
        $this->countryisocode = $countryisocode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStarRating()
    {
        return $this->star_rating;
    }

    /**
     * @param mixed $star_rating
     * @return AgodaHotel
     */
    public function setStarRating($star_rating)
    {
        $this->star_rating = $star_rating;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     * @return AgodaHotel
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     * @return AgodaHotel
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     * @return AgodaHotel
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCheckin()
    {
        return $this->checkin;
    }

    /**
     * @param mixed $checkin
     * @return AgodaHotel
     */
    public function setCheckin($checkin)
    {
        $this->checkin = $checkin;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCheckout()
    {
        return $this->checkout;
    }

    /**
     * @param mixed $checkout
     * @return AgodaHotel
     */
    public function setCheckout($checkout)
    {
        $this->checkout = $checkout;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumberrooms()
    {
        return $this->numberrooms;
    }

    /**
     * @param mixed $numberrooms
     * @return AgodaHotel
     */
    public function setNumberrooms($numberrooms)
    {
        $this->numberrooms = $numberrooms;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumberfloors()
    {
        return $this->numberfloors;
    }

    /**
     * @param mixed $numberfloors
     * @return AgodaHotel
     */
    public function setNumberfloors($numberfloors)
    {
        $this->numberfloors = $numberfloors;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhoto1()
    {
        return $this->photo1;
    }

    /**
     * @param mixed $photo1
     * @return AgodaHotel
     */
    public function setPhoto1($photo1)
    {
        $this->photo1 = $photo1;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhoto2()
    {
        return $this->photo2;
    }

    /**
     * @param mixed $photo2
     * @return AgodaHotel
     */
    public function setPhoto2($photo2)
    {
        $this->photo2 = $photo2;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhoto3()
    {
        return $this->photo3;
    }

    /**
     * @param mixed $photo3
     * @return AgodaHotel
     */
    public function setPhoto3($photo3)
    {
        $this->photo3 = $photo3;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhoto4()
    {
        return $this->photo4;
    }

    /**
     * @param mixed $photo4
     * @return AgodaHotel
     */
    public function setPhoto4($photo4)
    {
        $this->photo4 = $photo4;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhoto5()
    {
        return $this->photo5;
    }

    /**
     * @param mixed $photo5
     * @return AgodaHotel
     */
    public function setPhoto5($photo5)
    {
        $this->photo5 = $photo5;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOverview()
    {
        return $this->overview;
    }

    /**
     * @param mixed $overview
     * @return AgodaHotel
     */
    public function setOverview($overview)
    {
        $this->overview = $overview;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRatesFrom()
    {
        return $this->rates_from;
    }

    /**
     * @param mixed $rates_from
     * @return AgodaHotel
     */
    public function setRatesFrom($rates_from)
    {
        $this->rates_from = $rates_from;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContinentId()
    {
        return $this->continent_id;
    }

    /**
     * @param mixed $continent_id
     * @return AgodaHotel
     */
    public function setContinentId($continent_id)
    {
        $this->continent_id = $continent_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContinentName()
    {
        return $this->continent_name;
    }

    /**
     * @param mixed $continent_name
     * @return AgodaHotel
     */
    public function setContinentName($continent_name)
    {
        $this->continent_name = $continent_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCityId()
    {
        return $this->city_id;
    }

    /**
     * @param mixed $city_id
     * @return AgodaHotel
     */
    public function setCityId($city_id)
    {
        $this->city_id = $city_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountryId()
    {
        return $this->country_id;
    }

    /**
     * @param mixed $country_id
     * @return AgodaHotel
     */
    public function setCountryId($country_id)
    {
        $this->country_id = $country_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumberOfReviews()
    {
        return $this->number_of_reviews;
    }

    /**
     * @param mixed $number_of_reviews
     * @return AgodaHotel
     */
    public function setNumberOfReviews($number_of_reviews)
    {
        $this->number_of_reviews = $number_of_reviews;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRatingAverage()
    {
        return $this->rating_average;
    }

    /**
     * @param mixed $rating_average
     * @return AgodaHotel
     */
    public function setRatingAverage($rating_average)
    {
        $this->rating_average = $rating_average;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRatesCurrency()
    {
        return $this->rates_currency;
    }

    /**
     * @param mixed $rates_currency
     * @return AgodaHotel
     */
    public function setRatesCurrency($rates_currency)
    {
        $this->rates_currency = $rates_currency;
        return $this;
    }

}