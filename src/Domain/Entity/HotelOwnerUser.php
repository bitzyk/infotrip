<?php

namespace Infotrip\Domain\Entity;

use Infotrip\Service\HotelOwner\HotelOwnerService;

class HotelOwnerUser
{

    private $userId;

    private $email;

    private static $rootUsers = [
        'bogdan.criveanu@gmail.com',
        'cristi.bitoi@gmail.com',

    ];

    /**
     * @var HotelOwnerService
     */
    private $hotelOwnerService;

    public function __construct(
        HotelOwnerService $hotelOwnerService
    )
    {
        $this->hotelOwnerService = $hotelOwnerService;
    }


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

    /**
     * @return bool
     */
    public function isRootUser()
    {
        return (boolean) in_array($this->email, self::$rootUsers);
    }

    /**
     * @param $hotelId
     * @throws \Exception
     */
    public function deleteHotel(
        $hotelId
    )
    {
        // check if the hotelId to delete is one of the associated hotels for the given user
        if (! $this->hotelIdIsOneOfAssociatedHotels($hotelId)) {
            throw new \Exception('Invalid request');
        }

        // call the service to delete hotel
        $this->hotelOwnerService
            ->deleteHotel($hotelId, $this->userId);
    }

}