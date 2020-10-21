<?php

namespace WebSummerCamp\Tests\WebShop;

use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use WebSummerCamp\WebShop\ComboProduct;
use WebSummerCamp\WebShop\PhysicalProduct;

class ComboProductTest extends TestCase
{
    public function testComplexComboProductWithoutCustomPrice()
    {
        $products = [
            new PhysicalProduct(
                Uuid::uuid4(),
                new Money(12000, new Currency   ('EUR')),
                'WebSummerCamp'
            ),
            new ComboProduct(
                Uuid::uuid4(),
                'Nested Combo',
                [
                    new PhysicalProduct(
                        Uuid::uuid4(),
                        new Money(7000, new Currency   ('EUR')),
                        'WebSummerCamp'
                    ),
                    new PhysicalProduct(
                        Uuid::uuid4(),
                        new Money(8000, new Currency   ('EUR')),
                        'WebSummerCamp'
                    )
                ]
            ),
            new PhysicalProduct(
                Uuid::uuid4(),
                new Money(9000, new Currency   ('EUR')),
                'WebSummerCamp'
            )
        ];

        $combo = new ComboProduct(
            Uuid::uuid4(),
            'Test',
            $products
        );

        $this->assertEquals(
            new Money(36000, new Currency('EUR')),
            $combo->getUnitPrice()
        );
    }

    public function testComboProductWithCustomPrice()
    {
        $products = [
            new PhysicalProduct(
                Uuid::uuid4(),
                new Money(12000, new Currency   ('EUR')),
                'WebSummerCamp'
            ),
            new PhysicalProduct(
                Uuid::uuid4(),
                new Money(9000, new Currency   ('EUR')),
                'WebSummerCamp'
            )
        ];
        $combo = new ComboProduct(
            Uuid::uuid4(),
            'Test',
            $products,
            new Money(20000, new Currency('EUR')));

        $this->assertEquals(
            new Money(20000, new Currency('EUR')),
            $combo->getUnitPrice()
        );
    }

    public function testInvalidComboProduct(): void
    {
        $this->expectException(\Assert\AssertionFailedException::class);
        new ComboProduct(Uuid::uuid4(), 'Test', [
            new PhysicalProduct(
                Uuid::uuid4(),
                new Money(12000, new Currency   ('EUR')),
                'WebSummerCamp'
            )
        ]);
    }

    public function testSinglePhysicalProduct(): void
    {
        $product = new PhysicalProduct(
            Uuid::fromString('44d132cc-2c79-4212-8d23-a87956844381'),
            new Money(12000, new Currency   ('EUR')),
            'WebSummerCamp'
        );

        $this->assertEquals(
            Uuid::fromString('44d132cc-2c79-4212-8d23-a87956844381'),
            $product->getSku()
        );

        $this->assertEquals(
            new Money(12000, new Currency   ('EUR')),
            $product->getUnitPrice()
        );

        $this->assertEquals('WebSummerCamp', $product->getName());
    }
}