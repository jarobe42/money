<?php

namespace Money;

use DateTimeInterface;
use Money\Exception\UnresolvableCurrencyPairException;

/**
 * Provides a way to get a historical exchange rate from a third-party source and return a currency pair.
 */
interface HistoricalExchange extends Exchange
{
    /**
     * Returns a currency pair for the passed currencies with the rate coming from a third-party source at a certain date.
     *
     * @param Currency          $baseCurrency
     * @param Currency          $counterCurrency
     * @param DateTimeInterface $date
     *
     * @return CurrencyPair
     *
     * @throws UnresolvableCurrencyPairException When there is no currency pair (rate) available for the given currencies
     */
    public function historical(Currency $baseCurrency, Currency $counterCurrency, DateTimeInterface $date);
}
