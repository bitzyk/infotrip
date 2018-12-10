<?php

namespace Infotrip\Domain\Entity;

class HotelOwnerUser
{

    private $userId;

    private $email;

    /**
     * @var Hotel[]
     */
    private $associatedHotels;

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     * @return HotelOwnerUser
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return HotelOwnerUser
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return Hotel[]
     */
    public function getAssociatedHotels()
    {
        return $this->associatedHotels;
    }

    /**
     * @param Hotel[] $associatedHotels
     * @return HotelOwnerUser
     */
    public function setAssociatedHotels($associatedHotels)
    {
        $this->associatedHotels = $associatedHotels;
        return $this;
    }

    /**
     * @param Hotel $associatedHotel
     * @return HotelOwnerUser
     */
    public function addAssociatedHotels(Hotel $associatedHotel)
    {
        $this->associatedHotels[$associatedHotel->getId()] = $associatedHotel;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasAssociatedHotels()
    {
        return ! empty($this->associatedHotels);
    }

    /**
     * @param $hotelId
     * @return bool
     */
    public function hotelIdIsOneOfAssociatedHotels(
        $hotelId
    )
    {
        foreach ($this->getAssociatedHotels() as $associatedHotel) {
            if ($associatedHotel->getId() == $hotelId) {
                return true;
            }
        }

        return false;
    }


}