<?php

namespace Infotrip\Domain\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Events;
use Infotrip\Domain\Repository\HotelRepository;
use Infotrip\HotelParser\Entity\HotelInfo;
use Infotrip\HotelParser\Entity\NullHotelInfo;
use Infotrip\HotelParser\HotelParser;
use Infotrip\HotelParser\ImageCatcher\ImageCatcher;

/**
 * Class Hotel
 *
 * @ORM\Entity(repositoryClass="Infotrip\Domain\Repository\HotelRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="hotels")
 *
 * @package Infotrip\Domain\Entity
 */
class Hotel extends Base implements \JsonSerializable
{

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @Column(type="string") */
    private $name;

    /** @Column(type="string") */
    private $address;

    /** @Column(type="string") */
    private $zip;

    /** @Column(type="string", name="`city_hotel`") */
    private $cityHotel;

    /** @Column(type="string") */
    private $latitude;

    /** @Column(type="string") */
    private $longitude;

    /** @Column(type="string", name="`photo_url`") */
    private $photoUrl;

    /** @Column(type="string", name="`desc_en`") */
    private $descEn;

    /** @Column(type="string", name="`city_unique`") */
    private $cityUnique;

    /** @Column(type="string", name="`city_unique_old`") */
    private $cityUniqueOld;

    /** @Column(type="string", name="`continent_id`") */
    private $continentId;

    /** @Column(type="string") */
    private $videoId;

    /** @Column(type="string", name="`currencycode`") */
    private $currencycode;

    /** @Column(type="string") */
    private $minrate;

    /** @Column(type="string") */
    private $maxrate;

    /** @Column(type="string", name="`cc1`") */
    private $countryCode;

    /** @Column(type="string", name="`hotel_url`") */
    private $bookingHotelUrl;

    /**
     * @var Image[]
     */
    private $images = array();

    /**
     * @var HotelRepository
     */
    private $repository;

    /**
     * @var Hotel[]|null
     */
    private $relatedHotels = null;

    /**
     * @var HotelInfo
     */
    private $externalHotelInfo;

    /** @Column(type="integer", name="`visible`") */
    private $visible;

    /**
     * @var string
     */
    const CACHE_DIR = '/var/cache/bookingImages';

    const BOOKING_ID_PARAM = 'aid=1518628';
    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescEn()
    {
        return $this->descEn;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return mixed
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @return mixed
     */
    public function getCityHotel()
    {
        return $this->cityHotel;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }


    /**
     * @return mixed
     */
    public function getCityUnique()
    {
        return $this->cityUnique;
    }

    /**
     * @param mixed $cityUnique
     * @return Hotel
     */
    public function setCityUnique($cityUnique)
    {
        $this->cityUnique = $cityUnique;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCityUniqueOld()
    {
        return $this->cityUniqueOld;
    }

    /**
     * @param mixed $cityUniqueOld
     */
    public function setCityUniqueOld($cityUniqueOld)
    {
        $this->cityUniqueOld = $cityUniqueOld;
    }


    /**
     * @return mixed
     */
    public function getContinentId()
    {
        return $this->continentId;
    }

    /**
     * @return mixed
     */
    public function getVideoId()
    {
        return $this->videoId;
    }

    /** @ORM\postLoad */
    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        if (! $this->repository instanceof HotelRepository) {
            /** @var HotelRepository $repository */
            $this->repository = $eventArgs->getEntityManager()->getRepository(get_class($this));
        }

        $directory = ImageCatcher::getImageDirectory(
            RESOURCES_ROOT . Hotel::CACHE_DIR,
            $this->cityUnique,
            $this->id
        );

        if (file_exists($directory)) {
            if ($handle = opendir($directory)) {
                while (false !== ($entry = readdir($handle))) {
                    $pathInfo = pathinfo($entry);
                    if (isset($pathInfo['extension']) && $pathInfo['extension'] == 'jpg') {
                        $image = (new Image($this->id, $this->cityUnique))
                            ->setSrc(
                                ImageCatcher::getImageDirectory(
                                    self::CACHE_DIR,
                                    $this->cityUnique,
                                    $this->id
                                ) . '/' . $pathInfo['basename'])
                            ->setIsCached(true);

                        $this->addImage($image);
                    }
                }
                closedir($handle);
            }
        }
    }

    /**
     * Get related hotels for the given found hotel
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return Hotel[]
     */
    public function getRelatedHotels()
    {
        if (is_null($this->relatedHotels)) {
            $this->relatedHotels = (array) $this->repository->getRelatedHotels($this);
        }

        return (array) $this->relatedHotels;
    }

    /**
     * @return Hotel[]
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function refreshRelatedHotels()
    {
        $this->relatedHotels = (array) $this->repository->getRelatedHotels($this);

        return $this->getRelatedHotels();
    }

    /**
     * @return string
     */
    public function getCurrencycode()
    {
        return $this->currencycode;
    }

    /**
     * @return string
     */
    public function getCurrencucodeShort()
    {
        if ($this->currencycode === 'EUR') {
            return '&#8364;';
        }
    }

    /**
     * @return string
     */
    public function getMinrate()
    {
        return floor($this->minrate);
    }

    /**
     * @return string
     */
    public function getMaxrate()
    {
        return floor($this->maxrate);
    }

    /**
     * @return mixed
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param mixed $countryCode
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return mixed
     */
    public function getBookingHotelUrl()
    {
        return $this->bookingHotelUrl . '?' . self::BOOKING_ID_PARAM;
    }

    /**
     * @return Image[]
     */
    public function getImages()
    {
        if (
            count($this->images) == 0
        ) {
            if (
                $this->externalHotelInfo instanceof HotelInfo &&
                (count($this->externalHotelInfo->getImages()) > 0)

            ) {
                return $this->externalHotelInfo->getImages();
            } else {
                // if no photo were found in hotelInfo -> return the photo_url from db
                return [
                    (new Image($this->getId(), $this->getCityUnique()))
                        ->setSrc($this->photoUrl)
                ];
            }
        } else if (
            count($this->images) == 1
        ) {
            if(
                $this->externalHotelInfo instanceof HotelInfo &&
                (count($this->externalHotelInfo->getImages()) > 0)
            ) {
                return $this->externalHotelInfo->getImages();
            } else {
                return $this->images;
            }
        } else {
            return $this->images;
        }


    }

    /**
     * @param Image[] $images
     */
    public function setImages(array $images)
    {
        $this->images = $images;
    }

    /**
     * @param string $image
     */
    public function addImage($image)
    {
        $this->images[] = $image;
    }

    /**
     * @return HotelInfo
     */
    public function getExternalHotelInfo()
    {
        return $this->externalHotelInfo;
    }

    /**
     * @param HotelParser $hotelParser
     * @param bool $cached
     */
    public function requestExternalHotelInfo(
        HotelParser $hotelParser, $cached = true
    )
    {
        if ($cached) {
            $hotelInfo = $hotelParser->getCachedEntity($this);
        } else {
            $hotelInfo = $hotelParser
                ->parse($this, $cached);
        }

        if ($hotelInfo instanceof HotelInfo) {
            $this->setExternalHotelInfo($hotelInfo);
        } else {
            $this->setExternalHotelInfo(
                (new NullHotelInfo())
            );
        }
    }

    /**
     * @param HotelInfo $externalHotelInfo
     */
    public function setExternalHotelInfo(HotelInfo $externalHotelInfo)
    {
        $this->externalHotelInfo = $externalHotelInfo;
    }


    /**
     * @return string
     */
    public function getCountryName()
    {

        if (isset(\Infotrip\Domain\Entity\Country::$COUNTRY_CODE_LIST[strtoupper($this->countryCode)])) {
            return \Infotrip\Domain\Entity\Country::$COUNTRY_CODE_LIST[strtoupper($this->countryCode)];
        }

        return '';
    }

    /**
     * @return string
     */
    public function getContinentName()
    {
        if (isset(\Infotrip\Domain\Entity\Continent::$CONTINENT_ID[$this->continentId])) {
            return \Infotrip\Domain\Entity\Continent::$CONTINENT_ID[$this->continentId];
        }

        return '';
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * @return mixed
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * @param mixed $visible
     * @return Hotel
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        return $this;
    }


}