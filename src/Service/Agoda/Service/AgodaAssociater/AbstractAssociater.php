<?php

namespace Infotrip\Service\Agoda\Service\AgodaAssociater;


use Infotrip\Domain\Repository\AgodaHotelRepository;
use Infotrip\Domain\Repository\HotelAssocRepository;
use Infotrip\Domain\Repository\HotelRepository;
use Infotrip\Service\Agoda\Entities\AgodaAssociateResponse;
use Infotrip\Domain\Entity\HotelAssoc;

abstract class AbstractAssociater
{
    /**
     * @var AgodaHotelRepository
     */
    protected $agodaHotelRepository;

    /**
     * @var HotelAssocRepository
     */
    protected $hotelAssocRepository;

    /**
     * @var HotelRepository
     */
    protected $hotelRepository;

    abstract public function associate(
        AgodaAssociateResponse $agodaAssociateResponse
    );

    public function __construct(
        AgodaHotelRepository $agodaHotelRepository,
        HotelAssocRepository $hotelAssocRepository,
        HotelRepository $hotelRepository
    )
    {
        $this->agodaHotelRepository = $agodaHotelRepository;
        $this->hotelAssocRepository = $hotelAssocRepository;
        $this->hotelRepository = $hotelRepository;
    }

    /**
     * @param AgodaAssociateResponse $agodaAssociateResponse
     * @param array $toAssociate
     * @throws \Exception
     */
    protected function insertToAssociate(AgodaAssociateResponse $agodaAssociateResponse, array $toAssociate)
    {
        $grouped = array_chunk($toAssociate, 1000);

        foreach ($grouped as $bulkGroup) {

            $bulkEntities = [];
            foreach ($bulkGroup as $row) {
                $hotelAssoc = new HotelAssoc();
                $hotelAssoc
                    ->setHotelIdAgoda($row['agodaHotelId'])
                    ->setHotelIdBooking($row['bookingHotelId']);

                $bulkEntities[] = $hotelAssoc;
            }

            $this->hotelAssocRepository->insertBulk($bulkEntities);

            $agodaAssociateResponse
                ->incrementNewAssociations(count($bulkEntities));
        }
    }

}