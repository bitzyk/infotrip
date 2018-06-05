<?php
/**
 * Created by PhpStorm.
 * User: cbitoi
 * Date: 11/05/2018
 * Time: 15:39
 */

namespace Infotrip\HotelParser\ImageCatcher;

class ImageCatcher
{

    /** @var string */
    private $cacheDirectory;

    public function __construct($cacheDirectory)
    {
        $this->cacheDirectory = $cacheDirectory;
    }


    /**
     * @param array $images
     */
    public function catchImages(
        array $images
    )
    {
        foreach ($images as $nonCatchedImage) {

            if (
                ! isset($nonCatchedImage['src']) ||
                ! isset($nonCatchedImage['hotelId']) ||
                ! isset($nonCatchedImage['cityUnique']) ||
                file_exists($this->getImagePath(
                    $nonCatchedImage['hotelId'],
                    $nonCatchedImage['cityUnique'],
                    $nonCatchedImage['src'])
                )
            ) {
                continue;
            }

            // get image from booking
            $imageContent = file_get_contents($nonCatchedImage['src']);

            // cache image
            $this->cache(
                $imageContent,
                $nonCatchedImage['hotelId'],
                $nonCatchedImage['cityUnique'],
                $nonCatchedImage['src']
            );
        }
    }

    /**
     * @param string $imageContent
     * @param string $hotelId
     * @param string $cityUnique
     * @param string $initialSrc
     */
    private function cache(
        $imageContent,
        $hotelId,
        $cityUnique,
        $initialSrc
    )
    {
        $path = $this->getImagePath($hotelId, $cityUnique, $initialSrc);

        if (file_exists($path)) {
            return;
        }

        file_put_contents($path, $imageContent);

    }

    /**
     * @param string $hotelId
     * @param string $cityUnique
     * @param string $initialSrc
     * @return string
     */
    private function getImagePath(
        $hotelId,
        $cityUnique,
        $initialSrc
    )
    {
        $filename = self::getFilenamePath($hotelId, $initialSrc);

        $path = $this->getDirectoryPath($hotelId, $cityUnique) . '/' . $filename;

        return $path;
    }

    /**
     * @param string $hotelId
     * @param string $cityUnique
     * @return string
     */
    private function getDirectoryPath(
        $hotelId,
        $cityUnique
    )
    {
        $directory = self::getImageDirectory($this->cacheDirectory, $cityUnique, $hotelId);

        if (! file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        return $directory;
    }

    public static function getImageDirectory(
        $cacheDirectory, $cityUnique, $hotelId
    )
    {
        return sprintf(
            '%s/%s/%s',
            $cacheDirectory, $cityUnique, $hotelId
        );
    }

    /**
     * @param string $hotelId
     * @param string $initialSrc
     * @return string
     */
    public static function getFilenamePath($hotelId, $initialSrc)
    {
        $filename = sprintf(
            'image-hotel-%s-%s.jpg',
            $hotelId,
            md5($initialSrc)
        );

        return $filename;
    }
}