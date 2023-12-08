<?php

namespace App\Service\Journey;

use App\Request\JourneyRequest;

class DiscountService
{
    public function getDiscount(
        JourneyRequest $journeyRequest
    ): int
    {
        $baseCase = (float)$journeyRequest->baseCost;
        $ageDiscount = $this->getAgeDiscount(
            $journeyRequest->baseCost,
            $journeyRequest->yearOfBirth
        );
        $discountForBooking = $this->getDiscountForBooking(
            $journeyRequest->baseCost,
            $journeyRequest->travelStartDate,
            $journeyRequest->paymentDate
        );

        $finalDiscount = $ageDiscount + $discountForBooking;
        if ($finalDiscount) {
            $finalDiscount = $baseCase / 100 * $finalDiscount;
            $baseCase = $baseCase - $finalDiscount;
        }

        return $baseCase;
    }

    public function getAgeDiscount(
        float     $baseCost,
        \DateTime $yearOfBirth,

    ): int
    {
        $discount = 0;
        $currentDate = new \DateTime();
        $age = $currentDate->diff($yearOfBirth)->y;

        switch ($age) {
            case $age >= 3 && $age < 6;
                $discount = 80;
                break;
            case $age >= 6 && $age < 12;
                $discount = $baseCost / 100 * 30 >= 4500 ? 0 : 30;
                break;
            case $age >= 12 && $age < 18;
                $discount = 10;
                break;
        }

        return $discount;
    }

    public function getDiscountForBooking(
        float     $baseCost,
        \DateTime $travelStartDate,
        \DateTime $paymentDate
    ): int
    {
        $discount = 0;
        $currentDate = new \DateTime();
        $nextYear = clone $currentDate;
        $nextYear->modify('+1 year');

        if (
            $travelStartDate >= new \DateTime("{$currentDate->format('Y')}-01-15")
        ) {
            if ($paymentDate->format('m') == '08' && $paymentDate->format('y') == $currentDate->format('y')) {
                $discount = 7;
            } elseif ($paymentDate->format('m') == '09' && $paymentDate->format('y') == $currentDate->format('y')) {
                $discount = 5;
            } elseif ($paymentDate->format('m') == '10' && $paymentDate->format('y') == $currentDate->format('y')) {
                $discount = 3;
            }
        }

        if ($travelStartDate >= new \DateTime("{$nextYear->format('Y')}-04-01") &&
            $travelStartDate <= new \DateTime("{$nextYear->format('Y')}-09-30")
        ) {
            if ($paymentDate->format('m') <= '11' && $paymentDate->format('y') == $currentDate->format('y')) {
                $discount = 7;
            } elseif ($paymentDate->format('m') == '12' && $paymentDate->format('y') == $currentDate->format('y')) {
                $discount = 5;
            } elseif ($paymentDate->format('m') == '01' && $paymentDate->format('y') == $nextYear->format('y')) {
                $discount = 3;
            }
        }

        if (
            $travelStartDate >= new \DateTime("{$currentDate->format('Y')}-10-01") &&
            $travelStartDate <= new \DateTime("{$nextYear->format('Y')}-01-14")
        ) {
            if ($paymentDate->format('m') == '03' && $paymentDate->format('y') == $currentDate->format('y')) {
                $discount = 7;
            } elseif ($paymentDate->format('m') == '04' && $paymentDate->format('y') == $currentDate->format('y')) {
                $discount = 5;
            } elseif ($paymentDate->format('m') == '05' && $paymentDate->format('y') == $currentDate->format('y')) {
                $discount = 3;
            }
        }

        return $baseCost / 100 * $discount >= 1500 ? 0 : $discount;
    }
}