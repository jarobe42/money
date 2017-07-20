<?php

namespace Tests\Money;

use Money\Converter;
use Money\Currencies;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exchange;
use Money\Money;
use Prophecy\Prophecy\ObjectProphecy;

final class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider convertExamples
     * @test
     */
    public function it_converts_to_a_different_currency(
        $baseCurrencyCode,
        $counterCurrencyCode,
        $subunitBase,
        $subunitCounter,
        $ratio,
        $amount,
        $expectedAmount
    ) {
        $baseCurrency = new Currency($baseCurrencyCode);
        $counterCurrency = new Currency($counterCurrencyCode);
        $pair = new CurrencyPair($baseCurrency, $counterCurrency, $ratio);

        /** @var Currencies|ObjectProphecy $currencies */
        $currencies = $this->prophesize(Currencies::class);

        /** @var Exchange|ObjectProphecy $exchange */
        $exchange = $this->prophesize(Exchange::class);

        $converter = new Converter($currencies->reveal(), $exchange->reveal());

        $currencies->subunitFor($baseCurrency)->willReturn($subunitBase);
        $currencies->subunitFor($counterCurrency)->willReturn($subunitCounter);

        $exchange->quote($baseCurrency, $counterCurrency)->willReturn($pair);

        $money = $converter->convert(
            new Money($amount, new Currency($baseCurrencyCode)),
            $counterCurrency
        );

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals($expectedAmount, $money->getAmount());
        $this->assertEquals($counterCurrencyCode, $money->getCurrency()->getCode());
    }

    /**
     * @dataProvider convertHistoricalExamples
     * @test
     */
    public function it_converts_to_a_different_currency_historically(
        $date,
        $baseCurrencyCode,
        $counterCurrencyCode,
        $subunitBase,
        $subunitCounter,
        $ratio,
        $amount,
        $expectedAmount
    ) {
        $date = new \DateTime($date);
        $baseCurrency = new Currency($baseCurrencyCode);
        $counterCurrency = new Currency($counterCurrencyCode);
        $pair = new CurrencyPair($baseCurrency, $counterCurrency, $ratio);

        /** @var Currencies|ObjectProphecy $currencies */
        $currencies = $this->prophesize(Currencies::class);

        /** @var Exchange|ObjectProphecy $exchange */
        $exchange = $this->prophesize(Exchange::class);

        $converter = new Converter($currencies->reveal(), $exchange->reveal());

        $currencies->subunitFor($baseCurrency)->willReturn($subunitBase);
        $currencies->subunitFor($counterCurrency)->willReturn($subunitCounter);

        $exchange->historical($baseCurrency, $counterCurrency, $date)->willReturn($pair);

        $money = $converter->convertHistorical(
            new Money($amount, new Currency($baseCurrencyCode)),
            $counterCurrency,
            $date
        );

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals($expectedAmount, $money->getAmount());
        $this->assertEquals($counterCurrencyCode, $money->getCurrency()->getCode());
    }

    public function convertExamples()
    {
        return [
            ['USD', 'JPY', 2, 0, 101, 100, 101],
            ['JPY', 'USD', 0, 2, 0.0099, 1000, 990],
            ['USD', 'EUR', 2, 2, 0.89, 100, 89],
            ['EUR', 'USD', 2, 2, 1.12, 100, 112],
        ];
    }

    public function convertHistoricalExamples()
    {
        return [
            ['2017-01-01', 'USD', 'JPY', 2, 0, 101, 100, 101],
            ['2016-01-01', 'JPY', 'USD', 0, 2, 0.0099, 1000, 990]
        ];
    }
}
