<?php
namespace WebSummerCamp\Tests\WebShop\Coupon;

use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use WebSummerCamp\WebShop\Coupon\CouponBuilder;
use WebSummerCamp\WebShop\Coupon\LimitedLifetimeCoupon;
use WebSummerCamp\WebShop\Coupon\MinimumPurchaseAmount;
use WebSummerCamp\WebShop\Coupon\RateCoupon;
use WebSummerCamp\WebShop\Coupon\ValueCoupon;

class CouponBuilderTest extends TestCase
{
    public function  testCreateComplexValueCouponCombination(): void
    {
        $now = new \DateTimeImmutable('now');
        $nowFormatted = new \DateTimeImmutable($now->format('Y-m-d H:i:s'));
        $half_year = new \DateInterval('P6M');

        $expected = new LimitedLifetimeCoupon(
            new MinimumPurchaseAmount(
                new ValueCoupon('COUPON123', new Money(1500, new Currency('EUR'))),
                new Money(7500, new Currency('EUR'))
            ),
            $nowFormatted->sub($half_year),
            $nowFormatted->add($half_year)
        );

        $coupon = CouponBuilder::ofValue('COUPON123', 'EUR 1500')
            ->mustRequireMinimumPurchaseAmount('EUR 7500')
            ->mustBeValidBetween($nowFormatted->sub($half_year)->format('Y-m-d H:i:s'),  $nowFormatted->add($half_year)->format('Y-m-d H:i:s'))
            ->getCoupon();

        $this->assertEquals($expected, $coupon);
    }

    public function testCreateSimpleValueCoupon(): void
    {
        $this->assertEquals(
            new ValueCoupon(1, new Money(2000 , new Currency('EUR'))),
            CouponBuilder::ofValue('COUPON123', 'EUR 2000')->getCoupon()
        );
    }

    public function testCreateSimpleRateCoupon(): void
    {
        $this->assertEquals(
            new RateCoupon('COUPON123', .2),
            CouponBuilder::ofRate('COUPON123', .2)->getCoupon()
        );
    }
}