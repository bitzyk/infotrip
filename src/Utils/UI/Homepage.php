<?php

namespace Infotrip\Utils\UI;


use Infotrip\ViewHelpers\RouteHelper;

class Homepage
{

    /**
     * @var RouteHelper
     */
    private $routeHelper;

    const TOP_DESTINATION_DIR = '/img/top-destinations';

    public function __construct(
        RouteHelper $routeHelper
    )
    {
        $this->routeHelper = $routeHelper;
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
}