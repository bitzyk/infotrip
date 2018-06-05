<?php
namespace Infotrip\HotelParser;

use \Desarrolla2\Cache\Adapter\File;

abstract class AbstractHotelParser implements HotelParser
{

    /**
     * @var File
     */
    protected $fileCache;

    const KEY_CACHE_TYPE_HTML = 'html-';

    const KEY_CACHE_TYPE_HOTEL_INFO = 'hotel-info-';

    public function __construct(
        File $fileCache
    )
    {
        $this->fileCache = $fileCache;
        ini_set('memory_limit', '1G');
    }

    /**
     * @param string $hotelId
     * @param string $type
     * @return string
     */
    protected function getKeyCache($hotelId, $type = self::KEY_CACHE_TYPE_HTML)
    {
        if ($type == self::KEY_CACHE_TYPE_HTML) {
            return self::KEY_CACHE_TYPE_HTML . $hotelId;
        } elseif ($type == self::KEY_CACHE_TYPE_HOTEL_INFO) {
            return self::KEY_CACHE_TYPE_HOTEL_INFO . $hotelId;
        }
    }
}