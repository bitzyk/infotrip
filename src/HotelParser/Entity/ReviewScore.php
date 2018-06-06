<?php
/**
 * Created by PhpStorm.
 * User: cbitoi
 * Date: 09/05/2018
 * Time: 00:33
 */

namespace Infotrip\HotelParser\Entity;


class ReviewScore implements \JsonSerializable
{

    /**
     * @var string
     */
    private $label;

    /**
     * @var float
     */
    private $score;

    /**
     * @var float
     */
    private $pointsBase10;

    /**
     * @var float
     */
    private $pointsBase5;

    /**
     * @var int
     */
    private $starBase5;

    /**
     * @var int
     */
    private $percent = 0;

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return ReviewScore
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param int $score
     * @return ReviewScore
     */
    public function setScore($score)
    {
        if ($score) {
            $percentVariable = rand(1, 5);
            $sign = rand(0, 1);

            if ($sign === 0) {
                $this->score = ceil($score - (($percentVariable / 100) * $score));
            } else {
                $this->score = ceil($score + (($percentVariable / 100) * $score));
            }
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * @param int $percent
     * @return ReviewScore
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;
        return $this;
    }

    /**
     * @return float
     */
    public function getPointsBase10()
    {
        return $this->pointsBase10;
    }

    /**
     * @param float $pointsBase10
     * @return ReviewScore
     */
    public function setPointsBase10($pointsBase10)
    {
        $this->pointsBase10 = $pointsBase10;
        return $this;
    }

    /**
     * @return float
     */
    public function getPointsBase5()
    {
        return $this->pointsBase5;
    }

    /**
     * @param float $pointsBase5
     * @return ReviewScore
     */
    public function setPointsBase5($pointsBase5)
    {
        $this->pointsBase5 = $pointsBase5;
        $this->starBase5 = round($pointsBase5);
        return $this;
    }

    /**
     * @return int
     */
    public function getStarBase5()
    {
        return $this->starBase5;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}