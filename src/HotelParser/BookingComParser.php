<?php
/**
 * Created by PhpStorm.
 * User: cbitoi
 * Date: 08/05/2018
 * Time: 19:21
 */

namespace Infotrip\HotelParser;

use Infotrip\Domain\Entity\Hotel;
use Infotrip\Domain\Entity\Image;
use Infotrip\HotelParser\Entity\HotelComment;
use Infotrip\HotelParser\Entity\HotelInfo;
use Infotrip\HotelParser\Entity\ReviewScore;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\UnknownChildTypeException;

class BookingComParser extends AbstractHotelParser
    implements HotelParser
{

    const CURL_TIMEOUT = 20;

    const CURL_MAXREDIRS = 20;

    private static $headers = [
        'Connection: keep-alive',
        'Cache-Control: max-age=0',
        'Upgrade-Insecure-Requests: 1',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.9',
        'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36',
    ];

    /**
     * @var Hotel
     */
    private $hotel;

    /**
     * @var string
     */
    private $html;

    /**
     * @param Hotel $hotel
     * @param bool $cached
     * @return HotelInfo
     */
    public function parse(
        Hotel $hotel,
        $cached = true
    )
    {
        // set hotel
        $this->hotel = $hotel;

        // get hotel html
        $this->html = $this->getHotelHtml($cached);

        // parse html and return booking hotel info
        try {
            $hotelInfo = $this->parseHtml($cached);
        } catch (\Exception $e) {
        }

        return $hotelInfo;
    }

    /**
     * @param bool $cached
     * @return HotelInfo
     * @throws UnknownChildTypeException
     */
    private function parseHtml(
        $cached = true
    )
    {
        // check first if the entity is in cache
        if (
            $cached &&
            ($hotelInfo = $this->getCachedEntity($this->hotel))
        ) {
            return $hotelInfo;
        }

        $hotelInfo = new HotelInfo();

        // hydrate rating text
        $this->hydrateRatingText($hotelInfo);

        // hydrate review numbers
        $this->hydrateReviewsNumber($hotelInfo);

        // hydrate review scores
        $this->hydrateReviewScores($hotelInfo);

        // hydrate review scores
        $this->hydrateReviewFuncionsScores($hotelInfo);

        // hydrate review scores
        $this->hydrateReviewScoreOriginal($hotelInfo);

        // hydrate hotel comments
        $this->hydrateHotelComments($hotelInfo);

        // hydrate image urls
        $this->hydrateImageUrls($hotelInfo);

        // hydrate facilities
        $this->hydrateFacilities($hotelInfo);

        if (
        ! $this->hotel->getAddress()
        ) {
            // hydrate address
            $this->hydrateAddress($hotelInfo);
        }

        if (
        ! $this->hotel->getDescEn()
        ) {
            // hydrate description
            $this->hydrateDescription($hotelInfo);
        }

        if (
            ! $this->hotel->getLatitude() ||
            ! $this->hotel->getLongitude()
        ) {
            // hydrate description
            $this->hydrateGeoLocation($hotelInfo);
        }

        // hydration complete -> compute some maths
        $hotelInfo->compute();

        $keyCache = $this->getKeyCache($this->hotel->getId(), self::KEY_CACHE_TYPE_HOTEL_INFO);

        $this->fileCache
            ->set($keyCache, $hotelInfo);

        return $hotelInfo;
    }


    /**
     * @param Hotel $hotel
     * @return HotelInfo|null
     */
    public function getCachedEntity(
        Hotel $hotel
    )
    {
        // get key cache
        $keyCache = $this->getKeyCache($hotel->getId(), self::KEY_CACHE_TYPE_HOTEL_INFO);

        // check first if the entity is in cache
        if ($this->fileCache->has($keyCache)) {
            $hotelInfo = $this->fileCache->get($keyCache);
            return $hotelInfo;
        }

        return null;
    }

    /**
     * @param bool $cached
     * @return string
     */
    private function getHotelHtml($cached = true)
    {
        $keyCache = $this->getKeyCache($this->hotel->getId());

        $html = '';

        if (
            $cached &&
            $this->fileCache->has($keyCache)
        ) {
            $html = $this->fileCache->get($keyCache);
        } else {
            $html = $this->curlRequestHotelPage();

            $this->fileCache
                ->set($keyCache, $html);
        }

        return $html;
    }

    /**
     * @param HotelInfo $hotelInfo
     */
    private function hydrateRatingText(HotelInfo $hotelInfo)
    {
        $found = preg_match('/class="review-score-widget__text"[^>]*>(((?!<\/span>).)*)<\/span>/sm', $this->html, $matches);

        if (
            $found &&
            isset($matches[1])
        ) {
            $hotelInfo->setRatingText(
                trim(strip_tags($matches[1]))
            );
        }
    }

    /**
     * @param HotelInfo $hotelInfo
     */
    private function hydrateAddress(HotelInfo $hotelInfo)
    {
        $foundAddress = preg_match('/streetAddress" : "(((?!").)*)"/sm', $this->html, $matchesAddress);
        $foundPostalCode = preg_match('/postalCode" : "(((?!").)*)"/sm', $this->html, $matchesPostalCode);


        if (
            $foundAddress &&
            isset($matchesAddress[1])
        ) {
            $hotelInfo->setAddress(trim(strip_tags($matchesAddress[1])));
        }

        if (
            $foundPostalCode &&
            isset($matchesPostalCode[1])
        ) {
            $hotelInfo->setPostalCode(trim(strip_tags($matchesPostalCode[1])));
        }
    }

    /**
     * @param HotelInfo $hotelInfo
     */
    private function hydrateDescription(HotelInfo $hotelInfo)
    {
        $found = preg_match('/id="summary"[^>]*>(.*)hp-desc-review-highlight/sm', $this->html, $matches);

        if (
            $found &&
            isset($matches[1])
        ) {
            $hotelInfo->setDescription(trim(strip_tags($matches[1])));
        }
    }

    /**
     * @param HotelInfo $hotelInfo
     */
    private function hydrateGeoLocation(HotelInfo $hotelInfo)
    {
        $foundLat = preg_match('/b_map_center_latitude = (((?!;).)*);/sm', $this->html, $matchesLat);
        $foundLong = preg_match('/b_map_center_longitude = (((?!;).)*);/sm', $this->html, $matchesLong);


        if (
            $foundLat && $foundLong &&
            isset($matchesLat[1]) && isset($matchesLong[2])
        ) {
            $hotelInfo->setLatitude(trim(strip_tags($matchesLat[1])));
            $hotelInfo->setLongitude(trim(strip_tags($matchesLong[1])));
        }
    }


    /**
     * @param HotelInfo $hotelInfo
     */
    private function hydrateReviewsNumber(HotelInfo $hotelInfo)
    {
        $found = preg_match('/class="review-score-widget__subtext"[^>]*>(((?!<\/span>).)*)<\/span>/sm', $this->html, $matches);

        if (
            $found &&
            isset($matches[1])
        ) {
            $hotelInfo->setTotalReviewsNo(
                filter_var(trim(strip_tags($matches[1])), FILTER_SANITIZE_NUMBER_INT)
            );
        }
    }


    /**
     * @param HotelInfo $hotelInfo
     */
    private function hydrateFacilities(HotelInfo $hotelInfo)
    {
        $found = preg_match('/class="facilitiesChecklist"[^>]*>(.*?)<div class="feedback-loop/sm', $this->html, $matches);

        if (
            isset($matches[1])
        ) {

            $hotelInfo->setFacilities(
                str_replace('facilitiesChecklistSection', 'facilities-checklist', $matches[1])
            );
        }
    }

    /**
     * @param HotelInfo $hotelInfo
     */
    private function hydrateReviewScoreOriginal(HotelInfo $hotelInfo)
    {
        preg_match('/id="reviewFloater".*review-score-badge[^>]*>([^<]*)/sm', $this->html, $matches);

        if (
            isset($matches[1])
        ) {
            $hotelInfo->setReviewScoreOriginal(
                (float) trim(strip_tags($matches[1]))
            );
        }
    }

    /**
     * @param HotelInfo $hotelInfo
     */
    private function hydrateImageUrls(HotelInfo $hotelInfo)
    {
        preg_match('/id="photo_wrapper"(.*)id="reviewFloater/sm', $this->html, $matches);

        if (! isset($matches[1])) {
            return;
        }

        preg_match_all('/data-lazy="([^"]*)/sm', $matches[1], $matches2);

        if (! isset($matches2[1])) {
            return;
        }

        foreach ($matches2[1] as $src) {
            if (
                $src &&
                (
                    strpos($src, 'images/hotel') !== false ||
                    strpos($src, 'landmark') !== false
                )
            ) {
                $image = (new Image($this->hotel->getId(), $this->hotel->getCityUnique()))
                    ->setSrc($src);
                $hotelInfo->addImage($image);
            }
        }
//        elseif (
//            ($items = $this->dom->find('.bh-photo-grid a')) &&
//            count($items) >= 1
//        ) {
//            foreach ($items as $item) {
//                if ($item instanceof Dom\HtmlNode) {
//                    $src = $item->getAttribute('href');
//                    if (
//                        $src &&
//                        strpos($src, 'bstatic') !== false
//                    ) {
//                        $image = (new Image($this->hotel->getId(), $this->hotel->getCityUnique()))
//                            ->setSrc($src);
//                        $hotelInfo->addImage($image);
//                    }
//
//                }
//            }
//        }
    }

    /**
     * @param HotelInfo $hotelInfo
     * @throws UnknownChildTypeException
     */
    private function hydrateReviewScores(HotelInfo $hotelInfo)
    {
        $matches = array();
        $found = preg_match('/<ul id="review_list_score_distribution"[^>]*>((?!<\/ul>).)*<\/ul>/sm', $this->html, $matches);

        if (
            ! $found ||
            ! isset($matches[0])
        ) {
            return;
        }

        $localDom = new Dom();
        $localDom->load($matches[0]);
        $review_list_score_distribution = $localDom->find('#review_list_score_distribution');


        if (
            count($review_list_score_distribution) == 1 &&
            isset($review_list_score_distribution[0]) &&
            $review_list_score_distribution[0] instanceof Dom\HtmlNode
        ) {
            /** @var Dom\HtmlNode $inner */
            $inner = $review_list_score_distribution[0];

            $innerDom = new Dom();
            $innerDom->load($inner->innerHtml());
            $review_score_name = $innerDom->find('.review_score_name');
            $review_score_value = $innerDom->find('.review_score_value');

            if (
                ($review_score_name_count = count($review_score_name)) &&
                ($review_score_value_count = count($review_score_value)) &&
                ($review_score_name_count == $review_score_value_count)
            ) {
                /** @var Dom\HtmlNode $item */
                foreach ($review_score_name as $k => $item) {
                    if (
                        $item instanceof Dom\HtmlNode &&
                        isset($review_score_value[$k]) &&
                        $review_score_value[$k] instanceof Dom\HtmlNode
                    ) {
                        $reviewScore = new ReviewScore();
                        $reviewScore
                            ->setLabel(strip_tags($item->innerHtml()))
                            ->setScore(strip_tags($review_score_value[$k]->innerHtml()));

                        $hotelInfo
                            ->addReviewScores($reviewScore);
                    }
                }
            }

        }
    }

    /**
     * @param HotelInfo $hotelInfo
     * @throws UnknownChildTypeException
     */
    private function hydrateReviewFuncionsScores(HotelInfo $hotelInfo)
    {
        preg_match('/id="reviewFloater"[^>]*>(.*)hp_gallery_badges_container/sm', $this->html, $matches);

        if (! isset($matches[0]))
            return;

        preg_match_all('/"review_score_name">([^<]*)/sm', $matches[0], $review_score_name);
        preg_match_all('/"review_score_value">([^<]*)/sm', $matches[0], $review_score_value);


        if (
            ! isset($review_score_name[1]) ||
            ! isset($review_score_value[1])
        ) {
            return;
        }

        $review_score_name = $review_score_name[1];
        $review_score_value = $review_score_value[1];

        if (
            ($review_score_name_count = count($review_score_name)) &&
            ($review_score_value_count = count($review_score_value)) &&
            ($review_score_name_count == $review_score_value_count)
        ) {
            /** @var Dom\HtmlNode $item */
            foreach ($review_score_name as $k => $reviewName) {
                $reviewScore = new ReviewScore();
                $score = (float) trim(strip_tags($review_score_value[$k]));

                $reviewScore
                    ->setLabel(trim(strip_tags($reviewName)))
                    ->setScore($score)
                    ->setPointsBase10($score);

                $hotelInfo
                    ->addReviewFunctiosScore($reviewScore);
            }
        }
    }

    /**
     * @param HotelInfo $hotelInfo
     * @throws UnknownChildTypeException
     */
    private function hydrateHotelComments(HotelInfo $hotelInfo)
    {
        // @toto -> you have to review this when you want to use booking comments

//        $review_content = $this->dom->find('.featured_reviewer .review_content');
//        $review_user = $this->dom->find('.featured_reviewer .fixed_review_user');
//
//        if (
//            count($review_content) >= 0 &&
//            ($review_content_count = count($review_content)) &&
//            ($review_user_count = count($review_user)) &&
//            ($review_content_count == $review_user_count)
//        ) {
//            /** @var Dom\HtmlNode $item */
//            foreach ($review_content as $k => $item) {
//                if (
//                    $item instanceof Dom\HtmlNode
//                    && isset($review_user[$k]) &&
//                    $review_user[$k] instanceof Dom\HtmlNode
//                ) {
//                    $hotelComment = new HotelComment();
//
//                    $hotelComment
//                        ->setReviewComment(
//                            trim(str_replace(['<span','< />pan>'], '', $item->innerHtml()))
//                        );
//
//
//                    $reviewUser = trim(strip_tags($review_user[$k]->innerHtml()));
//                    $explode = explode(',', $reviewUser);
//
//                    if (isset($explode[0])) {
//                        $hotelComment->setReviewUser(
//                            trim($explode[0])
//                        );
//                    }
//                    if (isset($explode[1])) {
//                        $hotelComment->setReviewUserLocation(
//                            trim($explode[1])
//                        );
//                    }
//
//                    $hotelInfo
//                        ->addHotelComment($hotelComment);
//                }
//            }
//        }

    }

    /**
     * @return string
     */
    private function curlRequestHotelPage()
    {
        // create temporary cookie file
        $cookieFile = tempnam(APP_ROOT . '/../public_html/var/parser/booking/cookie','cookie_');

        $curl = curl_init($this->hotel->getBookingHotelUrl());
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_TIMEOUT,self::CURL_TIMEOUT);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_MAXREDIRS, self::CURL_MAXREDIRS );

        curl_setopt($curl, CURLOPT_AUTOREFERER, true );
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookieFile);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_VERBOSE, true);


        curl_setopt($curl, CURLOPT_HTTPHEADER, self::$headers);

        $content = curl_exec($curl);

        //$info = curl_getinfo($curl);
        //$error = curl_error($curl);

        curl_close($curl);

        unlink($cookieFile);

        return $content;
    }
}