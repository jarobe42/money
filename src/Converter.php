<?php

namespace Money;
use Money\Exception\ConverterException;

/**
 * Provides a way to convert Money to Money in another Currency using an exchange rate.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class Converter
{
    /**
     * @var Currencies
     */
    private $currencies;

    /**
     * @var Exchange
     */
    private $exchange;

    /**
     * @param Currencies $currencies
     * @param Exchange   $exchange
     */
    public function __construct(Currencies $currencies, Exchange $exchange)
    {
        $this->currencies = $currencies;
        $this->exchange = $exchange;
    }

    /**
     * @param Money    $money
     * @param Currency $counterCurrency
     * @param int      $roundingMode
     *
     * @return Money
     */
    public function convert(Money $money, Currency $counterCurrency, $roundingMode = Money::ROUND_HALF_UP)
    {
        $baseCurrency = $money->getCurrency();

        $pair = $this->exchange->quote($baseCurrency, $counterCurrency);
        $ratio = $this->getRatioForCurrencyPair($pair);

        $counterValue = $money->multiply($ratio, $roundingMode);

        return new Money($counterValue->getAmount(), $counterCurrency);
    }

    /**
     * @param Money $money
     * @param Currency $counterCurrency
     * @param \DateTimeInterface $date
     * @param int $roundingMode
     * @return Money
     */
    public function convertHistorical(Money $money, Currency $counterCurrency, \DateTimeInterface $date, $roundingMode = Money::ROUND_HALF_UP)
    {
        if(!$this->exchange instanceof HistoricalExchange) {
            throw new ConverterException(
                sprintf("%s Exchange does not support historical conversions", [get_class($this->exchange)])
            );
        }
        $baseCurrency = $money->getCurrency();
        $pair = $this->exchange->historical($baseCurrency, $counterCurrency, $date);

        $ratio = $this->getRatioForCurrencyPair($pair);

        $counterValue = $money->multiply($ratio, $roundingMode);

        return new Money($counterValue->getAmount(), $counterCurrency);
    }

    /**
     * @param CurrencyPair $pair
     * @return float|int
     */
    private function getRatioForCurrencyPair(CurrencyPair $pair)
    {
        $ratio = $pair->getConversionRatio();

        $baseCurrencySubunit = $this->currencies->subunitFor($pair->getBaseCurrency());
        $counterCurrencySubunit = $this->currencies->subunitFor($pair->getCounterCurrency());
        $subunitDifference = $baseCurrencySubunit - $counterCurrencySubunit;

        return $ratio / pow(10, $subunitDifference);
    }
}
