<?php

namespace Money\Exception;

use Money\Currency;
use Money\Exception;

/**
 * Thrown when there is no currency pair (rate) available for the given currencies.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class UnresolvableHistoricalCurrencyPairException extends \InvalidArgumentException implements Exception
{
    /**
     * Creates an exception from Currency objects.
     *
     * @param Currency $baseCurrency
     * @param Currency $counterCurrency
     *
     * @return UnresolvableHistoricalCurrencyPairException
     */
    public static function createFromCurrenciesAndDateTime(Currency $baseCurrency, Currency $counterCurrency, \DateTimeInterface $dateTime)
    {
        $message = sprintf(
            'Cannot resolve a currency pair for currencies: %s/%s on date %s',
            $baseCurrency->getCode(),
            $counterCurrency->getCode(),
            $dateTime->format('Y-m-d g:i:s')
        );

        return new self($message);
    }
}
