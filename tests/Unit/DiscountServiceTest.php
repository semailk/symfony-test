<?php

namespace App\Tests\Unit;

use App\Request\JourneyRequest;
use App\Service\Journey\DiscountService;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use Symfony\Component\Validator\Constraints\DateTime;

class DiscountServiceTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testGetDiscountForBooking(): void
    {
        $journeyRequest = new JourneyRequest();

        $discountService = new DiscountService();
        self::assertEquals(0, $discountService->getDiscountForBooking(10000, new \DateTime('01.01.2024'), $journeyRequest->paymentDate));
        self::assertEquals(7, $discountService->getDiscountForBooking(1000, new \DateTime('01.04.2024'), new \DateTime('05.10.2023')));
        self::assertEquals(5, $discountService->getDiscountForBooking(1000, new \DateTime('01.04.2024'), new \DateTime('05.12.2023')));
        self::assertEquals(3, $discountService->getDiscountForBooking(1000, new \DateTime('01.04.2024'), new \DateTime('05.01.2024')));
        self::assertEquals(7, $discountService->getDiscountForBooking(1000, new \DateTime('01.10.2023'), new \DateTime('05.03.2023')));
        self::assertEquals(5, $discountService->getDiscountForBooking(1000, new \DateTime('01.10.2023'), new \DateTime('05.04.2023')));
        self::assertEquals(3, $discountService->getDiscountForBooking(1000, new \DateTime('01.10.2023'), new \DateTime('05.05.2023')));
        self::assertEquals(7, $discountService->getDiscountForBooking(1000, new \DateTime('15.01.2024'), new \DateTime('05.08.2023')));
        self::assertEquals(5, $discountService->getDiscountForBooking(1000, new \DateTime('15.01.2024'), new \DateTime('05.09.2023')));
        self::assertEquals(3, $discountService->getDiscountForBooking(1000, new \DateTime('15.01.2024'), new \DateTime('05.10.2023')));
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testGetAgeDiscount(): void
    {
        $discountService = new DiscountService();
        $threeYearsDate = new \DateTime();
        $sixYearsDate = clone $threeYearsDate;
        $sixYearsDateTwo = clone $threeYearsDate;
        $twelveYearsOldDate = clone $threeYearsDate;

        self::assertEquals(0, $discountService->getAgeDiscount(20000, $sixYearsDateTwo->modify('-6 years')));
        self::assertEquals(80, $discountService->getAgeDiscount(1000, $threeYearsDate->modify('-3 years')));
        self::assertEquals(30, $discountService->getAgeDiscount(1000, $sixYearsDate->modify('-6 years')));
        self::assertEquals(10, $discountService->getAgeDiscount(1000, $twelveYearsOldDate->modify('-12 years')));
    }

    /**
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testGetDiscount(): void
    {
        $discountService = new DiscountService();

        $journeyRequest = new JourneyRequest();
        $journeyRequest->baseCost = 10000;
        $journeyRequest->travelStartDate = new \DateTime('01.01.2024');

        $journeyRequest->yearOfBirth = new \DateTime('01.01.2020');
        self::assertEquals(2000, $discountService->getDiscount($journeyRequest));
        $journeyRequest->paymentDate = new \DateTime('01.04.2023');
        self::assertEquals(1500, $discountService->getDiscount($journeyRequest));

        $journeyRequest->paymentDate = new \DateTime();
        $journeyRequest->yearOfBirth = new \DateTime('01.01.2017');
        self::assertEquals(7000, $discountService->getDiscount($journeyRequest));
        $journeyRequest->paymentDate = new \DateTime('01.04.2023');
        self::assertEquals(6500, $discountService->getDiscount($journeyRequest));


        $journeyRequest->paymentDate = new \DateTime();
        $journeyRequest->yearOfBirth = new \DateTime('01.01.2011');
        self::assertEquals(9000, $discountService->getDiscount($journeyRequest));
        $journeyRequest->paymentDate = new \DateTime('01.04.2023');
        self::assertEquals(8500, $discountService->getDiscount($journeyRequest));

        $journeyRequest->paymentDate = new \DateTime();
        $journeyRequest->yearOfBirth = new \DateTime('01.01.2000');
        self::assertEquals(10000, $discountService->getDiscount($journeyRequest));
        $journeyRequest->paymentDate = new \DateTime('01.04.2023');

        self::assertEquals(9500, $discountService->getDiscount($journeyRequest));
    }
}