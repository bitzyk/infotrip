<?php

namespace Infotrip\HotelParser\AvailabilityChecker;

use Infotrip\Domain\Entity\Hotel;

class BookingComAvailabilityChecker implements AvailabilityChecker
{

    /**
     * @param array $params
     * @param Hotel $hotel
     * @return string - availability url
     */
    public function getAvailabilityUrl(
        array $params,
        Hotel $hotel
    )
    {
        $bookingQueryParams = [];

        if (
            isset($params['start']) &&
            ($startTime = $params['start']) &&
            (boolean) ($startTime = strtotime($startTime))
        ) {
            $startDay = date('d', $startTime);
            $startMonth = date('m', $startTime);
            $startYear = date('Y', $startTime);

            $bookingQueryParams['checkin_monthday'] = $startDay;
            $bookingQueryParams['checkin_month'] = $startMonth;
            $bookingQueryParams['checkin_year'] = $startYear;
        }

        if (
            isset($params['end']) &&
            ($endTime = $params['end']) &&
            (boolean) ($endTime = strtotime($endTime))
        ) {
            $endDay = date('d', $endTime);
            $endMonth = date('m', $endTime);
            $endYear = date('Y', $endTime);

            $bookingQueryParams['checkout_monthday'] = $endDay;
            $bookingQueryParams['checkout_month'] = $endMonth;
            $bookingQueryParams['checkout_year'] = $endYear;
        }

        if(
            isset($params['adults']) &&
            ($adults = $params['adults']) &&
            is_numeric($adults)
        ) {
            $bookingQueryParams['group_adults'] = (int) $adults;
        } elseif (
            isset($params['adults']) &&
            $params['adults'] == 'noRadio' &&
            ($adults = $params['adultsSelect']) &&
            is_numeric($adults)
        ) {
            $bookingQueryParams['group_adults'] = (int) $adults;
        }

        if(
            isset($params['childrens']) &&
            ($childrens = $params['childrens']) &&
            is_numeric($childrens)
        ) {
            $bookingQueryParams['group_children'] = (int) $childrens;
        } elseif (
            isset($params['childrens']) &&
            $params['childrens'] == 'noRadio' &&
            ($childrens = $params['childrensSelect']) &&
            is_numeric($childrens)
        ) {
            $bookingQueryParams['group_children'] = (int) $childrens;
        }

        if(
            isset($params['rooms']) &&
            ($rooms = $params['rooms']) &&
            is_numeric($rooms)
        ) {
            $bookingQueryParams['no_rooms'] = (int) $rooms;
        } elseif (
            isset($params['rooms']) &&
            $params['rooms'] == 'noRadio' &&
            ($rooms = $params['roomsSelect']) &&
            is_numeric($rooms)
        ) {
            $bookingQueryParams['no_rooms'] = (int) $rooms;
        }

        $queryString = http_build_query($bookingQueryParams);

        if (isset($bookingQueryParams['group_children'])) {
            for($i=1; $i<=$bookingQueryParams['group_children']; $i++) {
                $queryString .= '&age=12';
            }
        }

        $url = $hotel->getBookingHotelUrl();

        if ($queryString) {
            $url .= '&' . $queryString;
        }

        return $url;
    }


}