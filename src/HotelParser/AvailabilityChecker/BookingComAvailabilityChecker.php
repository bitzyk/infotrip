<?php
/**
 * Created by PhpStorm.
 * User: cbitoi
 * Date: 11/05/2018
 * Time: 00:32
 */

namespace Infotrip\HotelParser\AvailabilityChecker;

use Infotrip\Domain\Entity\Hotel;
use Slim\Http\Request;

class BookingComAvailabilityChecker implements AvailabilityChecker
{

    /**
     * @param Request $request
     * @param Hotel $hotel
     * @return string - availability url
     */
    public function getAvailabilityUrl(
        Request $request,
        Hotel $hotel
    )
    {
        $bookingQueryParams = [];

        if (
            ($startTime = $request->getParam('start')) &&
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
            ($endTime = $request->getParam('end')) &&
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
            ($adults = $request->getParam('adults')) &&
            is_numeric($adults)
        ) {
            $bookingQueryParams['group_adults'] = (int) $adults;
        } elseif (
            $request->getParam('adults') == 'noRadio' &&
            ($adults = $request->getParam('adultsSelect')) &&
            is_numeric($adults)
        ) {
            $bookingQueryParams['group_adults'] = (int) $adults;
        }

        if(
            ($childrens = $request->getParam('childrens')) &&
            is_numeric($childrens)
        ) {
            $bookingQueryParams['group_children'] = (int) $childrens;
        } elseif (
            $request->getParam('childrens') == 'noRadio' &&
            ($childrens = $request->getParam('childrensSelect')) &&
            is_numeric($childrens)
        ) {
            $bookingQueryParams['group_children'] = (int) $childrens;
        }

        if(
            ($rooms = $request->getParam('rooms')) &&
            is_numeric($rooms)
        ) {
            $bookingQueryParams['no_rooms'] = (int) $rooms;
        } elseif (
            $request->getParam('rooms') == 'noRadio' &&
            ($rooms = $request->getParam('roomsSelect')) &&
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

        $url = $hotel->getBookingHotelUrl() . '&' . $queryString;

        return $url;
    }


}