<?php
namespace WebSummerCamp\Tests\WebShop\Coupon;

use DateInterval;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;
use WebSummerCamp\WebShop\Coupon\LimitedLifetimeCoupon;
use WebSummerCamp\WebShop\Coupon\MinimumPurchaseAmount;
use WebSummerCamp\WebShop\Coupon\RateCoupon;
use WebSummerCamp\WebShop\Coupon\ValueCoupon;

class LimitedLifetimeCouponTest extends TestCase
{
    public function  testComplexCouponCombination() {
        $now = new \DateTimeImmutable('now');
        $half_year = new DateInterval('P6M');

        $coupon = new LimitedLifetimeCoupon(
            new MinimumPurchaseAmount(
                new RateCoupon('COUPON123', .20),
                new Money(7500, new Currency('EUR'))
            ),
            $now->sub($half_year),
            $now->add($half_year)
        );

        $this->assertEquals(
            new Money(8000, new Currency('EUR')),
            $coupon->apply(new Money(10000, new Currency('EUR')))
        );
    }

    public function testCouponIsEligible(): void
    {
        $now = new \DateTimeImmutable('now');
        $half_year = new DateInterval('P6M');

        $coupon = new LimitedLifetimeCoupon(
            new ValueCoupon('COUPON123', new Money(2000, new Currency('EUR'))),
            $now->sub($half_year),
            $now->add($half_year)
        );

        $this->assertEquals(
            new Money(9000, new Currency('EUR')),
            $coupon->apply(new Money(11000, new Currency('EUR')))
        );
    }

    public function testCouponIsNotEligible(): void
    {
        $coupon = new LimitedLifetimeCoupon(
            new ValueCoupon('COUPON123', new Money(2000, new Currency('EUR'))),
            new \DateTimeImmutable('2018-01-01 00:00:00'),
            new \DateTimeImmutable('2018-12-31 00:00:00')
        );

        $this->assertEquals(
            new Money(11000, new Currency('EUR')),
            $coupon->apply(new Money(11000, new Currency('EUR')))
        );
    }
}