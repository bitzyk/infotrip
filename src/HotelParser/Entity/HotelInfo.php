<?php
/**
 * Created by PhpStorm.
 * User: cbitoi
 * Date: 09/05/2018
 * Time: 00:33
 */

namespace Infotrip\HotelParser\Entity;


use Infotrip\Domain\Entity\Image;

class HotelInfo implements \JsonSerializable
{

    /**
     * @var string
     */
    private $ratingText;

    /**
     * @var int
     */
    private $totalReviewsNo = 0;

    /**
     * @var int
     */
    private $guestRecommendPercent = 0;

    /**
     * @var float
     */
    private $reviewScoreTotal = 0;

    /**
     * @var float
     */
    private $reviewScoreOriginal = 0;

    /**
     * @var int
     */
    private $starsScore = 0;

    /**
     * @var ReviewScore[]
     */
    private $reviewScores = array();

    /**
     * @var ReviewScore[]
     */
    private $reviewFunctionsScores = array();

    /**
     * @var HotelComment[]
     */
    private $hotelComments = array();

    /**
     * @var Image[]
     */
    private $images = array();

    /**
     * @return string
     */
    public function getRatingText()
    {
        return $this->ratingText;
    }

    /**
     * @param string $ratingText
     */
    public function setRatingText($ratingText)
    {
        $this->ratingText = $ratingText;
    }

    /**
     * @return ReviewScore[]
     */
    public function getReviewScores()
    {
        return $this->reviewScores;
    }

    /**
     * @param ReviewScore[] $reviewScores
     * @return HotelInfo
     */
    public function setReviewScores(array $reviewScores)
    {
        $this->reviewScores = $reviewScores;
        return $this;
    }


    /**
     * @param ReviewScore $reviewScore
     * @return HotelInfo
     */
    public function addReviewScores(ReviewScore $reviewScore)
    {
        if ($this->totalReviewsNo > 0) {

            $reviewScore->setPercent(
                ceil(($reviewScore->getScore()/$this->totalReviewsNo) * 100)
            );
        }

        $this->reviewScores[] = $reviewScore;
        return $this;
    }

    /**
     * @return ReviewScore[]
     */
    public function getReviewFunctionsScores()
    {
        return $this->reviewFunctionsScores;
    }

    /**
     * @param ReviewScore[] $reviewFunctionsScores
     * @return HotelInfo
     */
    public function setReviewFunctionsScores($reviewFunctionsScores)
    {
        $this->reviewFunctionsScores = $reviewFunctionsScores;
        return $this;
    }

    /**
     * @param ReviewScore $reviewFunctionScore
     * @return HotelInfo
     */
    public function addReviewFunctiosScore($reviewFunctionScore)
    {
        if ($reviewFunctionScore->getPointsBase10()) {
            $reviewFunctionScore->setPointsBase5(
                number_format(
                    ($reviewFunctionScore->getPointsBase10() / 10) * 5,
                    1,
                    '.',
                    ''
                )
            );

        }

        $this->reviewFunctionsScores[] = $reviewFunctionScore;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalReviewsNo()
    {
        return $this->totalReviewsNo;
    }

    /**
     * @param int $totalReviewsNo
     */
    public function setTotalReviewsNo($totalReviewsNo)
    {
        $this->totalReviewsNo = (int) $totalReviewsNo;
    }

    /**
     * @return int
     */
    public function getGuestRecommendPercent()
    {
        return $this->guestRecommendPercent;
    }

    /**
     * @param int $guestRecommendPercent
     * @return HotelInfo
     */
    public function setGuestRecommendPercent($guestRecommendPercent)
    {
        $this->guestRecommendPercent = $guestRecommendPercent;
        return $this;
    }

    /**
     * @param float $reviewScoreOriginal
     * @return HotelInfo
     */
    public function setReviewScoreOriginal($reviewScoreOriginal)
    {
        $this->reviewScoreOriginal = (float) number_format($reviewScoreOriginal, 1, '.', '');
        return $this;
    }

    /**
     * @return float
     */
    public function getReviewScoreTotal()
    {
        return $this->reviewScoreTotal;
    }

    /**
     * @return int
     */
    public function getStarsScore()
    {
        return $this->starsScore;
    }

    /**
     * @param int $slice
     * @return HotelComment[]
     */
    public function getHotelComments($slice = 12)
    {
        return array_slice($this->hotelComments, 0, $slice);
    }

    /**
     * @param HotelComment[] $hotelComments
     */
    public function setHotelComments(array $hotelComments)
    {
        $this->hotelComments = $hotelComments;
    }

    /**
     * @param HotelComment $hotelComment
     */
    public function addHotelComment(HotelComment $hotelComment)
    {
        $this->hotelComments[] = $hotelComment;
    }

    /**
     * @return Image[]
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param Image[] $images
     */
    public function setImages(array $images)
    {
        $this->images = $images;
    }

    /**
     * @param Image $image
     */
    public function addImage(Image $image)
    {
        $this->images[] = $image;
    }

    /**
     *
     */
    public function compute()
    {
        // compute gues recommend (first two ratings)
        if ( count($this->reviewScores) >= 2) {
            $this->setGuestRecommendPercent(
                ceil((($this->reviewScores[0]->getScore() + $this->reviewScores[1]->getScore()) / $this->totalReviewsNo) * 100)
            );
        }

        if ( $this->reviewScoreOriginal > 0) {
            $computedScore = ($this->reviewScoreOriginal / 10) * 5;

            $this->reviewScoreTotal = (float) number_format($computedScore, 1, '.', '');

            $this->starsScore = round($this->reviewScoreTotal);
        }
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}