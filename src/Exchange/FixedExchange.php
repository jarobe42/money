<?php

namespace Money\Exchange;

use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exception\UnresolvableHistoricalCurrencyPairException;
use Money\Exchange;

/**
 * Provides a way to get exchange rate from a static list (array).
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class FixedExchange implements Exchange
{
    /**
     * @var array
     */
    private $list;

    /**
     * @param array $list
     */
    public function __construct(array $list)
    {
        $this->list = $list;
    }

    /**
     * {@inheritdoc}
     */
    public function quote(Currency $baseCurrency, Currency $counterCurrency)
    {
        if (isset($this->list[$baseCurrency->getCode()][$counterCurrency->getCode()])) {
            return new CurrencyPair(
                $baseCurrency,
                $counterCurrency,
                $this->list[$baseCurrency->getCode()][$counterCurrency->getCode()]
            );
        }

        throw UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency);
    }

    /**
     * @param \DateTime $historicalDate
     * @param Currency $baseCurrency
     * @param Currency $counterCurrency
     * @return CurrencyPair
     */
    public function quoteHistorical(\DateTime $historicalDate, Currency $baseCurrency, Currency $counterCurrency)
    {
        throw UnresolvableHistoricalCurrencyPairException::createFromCurrenciesAndDateTime(
            $baseCurrency,
            $counterCurrency,
            $historicalDate
        );
    }
}
