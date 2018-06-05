<?php
/**
 * Created by PhpStorm.
 * User: cbitoi
 * Date: 11/05/2018
 * Time: 19:24
 */

namespace Infotrip\Domain\Entity;


use Infotrip\HotelParser\ImageCatcher\ImageCatcher;

class Image implements \JsonSerializable
{
    /**
     * @var string
     */
    private $src;

    /**
     * @var bool
     */
    private $isCached = false;

    /**
     * @var string
     */
    private $hotelId;

    /**
     * @var string
     */
    private $cityUnique;


    public function __construct($hotelId, $cityUnique)
    {
        $this->hotelId = $hotelId;
        $this->cityUnique = $cityUnique;
    }

    /**
     * @return string
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * @param string $src
     * @return Image
     */
    public function setSrc($src)
    {
        $this->src = $src;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCached()
    {
        if ($this->isCached === true) {
            return $this->isCached;
        }

        $directory = ImageCatcher::getImageDirectory(
            RESOURCES_ROOT . Hotel::CACHE_DIR,
            $this->cityUnique,
            $this->hotelId
        );

        $filename = \Infotrip\HotelParser\ImageCatcher\ImageCatcher::getFilenamePath(
            $this->hotelId,
            $this->src
        );

        $imageCachedLocation = $directory . '/' . $filename;

        return file_exists($imageCachedLocation);
    }

    /**
     * @param bool $isCached
     * @return Image
     */
    public function setIsCached($isCached)
    {
        $this->isCached = $isCached;
        return $this;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }


}