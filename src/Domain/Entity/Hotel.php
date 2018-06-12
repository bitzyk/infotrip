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

    /**
     * @var string
     */
    const CACHE_DIR = '/var/cache/bookingImages';

    public static $COUNTRY_CODE_LIST = array(
        'AF' => 'Afghanistan',
        'AX' => 'Aland Islands',
        'AL' => 'Albania',
        'DZ' => 'Algeria',
        'AS' => 'American Samoa',
        'AD' => 'Andorra',
        'AO' => 'Angola',
        'AI' => 'Anguilla',
        'AQ' => 'Antarctica',
        'AG' => 'Antigua and Barbuda',
        'AR' => 'Argentina',
        'AM' => 'Armenia',
        'AW' => 'Aruba',
        'AU' => 'Australia',
        'AT' => 'Austria',
        'AZ' => 'Azerbaijan',
        'BS' => 'Bahamas the',
        'BH' => 'Bahrain',
        'BD' => 'Bangladesh',
        'BB' => 'Barbados',
        'BY' => 'Belarus',
        'BE' => 'Belgium',
        'BZ' => 'Belize',
        'BJ' => 'Benin',
        'BM' => 'Bermuda',
        'BT' => 'Bhutan',
        'BO' => 'Bolivia',
        'BA' => 'Bosnia and Herzegovina',
        'BW' => 'Botswana',
        'BV' => 'Bouvet Island (Bouvetoya)',
        'BR' => 'Brazil',
        'IO' => 'British Indian Ocean Territory (Chagos Archipelago)',
        'VG' => 'British Virgin Islands',
        'BN' => 'Brunei Darussalam',
        'BG' => 'Bulgaria',
        'BF' => 'Burkina Faso',
        'BI' => 'Burundi',
        'KH' => 'Cambodia',
        'CM' => 'Cameroon',
        'CA' => 'Canada',
        'CV' => 'Cape Verde',
        'KY' => 'Cayman Islands',
        'CF' => 'Central African Republic',
        'TD' => 'Chad',
        'CL' => 'Chile',
        'CN' => 'China',
        'CX' => 'Christmas Island',
        'CC' => 'Cocos (Keeling) Islands',
        'CO' => 'Colombia',
        'KM' => 'Comoros the',
        'CD' => 'Congo',
        'CG' => 'Congo the',
        'CK' => 'Cook Islands',
        'CR' => 'Costa Rica',
        'CI' => 'Cote d\'Ivoire',
        'HR' => 'Croatia',
        'CU' => 'Cuba',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'DJ' => 'Djibouti',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'EC' => 'Ecuador',
        'EG' => 'Egypt',
        'SV' => 'El Salvador',
        'GQ' => 'Equatorial Guinea',
        'ER' => 'Eritrea',
        'EE' => 'Estonia',
        'ET' => 'Ethiopia',
        'FO' => 'Faroe Islands',
        'FK' => 'Falkland Islands (Malvinas)',
        'FJ' => 'Fiji the Fiji Islands',
        'FI' => 'Finland',
        'FR' => 'France, French Republic',
        'GF' => 'French Guiana',
        'PF' => 'French Polynesia',
        'TF' => 'French Southern Territories',
        'GA' => 'Gabon',
        'GM' => 'Gambia the',
        'GE' => 'Georgia',
        'DE' => 'Germany',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GR' => 'Greece',
        'GL' => 'Greenland',
        'GD' => 'Grenada',
        'GP' => 'Guadeloupe',
        'GU' => 'Guam',
        'GT' => 'Guatemala',
        'GG' => 'Guernsey',
        'GN' => 'Guinea',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HT' => 'Haiti',
        'HM' => 'Heard Island and McDonald Islands',
        'VA' => 'Holy See (Vatican City State)',
        'HN' => 'Honduras',
        'HK' => 'Hong Kong',
        'HU' => 'Hungary',
        'IS' => 'Iceland',
        'IN' => 'India',
        'ID' => 'Indonesia',
        'IR' => 'Iran',
        'IQ' => 'Iraq',
        'IE' => 'Ireland',
        'IM' => 'Isle of Man',
        'IL' => 'Israel',
        'IT' => 'Italy',
        'JM' => 'Jamaica',
        'JP' => 'Japan',
        'JE' => 'Jersey',
        'JO' => 'Jordan',
        'KZ' => 'Kazakhstan',
        'KE' => 'Kenya',
        'KI' => 'Kiribati',
        'KP' => 'Korea',
        'KR' => 'Korea',
        'KW' => 'Kuwait',
        'KG' => 'Kyrgyz Republic',
        'LA' => 'Lao',
        'LV' => 'Latvia',
        'LB' => 'Lebanon',
        'LS' => 'Lesotho',
        'LR' => 'Liberia',
        'LY' => 'Libyan Arab Jamahiriya',
        'LI' => 'Liechtenstein',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'MO' => 'Macao',
        'MK' => 'Macedonia',
        'MG' => 'Madagascar',
        'MW' => 'Malawi',
        'MY' => 'Malaysia',
        'MV' => 'Maldives',
        'ML' => 'Mali',
        'MT' => 'Malta',
        'MH' => 'Marshall Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MU' => 'Mauritius',
        'YT' => 'Mayotte',
        'MX' => 'Mexico',
        'FM' => 'Micronesia',
        'MD' => 'Moldova',
        'MC' => 'Monaco',
        'MN' => 'Mongolia',
        'ME' => 'Montenegro',
        'MS' => 'Montserrat',
        'MA' => 'Morocco',
        'MZ' => 'Mozambique',
        'MM' => 'Myanmar',
        'NA' => 'Namibia',
        'NR' => 'Nauru',
        'NP' => 'Nepal',
        'AN' => 'Netherlands Antilles',
        'NL' => 'Netherlands',
        'NC' => 'New Caledonia',
        'NZ' => 'New Zealand',
        'NI' => 'Nicaragua',
        'NE' => 'Niger',
        'NG' => 'Nigeria',
        'NU' => 'Niue',
        'NF' => 'Norfolk Island',
        'MP' => 'Northern Mariana Islands',
        'NO' => 'Norway',
        'OM' => 'Oman',
        'PK' => 'Pakistan',
        'PW' => 'Palau',
        'PS' => 'Palestinian Territory',
        'PA' => 'Panama',
        'PG' => 'Papua New Guinea',
        'PY' => 'Paraguay',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'PN' => 'Pitcairn Islands',
        'PL' => 'Poland',
        'PT' => 'Portugal, Portuguese Republic',
        'PR' => 'Puerto Rico',
        'QA' => 'Qatar',
        'RE' => 'Reunion',
        'RO' => 'Romania',
        'RU' => 'Russian Federation',
        'RW' => 'Rwanda',
        'BL' => 'Saint Barthelemy',
        'SH' => 'Saint Helena',
        'KN' => 'Saint Kitts and Nevis',
        'LC' => 'Saint Lucia',
        'MF' => 'Saint Martin',
        'PM' => 'Saint Pierre and Miquelon',
        'VC' => 'Saint Vincent and the Grenadines',
        'WS' => 'Samoa',
        'SM' => 'San Marino',
        'ST' => 'Sao Tome and Principe',
        'SA' => 'Saudi Arabia',
        'SN' => 'Senegal',
        'RS' => 'Serbia',
        'SC' => 'Seychelles',
        'SL' => 'Sierra Leone',
        'SG' => 'Singapore',
        'SK' => 'Slovakia (Slovak Republic)',
        'SI' => 'Slovenia',
        'SB' => 'Solomon Islands',
        'SO' => 'Somalia, Somali Republic',
        'ZA' => 'South Africa',
        'GS' => 'South Georgia and the South Sandwich Islands',
        'ES' => 'Spain',
        'LK' => 'Sri Lanka',
        'SD' => 'Sudan',
        'SR' => 'Suriname',
        'SJ' => 'Svalbard & Jan Mayen Islands',
        'SZ' => 'Swaziland',
        'SE' => 'Sweden',
        'CH' => 'Switzerland, Swiss Confederation',
        'SY' => 'Syrian Arab Republic',
        'TW' => 'Taiwan',
        'TJ' => 'Tajikistan',
        'TZ' => 'Tanzania',
        'TH' => 'Thailand',
        'TL' => 'Timor-Leste',
        'TG' => 'Togo',
        'TK' => 'Tokelau',
        'TO' => 'Tonga',
        'TT' => 'Trinidad and Tobago',
        'TN' => 'Tunisia',
        'TR' => 'Turkey',
        'TM' => 'Turkmenistan',
        'TC' => 'Turks and Caicos Islands',
        'TV' => 'Tuvalu',
        'UG' => 'Uganda',
        'UA' => 'Ukraine',
        'AE' => 'United Arab Emirates',
        'GB' => 'United Kingdom',
        'US' => 'United States of America',
        'UM' => 'United States Minor Outlying Islands',
        'VI' => 'United States Virgin Islands',
        'UY' => 'Uruguay, Eastern Republic of',
        'UZ' => 'Uzbekistan',
        'VU' => 'Vanuatu',
        'VE' => 'Venezuela',
        'VN' => 'Vietnam',
        'WF' => 'Wallis and Futuna',
        'EH' => 'Western Sahara',
        'YE' => 'Yemen',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe'
    );

    public static $CONTINENT_ID = [
        1 => 'North America',
        2 => 'South America',
        3 => 'South America',
        5 => 'Africa',
        6 => 'Europe',
        7 => 'Asia',
        8 => 'Asia',
        9 => 'Australia',
        10 => 'North America',
    ];

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
        return $this->bookingHotelUrl;
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
                ->parse($this);
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

        if (isset(self::$COUNTRY_CODE_LIST[strtoupper($this->countryCode)])) {
            return self::$COUNTRY_CODE_LIST[strtoupper($this->countryCode)];
        }

        return '';
    }

    /**
     * @return string
     */
    public function getContinentName()
    {
        if (isset(self::$CONTINENT_ID[$this->continentId])) {
            return self::$CONTINENT_ID[$this->continentId];
        }

        return '';
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }


}