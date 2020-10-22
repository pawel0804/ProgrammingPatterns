<?php
namespace WebSummerCamp\WebShop\Coupon;

use Assert\Assertion;
use Money\Currency;
use Money\Money;

class ValueCouponBuilder implements CouponBuilder
{
    private $coupon;

    public function __construct(ValueCoupon $coupon)
    {
        $this->coupon = $coupon;
    }

    private static function parseMoney(string $value): Money
    {
        Assertion::regex($value, '/^[A-Z]{3} \d+$/');

        [$currencyCode, $amount] = explode(' ', $value);

        return new Money($amount, new Currency($currencyCode));
    }

    public function mustRequireMinimumPurchaseAmount(string $value): self
    {
        $this->coupon = new MinimumPurchaseAmount($this->coupon, static::parseMoney($value));

        return $this;
    }

    public function mustBeValidBetween(string $from, string $until): self
    {
        $this->coupon = new LimitedLifetimeCoupon(
            $this->coupon,
            new \DateTimeImmutable($from),
            new \DateTimeImmutable($until)
        );

        return $this;
    }

    public function getCoupon(): Coupon
    {
        return $this->coupon;
    }
}