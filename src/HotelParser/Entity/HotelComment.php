<?php
/**
 * Created by PhpStorm.
 * User: cbitoi
 * Date: 09/05/2018
 * Time: 00:33
 */

namespace Infotrip\HotelParser\Entity;


class HotelComment implements \JsonSerializable
{

    /**
     * @var string
     */
    private $reviewComment;

    /**
     * @var string
     */
    private $reviewUser;

    /**
     * @var string
     */
    private $reviewUserLocation;

    /**
     * @return string
     */
    public function getReviewComment()
    {
        return $this->reviewComment;
    }

    /**
     * @param string $reviewComment
     * @return HotelComment
     */
    public function setReviewComment($reviewComment)
    {
        $this->reviewComment = $reviewComment;
        return $this;
    }

    /**
     * @return string
     */
    public function getReviewUser()
    {
        return $this->reviewUser;
    }

    /**
     * @param string $reviewUser
     * @return HotelComment
     */
    public function setReviewUser($reviewUser)
    {
        $this->reviewUser = $reviewUser;
        return $this;
    }

    /**
     * @return string
     */
    public function getReviewUserLocation()
    {
        return $this->reviewUserLocation;
    }

    /**
     * @param string $reviewUserLocation
     */
    public function setReviewUserLocation($reviewUserLocation)
    {
        $this->reviewUserLocation = $reviewUserLocation;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

}