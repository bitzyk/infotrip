<?php

namespace Infotrip\Domain\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Events;

/**
 * Class HotelAssoc
 *
 * @ORM\Entity(repositoryClass="Infotrip\Domain\Repository\HotelAssocRepository")
 * @ORM\Table(name="hotels_assoc")
 *
 * @package Infotrip\Domain\Entity
 */
class HotelAssoc
{

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="integer")
     */
    private $hotel_id_booking;

    /**
     * @Column(type="integer")
     */
    private $hotel_id_agoda;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return HotelAssoc
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHotelIdBooking()
    {
        return $this->hotel_id_booking;
    }

    /**
     * @param mixed $hotel_id_booking
     * @return HotelAssoc
     */
    public function setHotelIdBooking($hotel_id_booking)
    {
        $this->hotel_id_booking = $hotel_id_booking;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHotelIdAgoda()
    {
        return $this->hotel_id_agoda;
    }

    /**
     * @param mixed $hotel_id_agoda
     * @return HotelAssoc
     */
    public function setHotelIdAgoda($hotel_id_agoda)
    {
        $this->hotel_id_agoda = $hotel_id_agoda;
        return $this;
    }

}