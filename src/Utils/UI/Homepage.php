<?php

namespace Infotrip\Utils\UI;


use Desarrolla2\Cache\Adapter\File;
use Infotrip\Domain\Entity\Hotel;
use Infotrip\Domain\Repository\HotelRepository;
use Infotrip\ViewHelpers\RouteHelper;

class Homepage
{

    /**
     * @var RouteHelper
     */
    private $routeHelper;

    const TOP_DESTINATION_DIR = '/img/top-destinations';

    /**
     * @var HotelRepository
     */
    private $hotelRepository;

    /**
     * @var File
     */
    private $cache;

    /**
     * Homepage constructor.
     * @param RouteHelper $routeHelper
     * @param HotelRepository $hotelRepository
     */
    public function __construct(
        RouteHelper $routeHelper,
        HotelRepository $hotelRepository,
        File $cache
    )
    {
        $this->routeHelper = $routeHelper;
        $this->hotelRepository = $hotelRepository;
        $this->cache = $cache;
    }


    public function getTopDestinations(
        $returnNo = 4
    )
    {
        $topDestinationsDirPath = RESOURCES_ROOT . self::TOP_DESTINATION_DIR;

        if (! file_exists($topDestinationsDirPath))
            return [];

        $images = array_diff(scandir($topDestinationsDirPath), array('..', '.'));

        $topDestinations = [];

        foreach ($images as $image) {
            $explodeImage = explode('_', str_replace('.jpg', '', $image));

            if (count($explodeImage) !== 2)
                continue;

            $topDestinations[] = array(
                'imagePathUI' => self::TOP_DESTINATION_DIR . '/' . $image,
                'cityUrl' => $this->routeHelper->getListCitiesUrl($explodeImage[0], $explodeImage[1]),
                'countryName' => $explodeImage[1],
            );
        }

        if (empty($topDestinations))
            return [];

        shuffle($topDestinations);

        $returnTopDestinations = array_slice($topDestinations, 0, $returnNo);

        return $returnTopDestinations;
    }

    /**
     * @return Hotel|null
     */
    public function getTodayDeal()
    {
        $hotelEnity = null;

        if (
            $this->cache &&
            $this->cache->has(HotelRepository::KEY_CACHE_RANDOM_HOTEL)
        ) {
            $hotelEnity = (new Hotel())->toObject(
                (array) json_decode($this->cache->get(HotelRepository::KEY_CACHE_RANDOM_HOTEL))
            );
        }

        return $hotelEnity;

    }
}