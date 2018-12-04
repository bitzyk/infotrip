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
 * Class UserHotel
 *
 * @ORM\Entity(repositoryClass="Infotrip\Domain\Repository\UserHotelRepository")
 * @ORM\Table(name="ho_user_hotel")
 *
 * @package Infotrip\Domain\Entity
 */
class UserHotel extends Base implements \JsonSerializable
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @Column(type="integer") */
    private $user_id;

    /** @Column(type="integer") */
    private $hotel_id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return UserHotel
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     * @return UserHotel
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHotelId()
    {
        return $this->hotel_id;
    }

    /**
     * @param mixed $hotel_id
     * @return UserHotel
     */
    public function setHotelId($hotel_id)
    {
        $this->hotel_id = $hotel_id;
        return $this;
    }


}